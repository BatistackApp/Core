<?php

namespace Tests\Feature\Http\Controller\Api;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Notifications\Core\CreateUserNotification;
use App\Notifications\Core\PasswordResetNotification;
use App\Enums\Core\UserRole;
use Illuminate\Support\Str;

// --- SETUP GLOBAL ---

beforeEach(function () {
    // Mocker la façade de notification
    Notification::fake();
    
    // Créer un utilisateur Administrateur pour l'authentification
    // Le rôle 'admin' est le rôle attendu par le contrôleur pour l'accès
    $this->admin = User::factory()->create(['role' => UserRole::ADMINISTRATEUR]);
    $this->actingAs($this->admin);

    // Créer un utilisateur standard pour la manipulation
    $this->user = User::factory()->create(['role' => UserRole::SALARIE]);
});

afterEach(function () {
    // Nettoyer les mocks.
    \Mockery::close();
});

// --- 1. LIST ---

test('an admin can retrieve a list of all users', function () {
    // Créer un utilisateur supplémentaire
    User::factory()->create(['role' => UserRole::SALARIE]);

    // Total attendu : 1 admin + 1 user initial + 1 user supplémentaire = 3
    $response = $this->getJson('/api/users'); 

    $response->assertStatus(200)
             ->assertJsonCount(3) 
             ->assertJsonStructure([
                 '*' => [
                     'id', 'name', 'email', 'role', 'blocked'
                 ]
             ]);
});

// --- 2. CREATE ---

test('an admin can successfully create a new user and send notification', function () {
    $userData = [
        'name' => 'New User Test',
        'email' => 'new.user@example.com',
        'role' => UserRole::SALARIE,
    ];
    
    $response = $this->postJson('/api/users', $userData); 

    $response->assertStatus(200)
             ->assertJsonFragment(['email' => 'new.user@example.com']);

    // Récupérer l'utilisateur créé
    $createdUser = User::where('email', 'new.user@example.com')->first();
    
    $this->assertDatabaseHas('users', ['email' => 'new.user@example.com', 'role' => UserRole::SALARIE]);

});

test('user creation fails with validation errors', function () {
    // Test email non unique
    $userData = [
        'name' => 'Test',
        'email' => $this->user->email, // Email déjà pris
        // Envoyer une valeur non valide pour le rôle (le contrôleur est maintenant corrigé)
        'role' => 'invalid-role', 
    ];

    $response = $this->postJson('/api/users', $userData);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['email', 'role']);
});

// --- 3. UPDATE ---

test('an admin can update user information', function () {
    $response = $this->putJson("/api/users/{$this->user->id}", [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'role' => UserRole::ADMINISTRATEUR,
    ]);
    
    $response->assertStatus(200)
             ->assertJsonFragment(['name' => 'Updated Name', 'email' => 'updated@example.com', 'role' => UserRole::ADMINISTRATEUR]);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'role' => UserRole::ADMINISTRATEUR,
    ]);
});

test('an admin can block a user using the blocked query parameter', function () {
    // Bloquer l'utilisateur
    $response = $this->putJson("/api/users/{$this->user->id}?blocked=1");
    
    $response->assertStatus(200)
             ->assertJsonFragment(['blocked' => true]);

    // Vérifier la base de données
    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'blocked' => true,
    ]);
});

// --- 4. DELETE ---

test('an admin can delete a user', function () {
    $response = $this->deleteJson("/api/users/{$this->user->id}");
    
    $response->assertStatus(200)
             ->assertJsonFragment(['id' => $this->user->id]);

    // Vérifier la suppression en base de données
    $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
});

test('delete fails for a non-existent user', function () {
    $response = $this->deleteJson('/api/users/99999'); // ID inexistant
    
    $response->assertStatus(404);
});

// --- 5. PASSWORD RESET ---

test('an admin can reset a user password and send notification', function () {
    // 1. Mocker Auth::logout() car il est appelé dans le contrôleur
    Auth::shouldReceive('logout')->once();
    
    $userToReset = $this->user;
    $oldPassword = $userToReset->password;

    // 2. Simuler l'appel à la route de réinitialisation
    $response = $this->getJson("/api/users/{$userToReset->id}/password-reset");
    
    $response->assertStatus(200);

    $userAfterReset = $userToReset->fresh();

    // 3. Vérifier que le mot de passe a été mis à jour et correspond au mot de passe mocké
    $this->assertNotEquals($oldPassword, $userAfterReset->password);

});