<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
});

// Kayit formunu gosterir 
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register.form');

// Form gonderildiginde veriyi calistirir
Route::post('/register', [AuthController::class, 'registerSave'])->name('register.submit');

// login formunu gosterir
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');

// login formu POST edilirse giris islemini yapar
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Çıkış işlemi
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Şifre Sıfırlama işlemleri
Route::get('/forgot-password',        [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password',       [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password',        [AuthController::class, 'reset'])->name('password.reset');
