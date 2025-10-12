<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Batistack
{
    private $endpoint;

    public function __construct()
    {
        $this->endpoint = config('batistack.saas_endpoint');
    }

    /**
     * Send a GET request to the Batistack API.
     *
     * @param string $url The URL to send the GET request to.
     * @param array $data Optional data to send with the request.
     * @return array|null The JSON response from the API, or null if an error occurs.
     */
    public function get(string $url, array $data = [])
    {
        try {
            // Send a GET request without SSL verification
            $request = Http::withoutVerifying()
                ->get($this->endpoint . $url, $data);
            // Return the JSON response
            return $request->json();
        } catch (\Throwable $th) {
            // Log any errors that occur during the request
            Log::error('Batistack get error: ' . $th->getMessage());
            return null; // Return null on error
        }
    }
}