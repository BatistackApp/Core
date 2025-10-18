<?php

declare(strict_types=1);
use App\Action\Aggregation\User\AuthenticateUser;
use App\Models\Core\Company;
use App\Services\Bridge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->bridgeMock = Mockery::mock(Bridge::class);
});

afterEach(function () {
    Mockery::close();
});

test('gets auth token and caches it', function () {
    $company = Company::factory()->create(['bridge_client_id' => 'test-client-id']);
    $token = 'test-access-token';

    $this->bridgeMock
        ->shouldReceive('post')
        ->with('aggregation/authorization/token', [
            'user_uuid' => 'test-client-id',
        ])
        ->once()
        ->andReturn(['access_token' => $token]);

    // Passer le mock au constructeur
    $authUserAction = new AuthenticateUser($this->bridgeMock);
    $authUserAction->get();

    expect(Cache::get('bridge_access_token'))->toBe($token);
});

test('logs emergency on bridge api exception', function () {
    $company = Company::factory()->create(['bridge_client_id' => 'test-client-id']);

    $this->bridgeMock
        ->shouldReceive('post')
        ->once()
        ->andThrow(new Exception('Auth API Error'));

    Log::shouldReceive('emergency')
        ->once()
        ->with('Bridge API error: Auth API Error', Mockery::type('array'));

    // Passer le mock au constructeur
    $authUserAction = new AuthenticateUser($this->bridgeMock);
    $authUserAction->get();
});
