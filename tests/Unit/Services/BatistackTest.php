<?php

declare(strict_types=1);

use App\Services\Batistack;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

// Utiliser TestCase pour activer les façades Laravel
uses(TestCase::class);

// Définir les variables partagées
$testEndpoint = 'https://api.example.com';

// Configuration avant chaque test
beforeEach(function () use ($testEndpoint) {
    // Configurer le mock pour la fonction config()
    Config::partialMock();
    Config::shouldReceive('get')
        ->withArgs(['batistack.saas_endpoint', null])
        ->andReturn($testEndpoint);
});

// Test de la méthode get avec une réponse réussie
test('get method returns json response', function () use ($testEndpoint) {
    // Arrange
    $testUrl = '/test-endpoint';
    $testData = ['param' => 'value'];
    $expectedResponse = ['status' => 'success', 'data' => ['key' => 'value']];

    // Mock HTTP facade
    Http::fake([
        "{$testEndpoint}{$testUrl}*" => Http::response($expectedResponse, 200),
    ]);

    // Act
    $batistack = new Batistack();
    $result = $batistack->get($testUrl, $testData);

    // Assert
    expect($result)->toBe($expectedResponse);
});

// Test de la méthode get avec une exception
test('get method returns null on exception', function () {
    // Arrange
    $testUrl = '/test-endpoint';

    // Mock HTTP facade to throw exception
    Http::fake(function () {
        throw new Exception('Test exception');
    });

    // Mock Log facade
    Log::shouldReceive('error')
        ->once()
        ->withArgs(function ($message) {
            return $message === 'Batistack get error: Test exception';
        });

    // Act
    $batistack = new Batistack();
    $result = $batistack->get($testUrl);

    // Assert
    expect($result)->toBeNull();
});

// Test de la méthode post avec une réponse réussie
test('post method returns json response', function () use ($testEndpoint) {
    // Arrange
    $testUrl = '/test-endpoint';
    $testData = ['param' => 'value'];
    $expectedResponse = ['status' => 'success', 'data' => ['key' => 'value']];

    // Mock HTTP facade
    Http::fake([
        "{$testEndpoint}{$testUrl}" => Http::response($expectedResponse, 200),
    ]);

    // Act
    $batistack = new Batistack();
    $result = $batistack->post($testUrl, $testData);

    // Assert
    expect($result)->toBe($expectedResponse);
});

// Test de la méthode post avec une exception
test('post method returns null on exception', function () {
    // Arrange
    $testUrl = '/test-endpoint';

    // Mock HTTP facade to throw exception
    Http::fake(function () {
        throw new Exception('Test exception');
    });

    // Mock Log facade
    Log::shouldReceive('error')
        ->once()
        ->withArgs(function ($message) {
            return $message === 'Batistack post error: Test exception';
        });

    // Act
    $batistack = new Batistack();
    $result = $batistack->post($testUrl);

    // Assert
    expect($result)->toBeNull();
});
