<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test('tableau de bord se charge rapidement', function () {
    // Créer un utilisateur pour le test
    $user = User::factory()->create();
    $this->actingAs($user);

    // Mesurer le temps de chargement du tableau de bord
    $startTime = microtime(true);

    $response = $this->get(route('dashboard'));

    $endTime = microtime(true);
    $loadTime = $endTime - $startTime;

    // Enregistrer les résultats
    Log::info("Temps de chargement du tableau de bord: {$loadTime} secondes");

    // Vérifier que la page se charge en moins de 1 seconde
    expect($loadTime)->toBeLessThan(1.0, 'Le tableau de bord prend trop de temps à charger');
    $response->assertStatus(200);
});
