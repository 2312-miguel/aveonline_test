<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\BalanceController;
use App\Http\Controllers\Api\LogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by RouteServiceProvider within the "api" group,
| which assigns the "api" middleware group. Here we enforce "check.token".
|
*/

// Authentication Routes
Route::post('/login', [ApiAuthController::class, 'login'])->name('api.login');

// Protected Routes with Token Check
Route::middleware(['check.token'])->group(function () {
    // Transaction Management
    Route::prefix('transactions')->group(function () {
        // Create new transaction (deposit/withdraw)
        Route::post('/', [TransactionController::class, 'store']);
        
        // Get transaction by number
        Route::get('/{transactionNumber}', [TransactionController::class, 'getTransactionByNumberApi']);
    });

    // User Balance Management
    Route::prefix('users')->group(function () {
        // Get user balance
        Route::get('/{userId}/balance', [TransactionController::class, 'getBalanceApi'])
            ->where('userId', '[0-9]+');

        // Add balance to user account
        Route::post('/{userId}/balance', [TransactionController::class, 'addBalanceApi'])
            ->where('userId', '[0-9]+');
            
        // Get balance summary
        Route::get('/{userId}/balance-summary', [BalanceController::class, 'getBalanceSummary'])
            ->where('userId', '[0-9]+')
            ->name('balance.summary');
    });
});

// Activity Logged Routes
Route::middleware(['log.activity', 'check.token'])->group(function () {
    // User Details
    Route::get('/users/{userId}/details', [UserController::class, 'getUserDetailsApi'])
        ->where('userId', '[0-9]+');
});

// Download system logs
Route::get('/logs/download', [LogController::class, 'downloadLogs'])
    ->name('logs.download');