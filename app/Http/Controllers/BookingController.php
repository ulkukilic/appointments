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
use App\Mail\AppointmentRequestedMail;
use App\Mail\AppointmentStatusMail;   


class BookingController extends Controller
{
   
    public function editCompany($companyId) //Superadmin – şirket düzenleme formu
    {
        $company = DB::table('companies')->where('company_uni_id', $companyId)->first(); // companies degiskeni tarafindan , company_uni_id degerini cekiyoruz
         return view('dash.superadminCompanyEdit', compact('company')); // blade'ine $comany verisini gonderiyoruz 
    }
     
    public function updateCompanyBySuperadmin(Request $request, $companyId) //  Superadmin – düzenlemeyi kaydet
    {
        DB::table('companies') ->where('company_uni_id', $companyId) ->update($request->only(['name','address','phone_number','description']));// Request icinde gerekli olan alanlari alip guncelliyoruz 
        return back()->with('success', 'Şirket başarıyla güncellendi.'); // islem sonrasi ayni sayfadaa donup basari mesaji veriyoruz
    }

    public function deleteCompany($companyId)// Şirket silme işlemi (Superadmin)
    {
        
        DB::table('companies')->where('company_uni_id', $companyId)->delete();// Şirket varsa sil
        return back()->with('success', 'Şirket başarıyla silindi.');
    }

    
    public function deleteUser($userId)// Kullanıcı silme işlemi (Superadmin)
    {
        
        DB::table('users')->where('user_uni_id', $userId)->delete(); // Kullanıcı varsa sil
        return back()->with('success', 'Kullanıcı başarıyla silindi.');
    }

    
    public function update(Request $request, $company_uni_id) // Yalnızca kendi şirketini güncelleyebilir (admin)
    {
         if (session('company_uni_id') != $company_uni_id) {
            abort(403); // Hata sayfası
        }
        DB::table('companies')
            ->where('company_uni_id', $company_uni_id)
            ->update($request->only(['name','address','phone_number','description']));
        return back()->with('success','Company updated.');
    
    }
 
    public function showCategory($category) // Kategori seçildiğinde o kategoriyi listelemek için kullanılacak fonksiyon
    {
         $companies = DB::table('companies')->where('category', $category)->get(); // O şirketin tüm bilgilerini çekecek
        return view('categories.show', compact('category','companies'));// category ve companies bilgilerini gönderir
    }

    
    public function showCompanyAvailability($category, $companyUniId) // Şirket seçilince personelin ve şirketin müsaitliğini gösterecek fonksiyon
    {
       
        $startDate = Carbon::now();  // Bugünün tarihini tutacak ki kontrol ona göre sağlansın
        $days      = 30;  // Kaç gün ilerideki rezervasyonları görebilir o kontrol edilecek
        $company = DB::table('companies')->where('company_uni_id', $companyUniId)->first(); // company_uni_id ile URL’den sadece onda kayıtlı veriler çekilecek
        $staffList = DB::table('staff_members')->where('company_uni_id', $companyUniId)->get(); // staff_members tablosundan, o şirkete ait tüm personelleri çek

        
        $staffData = $staffList->map(function($s) {// SQL'den availability_slots tablosundan her personele ait uygun zamanları getir
            // Personelin uygun olan, geleceğe dönük slotlarını çekiyoruz
            $slots = DB::table('availability_slots')
                ->where('staff_member_uni_id', $s->staff_member_uni_id)
                ->where('status', 'available')
                ->where('start_time', '>=', now())
                ->orderBy('start_time')
                ->get();

            
            return [  // Slotlar ve personel bilgisi birlikte döndürülür 
                'staff' => $s,
                'slots' => $slots
            ];
        });

        // Şirkete ait hizmetler: services ve company_services join ile getirilir
        $services = DB::table('company_services as cs')
                      ->join('services as s', 'cs.service_id', '=', 's.service_id')
                      ->where('cs.company_uni_id', $companyUniId)
                      ->select('s.*')
                      ->get();

        
        return view('categories.availability', [ // kategori, şirket, personeller ve hizmetler
            'category'   => $category,
            'company'    => $company,
            'staffData'  => $staffData,
            'services'   => $services,
            'days'       => $days
        ]);
    }

    public function book(Request $request)
    {
        // musteri randevusu icin gelen istegi doğrulamalı slot_id ile service_id integer olmalı
        $request->validate([
            'slot_id'    => 'required|integer',
            'service_id' => 'required|integer',
        ]);

        DB::beginTransaction();

        // Seçilen slot hâlâ müsait mi kontrol et ve başkası almasın diye al
        $slot = DB::table('availability_slots')
            ->join('staff_members', 'availability_slots.staff_member_uni_id', '=', 'staff_members.staff_member_uni_id')
            ->where('availability_slots.slot_id', $request->slot_id)
            ->where('availability_slots.status', 'available')
            ->select('availability_slots.*', 'staff_members.company_uni_id')
            ->lockForUpdate() // kilitleme islemi 
            ->first();

        if (! $slot) 
        {
            DB::rollBack(); // senden önce başkası aldıysa artık müsait değil yazısı görür
            return back()->with('error', 'selected stock not currently available');
        }

        
        DB::table('availability_slots')->where('slot_id', $request->slot_id)->update(['status' => 'booked']); // Slot durumunu booked olarak güncelle

        // appointments tablosuna randevu kaydı ekle
        $appointmentId = DB::table('appointments')->insertGetId([
            'user_uni_id'          => session('user_uni_id'),
            'staff_member_uni_id'  => $slot->staff_member_uni_id,
            'company_uni_id'       => $slot->company_uni_id,
            'service_id'           => $request->service_id,
            'slot_id'              => $request->slot_id,
            'scheduled_time'       => $slot->start_time,
            'status'               => 'pending', // beklemede
            'created_at'           => now(),
        ]);

        
        DB::commit();// onayla
 
             
        
        Mail::to(session('email'))->send(new AppointmentRequestedMail($appointmentId)); // Müşteriye onay e-postası gönder
        return redirect()->route('dash.customer')->with('success', 'Randevu isteğiniz başarıyla alındı.');  // Müşteri paneline yönlendir ve başarı mesajı göster
    }

    
    public function listStaff() // Admin: Şirkete ait personel listesini getir
    {
        // Yalnızca kendi şirketinin personelini döner
        $companyId = session('company_uni_id'); // oturumdaki sirket ID sine gore filtrelenir
        $staff = DB::table('staff_members')
                   ->where('company_uni_id', $companyId)
                   ->get();
          return view('dash.adminStaff', compact('staff'));
     }

