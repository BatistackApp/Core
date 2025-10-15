<?php

declare(strict_types=1);

use App\Enums\Core\ServiceStatus;
use App\Models\Core\Option;
use App\Models\Core\Service;
use App\Services\Batistack;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

$api = new Batistack();

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (): void {
    Log::info('Mise à jour du statut de la licence');
    $license = \App\Models\Core\Service::query()->first()->service_code;
    \App\Models\Core\Service::query()->where('service_code', $license)->update([
        'status' => app(Batistack::class)->get('/license/info', ['license_key' => $license])['status'],
    ]);
})->hourly();

Schedule::call(function () use ($api): void {
    Log::info('Backup: Vérification du statut de la licence');
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
})->twiceDaily(5, 21)
    ->name("Backup de l'application");
