<?php
namespace App\Action\Aggregation\User;

use App\Models\Core\Company;
use App\Services\Bridge;
use Illuminate\Support\Facades\Log;

class ConnectUser
{
    public function get()
    {
        $company = Company::query()->first();

        try {
            $session = app(Bridge::class)->post('aggregation/connect-sessions', [
                'user_email' => $company->email,
                'country_code' => 'FR',
                'callback_url' => config('services.bridge.callback_url'),
            ], cache()->get('bridge_access_token'));

            if (array_key_exists('errors', $session)) {
                return $session['errors'][0]['message'];
            }

            return $session['url'];
        } catch (\Exception $exception) {
            Log::emergency("Bridge API error: ".$exception->getMessage(), ['exception' => $exception]);
            return $exception->getMessage();
        }
    }
}