<?php
namespace App\Action\Aggregation\User;

use App\Models\Core\Company;
use App\Services\Bridge;
use Illuminate\Support\Facades\Log;

class CreateUser
{
    public function get()
    {
        $api = new Bridge();

        $company = Company::first();

        // Création du compte si il n'existe pas
        if (!$company->bridge_client_id) {
            try {
                $user_account = $api->post('aggregation/users', [
                    'external_user_id' => 'USER'.random_int(1, 50000),
                ]);
                $company->update(['bridge_client_id' => $user_account['uuid']]);
                return $user_account['uuid'];
            } catch (\Exception $exception) {
                Log::emergency("Bridge API error: ".$exception->getMessage(), ['exception' => $exception]);
            }
        }        
    }
}