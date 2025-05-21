<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
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
        $user = User::where('email', $request->email)->first();
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
        Mail::to($request->email)->send(new \App\Mail\ResetPasswordMail($user, $resetUrl));

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
        User::where('email',$request->email)
            ->update(['password'=>Hash::make($request->password)]);

        // Token’ı sil
        DB::table('password_resets')->where('email',$request->email)->delete();

        return redirect()->route('login.form')->with('success','Şifreniz başarıyla güncellendi.');
    }
}

//Note token random uretilen bir sayidir