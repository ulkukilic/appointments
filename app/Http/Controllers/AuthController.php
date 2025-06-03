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
use App\Mail\AppointmentStatusMail;  
class AuthController extends Controller
{
    
    
    public function showLoginForm() // giris formunu gosterir
    {
        return view('auth.login');  // resources/views/auth/login.blade.php
    }

    
    public function login(Request $request)// Login sayfasindaki Post islemlerini gorur 
    {
        
        $credentials = $request->validate([  // veriler dogrulama islemi gerceklestirilir 
            'email'    => 'required|email',
            'password' => 'required',
        ]);
             $user=DB::table('users')
             ->Where('email',$request->email)
             ->first(); // burda users tablosunda kaydi cekiyoruz
             
             if($user && Hash::check($request->password,$user->password)) //sifre kontrolu yapiliyor ve icerisinde ki degerler user tablosundaki degerlere bakiyor
             {
                 // Bir kullanıcı birden fazla şirkete sahip olabilir; biz varsayılan olarak ilkini alıyoruz
                $companyOwner = DB::table('company_owners')
                ->where('user_uni_id', $user->user_uni_id)
                ->first();

                session([
                   'user_uni_id'    => $user->user_uni_id,
                   'user_type_id'   => $user->user_type_id,
                   'full_name'      => $user->full_name,
                   'email'          =>  $user->email,
                    'company_uni_id' => $companyOwner ? $companyOwner->company_uni_id : null,
                ]);

                  // password dogru ve bilgilerde eslesme varsa o zaman user_type devreye giriyor ve logindan sonra gonderilmesi gereken panel sayfasina yonlendiriyor
                   if ($user->user_type_id == 1) 
                   {
                      return redirect()->route('dash.customer');
                   } 
                   elseif ($user->user_type_id == 2) 
                   {
                    return redirect()->route('dash.admin');
                   } 
                   elseif ($user->user_type_id == 3)
                   {
                   return redirect()->route('dash.superadmin');
                   }
        }

        return back()
            ->withErrors(['email' => 'The e-mail or password is incorrect.'])
            ->withInput($request->only('email'));
    }
             

      /* MODELS YAPISI KULLANIRKENN
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
    */
      public function logout(Request $request) // tutulan bilgier session da tutuldugu icin onlar temizleniyor
    {
         $request->session()->flush();
        return redirect()->route('login.form');
    }

    
    public function showRegistrationForm() // bu class kayit formunu gosteir 
    {
        return view ('auth.register'); // auth/register.blande.php sayfasini yukleme islemi gorur
    }

    public function registerSave(Request $request)// kayit formundan gelen POST isletigini gerceklestiri
    {
        Validator::make($request->all(),[
            'name'=>'required',  // name email ve passwordun zorunlu oldigini belirtit
            'surname'               => 'required', 
            'email'=>'required|email',
            'password'=>'required|min:8|confirmed' // min 8 karakterli olmali ve password_confirmation alani ile eslesmeli 
        ])->validate();

        $validated = $request->only(['name', 'surname','email', 'password']);

        // yeni kullanici olsutururken 
        DB::table('users')->insert([
            'full_name' => $validated['name']. ' ' . $validated['surname'],        // formdaki name alanı full_name sutununa kaydedilir
            'email'     => $validated['email'],       // formdaki email alanı email sutununa kaydedilir
            'password'  => Hash::make($validated['password']), // plain password bcrypt ile hash'lenir
            'user_type_id'  => 1,       ////// yeni kayıtta default müşteri tipi (1) atandı
            'created_at'=> Carbon::now(),
            
        ]);

       
        return redirect()   // kayit sonrasi login sayfasina yonlendirr
            ->route('login.form')  // routes/web.php de tanimli login rotasina gonderir
            ->with('success','Please Enter Login');
    }

   
    public function showLinkRequestForm()  // Sifre sifirlama linkini istedigimiz formu gosterir
    {
        return view('auth.forgot-password');
    }

    
    public function sendResetLinkEmail(Request $request) // Kullanıcıya e-posta ile reset link gönderir */
    {
        
        $request->validate(['email'=>'required|email']);// epoasta alanini dogrular zorunlu mail formatinda

         
        $user = DB::table('users')->where('email', $request->email)->first(); // veritabaninda kayitli olan e-posta var mi kontrol edilir
        if (!$user) 
        {
            return back()->withErrors(['email'=>'not found the user']);  // kayitli kullanici bulunmazsa bulunamadi diye hata mesaji dondur
        }

        
        $token = Str::random(10); // 10 karakter uzunlugunda randim bir token uretir ( Ie65464sdf) gibi bir sye
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token'=>$token, 'created_at'=>Carbon::now()]
        );

        
        $resetUrl = route('password.reset.form', $token); // ayni sayfaya geri doner ver mesaj verir 
        Mail::to($request->email)->send(new ResetPasswordMail($user, $resetUrl));

        return back()->with('status','The password reset link has been sent to your e-mail address.');
    }

 
    public function showResetForm($token)//Token’lı linkten gelen formu açar */
    {
        return view('auth.change-password', compact('token'));
    }

    
    public function reset(Request $request) /** 4) Yeni şifreyi kaydeder */
    {
    
        $request->validate([          // gerekli alanlari mail - token - sifre gibi alanalri dogrular
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

        
        DB::table('users')->where('email', $request->email)->update(['password'=>Hash::make($request->password)]); // Şifre güncelleme yapar
        DB::table('password_resets')->where('email',$request->email)->delete();// Token’ı sil
        return redirect()->route('login.form')->with('success','Şifreniz başarıyla güncellendi.');
    }
}

