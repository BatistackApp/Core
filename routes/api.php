<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CoreController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('core')->group(function (): void {
    Route::get('backup-restore', [CoreController::class, 'backupRestore']);
    Route::get('/storage/info', [CoreController::class, 'storageInfo']);
});

Route::prefix('users')->group(function (): void {
    Route::get('/', [UserController::class, 'list']);
});
