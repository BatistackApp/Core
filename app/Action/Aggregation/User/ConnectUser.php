<?php

declare(strict_types=1);

namespace App\Action\Aggregation\User;

use App\Models\Core\Company;
use App\Services\Bridge;
use Exception;
use Illuminate\Support\Facades\Log;

final readonly class ConnectUser
{
    public function __construct(private Bridge $bridge) {}

    public function get(): string
    {
        $company = Company::query()->first();

        try {
            $session = $this->bridge->post('aggregation/connect-sessions', [
                'user_email' => $company->email,
                'country_code' => 'FR',
                'callback_url' => config('services.bridge.callback_url'),
            ], cache()->get('bridge_access_token'));

            if (array_key_exists('errors', $session)) {
                return $session['errors'][0]['message'];
            }

            return $session['url'];
        } catch (Exception $exception) {
            Log::emergency('Bridge API error: '.$exception->getMessage(), ['exception' => $exception]);

            return $exception->getMessage();
        }
    }
}
