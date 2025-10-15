<?php

declare(strict_types=1);

namespace App\Jobs\Core;

use App\Enums\Core\ServiceStatus;
use App\Models\Core\Option;
use App\Models\Core\Service;
use App\Services\Batistack;
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
            if (\App\Models\Core\Service::query()->first()->status === ServiceStatus::OK->value) {
                Log::info('Backup: Service OK');
                if (\App\Models\Core\Option::query()->where('slug', 'sauvegarde-et-retentions')->exists()) {
                    Log::info('Backup: Option sauvegarde-et-retentions existe');
                    Artisan::call('backup:run', ['--only-db' => true]);
                    $api->post('/backup', [
                        'license_key' => \App\Models\Core\Service::query()->first()->service_code,
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
}
