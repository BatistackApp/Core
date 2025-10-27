<?php

use Illuminate\Support\Facades\Route;

Route::prefix('tiers')->group(function () {
    Route::get('/', \App\Livewire\Tiers\TiersList::class)->name('tiers.dashboard');
    Route::get('create', \App\Livewire\Tiers\TiersCreate::class)->name('tiers.create');
});