<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\Core\SyncOptionJob;
use App\Models\Comptabilite\PlanComptable;
use App\Models\Core\Bank;
use App\Models\Core\Company;
use App\Models\Core\ConditionReglement;
use App\Models\Core\Country;
use App\Models\Core\ModeReglement;
use App\Models\Core\Module;
use App\Models\Core\Option;
use App\Models\Core\Service;
use App\Services\Batistack;
use App\Services\Bridge;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

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
        $this->installCities();
        $this->installCountries();
        $this->installPcg();
        $this->defineCompanyInfo($license_key);
        $this->installConditionReglement();
        $this->installModeReglement();

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

        Service::query()->updateOrCreate(['service_code' => $response['service_code']], [
            'status' => $response['status'],
            'max_user' => $response['max_user'],
            'storage_limit' => $response['storage_limit'],
        ]);

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

            Module::query()->updateOrCreate(['slug' => Str::replace('module-', '', (string) $module['feature']['slug'])], [
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

            Option::query()->updateOrCreate(['slug' => $option['product']['slug']], [
                'name' => $option['product']['name'],
                'slug' => $option['product']['slug'],
                'settings' => json_encode($option['settings']),
            ]);

            // Déclenche le job de synchronisation des options
            dispatch(new SyncOptionJob($option['product']['slug'], $option['settings']));
        }

        $this->info('Installation des options réussie');
    }

    /**
     * Installation des villes.
     */
    private function installCities(): void
    {
        $this->info('Installation des villes');

        $cities = \App\Models\Core\City::query()->get();

        if ($cities->count() > 0) {
            $this->info('Villes déjà installées');

            return;
        }

        $this->info('Installation des villes');

        $cities = json_decode(file_get_contents(base_path('database/json/cities.json')), true);

        $chunks = array_chunk($cities, 500);
        $totalChunks = count($chunks);
        $this->info('Nombre de tranches : '.$totalChunks);

        foreach ($chunks as $i => $chunk) {
            $this->info('Traitement de la tranche '.($i + 1)."/{$totalChunks}");
            $bar = $this->output->createProgressBar(count($chunk));

            foreach ($chunk as $city) {
                $latLong = explode(',', (string) $city['coordonnees_gps']);
                \App\Models\Core\City::query()->updateOrCreate(['postal_code' => $city['Code_postal']], [
                    'city' => $city['Nom_commune'],
                    'postal_code' => $city['Code_postal'],
                    'latitude' => $latLong[0] ?? '',
                    'longitude' => $latLong[1] ?? '',
                ]);
                $bar->advance();
            }
            $bar->finish();
        }

        $this->info('Installation des villes réussie');
    }

    /**
     * Installation des pays.
     */
    private function installCountries(): void
    {
        if (Country::count() === 0) {
            $this->info('Installation des informations des pays');

            $countries = Http::withoutVerifying()
                ->get('https://gist.githubusercontent.com/revolunet/6173043/raw/222c4537affb1bdecbabcec51143742709aa0b6e/countries-FR.json')
                ->json();

            $bar = $this->output->createProgressBar(count($countries));
            $bar->start();

            foreach ($countries as $country) {
                Country::create([
                    'name' => $country,
                ]);
                $bar->advance();
            }
            $bar->finish();
        }
    }

    /**
     * Installation des plans comptables.
     */
    private function installPcg(): void
    {
        $collects = (new FastExcel)->import(base_path('database/json/pcg.xlsx'));
        if (PlanComptable::count() === 0) {
            $this->info('Installation du plan comptable général');
            $bar = $this->output->createProgressBar($collects->count());
            $bar->start();

            foreach ($collects as $account) {
                PlanComptable::create([
                    'code' => $account['Code'],
                    'account' => $account['account'],
                    'type' => $account['type'],
                    'lettrage' => $account['lettrage'] === true,
                    'principal' => $account['principal'],
                    'initial' => (float) $account['initial'],
                ]);
                $bar->advance();
            }

            $bar->finish();
        }
    }

    private function defineCompanyInfo(string $license_key)
    {
        $api = new Batistack();
        $response = $api->get('/license/info', ['license_key' => $license_key]);

        if (! isset($response['customer'])) {
            $this->error('Installation des informations de la société impossible');
        }

        // Vérifier si l'option 'aggregation-bancaire' est présente
        $hasBankAggregation = false;
        $bridge_client_id = null;
        if (isset($response['options']) && is_array($response['options'])) {
            foreach ($response['options'] as $option) {
                if (isset($option['product']['slug']) && $option['product']['slug'] === 'aggregation-bancaire') {
                    $hasBankAggregation = true;
                    $this->importBank();
                    break;
                }
            }
        }

        if ($hasBankAggregation) {
            // Link Vers Bridge Api
        }

        Company::query()->create([
            'name' => $response['customer']['entreprise'],
            'address' => $response['customer']['adresse'],
            'code_postal' => $response['customer']['code_postal'],
            'ville' => $response['customer']['ville'],
            'pays' => $response['customer']['pays'],
            'phone' => $response['customer']['tel'],
            'email' => $response['customer']['user']['email'],
            'bridge_client_id' => $bridge_client_id,
        ]);
    }

    private function importBank()
    {
        if (Bank::count() !== 0) {
            $bridge = new Bridge();
            $this->info('Installation des banques française');

            try {
                $call = $bridge->get('/providers?limit=500&country_code=FR')['resources'];
                $progress = $this->output->createProgressBar(count($call));
                $progress->start();

                foreach ($call as $bank) {
                    Bank::create([
                        'bridge_id' => $bank['id'],
                        'name' => $bank['name'],
                        'logo_bank' => $bank['images']['logo'],
                        'status_aggegation' => isset($bank['health_status']['aggregation']['status']) ? $bank['health_status']['aggregation']['status'] : null,
                        'status_payment' => isset($bank['health_status']['single_payment']['status']) ? $bank['health_status']['single_payment']['status'] : null,
                    ]);
                    $progress->advance();
                }

                $progress->finish();
            } catch (Exception $exception) {
                Log::error($exception);
                $this->error("Erreur lors de l'importation des banques, Base primaire insérer");
                if (app()->environment('local', 'testing')) {
                    $this->info('Importation des banques en mode local ou de test, banque de test insérée');
                    Bank::create([
                        'bridge_id' => 1,
                        'name' => 'Banque de Test',
                        'logo_bank' => 'https://bank.test',
                        'status_aggegation' => 'healthy',
                        'status_payment' => 'healthy',
                    ]);
                }
            }
        }
    }

    /**
     * Installation des conditions de réglement.
     */
    private function installConditionReglement(): void
    {
        if (ConditionReglement::count() === 0) {
            ConditionReglement::create([
                'code' => 'RECEP',
                'name' => 'A Réception',
                'name_document' => 'A Réception',
                'nb_jours' => 1,
                'fdm' => false,
            ]);
            ConditionReglement::create([
                'code' => '30D',
                'name' => '30 Jours',
                'name_document' => 'Réglement à 30 jours',
                'nb_jours' => 30,
                'fdm' => false,
            ]);
            ConditionReglement::create([
                'code' => '30DMONTH',
                'name' => '30 Jours fin de mois',
                'name_document' => 'Réglement à 30 jours fin de mois',
                'nb_jours' => 30,
                'fdm' => true,
            ]);
        }
    }

    /**
     * Installation des modes de réglement.
     */
    private function installModeReglement(): void
    {
        if (ModeReglement::count() === 0) {
            ModeReglement::create([
                'code' => 'CB',
                'name' => 'Carte Bancaire',
                'type_paiement' => json_encode(['client', 'fournisseur']),
                'bridgeable' => true,
            ]);
            ModeReglement::create([
                'code' => 'ESP',
                'name' => 'Espèce',
                'type_paiement' => json_encode(['client', 'fournisseur']),
                'bridgeable' => false,
            ]);
            ModeReglement::create([
                'code' => 'VIRSEPA',
                'name' => 'Virement SEPA',
                'type_paiement' => json_encode(['client', 'fournisseur']),
                'bridgeable' => true,
            ]);
            ModeReglement::create([
                'code' => 'PRLV',
                'name' => 'Prélèvement Bancaire',
                'type_paiement' => json_encode(['fournisseur']),
                'bridgeable' => false,
            ]);
            ModeReglement::create([
                'code' => 'CHQ',
                'name' => 'Chèque',
                'type_paiement' => json_encode(['fournisseur', 'client']),
                'bridgeable' => false,
            ]);
        }
    }
}
