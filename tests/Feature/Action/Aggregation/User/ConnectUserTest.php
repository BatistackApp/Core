<?php

declare(strict_types=1);
use App\Action\Aggregation\User\ConnectUser;
use App\Models\Core\Company;
use App\Services\Bridge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->bridgeMock = Mockery::mock(Bridge::class);
    Cache::put('bridge_access_token', 'test-token');
    $this->connectUserAction = new ConnectUser($this->bridgeMock);
});

afterEach(function () {
    Mockery::close();
});

it('returns connect session url on success', function () {
    $company = Company::factory()->create();
    $expectedUrl = 'https://connect.bridge.com/session-123';

    $this->bridgeMock
        ->shouldReceive('post')
        ->with('aggregation/connect-sessions', [
            'user_email' => $company->email,
            'country_code' => 'FR',
            'callback_url' => config('services.bridge.callback_url'),
        ], 'test-token')
        ->once()
        ->andReturn(['url' => $expectedUrl]);

    app()->instance(Bridge::class, $this->bridgeMock);

    $result = $this->connectUserAction->get();

    expect($result)->toBe($expectedUrl);
});

it('returns error message when bridge returns errors', function () {
    $company = Company::factory()->create();
    $errorMessage = 'Invalid token';

    $this->bridgeMock
        ->shouldReceive('post')
        ->once()
        ->andReturn(['errors' => [['message' => $errorMessage]]]);

    app()->instance(Bridge::class, $this->bridgeMock);

    $result = $this->connectUserAction->get();

    expect($result)->toBe($errorMessage);
});

it('returns exception message on bridge api exception', function () {
    $company = Company::factory()->create();

    $this->bridgeMock
        ->shouldReceive('post')
        ->once()
        ->andThrow(new Exception('Connection failed'));

    app()->instance(Bridge::class, $this->bridgeMock);

    Log::shouldReceive('emergency')
        ->once();

    $result = $this->connectUserAction->get();

    expect($result)->toBe('Connection failed');
});
