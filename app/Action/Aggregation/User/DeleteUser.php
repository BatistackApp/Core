<?php

declare(strict_types=1);

namespace App\Action\Aggregation\User;

use App\Models\Core\Company;
use App\Services\Bridge;
use Exception;
use Illuminate\Support\Facades\Log;

final readonly class DeleteUser
{
    // Delete the user from the bridge API
    public function __construct(private Bridge $bridge) {}

    public function get(): void
    {
        $company = Company::query()->first();

        try {
            $this->bridge->delete('aggregation/users/'.$company->bridge_client_id, withToken: cache()->get('bridge_access_token'));
            $company->update([
                'bridge_client_id' => null,
            ]);
        } catch (Exception $exception) {
            Log::emergency('Bridge API error: '.$exception->getMessage(), ['exception' => $exception]);
        }
    }
}
