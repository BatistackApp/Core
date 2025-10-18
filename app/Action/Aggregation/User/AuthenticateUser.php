<?php
namespace App\Action\Aggregation\User;

use App\Models\Core\Company;
use App\Services\Bridge;
use Illuminate\Support\Facades\Log;

class AuthenticateUser
{
    public function get()
    {
        $company = Company::query()->first();
        
        try {
            $authToken = app(Bridge::class)->post('aggregation/authorization/token', [
                'user_uuid' => $company->bridge_client_id,
            ]);
            cache()->put('bridge_access_token', $authToken['access_token']);
        } catch (\Exception $exception) {
            Log::emergency("Bridge API error: ".$exception->getMessage(), ['exception' => $exception]);
        }
    }
}