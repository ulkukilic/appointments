<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;              // Laravel’in authentication (giriş) sistemi
use Illuminate\Support\Facades\Hash;              // Şifreleri hash’lemek ve doğrulamak için
use Carbon\Carbon;                                // Tarih/saat işlemleri için
use App\Enums\TableNames;                         // Tablo adlarını enum’dan çekmek için
use Illuminate\Validation\ValidationException;    // Hatalı girişlerde exception fırlatmak için
use Illuminate\Support\Facades\Validator;  // Manuel doğrulama
use App\Models\User;                       // Eloquent User modeli

class RegisterController extends Controller
{
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
    // yeni kullanici olsutururken 
           $user = User::create([
            'full_name' => $validated['name'],        // formdaki name alanı full_name sutununa kaydedilir
            'email'     => $validated['email'],       // formdaki email alanı email sutununa kaydedilir
            'password'  => Hash::make($validated['password']), // plain password bcrypt ile hash'lenir
        ]);
        // kayit sonrasi login sayfasina yonlendirr 
        return redicted()
        ->route('login.form')  // routes/web.php de tanimli login rotasina gonderir
        ->with('Success','Please Enter Login');
    }

}
