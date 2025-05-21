<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // user modeli users tablosunu temsil eder 
    // Authentication islemlerini kolyastir
    
    use Notifiable;

    protected $primaryKey = 'user_id';          // companyappointment.sql de user_id kullanilidigi iicin

     protected $fillable = [
        'full_name',    // Kullanıcının tam adı
        'email',        // E-posta adresi
        'password',     // Şifre (hashlenmiş olarak saklanır)
        'user_type_id', // Kullanıcı tipi ilişkili foreign key
        'phone_number', // Opsiyonel telefon numarası
        'gender',       // Cinsiyet (Female/Male/Other)
    ];

    protected $hidden = [
        'password',       // Hash'lenmiş şifre
        'remember_token', // "Beni hatırla" token'ı
    ];
    // Models/User.php = Veritabanindaki user tablosunu PHP ile yonetmek icin kullanilri
}
