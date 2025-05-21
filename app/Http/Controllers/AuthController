<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;         // Auth işlemleri için
use Illuminate\Support\Facades\Hash;         // Şifreleri hash’lemek ve doğrulamak için
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;    // Manuel doğrulama
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\ResetPasswordMail;              // Mail sınıfı

class AuthController extends Controller
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

    // bu class kayit formunu gosteir 
    public function showRegistrationForm()
    {
        return view ('auth.register'); // auth/register.blande.php sayfasini yukleme islemi gorur
    }

    public function registerSave(Request $request)// kayit formundan gelen POST isletigini gerceklestiri
    {
        Validator::make($request->all(),[
            'name'=>'required',  // name email ve passwordun zorunlu oldigini belirtit
            'email'=>'required|email',
            'password'=>'required|min:8|confirmed' // min 8 karakterli olmali ve password_confirmation alani ile eslesmeli 
        ])->validate();

        $validated = $request->only(['name', 'email', 'password']);

        // yeni kullanici olsutururken 
        DB::table('users')->insert([
            'full_name' => $validated['name'],        // formdaki name alanı full_name sutununa kaydedilir
            'email'     => $validated['email'],       // formdaki email alanı email sutununa kaydedilir
            'password'  => Hash::make($validated['password']), // plain password bcrypt ile hash'lenir
            'created_at'=> Carbon::now(),
            'updated_at'=> Carbon::now(),
        ]);

        // kayit sonrasi login sayfasina yonlendirr 
        return redirect()
            ->route('login.form')  // routes/web.php de tanimli login rotasina gonderir
            ->with('Success','Please Enter Login');
    }

    // Sifre sifirlama linkini istedigimiz formu gosterir
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    // Kullanıcıya e-posta ile reset link gönderir */
    public function sendResetLinkEmail(Request $request)
    {
        // epoasta alanini dogrular zorunlu mail formatinda
        $request->validate(['email'=>'required|email']);

        // veritabaninda kayitli olan e-posta var mi kontrol edilir 
        $user = DB::table('users')->where('email', $request->email)->first();
        if (!$user) {
            // kayitli kullanici bulunmazsa bulunamadi diye hata mesaji dondur
            return back()->withErrors(['email'=>'not found the user']);
        }

        // 10 karakter uzunlugunda randim bir token uretir ( Ie65464sdf) gibi bir sye
        $token = Str::random(10);
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token'=>$token, 'created_at'=>Carbon::now()]
        );

        // ayni sayfaya geri doner ver mesaj verir 
        $resetUrl = route('password.reset.form', $token);
        Mail::to($request->email)->send(new ResetPasswordMail($user, $resetUrl));

        return back()->with('status','Şifre sıfırlama linki e-posta adresinize gönderildi.');
    }

    /** 3) Token’lı linkten gelen formu açar */
    public function showResetForm($token)
    {
        return view('auth.change-password', compact('token'));
    }

    /** 4) Yeni şifreyi kaydeder */
    public function reset(Request $request)
    {
        // gerekli alanlari mail - token - sifre gibi alanalri dogrular
        $request->validate([
            'token'=>'required',
            'email'=>'required|email',
            'password'=>'required|min:8|confirmed',
        ]);

        $record = DB::table('password_resets')
            ->where(['email'=>$request->email, 'token'=>$request->token])
            ->first();

        if (!$record || Carbon::parse($record->created_at)->addHours(6)->isPast()) {
            return back()->withErrors(['token'=>'Geçersiz veya süresi dolmuş token']);
        }

        // Şifre güncelle
        DB::table('users')->where('email', $request->email)
            ->update(['password'=>Hash::make($request->password)]);

        // Token’ı sil
        DB::table('password_resets')->where('email',$request->email)->delete();

        return redirect()->route('login.form')->with('success','Şifreniz başarıyla güncellendi.');
    }
}
