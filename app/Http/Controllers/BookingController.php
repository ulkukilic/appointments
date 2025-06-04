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
       // Oturumdan geçerli şirket ID'sini alır
            $companyId = session('company_uni_id');
            $userTypeId = session('user_type_id');

            // Eğer adminse ve şirket ID'si yoksa erişimi reddet
            if ($userTypeId == 2 && !$companyId) {
                abort(403, 'Şirket bilgisi bulunamadı.');
            }


            // Şirkete ait personel kayıtlarını tam isme göre sıralayarak çeker
            $staff = DB::table('staff_members')
                ->where('company_uni_id', $companyId)
                ->orderBy('full_name')
                ->get();

            // 'dash.adminStaff' görünümüne personel verilerini gönderir
            return view('dash.adminStaff', compact('staff'));

     }
            
     public function editStaff($id)
   {
        // Oturumdan şirket ID'sini al; yoksa yetkisiz erişim hatası döndür
        $companyId = session('company_uni_id');
        $userTypeId = session('user_type_id');

        // Eğer adminse ve şirket ID'si yoksa erişimi reddet
        if ($userTypeId == 2 && !$companyId) {
            abort(403, 'Şirket bilgisi bulunamadı.');
        }

        if ($id == 0) {
            // “Yeni Personel Ekle” demek: boş bir $staff değişkeni gönderelim
            return view('dash.adminStaffEdit'); 
        } 
        // Verilen ID ve şirkete ait personel kaydını al
        $staff = DB::table('staff_members')
            ->where('staff_member_uni_id', $id)
            ->where('company_uni_id', $companyId)
            ->first();

        // Personel bulunamazsa 404 döndür
        if (!$staff) {
            abort(404, 'Çalışan bulunamadı.');
        }

        // Düzenleme formunu görüntüle, mevcut personel verisini ilet
        return view('dash.adminStaffEdit', compact('staff'));
   }

