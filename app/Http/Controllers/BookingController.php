<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;        
use App\Http\Controllers\ForgotPasswordController;  
use Illuminate\Support\Facades\Hash;         // Şifreleri hash’lemek için  
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;          
use Illuminate\Support\Facades\Validator;    
use Illuminate\Support\Str;                 
use Carbon\Carbon;                           // Tarih/saat işlemleri için  
use App\Mail\ResetPasswordMail;              // Şifre sıfırlama postası  

class BookingController extends Controller
{
    // Kategori seçildiğinde o kategoriyi listelemek için kullanılacak fonksiyon  
    public function showCategory($category)
    {
        // O şirketin tüm bilgilerini çekicek
        $companies = DB::table('companies')
                       ->where('category', $category)
                       ->get();

        // category ve companies bilgilerini gönderir
        return view('categories.show', compact('category','companies'));
    }

    // Şirket seçilince personelin ve şirketin müsaitliğini gösterecek fonksiyon  
    public function showCompanyAvailability($category, $companyUniId)
    {
        // Bugünün tarihini tutacak ki kontrol ona göre sağlansın
        $startDate = Carbon::now();  
        // Kaç gün ilerideki rezervasyonları görebilir o kontrol edilecek
        $days      = 30;            

        // company_uni_id ile URL’den sadece onda kayıtlı veriler çekilecek
        $company = DB::table('companies')
                     ->where('company_uni_id', $companyUniId)
                     ->first();

        // staff_members tablosundan, o şirkete ait tüm personelleri çek
        $staffList = DB::table('staff_members')
                       ->where('company_uni_id', $companyUniId)
                       ->get();

        // Her personel için müsaitlik desenlerini bul ve 30 günlük slot listesi oluştur
        $staffData = $staffList->map(function($s) use($startDate, $days) {
            // weekly_availability tablosundan, personelin haftalık çalışma desenlerini al
            $patterns = DB::table('weekly_availability')
                          ->where('staff_member_uni_id', $s->staff_member_uni_id)
                          ->get();

            // Oluşturulacak slotları depolamak için boş bir dizi oluşturacak
            $slots = collect();

            // 0’dan $days-1’e kadar dönerek her gün için
            for ($i = 0; $i < $days; $i++) {
                $date = $startDate->copy()->addDays($i);
                $wday = $date->dayOfWeek; // 0=Sunday, …, 6=Saturday

                // O güne denk gelen tüm weekly_availability satırları için
                foreach ($patterns as $p) {
                    if ($p->weekday == $wday) {
                        // Uygun günlük slotu ekle
                        $slots->push([
                            'start_time' => $date->format('Y-m-d').' '.$p->start_time,
                            'end_time'   => $date->format('Y-m-d').' '.$p->end_time,
                        ]);
                    }
                }
            }

            // Her personel için hem personel hem de slot bilgisini döndür
            return [
                'staff' => $s,
                'slots' => $slots
            ];
        });

        // company_services tablosuyla join yaparak o şirkete ait hizmetleri al
        $services = DB::table('company_services as cs')
                      ->join('services as s', 'cs.service_id', '=', 's.service_id')
                      ->where('cs.company_uni_id', $companyUniId)
                      ->select('s.*')
                      ->get();

        // categories.availability blade’ine tüm verileri yolla
        return view('categories.availability', [
            'category'   => $category,
            'company'    => $company,
            'staffData'  => $staffData,
            'services'   => $services,
            'days'       => $days
        ]);
    }
}
