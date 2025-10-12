<?php

declare(strict_types=1);

use App\Enums\Core\ServiceStatus;
use App\Models\Core\Service;
use App\Services\Batistack;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Log::info("Mise à jour du statut de la licence");
    $license = Service::all()->first()->service_code;
    Service::where('service_code', $license)->update([
        'status' => app(Batistack::class)->get('/license/info', ['license_key' => $license])['status'],
    ]);
})->hourly();
