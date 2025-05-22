<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;         // Auth işlemleri için
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Support\Facades\Hash;         // Şifreleri hash’lemek ve doğrulamak için
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;    // Manuel doğrulama
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\ResetPasswordMail;              // Mail sınıfı

public function showCategory($category)
{
    // Örnek sabit data; prod’da DB’den çekeceksin
    $itemsMap = [
      'hospital'   => ['City Hospital','General Hospital'],
      'hair-salon' => ['Chic Hair','Glamour Salon'],
      'barber'     => ['Classic Barber','Modern Cut'],
      'dentist'    => ['Smile Dental','Bright Teeth'],
      'cafe'       => ['Coffee Corner','Happy Beans'],
    ];

    $items = $itemsMap[$category] ?? [];

    return view('categories.show', compact('category','items'));
}
