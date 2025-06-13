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
// Ana sayfaya gelen istekleri login ekranına yönlendir
 Route::get('/', fn() => redirect()->route('login.form'));


// — AuthController: giriş, kayıt, şifre sıfırlama
    Route::controller(AuthController::class)->group(function () {
          // Giriş / Ana sayfa
          Route::get('login', 'showLoginForm')->name('login.form');
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
          Route::get('admin', [BookingController::class, 'adminDashboard'])->name('dash.admin')->middleware('userType:2');
          Route::get('superadmin', fn() => view('dash.superAdmin'))->name('dash.superadmin')->middleware('userType:3');
          
      });

    Route::controller(BookingController::class)->group(function () {

          Route::get('categories/{category}', 'showCategory')->name('categories.show');
          Route::get('categories/{category}/companies/{company}', 'showCompanyAvailability')->name('categories.company.availability');
          Route::post('appointment/book', 'book')->middleware('userType:1')->name('appointment.book'); // Randevu oluşturma (yalnızca müşteri)
          Route::get('appointment/book', fn() => redirect()->route('dash.customer')); // GET isteği atılırsa müşteri dash'e yönlendir
          Route::post('reviews/{company}', 'storeReview')->middleware('userType:1')->name('reviews.store');
    
     });
       
      // — Customer rotaları (user_type = 1)
         Route::prefix('dash/customer')->middleware('userType:1')->controller(BookingController::class)->group(function () {
        Route::get('/', 'showCustomerDashboard')->name('dash.customer');
         Route::get('appointments', 'customerAppointments')->name('dash.customer.appointments');
         
    });


    // — admin rotaları (user_type = 2)
    Route::prefix('admin')->middleware('userType:2')->controller(BookingController::class)->group(function () {   // sadece adminin yapabildigi kontroller
    Route::get('categories', 'adminCategories')->name('admin.categories.index');// Kategori yönetimi
    Route::get('reviews', 'adminReviews')->name('admin.reviews.index');
   Route::delete('reviews/{id}',   'deleteAdminReview')->name('admin.reviews.delete');
    Route::get('staff', 'listStaff')->name('admin.staff.index'); // Çalışan yönetimi ekleyebilir silebilir
    Route::post('staff', 'addStaff')->name('admin.staff.add');
    Route::get('staff/edit/{id}', 'editStaff')->name('admin.staff.edit');
    Route::post('staff/update/{id}', 'updateStaff')->name('admin.staff.update');
    Route::delete('staff/{id}', 'deleteStaff')->name('admin.staff.delete');
     Route::post('staff/{id}/toggle','toggleStaff')->name('admin.staff.toggle');

    Route::get('appointments', 'adminAppointments')->name('admin.appointments');  // Randevu yönetimi kontrol ve update
    Route::post('appointments/{id}', 'updateStatus')->name('admin.appointments.update');
    Route::post('companies/{company_uni_id}/update', 'updateCompany')->name('admin.companies.update');// Şirket güncelleme
    Route::get('admin/categories', 'adminCategories')->name('admin.categories.index');

    Route::get('availability', 'showAvailabilityManagement')->name('admin.availability.index');
    Route::post('availability/add', 'addAvailabilitySlot')->name('admin.availability.add');
    Route::get('availability/add', fn() => redirect()->route('dash.admin'));
    Route::post('availability/{slotId}', 'updateAvailabilitySlot')->name('admin.availability.update');
    Route::get('services',        'adminServices')->name('admin.services.index');
    Route::get('services/create', 'showServiceForm')->name('admin.services.create');
    Route::post('services',       'storeService')->name('admin.services.store');

  });

  // — Süperadmin rotaları (user_type = 3)
    Route::prefix('dash/superadmin')->middleware('userType:3')->controller(BookingController::class)->group(function () {
    Route::get('appointments', 'superadminAppointments')->name('superadmin.appointments');
    Route::post('appointments/{id}', 'updateStatus')->name('superadmin.appointments.update');
    Route::get('users', 'superadminUsers')->name('superadmin.users.index');
    Route::delete('user/{id}', 'deleteUser')->name('superadmin.user.delete'); // Kullanıcı yönetimi
    Route::get('users/create',     'superadminCreateUser')->name('superadmin.users.create');
    Route::post('users',           'superadminStoreUser')->name('superadmin.users.store');
    Route::get('users/{id}/edit',  'superadminEditUser')->name('superadmin.users.edit');
    Route::put('users/{id}',       'superadminUpdateUser')->name('superadmin.users.update');
    Route::delete('company/{id}', 'deleteCompany')->name('superadmin.company.delete'); // Şirket yönetimi tum sirketleri silebilir
    Route::get('companies', 'superadminCompanies') ->name('superadmin.companies.index');
    Route::get('company/{id}/edit', 'editCompany')->name('superadmin.company.edit');
    Route::post('company/{id}', 'updateCompanyBySuperadmin')->name('superadmin.company.update');
     Route::get('companies/create', 'superadminCreateCompany')->name('superadmin.companies.create');
    Route::post('companies', 'superadminStoreCompany')->name('superadmin.companies.store');
    Route::get('companies/{id}/edit', 'editCompany')->name('superadmin.companies.edit');
    Route::put('companies/{id}', 'updateCompanyBySuperadmin')->name('superadmin.companies.update');
    Route::get('reviews', 'superadminReviews')->name('superadmin.reviews.index');
   Route::get('users/admins', 'superadminUsersAdmins')->name('superadmin.users.admins');
   Route::get('users/customers', 'superadminUsersCustomers')->name('superadmin.users.customers');
  Route::get('users/staff', 'superadminUsersStaff')->name('superadmin.users.staff');
  Route::get('users/admins',      'superadminUsersAdmins')     ->name('superadmin.users.admins');     // ← EKLENDİ
    Route::get('users/customers',   'superadminUsersCustomers')->name('superadmin.users.customers');  // ← EKLENDİ
    Route::get('users/staff',       'superadminUsersStaff')    ->name('superadmin.users.staff');      // ← EKLENDİ

});
