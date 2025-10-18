<?php

declare(strict_types=1);

use App\Services\Batistack;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', fn (): Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory => view('welcome'))->name('home');
Route::get('/test', function (): void {
    $api = new Batistack();

    dd($api->get('/license/info', ['license_key' => 'SRV-20251017-9S3N8']));
});

Route::middleware(['auth'])->group(function (): void {
    Route::get('/dashboard', fn (): Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory => view('dashboard'))->name('dashboard');
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

require __DIR__.'/auth.php';
