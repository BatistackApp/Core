<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

final class CoreController extends Controller
{
    public function backupRestore(Request $request): JsonResponse
    {
        $backupName = $request->backup ?? 'latest';
        $output = Artisan::call('backup:restore --no-interaction --backup='.$backupName);

        if ($output === 0) {
            return response()->json([
                'message' => 'Restauration effectuée avec succès',
            ]);
        }

        return response()->json([
            'message' => 'Erreur lors de la restauration',
        ], 500);

    }
}
