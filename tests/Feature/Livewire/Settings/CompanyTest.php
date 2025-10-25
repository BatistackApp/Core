<?php

declare(strict_types=1);

use App\Livewire\Core\Settings\Company;
use App\Models\Core\Company as CoreCompany;
use App\Models\User;
use function Pest\Livewire\livewire;

test('Accès à la page de paramètres de l\'entreprise', function () {
    $user = User::factory()->create();
    $company = CoreCompany::factory()->create();
    $this->actingAs($user);

    livewire(Company::class, ['company' => $company])
        ->assertStatus(200);
});

test("Mise à jour des informations de l'entreprise", function () {
    $user = User::factory()->create();
    $company = CoreCompany::factory()->create();
    $this->actingAs($user);

    livewire(Company::class, ['company' => $company])
        ->assertStatus(200)
        ->fillForm([
            'email' => 'test@example.com',
            'phone' => '0123456789',
            'fax' => '0123456789',
            'web' => 'https://example.com',
            'siret' => '12345678901234',
            'ape' => '12345',
            'num_tva' => 'FR12345678901',
            'capital' => '100000',
            'rcs' => '1234567890123',
        ])
        ->call('updateSetting')
        ->assertHasNoErrors();
});