<?php

declare(strict_types=1);

use App\Livewire\Core\Page\Notification;
use App\Models\User;
use Livewire\Livewire;
use function Pest\Livewire\livewire;

test("Rendu du composant de notification", function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    
    livewire(Notification::class, ['notifications' => $user->notifications])
        ->assertStatus(200);
});
