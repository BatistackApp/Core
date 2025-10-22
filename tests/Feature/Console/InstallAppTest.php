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

// Fonctions helper définies au niveau global
function getDefaultBatistackResponse(array $overrides = []): array
{
    $default = [
        'id' => 1,
        'service_code' => 'test-service',
        'status' => 'active',
        'max_user' => 10,
        'storage_limit' => 1000,
        'product' => ['features' => []],
        'modules' => [],
        'options' => [],
        'customer' => [
            'entreprise' => 'Test Company',
            'adresse' => 'Test Address',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'pays' => 'France',
            'tel' => '0123456789',
            'user' => ['email' => 'test@company.com']
        ]
    ];

    return array_merge($default, $overrides);
}

function mockCountriesApi(): void
{
    Http::fake([
        'https://gist.githubusercontent.com/revolunet/6173043/raw/222c4537affb1bdecbabcec51143742709aa0b6e/countries-FR.json' => Http::response(['France', 'Belgique'])
    ]);
}

beforeEach(function () {
    $this->batistackMock = Mockery::mock(Batistack::class);
    $this->app->instance(Batistack::class, $this->batistackMock);
    
    // Prévenir les requêtes HTTP non mockées
    Http::preventStrayRequests();
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
        ->times(5)
        ->andReturn(getDefaultBatistackResponse());

    mockCountriesApi();

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
        ->times(5)
        ->andReturn(getDefaultBatistackResponse());

    mockCountriesApi();    

    $this->artisan('app:install', ['license_key' => $licenseKey])
        ->expectsOutput('License key valide')
        ->assertExitCode(0);
});

test('installs all components successfully', function () {
    $licenseKey = 'valid-license';
    
    // Mock Batistack responses
    $this->batistackMock
        ->shouldReceive('get')
        ->with('/license/info', ['license_key' => $licenseKey])
        ->times(5)
        ->andReturn(getDefaultBatistackResponse([
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
            ]
        ]));

    mockCountriesApi();

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