public function updateStaff(Request $request, $id)
{
      $companyId = session('company_uni_id');
        $companyId = session('company_uni_id');
        $userTypeId = session('user_type_id');

        // Eğer adminse ve şirket ID'si yoksa erişimi reddet
        if ($userTypeId == 2 && !$companyId) {
            abort(403, 'Şirket bilgisi bulunamadı.');
        }



    // İsim ve deneyim seviyesi alanlarını doğrula
    $request->validate([
        'full_name'        => 'required|string|max:100',
        'experience_level' => 'required|string|max:50',
    ]);

    // Personel kaydını güncelle; yetkiniz yoksa veya kayıt yoksa $updated false olur
    $updated = DB::table('staff_members')
        ->where('staff_member_uni_id', $id)
        ->where('company_uni_id', $companyId)
        ->update([
            'full_name'        => $request->full_name,
            'experience_level' => $request->experience_level,
            'updated_at'       => now(),
        ]);

    // Güncelleme başarısızsa kullanıcıya hata mesajı göster
    if (!$updated) {
        return back()->with('error', 'Güncelleme sırasında bir hata oluştu veya yetkiniz yok.');
    }

    // Başarılıysa personele ait liste sayfasına yönlendir ve başarı mesajı göster
    return redirect()->route('admin.staff.index')->with('success', 'Çalışan başarıyla güncellendi.');
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

  
         public function adminCategories()
        {
             $categories = DB::table('companies')
            ->select('category')
            ->distinct()
            ->pluck('category');  // Sadece ‘slug’ değerlerini (ör: ['hospital','barber',...]) çekiyoruz.

            return view('dash.adminCategories', compact('categories'));
        }


      public function updateStatus(Request $request, $id)
  {
       $status = $request->input('status');
       $email  = $request->input('email');

    $updated = DB::table('appointments')->where('appointment_id', $id)->update([
        'status' => $status]);

    // Başarıyla güncellendiyse e-posta (isteğe bağlı)
    if ($updated && $email) 
    {
        // Mail::to($email)->send(new AppointmentStatusMail($id, $status));
    }

    // Kullanıcı tipi kontrolü
    $userType = session('user_type_id');

    if ($userType == 3) 
    {
        // SuperAdmin aynı sayfada kalmalı
        return redirect()->back()->with('success', 'Status updated.');
    } 
    
    else 
    {
        // Admin için admin randevu sayfasına yönlendir
        return redirect()->route('admin.appointments')->with('success', 'Status updated.');
    }      
  }

   

    public function showAvailabilityManagement()
  { 
        // Şirket ID'sini oturumdan alır
        $companyId = session('company_uni_id');

        // Şirkete ait tüm personel kayıtlarını çeker
        $staffList = DB::table('staff_members')
                    ->where('company_uni_id', $companyId)
                    ->orderBy('full_name') 
                    ->get();

        // Her personel için uygunluk slotlarını sorgular ve bir araya getirir
        $availabilityData = $staffList->map(function($s) {
            // Bu personelin tüm slotlarını başlangıç zamanına göre sıralı şekilde alır
            $slots = DB::table('availability_slots as av')
                    ->leftJoin('staff_services as ss', 'av.staff_member_uni_id', '=', 'ss.staff_member_uni_id')
                    ->leftJoin('services as sv', 'ss.service_id', '=', 'sv.service_id')
                    ->where('av.staff_member_uni_id', $s->staff_member_uni_id)
                    ->orderBy('av.start_time')
                    ->select(
                        'av.slot_id',
                        'av.start_time',
                        'av.end_time',
                        'av.status',
                        'ss.service_id',
                        'sv.name as service_name',
                        'sv.standard_duration'
                    )
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
        $slot = DB::table('availability_slots as av')
                ->join('staff_members as sm', 'av.staff_member_uni_id', '=', 'sm.staff_member_uni_id')
                ->where('av.slot_id', $slotId)
                ->where('sm.company_uni_id', $companyId)
                ->select('av.*')
                ->first();

        // Slot bulunamazsa yetkisiz erişim olarak işlem yapar
        if (! $slot) {
            abort(403);
        }

        // Slot durumunu gelen değerle günceller
        DB::table('availability_slots')
                ->where('slot_id', $slotId)
                ->update([
                    'status'     => $request->status,
                    'updated_at' => now(),
                ]);

        // Geri dönerek başarı mesajı verir
        return back()->with('success', 'Slot durumu güncellendi.');
}

  public function adminAppointments()
  {
    // Oturumdan şirket ID'sini al; yoksa 403 hatası döndür
    $companyId = session('company_uni_id');
    $userTypeId = session('user_type_id');

    // Eğer adminse ve şirket ID'si yoksa erişimi reddet
    if ($userTypeId == 2 && !$companyId) {
        abort(403, 'Şirket bilgisi bulunamadı.');
    }


    // Şirkete ait tüm randevuları al, müşteri, hizmet ve personel bilgilerini ilişkilendir
    $list = DB::table('appointments as a')
        ->leftJoin('users as u', 'a.user_uni_id', '=', 'u.user_uni_id')       // Müşteri bilgisi
        ->leftJoin('services as s', 'a.service_id', '=', 's.service_id')      // Hizmet bilgisi
        ->leftJoin('staff_members as sm', 'a.staff_member_uni_id', '=', 'sm.staff_member_uni_id') // Personel bilgisi
        ->where('a.company_uni_id', $companyId)                                // Yalnızca bu şirkete ait randevular
        ->select(
            'a.appointment_id',       // Randevu ID'si
            'a.scheduled_time',       // Planlanan randevu zamanı
            'a.status',               // Randevu durumu (örn. pending, confirmed)
            'u.full_name as customer_name', // Müşteri adı
            'u.email',                // Müşteri e-posta adresi
            's.name as service_name', // Hizmet adı
            'sm.full_name as staff_name' // Personel adı
        )
        ->orderBy('a.created_at', 'desc') // Yeni randevular en üstte olacak şekilde sırala
        ->get();

    // 'dash.adminAppointments' görünümüne randevu listesi verisini gönder
    return view('dash.adminAppointments', compact('list'));
  }

public function deleteCompany($companyId)
{
    // Verilen şirket ID'sine ait kaydı 'companies' tablosundan sil
    $deleted = DB::table('companies')
        ->where('company_uni_id', $companyId)
        ->delete();

    // Silme işlemi başarısızsa hata mesajı ile geri dön
    if (!$deleted) {
        return back()->with('error', 'Silme sırasında bir hata oluştu.');
    }

    // Başarılıysa başarılı mesajı ile geri dön
    return back()->with('success', 'Şirket başarıyla silindi.');
}

public function deleteUser($userId)
{
    // Verilen kullanıcı ID'sine ait kaydı 'users' tablosundan sil
    $deleted = DB::table('users')
        ->where('user_uni_id', $userId)
        ->delete();

    // Silme işlemi başarısızsa hata mesajı ile geri dön
    if (!$deleted) {
        return back()->with('error', 'Silme sırasında bir hata oluştu.');
    }

    // Başarılıysa başarılı mesajı ile geri dön
    return back()->with('success', 'Kullanıcı başarıyla silindi.');
}

public function showCompanyAvailability($category, $companyUniId)
{
    // Şirket bilgisini 'companies' tablosundan al; eğer yoksa null döner
    $company = DB::table('companies')
        ->where('company_uni_id', $companyUniId)
        ->first();

    // Görünümde kullanılacak varsayılan gün sayısı
    $days = 30;

    // Şirkete ait tüm personel kayıtlarını çek
    $staffList = DB::table('staff_members')
        ->where('company_uni_id', $companyUniId)
        ->get();

    // Her personel için gelecek tarihteki müsait slotları topla
    $staffData = $staffList->map(function ($s) {
        // Sadece "available" durumundaki ve şu an veya sonrasında başlayacak slotları al
        $slots = DB::table('availability_slots')
            ->where('staff_member_uni_id', $s->staff_member_uni_id)
            ->where('status', 'available')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->get();

        return [
            'staff' => $s,     // Personel bilgisi
            'slots' => $slots, // Bu personele ait gelecek müsait slotlar
        ];
    });

    // Şirkete ait hizmetleri 'company_services' tablosundan, hizmet bilgileriyle birlikte çek
    $services = DB::table('company_services as cs')
        ->join('services as s', 'cs.service_id', '=', 's.service_id')
        ->where('cs.company_uni_id', $companyUniId)
        ->select('s.*')
        ->get();

    // Verileri 'categories.availability' görünümüne gönder
    return view('categories.availability', [
        'category'  => $category,
        'company'   => $company,
        'staffData' => $staffData,
        'services'  => $services,
        'days'      => $days,
    ]);
 }
}