<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('requête utilisateurs est performante', function () {
    // Créer plusieurs utilisateurs pour le test
    User::factory()->count(100)->create();

    // Mesurer le temps d'exécution de la requête
    $startTime = microtime(true);

    $users = User::all();

    $endTime = microtime(true);
    $queryTime = $endTime - $startTime;

    // Enregistrer les résultats
    Log::info("Temps d'exécution de la requête User::all(): {$queryTime} secondes");

    // Vérifier que la requête s'exécute en moins de 0.5 seconde
    expect($queryTime)->toBeLessThan(0.5, "La requête User::all() prend trop de temps");
});
