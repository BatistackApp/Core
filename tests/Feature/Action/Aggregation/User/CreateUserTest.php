<?php

declare(strict_types=1);

use App\Action\Aggregation\User\CreateUser;
use App\Models\Core\Company;
use App\Services\Bridge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test('creates bridge user when company has no bridge_client_id', function () {
    $company = Company::factory()->create(['bridge_client_id' => null]);
    $userUuid = 'user-12345';

    // Crée un mock partiel sur une instance existante
    $bridgeInstance = app(Bridge::class);
    $bridgeMock = Mockery::mock($bridgeInstance);

    $bridgeMock
        ->shouldReceive('post')
        ->with('aggregation/users', Mockery::type('array'))
        ->once()
        ->andReturn(['uuid' => $userUuid]);

    $createUserAction = new CreateUser($bridgeMock);
    $result = $createUserAction->get();

    expect($result)->toBe($userUuid);
    expect($company->fresh()->bridge_client_id)->toBe($userUuid);
});

test('does not create bridge user when company already has bridge_client_id', function () {
    $existingUuid = 'existing-123';
    $company = Company::factory()->create(['bridge_client_id' => $existingUuid]);

    $bridgeInstance = app(Bridge::class);
    $bridgeMock = Mockery::mock($bridgeInstance);

    $bridgeMock
        ->shouldReceive('post')
        ->never();

    $createUserAction = new CreateUser($bridgeMock);
    $result = $createUserAction->get();

    expect($result)->toBeNull();
    expect($company->fresh()->bridge_client_id)->toBe($existingUuid);
});

test('logs emergency on bridge api exception', function () {
    $company = Company::factory()->create(['bridge_client_id' => null]);

    $bridgeInstance = app(Bridge::class);
    $bridgeMock = Mockery::mock($bridgeInstance);

    $bridgeMock
        ->shouldReceive('post')
        ->once()
        ->andThrow(new Exception('API Error'));

    Log::shouldReceive('emergency')
        ->once()
        ->with('Bridge API error: API Error', Mockery::type('array'));

    $createUserAction = new CreateUser($bridgeMock);
    $result = $createUserAction->get();

    expect($result)->toBeNull();
});
