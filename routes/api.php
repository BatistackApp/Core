<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn(Request $request) => $request->user())->middleware('auth:sanctum');

Route::prefix('core')->group(function (): void {
    Route::get('backup-restore', [CoreController::class, 'backupRestore']);
    Route::get('/storage/info', [CoreController::class, 'storageInfo']);
});
