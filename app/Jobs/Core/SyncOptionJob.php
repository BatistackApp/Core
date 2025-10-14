<?php

namespace App\Jobs\Core;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;

class SyncOptionJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $slugOption, public array $settings)
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
        Artisan::call('backup:run --only-db');
    }

    /**
     * Synchronisation des extensions de stockages.
     */
    private function syncExtensionStockages(): void
    {
        //
    }
}
