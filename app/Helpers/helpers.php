<?php

use App\Models\Core\Module;

if (! function_exists('get_version')) {
    function get_version(): string
    {
        if (config('app.env') === 'local' || config('app.env') === 'testing') {
            return 'dev';
        } else {
            // Appel à github pour rechercher la dernière version du programme
            $url = 'https://api.github.com/repos/batistack/app_batistack/releases/latest';
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            return $data['tag_name'];
        }
    }
}

if (! function_exists('moduleIsExist')) {
    function moduleIsExist(string $slug): bool
    {
        return Module::where('slug', $slug)->exists();
    }
}