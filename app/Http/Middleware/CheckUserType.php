<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  int  $type  // /////// beklenen user_type_id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $type)
    {
        // /////// Kullanıcının tipi session’dan geliyorsa kontrol et
        if (session('user_type_id') != (int) $type) {
            // İstersen hata sayfasına yönlendir veya login’e at
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
} /// giris yetkisi olmayan baska bir panele gecis yapamaz izin verilmez
