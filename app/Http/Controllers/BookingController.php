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
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{

  public function adminDashboard()
{
     $companyId = session('company_uni_id'); // oturumu admin acanlar icin sirketin companu_uni_id sini aliyouz 

           
     $list = DB::table('appointments as a') // Randevu listesi sirkete ait tum randevulari musterileri , hizmetleri ve personelleri listelemek 
      ->leftJoin('users as u', 'a.user_uni_id', '=', 'u.user_uni_id')
      ->leftJoin('services as s', 'a.service_id', '=', 's.service_id')
      ->leftJoin('staff_members as sm', 'a.staff_member_uni_id', '=', 'sm.staff_member_uni_id')
      ->where('a.company_uni_id', $companyId)
      ->select(
                    'a.appointment_id',
                    'a.scheduled_time',
                    'a.status',
                    'u.full_name as customer_name',
                    'u.email',
                    's.name as service_name',
                    'sm.full_name as staff_name'
                )
                ->orderBy('a.created_at', 'desc') // en  guncel olandan baslamasi icin 
                ->get();

            
     $categories = DB::table('companies')// Sirkete ait kategorileri almakta
        ->where('company_uni_id', $companyId)
        ->pluck('category')
        ->unique() // ayni kategoriden sadece bir tane alinmasi icin 
        ->values();

            
     $staff = DB::table('staff_members') // tum sirket calisanlarini company_uni_id den almak full_namini
        ->where('company_uni_id', $companyId)
        ->orderBy('full_name')
        ->get();

        
     $availabilityData = $staff->map(function($s)   // Her personelin musaitlik saatleri , hangi hizmetlerce calistigini sorgu ile almak 
     {
         $slots = DB::table('availability_slots as av')
           ->leftJoin('staff_services as ss', 'av.staff_member_uni_id', '=', 'ss.staff_member_uni_id')
            ->leftJoin('services as sv', 'ss.service_id', '=', 'sv.service_id') // Hizmet detayını almak için
            ->where('av.staff_member_uni_id', $s->staff_member_uni_id)
            ->orderBy('av.start_time') // Slotları zamana göre sırala
            ->select(
                        'av.slot_id', 'av.start_time', 'av.end_time', 'av.status',
                        'ss.service_id', 'sv.name as service_name', 'sv.standard_duration'
                    )
                    ->get();

        return ['staff' => $s, 'slots' => $slots];
     });
   
     $reviews = DB::table('reviews')
            ->where('company_uni_id', $companyId)
            ->join('users', 'reviews.user_uni_id', '=', 'users.user_uni_id')
            ->select(
                    'reviews.review_id',
                    'reviews.rating',
                    'reviews.comment',
                    'reviews.created_at',
                    'users.full_name as customer_name'
                )
            ->orderBy('reviews.created_at', 'desc')
            ->get();
             
      
      $services = DB::table('company_services as cs') // Şirkete ait hizmetleri çekiyoruz ki adminStaff.blade.php kullanabilsin
            ->join('services as s', 'cs.service_id', '=', 's.service_id')
            ->where('cs.company_uni_id', $companyId)
            ->select('s.service_id', 's.name', 's.standard_duration')
            ->get();
            // Hepsini vire olarak aktar 
     return view('dash.admin', compact('list', 'categories', 'staff', 'availabilityData', 'reviews', 'services' ));
         
    }
            
   

  public function adminCategories()
{
    
    $companyId = session('company_uni_id');  // Session'dan şirket ve kullanıcı tipini alıyoruz
    $userType = session('user_type_id');

    // SuperAdmin = 3 Admin = 2   // Admin ise sadece kendi sirketinin kategorilerini gorsun 
    if ($userType == 2) 
    {
       
        $categories = DB::table('companies') //  Sadece kullanıcının sahibi olduğu şirketlerin kategorilerini göstermek
            ->where('owner_user_uni_id', session('user_uni_id')) // Kullanıcının sahip olduğu şirketler
            ->pluck('category') // Sadece kategori isimlerini al
            ->unique() // Aynı kategorilerden sadece biri
            ->values(); // Dizi şeklinde al
    } 
    else 
    {
        
        $categories = DB::table('companies')->pluck('category')->unique()->values(); // Eger SuperAdmin ise ayni sayfada kalip  tum categorileri gostericek
    }

    // Kategorileri adminCategories görünümüne gönder
    return view('dash.adminCategories', compact('categories'));
}



  public function editCompany($companyId) //Superadmin – şirket düzenleme formu
    {
        $company = DB::table('companies')->where('company_uni_id', $companyId)->first(); // companies degiskeni tarafindan , company_uni_id degerini cekiyoruz
        return view('dash.superadminCompanyEdit', compact('company')); // blade'ine $company verisini gonderiyoruz 
    }
     

  public function updateCompanyBySuperadmin(Request $request, $companyId) //  Superadmin – düzenlemeyi kaydet
    {
        DB::table('companies') ->where('company_uni_id', $companyId) ->update($request->only(['name','address','phone_number','description']));// Request icinde gerekli olan alanlari alip guncelliyoruz 
        return back()->with('success', 'Şirket başarıyla güncellendi.'); // islem sonrasi ayni sayfadaa donup basari mesaji veriyoruz
    }

   
    
   public function update(Request $request, $company_uni_id) // Yalnızca kendi şirketini güncelleyebilir (admin)
    {
            if (session('company_uni_id') != $company_uni_id)  // session da tutulan kullanicinin company_uni_id si ile ayni sirket id sini mi guncelliyor kontrol edilir 
            {
               abort(403); // Hata sayfası
            }
            DB::table('companies') // kendi sirketinin bilgilerini guncelleyebilir databaseden
                ->where('company_uni_id', $company_uni_id)
                ->update($request->only(['name','address','phone_number','description']));
            return back()->with('success','Company updated.');
    
    }
 
  public function showCategory($category) // Kategori seçildiğinde o kategoriyi listelemek için kullanılacak fonksiyon
    {
         $companies = DB::table('companies')->where('category', $category)->get(); // O şirketin tüm  category bilgilerini çekecek
        return view('categories.show', compact('category','companies'));// category ve companies bilgilerini gönderir
    }

   public function deleteCompany($companyId)
   {
         DB::beginTransaction();  // veritanabi islemi baslatilir ve veritabaninda yapilicak olan islemler tamamlanilir

            try {
                // 1. Randevuları sil
                DB::table('appointments')
                    ->where('company_uni_id', $companyId)
                    ->delete();

                // 2. Personelleri sil
                DB::table('staff_members')
                    ->where('company_uni_id', $companyId)
                    ->delete();

                // 3. Şirket sahiplerini sil
                DB::table('company_owners')
                    ->where('company_uni_id', $companyId)
                    ->delete();

                // 4. Son olarak şirketi sil
                $deleted = DB::table('companies')
                    ->where('company_uni_id', $companyId)
                    ->delete();

                if (!$deleted) 
                {
                    DB::rollBack();
                    return back()->with('error', 'Şirket silinemedi.');
                }

                DB::commit(); // islemler dogru donerse islem veritabanina kalici olarak kaydedilir 
                return back()->with('success', 'Şirket ve ilişkili tüm kayıtlar başarıyla silindi.');
            } 
            catch (\Exception $e) 
            {
                DB::rollBack();
                return back()->with('error', 'Silme sırasında bir hata oluştu: ' . $e->getMessage());
            }
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

            // Şirkete ait hizmetleri çekiyoruz (hizmetin ID'si, adı ve standart süresiyle birlikte)
            $services = DB::table('company_services as cs')
                ->join('services as s', 'cs.service_id', '=', 's.service_id')
                ->where('cs.company_uni_id', $companyId)
                ->select('s.service_id', 's.name', 's.standard_duration')
                ->get();

            // 'dash.adminStaff' blade dosyasına hem personel hem de hizmet listesini gönderiyoruz
            return view('dash.adminStaff', compact('staff', 'services'));

     }
            
  public function editStaff($id)
   {
        // Oturumdan şirket ID'sini al; yoksa yetkisiz erişim hatası döndür
        $companyId = session('company_uni_id');
        $userTypeId = session('user_type_id');

        
        if ($userTypeId == 2 && !$companyId)  // Eğer adminse ve şirket ID'si yoksa erişimi reddet
        { 
            abort(403, 'Şirket bilgisi bulunamadı.');
        }

        if ($id == 0)  // yeni personel ekleme 
        {
           return view('dash.adminStaffEdit');   // bos form acilir yeni personel eklemek icin 
        } 
       
        $staff = DB::table('staff_members')  // Verilen ID ve şirkete ait personel kaydını al
            ->where('staff_member_uni_id', $id)
            ->where('company_uni_id', $companyId)
            ->first();

    
        if (!$staff)  // Personel bulunamazsa 404 döndür
        {
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

        
        if ($userTypeId == 2 && !$companyId) // Eğer adminse ve şirket ID'si yoksa erişimi reddet
        {
            abort(403, 'Şirket bilgisi bulunamadı.');
        }


        $request->validate([ // İsim ve deneyim seviyesi alanlarını doğrula
        'full_name'        => 'required|string|max:100',
        'experience_level' => 'required|string|max:50',
         ]);

    
        $updated = DB::table('staff_members') // Sadece o şirkete ve personele ait kaydı güncelle
            ->where('staff_member_uni_id', $id)
            ->where('company_uni_id', $companyId)
            ->update([
                'full_name'        => $request->full_name,
                'experience_level' => $request->experience_level,
                'updated_at'       => now(),
            ]);

    
    if (!$updated)  // Güncelleme başarısızsa kullanıcıya hata mesajı göster
    {
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

        
        DB::table('staff_members')->insert([  // Admin: sadece kendi şirketine personel ekle
            'company_uni_id'    =>  session('company_uni_id'),
            'full_name'         => $request->full_name,
            'experience_level'  => $request->experience_level,
            'created_at'        => now(),
        ]);

        return back()->with('success','Employee added. ');
    }

  public function deleteStaff($id)
{
        
        DB::table('staff_members') // Admin: sadece kendi şirketinin personelini sil
          ->where('staff_member_uni_id', $id)
          ->where('company_uni_id', session('company_uni_id'))
          ->delete();

        return back()->with('success','Çalışan silindi.');
}

 public function addAvailabilitySlot(Request $request)
{
    $request->validate([ // Slot eklerken gerekli alanların doğrulanması
        'staff_member_uni_id' => 'required|exists:staff_members,staff_member_uni_id',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after:start_time',
         'status'     => 'required|in:available,unavailable',
    ]);

    
    $companyId = session('company_uni_id');

    $staff = DB::table('staff_members')  // Eklenmek istenen personel gerçekten bu şirkete mi ait kontrol et
        ->where('staff_member_uni_id', $request->staff_member_uni_id)
        ->where('company_uni_id', $companyId)
        ->first();

    if (!$staff)    // Yetki yoksa veya personel bulunamazsa erişimi engelle
    {
        abort(403, 'Bu personele slot ekleme yetkiniz yok.');
    }

    DB::table('availability_slots')->insert([ // Yeni slot ekle
        'staff_member_uni_id' => $request->staff_member_uni_id,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
         'status' => $request->status,
       
    ]);

    return redirect()->back()->with('success', 'Yeni slot başarıyla eklendi.'); // Aynı sayfaya başarı mesajıyla dön
}


public function updateAvailabilitySlot(Request $request,int $slotId)
{
         $companyId = session('company_uni_id');
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
       // if (! $slot) {
         //   abort(403);
        //}

        
        DB::table('availability_slots')// Slot durumunu gelen değerle günceller
                ->where('slot_id', $slotId)
                ->update([
                    'status'     => $request->status,
                    
                ]);

        
        return back()->with('success', 'Slot durumu güncellendi.'); // Geri dönerek başarı mesajı verir
}

  public function showAvailabilityManagement()
 { 
        
        $companyId = session('company_uni_id');  // oturumda sirket uni_id si alinir 

        $staffList = DB::table('staff_members') // Şirkete ait tüm personel kayıtlarını çeker
                    ->where('company_uni_id', $companyId)
                    ->orderBy('full_name') 
                    ->get();

        $availabilityData = $staffList->map(function($s) {  // Her personel için uygunluk slotlarını sorgular ve bir araya getirir

            $slots = DB::table('availability_slots as av')  // Bu personelin tüm slotlarını başlangıç zamanına göre sıralı şekilde alır
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

        return view('dash.adminAvailability', [  // 'dash.adminAvailability' görünümüne veriyi gönderir
            'availabilityData' => $availabilityData
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
                    
                ]);

                
                DB::commit();// onayla
        
                    
                
                Mail::to(session('email'))->send(new AppointmentRequestedMail($appointmentId)); // Müşteriye onay e-postası gönder
                return redirect()->route('dash.customer')->with('success', 'Randevu isteğiniz başarıyla alındı.');  // Müşteri paneline yönlendir ve başarı mesajı göster
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

 
    public function storeReview(Request $request, $companyUniId) 
 {
        $request->validate([  // Kullanıcıdan gelen verileri doğrula (puan zorunlu, yorum opsiyonel)
                'rating'  => 'required|integer|min:1|max:5',  // 1-5 arası puan zorunlu
                'comment' => 'nullable|string',               // Yorum opsiyonel
            ]);

            
         DB::table('reviews')->insert([ // reviews tablosuna yeni bir yorum ekle
                'user_uni_id'       => session('user_uni_id'),     // Yorumu yapan kullanıcının ID'si (oturumdan alınır)
                'company_uni_id'    => $companyUniId,              // Yorum yapılan şirketin ID'si
                'rating'            => $request->rating,           // Kullanıcının verdiği puan
                'comment'           => $request->comment,          // Kullanıcının yorumu
                'created_at'        => now(),                      // Yorum eklenme tarihi
            ]);

            
        return back()->with('success', 'Yorumunuz başarıyla eklendi.'); // İşlem başarılıysa, önceki sayfaya dönüp başarı mesajı göster
}

}