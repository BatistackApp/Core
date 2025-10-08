<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test('requêtes multiples sont performantes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $routes = [
        route('home'),
        route('dashboard'),
        route('profile.edit'),
        route('password.edit'),
    ];

    $totalTime = 0;

    // Exécuter plusieurs requêtes séquentiellement et mesurer le temps total
    $startTime = microtime(true);

    foreach ($routes as $route) {
        $this->get($route);
    }

    $endTime = microtime(true);
    $totalTime = $endTime - $startTime;

    // Enregistrer les résultats
    Log::info('Temps total pour exécuter '.count($routes)." requêtes: {$totalTime} secondes");

    // Vérifier que toutes les requêtes s'exécutent en moins de 3 secondes
    expect($totalTime)->toBeLessThan(3.0, "L'exécution de plusieurs requêtes prend trop de temps");
});
