<?php

use App\Models\Core\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\Core\BackupRestoreSuccessful;

beforeEach(function () {
    // 1. Faker la façade de notification pour intercepter les envois
    Notification::fake();
    
    // 2. Créer quelques utilisateurs pour le test (si nécessaire)
    User::factory()->count(2)->create(); 
    
    // 3. Authentifier l'utilisateur si la route le requiert
    // $this->actingAs(User::factory()->create());
});

// Test avec factory
it('returns storage information correctly with factory', function () {
    // Créer un service en base de données
    $service = Service::factory()->create([
        'storage_limit' => 2
    ]);

    // Taille simulée des fichiers
    $file1Size = 0.8 * 1024 * 1024 * 1024;
    $file2Size = 0.7 * 1024 * 1024 * 1024;
    $totalSizeBytes = $file1Size + $file2Size;
    
    $expectedStorageLimitBytes = $service->storage_limit * 1024 * 1024 * 1024;

    $expectedData = [
        'storage_used' => $totalSizeBytes,
        'storage_used_gb' => round($totalSizeBytes / (1024 * 1024 * 1024), 2),
        'storage_used_mb' => round($totalSizeBytes / (1024 * 1024), 2),
        'storage_used_percentage' => round(($totalSizeBytes / $expectedStorageLimitBytes) * 100, 2),
    ];

    // Mock du Storage
    Storage::shouldReceive('disk->allFiles')
        ->with('upload')
        ->andReturn(['upload/file1.jpg', 'upload/file2.pdf']);
    
    Storage::shouldReceive('disk->size')
        ->with('upload/file1.jpg')
        ->andReturn($file1Size);
    
    Storage::shouldReceive('disk->size')
        ->with('upload/file2.pdf')
        ->andReturn($file2Size);

    $controller = new \App\Http\Controllers\Api\CoreController();
    $response = $controller->storageInfo(new Request());

    $responseData = $response->getData();
    
    expect($responseData)->toHaveCount(1);
    expect((array)$responseData[0])->toEqual($expectedData);
});

// --- SCÉNARIO 1 : RESTAURATION AVEC SUCCÈS ---

test("Exécute la restauration de la sauvegarde avec succès et notifie les utilisateurs", function () {
    // --- Mocker les commandes Artisan en cas de SUCCÈS ---
    // 1. On s'attend à ce que 'down' soit appelé en premier
    Artisan::shouldReceive('call')
           ->once()
           ->with('down')
           ->andReturn(0);

    // 2. On s'attend à ce que 'backup:restore' soit appelé et retourne 0 (succès)
    Artisan::shouldReceive('call')
           ->once()
           ->with('backup:restore --no-interaction')
           ->andReturn(0);

    // 3. On s'attend à ce que 'up' soit appelé après le succès de la restauration
    Artisan::shouldReceive('call')
           ->once()
           ->with('up')
           ->andReturn(0);


    // --- EXÉCUTER ET VÉRIFIER ---
    $response = $this->getJson('/api/core/backup-restore'); 

    $response->assertStatus(200)
             ->assertJson([
                 'message' => 'Restauration effectuée avec succès',
             ]);

    // VÉRIFIER LES NOTIFICATIONS
    $users = User::all();
    
    // Vérifier que la notification a été envoyée au bon nombre d'utilisateurs
    Notification::assertCount($users->count(), BackupRestoreSuccessful::class);
    
    // Vérifier que chaque utilisateur a bien reçu la notification
    $users->each(function (User $user) {
        Notification::assertSentTo($user, BackupRestoreSuccessful::class);
    });

});

// --- SCÉNARIO 2 : ÉCHEC DE LA RESTAURATION ---

test("Gère l'échec de la restauration de la sauvegarde et retourne l'output d'Artisan", function () {
    $errorMessage = "Backup file not found.";

    // --- Mocker les commandes Artisan en cas d'ÉCHEC ---
    // 1. 'down' est appelé
    Artisan::shouldReceive('call')
           ->once()
           ->with('down')
           ->andReturn(0); // Le passage en mode down est toujours considéré comme un succès

    // 2. 'backup:restore' est appelé et retourne 1 (échec) et un message d'erreur
    Artisan::shouldReceive('call')
           ->once()
           ->with('backup:restore --no-interaction')
           ->andReturn($errorMessage); // Le contrôleur retourne directement cet output s'il n'est pas 0

    // 3. On VÉRIFIE que 'up' n'est PAS appelé car la restauration a échoué
    Artisan::shouldNotReceive('call')
           ->with('up'); 
           
    // --- EXÉCUTER ET VÉRIFIER ---
    $response = $this->getJson('/api/core/backup-restore'); 

    $response->assertStatus(200)
             // CORRECTION: assertExactJson et assertJson attendent un array.
             // assertContent() vérifie le corps brut de la réponse HTTP.
             // On utilise json_encode() pour obtenir la chaîne exacte, incluant les guillemets.
             ->assertContent(json_encode($errorMessage));
    
    // VÉRIFIER L'ABSENCE DE NOTIFICATION
    Notification::assertNothingSent();

});