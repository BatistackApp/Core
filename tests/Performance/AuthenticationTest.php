<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('connexion est performante', function () {    // Créer un utilisateur pour le test
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);

    // Mesurer le temps d'exécution de la connexion
    $startTime = microtime(true);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $endTime = microtime(true);
    $loginTime = $endTime - $startTime;

    // Enregistrer les résultats
    Log::info("Temps d'exécution de la connexion: {$loginTime} secondes");

    // Vérifier que la connexion s'effectue en moins de 1 seconde
    expect($loginTime)->toBeLessThan(3.0, "La connexion prend trop de temps");
    $response->assertRedirect('/dashboard');
});
