<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test("page d'accueil se charge rapidement", function () {
    // Mesurer le temps de chargement de la page d'accueil
    $startTime = microtime(true);

    $response = $this->get(route('home'));

    $endTime = microtime(true);
    $loadTime = $endTime - $startTime;

    // Enregistrer les résultats
    Log::info("Temps de chargement de la page d'accueil: {$loadTime} secondes");

    // Vérifier que la page se charge en moins de 1 seconde
    expect($loadTime)->toBeLessThan(1.0, "La page d'accueil prend trop de temps à charger");
    $response->assertStatus(200);
});
