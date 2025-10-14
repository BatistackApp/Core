<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\Core\SyncOptionJob;
use App\Models\Core\Module;
use App\Models\Core\Option;
use App\Models\Core\Service;
use App\Services\Batistack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class InstallApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install {license_key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Installation de l'application";

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $license_key = $this->argument('license_key');

        $this->verifKey($license_key);
        $this->installService($license_key);
        $this->installModules($license_key);
        $this->installOptions($license_key);

        return 0;
    }

    /**
     * Vérification de la clé d'activation.
     */
    private function verifKey(string $license_key): void
    {
        $api = new Batistack();
        $response = $api->get('/license/info', ['license_key' => $license_key]);

        if (! isset($response['id'])) {
            $this->error('License key invalide');
        }

        $this->info('License key valide');
    }

    /**
     * Installation du service.
     */
    private function installService(string $license_key): void
    {
        $api = new Batistack();
        $response = $api->get('/license/info', ['license_key' => $license_key]);

        if (! isset($response['id'])) {
            $this->error('Installation du service impossible');
        }

        info("Installation du service : {$response['id']}");

        Service::updateOrCreate(
            ['service_code' => $response['service_code']],
            [
                'status' => $response['status'],
                'max_user' => $response['max_user'],
                'storage_limit' => $response['storage_limit'],
            ]
        );

        Storage::disk('public')->makeDirectory('upload');

        $this->info('Installation du service réussie');
    }

    /**
     * Installation des modules.
     */
    private function installModules(string $license_key): void
    {
        $api = new Batistack();
        $response = $api->get('/license/info', ['license_key' => $license_key]);

        if (! isset($response['product']['features'])) {
            $this->error('Installation des modules impossible');
        }

        $this->info('Installation des modules');

        foreach ($response['modules'] as $module) {
            $this->info("Installation du module : {$module['feature']['name']}");

            Module::updateOrCreate(
                ['slug' => Str::replace('module-', '', (string) $module['feature']['slug'])],
                [
                    'name' => $module['feature']['name'],
                    'is_active' => $module['is_active'],
                ]);
            // Lancement du seeder si disponible
        }

        $this->info('Installation des modules réussie');
    }

    /**
     * Installation des options.
     */
    private function installOptions(string $license_key): void
    {
        $api = new Batistack();
        $response = $api->get('/license/info', ['license_key' => $license_key]);

        if (! isset($response['options'])) {
            $this->error('Installation des options impossible');
        }

        $this->info('Installation des options');

        foreach ($response['options'] as $option) {
            $this->info("Installation de l'option : {$option['product']['name']}");

            Option::updateOrCreate(
                ['slug' => $option['product']['slug']],
                [
                    'name' => $option['product']['name'],
                    'slug' => $option['product']['slug'],
                    'settings' => json_encode($option['settings']),
                ]
            );

            // Déclenche le job de synchronisation des options
            dispatch(new SyncOptionJob($option['product']['slug'], $option['settings']));
        }

        $this->info('Installation des options réussie');
    }
}
