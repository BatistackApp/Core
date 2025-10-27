<?php

use App\Livewire\Tiers\TiersCreate;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(TiersCreate::class)
        ->assertStatus(200);
});
