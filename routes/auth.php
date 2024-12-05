<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/register', function () {
    return view('livewire.pages.auth.custom-register', [
        'example' => fn () => 'This is a Closure', // Example of problematic data
    ]);
})->name('register');

        
        Route::get('/login', function () {
            return view('livewire.pages.auth.custom-login');
        })->name('login');
        
Route::get('/forgot-password', function () {
    return view('livewire.pages.auth.custom-forgot-password');
})->name('password.request');

Route::get('/reset-password/{token}', function ($token) {
    return view('livewire.pages.auth.custom-reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');


Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});
