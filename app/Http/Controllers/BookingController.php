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
    public function update(Request $request, $company_uni_id)
    {
    // yanliz kendi sirketini guncelleyebilir admin
    if (Auth::user()->company_uni_id != $company_uni_id) {
        abort(403);// hata sayfasina yonlenriiyor erisimi olmayanlari
    }

    // Guncelleme yapilmis bilgileri almak ve guncellemek icin 
    DB::table('companies')->where('company_uni_id', $company_uni_id)
      ->update($request->only(['name','address','phone_number','description']));

    return back()->with('success','Company updated.');
     }

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
     public function book(Request $request)
    {
        //  musteri randevusu icin gelen istegi dogrulamali slot_id ile service_id integer olmali
        $request->validate([
            'slot_id'    => 'required|integer',
            'service_id' => 'required|integer',
        ]);

        
        DB::beginTransaction();

        // Seçilen slot hâlâ müsait mi kontrol et  ve baslasi almasin diye al
        $slot = DB::table('availability_slots')
            ->where('slot_id', $request->slot_id)
            ->where('status', 'available')
            ->lockForUpdate()
            ->first();

        if (! $slot) {
            // senden once baskasi aldiysa artik musait degil yaziis gorur
            DB::rollBack();
            return back()->with('error', 'Seçilen slot artık müsait değil.');
        }

        // 4) Slot durumunu booked olarak güncelle
        DB::table('availability_slots')
            ->where('slot_id', $request->slot_id)
            ->update(['status' => 'booked']);

        // 5) appointments tablosuna randevu kaydı ekle
        $appointmentId = DB::table('appointments')->insertGetId([
            'user_uni_id'          => Auth::id(),
            'staff_member_uni_id'  => $slot->staff_member_uni_id,
            'company_uni_id'       => $slot->company_uni_id,
            'service_id'           => $request->service_id,
            'slot_id'              => $request->slot_id,
            'scheduled_time'       => $slot->start_time,
            'status'               => 'pending', // beklemede
            'created_at'           => now(),
        ]);

        //  Transaction onayla
        DB::commit();

        //  Müşteriye onay e-postası gönder
        Mail::to(Auth::user()->email)
            ->send(new AppointmentRequestedMail($appointmentId));

        //  Müşteri paneline yönlendir ve başarı mesajı göster
        return redirect()->route('dash.customer')
                         ->with('success', 'Randevu isteğiniz başarıyla alındı.');
    }

     
                // Şirket silme işlemi (Superadmin)
        public function deleteCompany($companyId)
       {
       // Şirket varsa sil
        DB::table('companies')->where('company_uni_id', $companyId)->delete();

        return back()->with('success', 'Şirket başarıyla silindi.');
        }

      // Kullanıcı silme işlemi (Superadmin)
      public function deleteUser($userId)
       {
             // Kullanıcı varsa sil
          DB::table('users')->where('user_uni_id', $userId)->delete();

            return back()->with('success', 'Kullanıcı başarıyla silindi.');
        }  
    
// Admin: Şirkete ait personel listesini getir 
    public function listStaff()
    {
        // Yalnızca kendi şirketinin personelini döner
        $companyId = Auth::user()->company_uni_id;     // şirket sahibi olduğumuzu bu alandan anlarız
        $staff = DB::table('staff_members')
                   ->where('company_uni_id', $companyId)
                   ->get();

        return view('dash.admin-staff', compact('staff'));
    }
       public function addStaff(Request $request)
    {
        $request->validate([
            'full_name'        => 'required|string',
            'experience_level' => 'required|string',
        ]);
        // Admin: sadece kendi şirketine personel ekle \
        DB::table('staff_members')->insert([
            'company_uni_id'    => Auth::user()->company_uni_id,
            'full_name'         => $request->full_name,
            'experience_level'  => $request->experience_level,
            'created_at'        => now(),
        ]);
        
        return back()->with('success','Çalışan eklendi.');
    }
    
    public function deleteStaff($id)
    {
        // Admin: sadece kendi şirketinin personelini sil 
        DB::table('staff_members')
          ->where('staff_member_uni_id', $id)
          ->where('company_uni_id', Auth::user()->company_uni_id)
          ->delete();
            return back()->with('success','Çalışan silindi.');
    }
      public function adminAppointments()
    {
        // Admin: sadece kendi şirkete ait randevuları listele 
        $list = DB::table('appointments as a')
            ->join('users as u', 'a.user_uni_id', '=', 'u.user_uni_id')
            ->where('a.company_uni_id', Auth::user()->company_uni_id)
            ->select('a.*','u.full_name','u.email')
            ->orderBy('a.created_at','desc')
            ->get();
             
        return view('dash.adminAppointments', compact('list'));
    }

    public function updateStatus(Request $r, $id)
    {
        $r->validate(['status'=>'required|in:pending,confirmed,cancelled']);

        // Admin: sadece kendi şirkete ait randevuya dokun 
        $updated = DB::table('appointments')
            ->where('appointment_id', $id)
            ->where('company_uni_id', Auth::user()->company_uni_id)
            ->update(['status'=>$r->status]);
             if ($updated) {
            // Mail gönderirken Auth::user()->email kullanırsın
            Mail::to($r->email)->send(new AppointmentStatusMail($id,$r->status));
        }

        return back()->with('success','Durum güncellendi.');
    }
 /**
     * Superadmin – şirket düzenleme formu
     */
    public function editCompany($companyId)
    {
        $company = DB::table('companies')
                     ->where('company_uni_id', $companyId)
                     ->first();
        return view('dash.superadminCompanyEdit', compact('company'));
    }

    /**
     * Superadmin – düzenlemeyi kaydet
     */
    public function updateCompanyBySuperadmin(Request $request, $companyId)
    {
        DB::table('companies')
          ->where('company_uni_id', $companyId)
          ->update($request->only(['name','address','phone_number','description']));

        return back()->with('success', 'Şirket başarıyla güncellendi.');
    }
}



