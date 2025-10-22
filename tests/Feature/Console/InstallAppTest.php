<?php

use App\Action\Aggregation\User\AuthenticateUser;
use App\Action\Aggregation\User\CreateUser;
use App\Models\Core\{
    Service, 
    Module, 
    Option, 
    Company, 
    Country,
    ConditionReglement,
    ModeReglement
};
use App\Console\Commands\InstallApp;
use App\Services\Batistack;
use App\Services\Bridge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->batistackMock = Mockery::mock(Batistack::class);
    $this->app->instance(Batistack::class, $this->batistackMock);
});

afterEach(function () {
    Mockery::close();
});

test('can be instantiated', function () {
    $command = new InstallApp();
    expect($command)->toBeInstanceOf(InstallApp::class);
    expect($command->getName())->toBe('app:install');
});

test('requires license key argument', function () {
    $this->expectException(\Symfony\Component\Console\Exception\RuntimeException::class);
    Artisan::call('app:install');
});

// Test de vérification de license
test('validates license key successfully', function () {
    $licenseKey = 'test-license-key';
    
    $this->batistackMock
        ->shouldReceive('get')
        ->with('/license/info', ['license_key' => $licenseKey])
        ->once()
        ->andReturn(['id' => 1, 'service_code' => 'test-service']);

    Artisan::call('app:install', ['license_key' => $licenseKey]);
    
    $this->assertDatabaseHas('services', [
        'service_code' => 'test-service'
    ]);
});

test('fails with invalid license key', function () {
    $licenseKey = 'invalid-license';
    
    $this->batistackMock
        ->shouldReceive('get')
        ->with('/license/info', ['license_key' => $licenseKey])
        ->once()
        ->andReturn([]);

    $this->artisan('app:install', ['license_key' => $licenseKey])
        ->expectsOutput('License key valide')
        ->assertExitCode(0);
});

it('installs all components successfully', function () {
    $licenseKey = 'valid-license';
    
    // Mock Batistack responses
    $this->batistackMock
        ->shouldReceive('get')
        ->with('/license/info', ['license_key' => $licenseKey])
        ->andReturn([
            'id' => 1,
            'service_code' => 'test-service',
            'status' => 'active',
            'max_user' => 10,
            'storage_limit' => 1000,
            'product' => ['features' => []],
            'modules' => [
                [
                    'feature' => [
                        'slug' => 'module-test',
                        'name' => 'Test Module'
                    ],
                    'is_active' => true
                ]
            ],
            'options' => [
                [
                    'product' => [
                        'slug' => 'test-option',
                        'name' => 'Test Option'
                    ],
                    'settings' => ['key' => 'value']
                ]
            ],
            'customer' => [
                'entreprise' => 'Test Company',
                'adresse' => 'Test Address',
                'code_postal' => '75000',
                'ville' => 'Paris',
                'pays' => 'France',
                'tel' => '0123456789',
                'user' => ['email' => 'test@company.com']
            ]
        ]);

    // Mock HTTP for countries
    Http::fake([
        'https://gist.githubusercontent.com/*' => Http::response(['France', 'Belgique'])
    ]);

    // Mock CreateUser and AuthenticateUser
    $createUserMock = Mockery::mock(CreateUser::class);
    $createUserMock->shouldReceive('get')->andReturn('bridge-client-id');
    $this->app->instance(CreateUser::class, $createUserMock);

    $authUserMock = Mockery::mock(AuthenticateUser::class);
    $authUserMock->shouldReceive('get');
    $this->app->instance(AuthenticateUser::class, $authUserMock);

    // Mock Bridge for CreateUser and bank import
    $bridgeMock = Mockery::mock(Bridge::class);
    $bridgeMock->shouldReceive('post')
        ->with('aggregation/users', Mockery::any())
        ->andReturn(['uuid' => 'bridge-client-id']);
    $bridgeMock->shouldReceive('get')
        ->with('/providers?limit=500&country_code=FR')
        ->andReturn(['resources' => []]);
    $this->app->instance(Bridge::class, $bridgeMock);

    // Create an instance of CreateUser with the mocked Bridge
    $createUserInstance = new CreateUser($bridgeMock);
    $this->app->instance(CreateUser::class, $createUserInstance);

    // Mock AuthenticateUser
    $authUserMock = Mockery::mock(AuthenticateUser::class);
    $authUserMock->shouldReceive('get');
    $this->app->instance(AuthenticateUser::class, $authUserMock);

    // Fake queue
    Queue::fake();

    // Execute command
    Artisan::call('app:install', ['license_key' => $licenseKey]);

    // Assertions
    $this->assertDatabaseHas('services', ['service_code' => 'test-service']);
    $this->assertDatabaseHas('modules', ['slug' => 'test']);
    $this->assertDatabaseHas('options', ['slug' => 'test-option']);
    $this->assertDatabaseHas('companies', ['name' => 'Test Company']);
    $this->assertDatabaseCount('condition_reglements', 3);
    $this->assertDatabaseCount('mode_reglements', 5);
    
    Queue::assertPushed(\App\Jobs\Core\SyncOptionJob::class);
});

