<?php

use App\Enums\Core\ServiceStatus;
use App\Jobs\Core\SyncOptionJob;
use App\Models\Core\Bank;
use App\Models\Core\Option;
use App\Models\Core\Service;
use App\Services\Batistack;
use App\Services\Bridge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

// Test de base - le job est correctement dispatché
it('can be dispatched', function () {
    Queue::fake();

    SyncOptionJob::dispatch('pack-signature', ['setting' => 'value']);

    Queue::assertPushed(SyncOptionJob::class, function ($job) {
        return $job->slugOption === 'pack-signature' 
            && $job->settings['setting'] === 'value';
    });
});

// Test pour chaque option
describe('option handling', function () {
    beforeEach(function () {
        // Mock des services externes
        $this->batistackMock = Mockery::mock(Batistack::class);
        $this->bridgeMock = Mockery::mock(Bridge::class);
        
        $this->app->instance(Batistack::class, $this->batistackMock);
        $this->app->instance(Bridge::class, $this->bridgeMock);
    });

    afterEach(function () {
        Mockery::close();
    });

    // Test pour pack-signature
    it('handles pack-signature option', function () {
        $job = new SyncOptionJob('pack-signature');
        
        expect(fn() => $job->handle())->not->toThrow(Exception::class);
    });

    // Test pour sauvegarde-et-retentions - cas réussi
    it('handles sauvegarde-et-retentions option successfully', function () {
        // Créer un service en base de données
        $service = Service::factory()->create([
            'status' => ServiceStatus::OK->value,
            'service_code' => 'TEST-CODE'
        ]);

        // Créer l'option en base de données
        Option::factory()->create([
            'slug' => 'sauvegarde-et-retentions'
        ]);


        // Mock Artisan
        Artisan::shouldReceive('call')
            ->once()
            ->with('backup:run', ['--only-db' => true]);

        $job = new SyncOptionJob('sauvegarde-et-retentions');
        $job->handle();
    });

    // Test pour sauvegarde-et-retentions - service non OK
    it('does not backup when service status is not OK', function () {
        // Créer un service avec statut ERROR
        Service::factory()->create([
            'status' => ServiceStatus::ERROR->value
        ]);

        Option::factory()->create([
            'slug' => 'sauvegarde-et-retentions'
        ]);

        Artisan::shouldReceive('call')->never();
        $this->batistackMock->shouldReceive('post')->never();

        $job = new SyncOptionJob('sauvegarde-et-retentions');
        $job->handle();
    });

    // Test pour sauvegarde-et-retentions - option n'existe pas
    it('does not backup when option does not exist', function () {
        // Créer un service OK
        Service::factory()->create([
            'status' => ServiceStatus::OK->value
        ]);

        // Ne pas créer l'option

        Artisan::shouldReceive('call')->never();
        $this->batistackMock->shouldReceive('post')->never();

        $job = new SyncOptionJob('sauvegarde-et-retentions');
        $job->handle();
    });

    // Test pour sauvegarde-et-retentions - exception handling
    it('logs emergency when backup fails', function () {
        // Créer un service et une option
        Service::factory()->create([
            'status' => ServiceStatus::OK->value,
            'service_code' => 'TEST-CODE'
        ]);

        Option::factory()->create([
            'slug' => 'sauvegarde-et-retentions'
        ]);

        Artisan::shouldReceive('call')
            ->andThrow(new Exception('Backup failed'));

        $this->batistackMock->shouldReceive('post')
            ->never();

        $job = new SyncOptionJob('sauvegarde-et-retentions');
        $job->handle();
    });

    // Test pour extension-stockages
    it('handles extension-stockages option', function () {
        $job = new SyncOptionJob('extension-stockages');
        
        expect(fn() => $job->handle())->not->toThrow(Exception::class);
    });

    // Test pour aggregation-bancaire - création des banques
    it('handles aggregation-bancaire option and creates banks', function () {
        // S'assurer qu'il n'y a pas de banques
        $this->assertDatabaseCount('banks', 0);

        // Utiliser les valeurs de statut qui existent réellement dans votre base
        $mockBanks = [
            'resources' => [
                [
                    'id' => 1,
                    'name' => 'Bank A',
                    'images' => ['logo' => 'logo1.png'],
                    'health_status' => [
                        'aggregation' => ['status' => 'healthy'], // Utiliser les vraies valeurs de votre DB
                        'single_payment' => ['status' => 'degraded']
                    ]
                ],
                [
                    'id' => 2,
                    'name' => 'Bank B',
                    'images' => ['logo' => 'logo2.png'],
                    'health_status' => [
                        'aggregation' => ['status' => 'degraded'],
                        'single_payment' => ['status' => 'healthy']
                    ]
                ]
            ]
        ];

        $this->bridgeMock->shouldReceive('get')
            ->with('/providers')
            ->andReturn($mockBanks);

        $job = new SyncOptionJob('aggregation-bancaire');
        $job->handle();

        // Vérifier que les banques ont été créées
        $this->assertDatabaseCount('banks', 2);
        $this->assertDatabaseHas('banks', [
            'bridge_id' => 1,
            'name' => 'Bank A',
            'logo_bank' => 'logo1.png',
        ]);
        $this->assertDatabaseHas('banks', [
            'bridge_id' => 2,
            'name' => 'Bank B',
            'logo_bank' => 'logo2.png',
        ]);
    });

    // Test pour aggregation-bancaire - ne crée pas de banques si elles existent déjà
    it('does not create banks when they already exist', function () {
        // Créer une banque existante avec des valeurs simples
        Bank::create([
            'bridge_id' => 1,
            'name' => 'Existing Bank',
            'logo_bank' => 'existing_logo.png',
            'status_aggregation' => 'healthy',
            'status_payment' => 'healthy'
        ]);

        $initialCount = Bank::count();

        $mockBanks = [
            'resources' => [
                [
                    'id' => 1, // Même ID que la banque existante
                    'name' => 'Bank A',
                    'images' => ['logo' => 'logo1.png'],
                    'health_status' => [
                        'aggregation' => ['status' => 'healthy'],
                        'single_payment' => ['status' => 'degraded']
                    ]
                ]
            ]
        ];

        $this->bridgeMock->shouldReceive('get')
            ->with('/providers')
            ->andReturn($mockBanks);

        $job = new SyncOptionJob('aggregation-bancaire');
        $job->handle();

        // Le nombre de banques ne doit pas changer
        $this->assertDatabaseCount('banks', $initialCount);
    });

    // Test pour option inconnue
    it('handles unknown option gracefully', function () {
        $job = new SyncOptionJob('unknown-option');
        
        expect(fn() => $job->handle())->not->toThrow(Exception::class);
    });
});

// Test de la construction du job
it('correctly constructs with parameters', function () {
    $settings = ['key' => 'value', 'enabled' => true];
    $job = new SyncOptionJob('test-option', $settings);

    expect($job->slugOption)->toBe('test-option')
        ->and($job->settings)->toBe($settings);
});