    public function addStaff(Request $request)
    {
        $request->validate([
            'full_name'        => 'required|string',
            'experience_level' => 'required|string',
        ]);

        // Admin: sadece kendi şirketine personel ekle
        DB::table('staff_members')->insert([
            'company_uni_id'    =>  session('company_uni_id'),
            'full_name'         => $request->full_name,
            'experience_level'  => $request->experience_level,
            'created_at'        => now(),
        ]);

        return back()->with('success','Employee added. ');
    }

    public function deleteStaff($id)
    {
        // Admin: sadece kendi şirketinin personelini sil
        DB::table('staff_members')
          ->where('staff_member_uni_id', $id)
          ->where('company_uni_id', session('company_uni_id'))
          ->delete();

        return back()->with('success','Çalışan silindi.');
    }

    public function adminAppointments()
    {    $companyId = session('company_uni_id');
        // Admin: sadece kendi şirkete ait randevuları listele
        $list = DB::table('appointments as a')
            ->join('users as u', 'a.user_uni_id', '=', 'u.user_uni_id')
            ->where('a.company_uni_id', session('company_uni_id'))
            ->select('a.*','u.full_name','u.email')
            ->orderBy('a.created_at','desc')
            ->get();

        return view('dash.adminAppointments', compact('list'));
    }
         public function adminCategories()
        {
        $categories = DB::table('categories')->get(); // veya elle sabit bir liste
        return view('dash.adminCategories', compact('categories'));
        }


      public function updateStatus(Request $r, $id)
    {
          
    
           $r->validate([    // Geçerli statü değerlerini kontrol et
            'status' => 'required|in:pending,confirmed,cancelled'
             ]);

 
             $query = DB::table('appointments')
             ->where('appointment_id', $id);

           if (session('user_type_id') !== 3)
             {
               $query->where('company_uni_id', session('company_uni_id')); // superadmin degilse sadece kendi sirketine mudahele edebilir
             }

   
           $updated = $query->update([  // statusu update edildi
          'status' => $r->status
         ]);

   
            if ($updated) 
            {
               Mail::to($r->email) ->send(new AppointmentStatusMail($id, $r->status));   // statu degistirilmisse musteriye mail gider
            }
            
              if (session('user_type_id') === 3) 
              {
               return redirect()->route('superadmin.appointments')->with('success','Status updated.');
              }
        return redirect()->route('admin.appointments')->with('success','Status updated.');
}


    public function showAvailabilityManagement()
{
    // Şirket ID'sini oturumdan alır
    $companyId = session('company_uni_id');

    // Şirkete ait tüm personel kayıtlarını çeker
    $staffList = DB::table('staff_members')
                   ->where('company_uni_id', $companyId)
                   ->get();

    // Her personel için uygunluk slotlarını sorgular ve bir araya getirir
    $availabilityData = $staffList->map(function($s) {
        // Bu personelin tüm slotlarını başlangıç zamanına göre sıralı şekilde alır
        $slots = DB::table('availability_slots')
                   ->where('staff_member_uni_id', $s->staff_member_uni_id)
                   ->orderBy('start_time')
                   ->get();

        return [
            'staff' => $s,    // Personel bilgisi
            'slots' => $slots // Bu personele ait slot bilgileri
        ];
    });

    // 'dash.adminAvailability' görünümüne veriyi gönderir
    return view('dash.adminAvailability', [
        'availabilityData' => $availabilityData
    ]);
}

public function updateAvailabilitySlot(Request $request, $slotId)
{
    // Gelen istekteki 'status' alanını 'available' veya 'unavailable' olarak doğrular
    $request->validate([
        'status' => 'required|in:available,unavailable'
    ]);

    // Slotun, oturumdaki şirkete ait personelden birine ait olup olmadığını kontrol eder
    $slot = DB::table('availability_slots')
              ->join('staff_members', 'availability_slots.staff_member_uni_id', '=', 'staff_members.staff_member_uni_id')
              ->where('availability_slots.slot_id', $slotId)
              ->where('staff_members.company_uni_id', session('company_uni_id'))
              ->select('availability_slots.*')
              ->first();

    // Slot bulunamazsa yetkisiz erişim olarak işlem yapar
    if (! $slot) {
        abort(403);
    }

    // Slot durumunu gelen değerle günceller
    DB::table('availability_slots')
      ->where('slot_id', $slotId)
      ->update(['status' => $request->status]);

    // Geri dönerek başarı mesajı verir
    return back()->with('success', 'Slot durumu güncellendi.');
}
}