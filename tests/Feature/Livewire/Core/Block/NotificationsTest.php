<?php

use App\Livewire\Core\Block\Notifications;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Notifications::class)
        ->assertStatus(200);
});
