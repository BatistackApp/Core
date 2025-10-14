<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Core\BackupRestoreSuccessful;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

final class CoreController extends Controller
{
    public function backupRestore(Request $request): JsonResponse
    {
        Artisan::call('down');
        $output = Artisan::call('backup:restore --no-interaction');

        if ($output === 0) {
            Artisan::call('up');
            User::all()->each(function (User $user) {
                $user->notify(new BackupRestoreSuccessful());
            });
            return response()->json([
                'message' => 'Restauration effectuée avec succès',
            ]);
        } else {
            return response()->json($output);
        }

        return response()->json([
            'message' => 'Erreur lors de la restauration',
        ], 500);

    }
}
