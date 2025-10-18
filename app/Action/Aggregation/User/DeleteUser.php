<?php

use App\Models\Core\Company;
use App\Services\Bridge;
use Illuminate\Support\Facades\Log;

class DeleteUser
{
    // 
    public function get()
    {
        $company = Company::query()->first();

        try {
            app(Bridge::class)->delete('aggregation/users/'.$company->bridge_user_id);
            $company->update([
                'bridge_user_id' => null,
            ]);
        } catch (Exception $exception) {
            Log::emergency("Bridge API error: ".$exception->getMessage(), ['exception' => $exception]);
        }
    }
}