<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
Route::get('/clrall', function () {    Artisan::call('cache:clear');    Artisan::call('view:clear');    Artisan::call('config:cache');    Artisan::call('route:clear');    Artisan::call('optimize');    echo "Cache temizlendi!";});

// Anasayfa / Login
Route::get('/', fn() => view('auth.login'))->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Kayıt
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'registerSave'])->name('register.submit');

// Çıkış
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Şifre Sıfırlama
Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.reset');


//Müşteri, Admin ve Süperadmin Dashboard’ları
Route::get('/dash/customer', fn() => view('dash.customer'))
     ->name('dash.customer')
     ->middleware('userType:1');

Route::get('/dash/admin', fn() => view('dash.admin'))
     ->name('dash.admin')
     ->middleware('userType:2');

Route::get('/dash/superadmin', fn() => view('dash.superAdmin'))
     ->name('dash.superadmin')
     ->middleware('userType:3');


// Kategori ve Şirket Görüntüleme
Route::get('/categories/{category}', [BookingController::class, 'showCategory'])
     ->name('categories.show');

Route::get('/categories/{category}/companies/{company}', [BookingController::class, 'showCompanyAvailability'])
     ->name('categories.company.availability');


//Randevu Oluşturma (Sadece Müşteri)
Route::post('/appointment/book', [BookingController::class, 'book'])
     ->middleware('userType:1')
     ->name('appointment.book');
     Route::get('/appointment/book', function() {
    return redirect()->route('dash.customer');
     });
// Admin (user_type = 2) Yetkili Rotalar
Route::middleware('userType:2')->group(function(){
    // Admin kategori yönetimi
    Route::get('/dash/categories', [BookingController::class,'adminCategories'])
         ->name('admin.categories.index');

    // Çalışan yönetimi
    Route::get('/admin/staff', [BookingController::class, 'listStaff'])->name('admin.staff.index');
    Route::post('/admin/staff/add', [BookingController::class, 'addStaff'])->name('admin.staff.add');
    Route::delete('/admin/staff/{id}', [BookingController::class, 'deleteStaff'])->name('admin.staff.delete');

    // Randevu yönetimi
    Route::get('/admin/appointments', [BookingController::class,'adminAppointments'])->name('admin.appointments');
    Route::post('/admin/appointments/{id}', [BookingController::class,'updateStatus'])->name('admin.appointments.update');

    // Şirket güncelleme (Admin kendi şirketine)
    Route::post('/admin/companies/{company_uni_id}/update', [BookingController::class, 'updateCompany'])->name('admin.companies.update');
});


//Süperadmin (user_type = 3) Yetkili Rotalar
Route::middleware('userType:3')->group(function(){
    // Tüm şirketlerin randevu yönetimi
    Route::get('/superadmin/appointments', [BookingController::class,'adminAppointments'])
         ->name('superadmin.appointments');
    Route::post('/superadmin/appointments/{id}', [BookingController::class,'updateStatus'])
         ->name('superadmin.appointments.update');

    // Şirket yönetimi
    Route::delete('/superadmin/company/{id}', [BookingController::class, 'deleteCompany'])
         ->name('superadmin.company.delete');
    Route::get('/superadmin/company/{id}/edit', [BookingController::class, 'editCompany'])
         ->name('superadminCompanyEdit');
    Route::put('/superadmin/company/{id}', [BookingController::class, 'updateCompanyBySuperadmin'])
         ->name('superadmin.company.update');

    // Kullanıcı yönetimi
    Route::delete('/superadmin/user/{id}', [BookingController::class, 'deleteUser'])
         ->name('superadmin.user.delete');
});


/*

      Route::controller(SettingController::class)->prefix('setting')->group(function(){

            Route::get('list', 'settingList')->name('settingList');
            Route::get('edit', 'settingEdit')->name('settingEdit');
            Route::get('translations', 'settingTranslationsList')->name('settingTranslationsList');

            Route::post('edit', 'settingEditPost')->name('settingEditPost');
            Route::post('translations', 'settingTranslationsEditPost')->name('settingTranslationsEditPost');
            Route::post('translations', 'settingTranslationsAddtPost')->name('settingTranslationsAddtPost');
            Route::post('our-excel-upload', 'settingOurExcelUploadPost')->name('settingOurExcelUploadPost');

                });
 Route::get('order-list/{token}', [CompanyController::class, 'companyOrderList'])->name('companyOrderList');
            Route::get('order-to-excel/{token}', [CompanyController::class, 'companyOrderToExcel'])->name('companyOrderToExcel');
            Route::get('order-history/{token}', [CompanyController::class, 'companyOrderHistoryList'])->name('companyOrderHistoryList');
            Route::get('order-completed-detay-list/{token}', [CompanyController::class, 'companyOrderCompletedDetayList'])->name('companyOrderCompletedDetayList');


            Route::post('add', [CompanyController::class, 'companyAddPost'])->name('companyAddPost');
            Route::post('edit', [CompanyController::class, 'companyEditPost'])->name('companyEditPost');
            Route::post('status', [CompanyController::class, 'companyStatusPost'])->name('companyStatusPost');
            Route::post('mapping', [CompanyController::class, 'companyMappingPost'])->name('companyMappingPost');
            Route::post('product-add', [CompanyController::class, 'companyProductAddPost'])->name('companyProductAddPost');
            */