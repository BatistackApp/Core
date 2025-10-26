<?php

declare(strict_types=1);

namespace App\Jobs\Core;

use App\Enums\Core\ServiceStatus;
use App\Models\Core\Bank;
use App\Models\Core\Option;
use App\Models\Core\Service;
use App\Services\Batistack;
use App\Services\Bridge;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

final class SyncOptionJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $slugOption, public array $settings = [])
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        match ($this->slugOption) {
            'pack-signature' => $this->syncPackSignature(),
            'sauvegarde-et-retentions' => $this->syncSauvegardeRetentions(),
            'extension-stockages' => $this->syncExtensionStockages(),
            'aggregation-bancaire' => $this->syncAggregationBancaire(),
            default => null
        };
    }

    /**
     * Synchronisation du pack de signature.
     */
    private function syncPackSignature(): void
    {
        //
    }

    /**
     * Synchronisation de la sauvegarde et des retentions.
     */
    private function syncSauvegardeRetentions(): void
    {
        $api = new Batistack();

        try {
            if (Service::query()->first()->status === ServiceStatus::OK->value) {
                Log::info('Backup: Service OK');
                if (Option::query()->where('slug', 'sauvegarde-et-retentions')->exists()) {
                    Log::info('Backup: Option sauvegarde-et-retentions existe');
                    Artisan::call('backup:run', ['--only-db' => true]);
                    $api->post('/backup', [
                        'license_key' => Service::query()->first()->service_code,
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::emergency('Backup: Erreur lors de la synchronisation de la sauvegarde et des retentions', ['exception' => $e]);
        }
    }

    /**
     * Synchronisation des extensions de stockages.
     */
    private function syncExtensionStockages(): void
    {
        //
    }

    /**
     * Synchronisation de l'aggregation bancaire.
     */
    private function syncAggregationBancaire(): void
    {
        $banks = app(Bridge::class)->get('/providers');

        if (count(Bank::all()) === 0) {
            foreach ($banks['resources'] as $bank) {
                Bank::query()->create([
                    'bridge_id' => $bank['id'],
                    'name' => $bank['name'],
                    'logo_bank' => $bank['images']['logo'],
                    'status_aggregation' => $bank['health_status']['aggregation']['status'],
                    'status_payment' => $bank['health_status']['single_payment']['status'],
                ]);
            }
        }        
    }
}
