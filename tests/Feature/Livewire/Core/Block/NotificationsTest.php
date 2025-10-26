<?php

use App\Livewire\Core\Block\Notifications;
use App\Models\User;
use Livewire\Livewire;

it("Accès aux notifications non lues", function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Notifications::class, ['notifications' => $user->unreadNotifications])
        ->assertStatus(200);
});
