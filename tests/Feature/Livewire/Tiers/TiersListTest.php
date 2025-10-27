<?php

use App\Livewire\Tiers\TiersList;
use App\Models\User;
use Livewire\Livewire;
use function Pest\Livewire\livewire;

test("Render TiersList component", function () {
    $user = User::factory()->create();
    $this->actingAs($user);


    livewire(TiersList::class)
        ->assertStatus(200);
});
