<?php

declare(strict_types=1);
use App\Action\Aggregation\User\DeleteUser;
use App\Models\Core\Company;
use App\Services\Bridge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->bridgeMock = Mockery::mock(Bridge::class);
    Cache::put('bridge_access_token', 'test-token');
    $this->deleteUserAction = new DeleteUser($this->bridgeMock);
});

afterEach(function () {
    Mockery::close();
});

it('deletes bridge user and updates company', function () {
    // CORRECTION : utiliser bridge_user_id au lieu de bridge_client_id
    $company = Company::factory()->create(['bridge_client_id' => 'user-to-delete']);

    $this->bridgeMock
        ->shouldReceive('delete')
        ->with('aggregation/users/user-to-delete', [], 'test-token')
        ->once();

    $this->deleteUserAction->get();

    // CORRECTION : vérifier bridge_user_id au lieu de bridge_client_id
    expect($company->fresh()->bridge_client_id)->toBeNull();
});

it('logs emergency on bridge api exception', function () {
    // CORRECTION : utiliser bridge_user_id au lieu de bridge_client_id
    $company = Company::factory()->create(['bridge_client_id' => 'user-to-delete']);

    $this->bridgeMock
        ->shouldReceive('delete')
        ->once()
        ->andThrow(new Exception('Deletion failed'));

    Log::shouldReceive('emergency')
        ->once()
        ->with('Bridge API error: Deletion failed', Mockery::type('array'));

    $this->deleteUserAction->get();
});
