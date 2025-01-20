<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['success' => true, 'message' => 'Welcome to the API'];
});

Route::prefix('api')->group(function () {

    // **Public Routes**
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login');
    Route::post('register', [AuthController::class, 'register'])
        ->name('register');
    Route::post('forgot-password', [AuthController::class, 'sendResetPasswordLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.store');
    Route::post('verification-notification', [AuthController::class, 'verificationNotification'])
        ->middleware('throttle:verification-notification')
        ->name('verification.send');
    Route::get('verify-email/{ulid}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Social Login
    Route::get('login/{provider}/redirect', [AuthController::class, 'redirect'])
        ->name('login.provider.redirect');
    Route::get('login/{provider}/callback', [AuthController::class, 'callback'])
        ->name('login.provider.callback');

    // **Protected Routes (requires authentication)**
    Route::middleware(['auth:sanctum'])->group(function () {
        // Authenticated User Routes
        Route::post('logout', [AuthController::class, 'logout'])
            ->name('logout');
        Route::get('user', [AuthController::class, 'user'])
            ->name('user');

        // Account Management
        Route::prefix('account')->group(function () {
            Route::post('update', [AccountController::class, 'update'])
                ->name('account.update');
            Route::post('password', [AccountController::class, 'password'])
                ->name('account.password');
        });

        // File Uploads
        Route::middleware(['throttle:uploads'])->group(function () {
            Route::post('upload', [UploadController::class, 'image'])
                ->name('upload.image');
        });
    });
});
