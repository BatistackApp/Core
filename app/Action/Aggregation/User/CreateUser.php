<?php

declare(strict_types=1);

namespace App\Action\Aggregation\User;

use App\Models\Core\Company;
use App\Services\Bridge;
use Exception;
use Illuminate\Support\Facades\Log;

final readonly class CreateUser
{
    public function __construct(private Bridge $bridge) {}

    public function get(): string|null
    {
        $company = \App\Models\Core\Company::query()->first();

        // Création du compte si il n'existe pas
        if (! $company->bridge_client_id) {
            try {
                $user_account = $this->bridge->post('aggregation/users', [
                    'external_user_id' => 'USER'.random_int(1, 50000),
                ]);
                $company->update(['bridge_client_id' => $user_account['uuid']]);

                return $user_account['uuid'];
            } catch (Exception $exception) {
                Log::emergency('Bridge API error: '.$exception->getMessage(), ['exception' => $exception]);

                return null;
            }
        } else {
            return null;
        }
    }
}
