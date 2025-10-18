<?php

declare(strict_types=1);

namespace App\Action\Aggregation\User;

use App\Models\Core\Company;
use App\Services\Bridge;
use Exception;
use Illuminate\Support\Facades\Log;

final readonly class AuthenticateUser
{
    public function __construct(
        private Bridge $bridge,
    ) {}

    public function get(): void
    {
        $company = Company::query()->first();

        try {
            $authToken = $this->bridge->post('aggregation/authorization/token', [
                'user_uuid' => $company->bridge_client_id,
            ]);
            cache()->put('bridge_access_token', $authToken['access_token']);
        } catch (Exception $exception) {
            Log::emergency('Bridge API error: '.$exception->getMessage(), ['exception' => $exception]);
        }
    }
}
