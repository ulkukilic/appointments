<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;



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

// Customer dashboard – sadece user_type_id = 1
Route::get('/dash/customer', function () {
    return view('dash.customer');
})
->name('dash.customer')
->middleware('userType:1');   

// Admin dashboard – sadece user_type_id = 2
Route::get('/dash/admin', function () {
    return view('dash.admin');
})
->name('dash.admin')
->middleware('userType:2');  

// Superadmin dashboard – sadece user_type_id = 3
Route::get('/dash/superadmin', function () {
    return view('dash.superAdmin');
})
->name('dash.superadmin')
 ->middleware('userType:3');   
  /// baska kullaninin yetkisi olmayana url ile girmesin diye