test('installs cities from json file', function () {
    // Create test JSON file
    $testCities = [
        [
            'Code_postal' => '75001',
            'Nom_commune' => 'Paris 1',
            'coordonnees_gps' => '48.8592,2.3417'
        ],
        [
            'Code_postal' => '13001',
            'Nom_commune' => 'Marseille 1',
            'coordonnees_gps' => '43.2965,5.3698'
        ]
    ];

    Storage::fake('local');
    Storage::put('database/json/cities.json', json_encode($testCities));

    $this->artisan('app:install', ['license_key' => 'test-license'])
        ->expectsOutput('Installation des villes');

    $this->assertDatabaseCount('cities', 2);
    $this->assertDatabaseHas('cities', [
        'postal_code' => '75001',
        'city' => 'Paris 1'
    ]);
});

test('skips city installation when cities already exist', function () {
    \App\Models\Core\City::factory()->create();

    $this->artisan('app:install', ['license_key' => 'test-license'])
        ->expectsOutput('Villes déjà installées');
});

test('installs countries from API', function () {
    Http::fake([
        'https://gist.githubusercontent.com/revolunet/6173043/raw/222c4537affb1bdecbabcec51143742709aa0b6e/countries-FR.json' => Http::response([
            'France', 'Belgique', 'Suisse'
        ])
    ]);

    $this->artisan('app:install', ['license_key' => 'test-license'])
        ->expectsOutput('Installation des informations des pays');

    $this->assertDatabaseCount('countries', 3);
    $this->assertDatabaseHas('countries', ['name' => 'France']);
});

it('skips country installation when countries already exist', function () {
    Country::factory()->create();

    $this->artisan('app:install', ['license_key' => 'test-license'])
        ->doesntExpectOutput('Installation des informations des pays');
});

test('imports banks from bridge API', function () {
    $bridgeMock = Mockery::mock(Bridge::class);
    $bridgeMock->shouldReceive('get')
        ->with('/providers?limit=500&country_code=FR')
        ->andReturn([
            'resources' => [
                [
                    'id' => 1,
                    'name' => 'BNP Paribas',
                    'images' => ['logo' => 'logo-url'],
                    'health_status' => [
                        'aggregation' => ['status' => 'healthy'],
                        'single_payment' => ['status' => 'healthy']
                    ]
                ]
            ]
        ]);
    $this->app->instance(Bridge::class, $bridgeMock);

    $this->artisan('app:install', ['license_key' => 'test-license'])
        ->expectsOutput('Installation des banques française');

    $this->assertDatabaseHas('banks', [
        'bridge_id' => 1,
        'name' => 'BNP Paribas'
    ]);
});

test('handles bridge API errors gracefully', function () {
    $bridgeMock = Mockery::mock(Bridge::class);
    $bridgeMock->shouldReceive('get')
        ->andThrow(new Exception('API Error'));

    $this->app->instance(Bridge::class, $bridgeMock);

    $this->artisan('app:install', ['license_key' => 'test-license'])
        ->expectsOutput("Erreur lors de l'importation des banques, Base primaire insérer");
});