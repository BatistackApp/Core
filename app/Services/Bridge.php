<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Core\Company;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Bridge
{
    private string $client_id;

    private string $client_secret;

    public function __construct()
    {
        $this->client_id = config('services.bridge.client_id');
        $this->client_secret = config('services.bridge.client_secret');
    }

    /**
     * @return array<string, mixed>|null
     */
    public function get(string $folder, ?array $data = null, ?string $withToken = null): ?array
    {
        try {
            if ($withToken !== null && $withToken !== '' && $withToken !== '0') {
                /** @var array<string, mixed>|null $request */
                $request = Http::withoutVerifying()->withHeaders([
                    'Bridge-Version' => config('services.bridge.version'),
                    'Client-Id' => $this->client_id,
                    'Client-Secret' => $this->client_secret,
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                    'authorization' => 'Bearer '.$withToken,
                ])
                    ->get(config('services.bridge.endpoint').$folder, $data)
                    ->json();
            } else {
                /** @var array<string, mixed>|null $request */
                $request = Http::withoutVerifying()->withHeaders([
                    'Bridge-Version' => config('services.bridge.version'),
                    'Client-Id' => $this->client_id,
                    'Client-Secret' => $this->client_secret,
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ])
                    ->get(config('services.bridge.endpoint').$folder, $data)
                    ->json();
            }

            return $request !== null ? (array) $request : null;
        } catch (Exception $exception) {
            Log::emergency($exception);
            Log::channel('github')->emergency($exception);

            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function post(string $folder, ?array $data = null, ?string $withToken = null): ?array
    {
        try {
            if ($withToken !== null && $withToken !== '' && $withToken !== '0') {
                /** @var array<string, mixed>|null $request */
                $request = Http::withoutVerifying()->withHeaders([
                    'Bridge-Version' => config('services.bridge.version'),
                    'Client-Id' => $this->client_id,
                    'Client-Secret' => $this->client_secret,
                    'accept' => 'application/json',
                    'authorization' => 'Bearer '.$withToken,
                    'content-type' => 'application/json',
                ])
                    ->post(config('services.bridge.endpoint').$folder, $data)
                    ->json();
            } else {
                /** @var array<string, mixed>|null $request */
                $request = Http::withoutVerifying()->withHeaders([
                    'Bridge-Version' => config('services.bridge.version'),
                    'Client-Id' => $this->client_id,
                    'Client-Secret' => $this->client_secret,
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ])
                    ->post(config('services.bridge.endpoint').$folder, $data)
                    ->json();
            }

            return $request !== null ? (array) $request : null;
        } catch (Exception $exception) {
            Log::emergency($exception);
            Log::channel('github')->emergency($exception);

            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function delete(string $folder, ?array $data = null, ?string $withToken = null): ?array
    {
        try {
            if ($withToken !== null && $withToken !== '' && $withToken !== '0') {
                /** @var array<string, mixed>|null $request */
                $request = Http::withoutVerifying()->withHeaders([
                    'Bridge-Version' => config('services.bridge.version'),
                    'Client-Id' => $this->client_id,
                    'Client-Secret' => $this->client_secret,
                    'accept' => 'application/json',
                    'authorization' => 'Bearer '.$withToken,
                    'content-type' => 'application/json',
                ])
                    ->delete(config('services.bridge.endpoint').$folder, $data)
                    ->json();
            } else {
                /** @var array<string, mixed>|null $request */
                $request = Http::withoutVerifying()->withHeaders([
                    'Bridge-Version' => config('services.bridge.version'),
                    'Client-Id' => $this->client_id,
                    'Client-Secret' => $this->client_secret,
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ])
                    ->delete(config('services.bridge.endpoint').$folder, $data)
                    ->json();
            }

            return $request !== null ? (array) $request : null;
        } catch (Exception $exception) {
            Log::emergency($exception);
            Log::channel('github')->emergency($exception);

            return null;
        }
    }

    public function getAccessToken(): void
    {
        if (! cache()->has('bridge_access_token')) {
            $authToken = $this->post('aggregation/authorization/token', [
                'user_uuid' => Company::query()->first()->bridge_client_id,
            ]);

            if ($authToken && isset($authToken['access_token'])) {
                cache()->put('bridge_access_token', $authToken['access_token']);
            }
        }
    }
}
