<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\BookingController;

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

//  Kategoriye tıklayınca şirket listesini gösterecek
Route::get('/categories/{category}', [BookingController::class, 'showCategory'])
     ->name('categories.show');

//  Bir şirket tıklandığında müsaitliklerini gösterecek
Route::get('/categories/{category}/companies/{company}', [BookingController::class, 'showCompanyAvailability'])
     ->name('categories.company.availability');

  //admin icin sirketi update edebilmesi icin
Route::post(
    '/admin/companies/{company_uni_id}/update',
    [BookingController::class, 'updateCompany']
)->name('admin.companies.update');

Route::middleware('userType:2')->group(function(){
    // Randevu listesini görüntüle
    Route::get('/admin/appointments', [BookingController::class,'adminAppointments'])
         ->name('adminAppointments');
    // Randevu durumunu güncelle
    Route::post('/admin/appointments/{id}', [BookingController::class,'updateStatus'])
         ->name('adminAppointments.update');
});

   // sadece user_type_3 icin tum sirketlerin yonetim routlari
Route::middleware('userType:3')->group(function(){
    // Randevu listesini görüntüle (Superadmin tüm şirketler için)
    Route::get('/superadmin/appointments', [BookingController::class,'adminAppointments'])
         ->name('superadminApointments');
    // Randevu durumunu güncelle (Superadmin tüm şirketler için)
    Route::post('/superadmin/appointments/{id}', [BookingController::class,'updateStatus'])
         ->name('superadminAppointments.update');
});
// Superadmin -> Şirket silme
Route::delete('/superadmin/company/{id}', [BookingController::class, 'deleteCompany'])
    ->name('superadmin.company.delete');

// Superadmin -> Kullanıcı silme
Route::delete('/superadmin/user/{id}', [BookingController::class, 'deleteUser'])
    ->name('superadmin.user.delete');
// Admin çalışan yönetimi (userType:2)
Route::middleware('userType:2')->group(function() {
    Route::post('/admin/staff/add', [BookingController::class, 'addStaff'])->name('admin.staff.add');
    Route::delete('/admin/staff/{id}/delete', [BookingController::class, 'deleteStaff'])->name('admin.staff.delete');
});
