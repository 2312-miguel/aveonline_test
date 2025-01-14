<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider and will be assigned
| to the "web" middleware group.
|
*/

// Public Routes
Route::get('/', function () {
    return redirect('/login');
});

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.show');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.show');
Route::post('/register', [RegisterController::class, 'register'])->name('register');


// Protected Routes (Requires Authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', function () {
        return view('welcome');
    })->name('home');

    // User Management
    Route::prefix('users')->group(function () {
        // Display Users List with Pagination
        Route::get('/', [UserController::class, 'index'])->name('users.index');

        // Show User Transactions History
        Route::get('/{userId}/transactions', [TransactionController::class, 'showUserTransactions'])
            ->name('user.transactions')
            ->where('userId', '[0-9]+');
    });
});

