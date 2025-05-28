<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;

// — Cache temizleme
Route::get('/clrall', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    Artisan::call('route:clear');
    Artisan::call('optimize');
    echo "Cache temizlendi!";
});

// — AuthController: giriş, kayıt, şifre sıfırlama
    Route::controller(AuthController::class)->group(function () {
          // Giriş / Ana sayfa
          Route::get('/', 'showLoginForm')->name('login.form');
          Route::post('login','login')->name('login.submit');

          // Kayıt
          Route::get('register', 'showRegistrationForm')->name('register.form');
          Route::post('register', 'registerSave')->name('register.submit');

          // Çıkış
          Route::post('logout', 'logout')->name('logout');

          // Şifre sıfırlama
          Route::get('forgot-password', 'showLinkRequestForm')->name('password.request');
          Route::post('forgot-password', 'sendResetLinkEmail')->name('password.email');
          Route::get('reset-password/{token}', 'showResetForm')->name('password.reset.form');
          Route::post('reset-password', 'reset')->name('password.reset');
     });


   //Müşteri, Admin ve Süperadmin Dashboard’ları
    Route::prefix('dash')->group(function () { // prefix ornegin 'dash' yazildi normalde getden sonra hepsi icin dash yazilmasi gerkeiyordu fakat simdi en basa das yazarak onu kisalltik uzun projelerde isine yarayabilir
          
          Route::get('customer', fn() => view('dash.customer'))->name('dash.customer')->middleware('userType:1');
          Route::get('admin', fn() => view('dash.admin'))->name('dash.admin')->middleware('userType:2');
          Route::get('superadmin', fn() => view('dash.superAdmin'))->name('dash.superadmin')->middleware('userType:3');
          
      });

    Route::controller(BookingController::class)->group(function () {

          Route::get('categories/{category}', 'showCategory')->name('categories.show');
          Route::get('categories/{category}/companies/{company}', 'showCompanyAvailability')->name('categories.company.availability');
          Route::post('appointment/book', 'book')->middleware('userType:1')->name('appointment.book'); // Randevu oluşturma (yalnızca müşteri)
          Route::get('appointment/book', fn() => redirect()->route('dash.customer')); // GET isteği atılırsa müşteri dash'e yönlendir

     });

    // — admin rotaları (user_type = 2)
    Route::prefix('admin')->middleware('userType:2')->controller(BookingController::class)->group(function () {   // sadece adminin yapabildigi kontroller
    Route::get('categories', 'adminCategories')->name('admin.categories.index');// Kategori yönetimi
    
    Route::get('staff', 'listStaff')->name('admin.staff.index'); // Çalışan yönetimi ekleyebilir silebilir
    Route::post('staff', 'addStaff')->name('admin.staff.add');
    Route::delete('staff/{id}', 'deleteStaff')->name('admin.staff.delete');

    Route::get('appointments', 'adminAppointments')->name('admin.appointments');  // Randevu yönetimi kontrol ve update
    Route::post('appointments/{id}', 'updateStatus')->name('admin.appointments.update');
    Route::post('companies/{company_uni_id}/update', 'updateCompany')->name('admin.companies.update');// Şirket güncelleme
});

  // — Süperadmin rotaları (user_type = 3)
    Route::prefix('dash/superadmin')->middleware('userType:3')->controller(BookingController::class)->group(function () {
   
    Route::get('appointments', 'adminAppointments')->name('superadmin.appointments'); // Tüm şirketlerin randevu yönetimini yapar
    Route::post('appointments/{id}', 'updateStatus')->name('superadmin.appointments.update');
    Route::delete('company/{id}', 'deleteCompany')->name('superadmin.company.delete'); // Şirket yönetimi tum sirketleri silebilir
    Route::get('company/{id}/edit', 'editCompany')->name('superadmin.company.edit');
    Route::post('company/{id}', 'updateCompanyBySuperadmin')->name('superadmin.company.update');
    Route::delete('user/{id}', 'deleteUser')->name('superadmin.user.delete'); // Kullanıcı yönetimi
   
});
