<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('core')->group(function () {
    Route::get('backup-restore', [CoreController::class, 'backupRestore']);
});
