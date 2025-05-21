<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;         // Auth işlemleri için


class LoginController extends Controller
{
    /** Giris formunu gosterir */
    public function showLoginForm()
    {
        return view('auth.login');             // resources/views/auth/login.blade.php
    }

/** login sayfasindaki POST islemlerini gorur  */
    public function login(Request $request)
    {
        // veriler dogrulama islemi gerceklestirilir 
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Auth::attempt ile giris yapmayi dener  kimlik dogrulaasi yapmaya calsiri
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();  
            return redirect()->intended('dashboard');
        }

        // basarisiz olursa basa donuyor ama maili tutuyr
        return back()
            ->withErrors(['email' => 'E-posta veya şifre hatalı.'])
            ->withInput($request->only('email'));
    }
      public function logout(Request $request)
    {
        Auth::logout();                               // Oturumu kapat
        $request->session()->invalidate();             // Session oturum kapandigi icin temizler
        $request->session()->regenerateToken();       
        return redirect()->route('login.form');       // Giriş sayfasına geri doner
    }
 
}