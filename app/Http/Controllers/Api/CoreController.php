<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CoreController extends Controller
{
    public function backupRestore(Request $request)
    {
        $output = Artisan::call("backup:restore --no-interaction --backup=" . $request->backup ?? 'latest');
        
        if($output === 0) {
            return response()->json([
                'message' => 'Restauration effectuée avec succès',
            ]);
        } else {
            return response()->json([
                'message' => 'Erreur lors de la restauration',
            ], 500);
        }
    }
}
