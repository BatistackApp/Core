<?php

use Illuminate\Support\Facades\Route;

Route::prefix('tiers')->group(function () {
    Route::get('/', \App\Livewire\Tiers\TiersList::class)->name('tiers.dashboard');
});