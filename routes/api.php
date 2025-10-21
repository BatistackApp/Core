<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CoreController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('core')->group(function (): void {
    Route::get('backup-restore', [CoreController::class, 'backupRestore']);
    Route::get('/storage/info', [CoreController::class, 'storageInfo']);
});

Route::prefix('users')->group(function (): void {
    Route::get('/', [UserController::class, 'list']);
    Route::post('/', [UserController::class, 'create']);
    Route::get('/{user_id}/password-reset', [UserController::class, 'passwordReset']);
    Route::put('/{user_id}', [UserController::class, 'update']);
    Route::delete('/{user_id}', [UserController::class, 'delete']);
});
