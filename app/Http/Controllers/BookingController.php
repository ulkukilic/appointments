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
    // Oturumdan admin kullanıcının ID’sini al
    $userId = session('user_uni_id');

    // Admin’in sahip olduğu company_uni_id’leri al
    $companyIds = DB::table('company_owners')
        ->where('user_uni_id', $userId)
        ->pluck('company_uni_id')
        ->toArray();

    // Eğer hiç şirketi yoksa boş collection, yoksa o ID’ler içindeki companies’ı getir
    if (empty($companyIds)) {
        $companies = collect();
    } else {
        $companies = DB::table('companies')
            ->whereIn('company_uni_id', $companyIds)
            ->orderBy('name')
            ->get();
    }

    // dash.adminCompanies blade’ine gönderiyoruz
return view('dash.adminCategories', compact('companies'));   
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

   // BookingController.php içinde
public function superadminAppointments()
{
    // Tüm şirketlerin tüm randevularını çekin
    $list = DB::table('appointments as a')
        ->leftJoin('users as u','a.user_uni_id','u.user_uni_id')
        ->leftJoin('services as s','a.service_id','s.service_id')
        ->leftJoin('staff_members as sm','a.staff_member_uni_id','sm.staff_member_uni_id')
        ->leftJoin('companies as c','a.company_uni_id','c.company_uni_id')
        ->select(
          'a.appointment_id',
          'c.name as company_name',
          'u.full_name as customer_name',
          'u.email',
          's.name as service_name',
          'sm.full_name as staff_name',
          'a.scheduled_time',
          'a.status'
        )
        ->orderBy('a.created_at','desc')
        ->get();

    return view('dash.superadminAppointments', compact('list'));
}
public function superadminUsers()
{
    $users = DB::table('users')->get();
    return view('dash.superadminUsers', compact('users'));
}
 public function superadminCreateUser()
    {
        return view('dash.superadminUsersCreate');
    }

    // Formdan geleni kaydet
    public function superadminStoreUser(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6|confirmed',
        ]);
        $data['password'] = Hash::make($data['password']);
        DB::table('users')->insert([
            'user_uni_id' => Str::uuid(),
            'full_name'   => $data['full_name'],
            'email'       => $data['email'],
            'password'    => $data['password'],
            'created_at'  => now(),
        ]);
        return redirect()->route('superadmin.users.index')
                         ->with('success','Kullanıcı başarıyla eklendi.');
    }
    public function toggleStaff($id)
{
    // fetch staff
    $staff = DB::table('staff_members')->where('staff_member_uni_id',$id)->first();
    if (!$staff || $staff->company_uni_id != session('company_uni_id')) {
        abort(403);
    }
    // toggle
    $new = $staff->is_active ? 0 : 1;
    DB::table('staff_members')
      ->where('staff_member_uni_id',$id)
      ->update(['is_active'=>$new]);
    return back()->with('success','Çalışan durumu güncellendi.');
}
public function adminServices()
{
    $companyId = session('company_uni_id');
    $services = DB::table('company_services as cs')
                  ->join('services as s','cs.service_id','s.service_id')
                  ->where('cs.company_uni_id',$companyId)
                  ->select('cs.id','s.name','cs.price','cs.duration')
                  ->get();
    return view('dash/adminServices',compact('services'));
}

public function showServiceForm()
{
    return view('dash/adminServicesCreate');
}

public function storeService(Request $r)
{
    $r->validate([
      'name'=>'required|string',
      'price'=>'required|numeric',
      'duration'=>'required|integer',
    ]);
    // insert into services if new
    $service = DB::table('services')->where('name',$r->name)->first();
    if (!$service) {
      $sid = DB::table('services')->insertGetId([
        'name'=>$r->name,
        'standard_duration'=>$r->duration,
        'created_at'=>now(),
      ]);
    } else {
      $sid = $service->service_id;
    }
    // link to company
    DB::table('company_services')->insert([
      'company_uni_id'=>session('company_uni_id'),
      'service_id'=>$sid,
      'price'=>$r->price,
      'duration'=>$r->duration,
      'created_at'=>now(),
    ]);
    return redirect()->route('admin.services.index')
                     ->with('success','Yeni servis eklendi.');
}

public function superadminStoreCompany(Request $request)
{
    $data = $request->validate([
        'name'             => 'required|string|max:255',
        'category'         => 'required|string|max:100',
        'email'            => 'nullable|email',
        'address'          => 'nullable|string',
        'phone_number'     => 'nullable|string',
        'description'      => 'nullable|string',
        'owner_identifier' => 'required|string',
    ]);

    // 1) create the company
    $companyId = DB::table('companies')->insertGetId([
        'name'           => $data['name'],
        'category'       => $data['category'],
        'email'          => $data['email'] ?? null,
        'address'        => $data['address'] ?? null,
        'phone_number'   => $data['phone_number'] ?? null,
        'description'    => $data['description'] ?? null,
        'created_at'     => now(),
    ]);

    // 2) find or create the user by email or full_name
    $identifier = $data['owner_identifier'];
    $user = DB::table('users')
        ->where('email', $identifier)
        ->orWhere('full_name', $identifier)
        ->first();

    if (! $user) {
        // if it’s not an email, make up a dummy one
        $email = filter_var($identifier, FILTER_VALIDATE_EMAIL)
                 ? $identifier
                 : Str::slug($identifier).'@example.com';

        $userId = DB::table('users')->insertGetId([
            'full_name'    => $identifier,
            'email'        => $email,
            'password'     => Hash::make(Str::random(12)),
            'user_type_id' => 2, // Admin type
            'created_at'   => now(),
        ]);
    } else {
        $userId = $user->user_uni_id;
        // if they exist but aren’t Admin, upgrade them
        if ($user->user_type_id != 2) {
            DB::table('users')->where('user_uni_id', $userId)
              ->update(['user_type_id' => 2]);
        }
    }

    // 3) assign as owner
    DB::table('company_owners')->insert([
        'company_uni_id' => $companyId,
        'user_uni_id'    => $userId,
        'created_at'     => now(),
    ]);

    return redirect()->route('superadmin.companies.index')
                     ->with('success','Yeni şirket ve sahibi başarıyla eklendi.');
}

    // Düzenleme formu
    public function superadminEditUser($id)
    {
        $user = DB::table('users')->where('user_uni_id',$id)->first();
        return view('dash.superadminUsersEdit', compact('user'));
    }

    // Güncelleme
    public function superadminUpdateUser(Request $request, $id)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'email'     => "required|email|unique:users,email,{$id},user_uni_id",
            'password'  => 'nullable|string|min:6|confirmed',
        ]);
        $update = [
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'updated_at'=> now(),
        ];
        if ($request->filled('password')) {
            $update['password'] = Hash::make($data['password']);
        }
        DB::table('users')->where('user_uni_id',$id)->update($update);
        return redirect()->route('superadmin.users.index')
                         ->with('success','Kullanıcı başarıyla güncellendi.');
    }

// Yeni şirket formunu gösterir
public function superadminCreateCompany()
{
    return view('dash.superadminCompaniesCreate');
}


 public function superadminUsersAdmins()
    {
        $users = DB::table('users')
                   ->where('user_type_id', 2)
                   ->get();

        return view('dash.superadminUsersAdmins', compact('users'));
    }

    /**
     * Müşteri kullanıcılarını listele
     */
    public function superadminUsersCustomers()
    {
        $users = DB::table('users')
                   ->where('user_type_id', 1)
                   ->get();

        return view('dash.superadminUsersCustomers', compact('users'));
    }

    /**
     * Çalışan kullanıcılarını listele
     */
    public function superadminUsersStaff()
    {
        $staff = DB::table('staff_members')
                   ->get();

        return view('dash.superadminUsersStaff', compact('staff'));
    }
public function superadminReviews()
{
    $reviews = DB::table('reviews as r')
                 ->join('users as u','r.user_uni_id','u.user_uni_id')
                 ->select('r.*','u.full_name as user_name')
                 ->get();
    return view('dash.superadminReviews', compact('reviews'));
}
  
public function superadminCompanies()
{
    $companies = DB::table('companies')->get();
    return view('dash.superadminCompanies', compact('companies'));
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
               ->select('staff_member_uni_id','full_name','experience_level','is_active')
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

        
       $staffId= DB::table('staff_members')->insertGetId([  // Admin: sadece kendi şirketine personel ekle
            'company_uni_id'    =>  session('company_uni_id'),
            'full_name'         => $request->full_name,
            'experience_level'  => $request->experience_level,
            'created_at'        => now(),
        ]);
         // Eğer formdan 'service_ids' alanı geldiyse (bir veya birden fazla hizmet seçildiyse)
           if ($request->has('service_ids'))
          {
            foreach ($request->service_ids as $serviceId)// Her bir seçilen hizmet ID'si için döngü başlat
            {
                // staff_services tablosuna personelin hangi hizmetleri verdiğini ekle
                $staffId=DB::table('staff_services')->insert([
                    'staff_member_uni_id' => $staffId,     // Hangi personele ait olduğunu belirt
                    'service_id'          => $serviceId,   // Hangi hizmeti verdiğini belirt
                ]);
    }
}

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
     
    public function updateStatus(Request $request, $appointmentId)
{
    $request->validate([
        'status' => 'required|in:pending,confirmed,cancelled',
        'email'  => 'nullable|email'
    ]);


    $appointment = DB::table('appointments')// Randevunun var olup olmadığını kontrol et
        ->where('appointment_id', $appointmentId)
        ->first();

    if (!$appointment) 
    {
        return back()->with('error', 'Randevu bulunamadı.');
    }

   
    DB::table('appointments')  // Randevu statüsünü güncelle
        ->where('appointment_id', $appointmentId)
        ->update([
            'status' => $request->status
        ]);

    // Eğer e-posta adresi varsa bildirim gönder
    //if ($request->filled('email')) {
    //  Mail::to($request->email)->send(new AppointmentStatusMail(
    //     $appointmentId,
    //     $request->status
    //  ));
    //}

    return back()->with('success', 'Randevu durumu güncellendi.');
}

public function adminAppointments()
{
    // Oturumdan user ID ve userType al
    $userId = session('user_uni_id');
    $userTypeId = session('user_type_id');

    // Admin ise, userId mutlaka olmalı
    if ($userTypeId == 2 && !$userId) {
        abort(403, 'Şirket bilgisi bulunamadı.');
    }

    // Admin’in sahip olduğu tüm company_uni_id’leri alıyoruz
    $companyIds = DB::table('company_owners')
        ->where('user_uni_id', $userId)
        ->pluck('company_uni_id')
        ->toArray();

    // Eğer Admin’in hiç şirketi yoksa boş liste
    if (empty($companyIds)) 
    {
        $list = collect();
    } 
    else
     {
        // Birden fazla şirkete ait randevuları, aynı anda ilgili tablolarla JOIN ederek alıyoruz
        $list = DB::table('appointments as a')
            ->leftJoin('users as u', 'a.user_uni_id', '=', 'u.user_uni_id')
            ->leftJoin('services as s', 'a.service_id', '=', 's.service_id')
            ->leftJoin('staff_members as sm', 'a.staff_member_uni_id', '=', 'sm.staff_member_uni_id')
            ->leftJoin('companies as c', 'a.company_uni_id', '=', 'c.company_uni_id')
            ->whereIn('a.company_uni_id', $companyIds)  // ↔ Burada tek ID değil, dizi kullanıyoruz
            ->select(
                'a.appointment_id',
                'c.name as company_name',       
                'a.scheduled_time',
                'a.status',
                'u.full_name as customer_name',
                'u.email',
                's.name as service_name',
                'sm.full_name as staff_name'
            )
            ->orderBy('a.created_at', 'desc')
            ->get();
    }

    // Listeyi view’e gönder
    return view('dash.adminAppointments', compact('list'));
}

  // app/Http/Controllers/BookingController.php

public function adminReviews()
{
    $companyIds = DB::table('company_owners')
        ->where('user_uni_id', session('user_uni_id'))
        ->pluck('company_uni_id');

    $reviews = DB::table('reviews as r')
        ->whereIn('r.company_uni_id', $companyIds)
        ->join('users as u','r.user_uni_id','u.user_uni_id')
        ->select(
            'r.review_id',
            'u.full_name as customer',
            'r.comment',
            'r.rating',
            'r.staff_member_uni_id',
            'r.created_at'
        )
        ->orderBy('r.created_at','desc')
        ->get();

    return view('dash/adminReviews', compact('reviews'));
}
// Show the customer dashboard (categories view)
public function showCustomerDashboard()
{
    // same logic you had in dash/customer.blade.php for categories
    $categories = [
      'hospital'   => ['Hospital',     'hospital.jpg'],
      'dentist'    => ['Dentist',      'dentist.jpg'],
      // … etc …
    ];
    return view('dash.customer', compact('categories'));
}
public function showCustomerCategories()
{
    // pull distinct categories from companies
    $categories = DB::table('companies')
        ->pluck('category')
        ->unique()
        ->values();

    return view('dash.customerCategories', compact('categories'));
}
// List this customer’s appointments
public function customerAppointments()
{
    $userId = session('user_uni_id');

    $list = DB::table('appointments as a')
        ->leftJoin('companies as c', 'a.company_uni_id', '=', 'c.company_uni_id')
        ->leftJoin('services as s',  'a.service_id',     '=', 's.service_id')
        ->leftJoin('staff_members as sm','a.staff_member_uni_id','=','sm.staff_member_uni_id')
        ->where('a.user_uni_id', $userId)
        ->select(
            'a.appointment_id',
            'c.name      as company_name',
            's.name      as service_name',
            'sm.full_name as staff_name',
            'a.scheduled_time',
            'a.status'
        )
        ->orderBy('a.created_at','desc')
        ->get();

    return view('dash.customerAppointments', compact('list'));
}

// new: allow admin to delete a review
public function deleteAdminReview($id)
{
    DB::table('reviews')->where('review_id', $id)->delete();
    return back()->with('success','Yorum silindi.');
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
                'staff_member_uni_id' => $request->staff_member_uni_id,
                'rating'            => $request->rating,           // Kullanıcının verdiği puan
                'comment'           => $request->comment,          // Kullanıcının yorumu
                'created_at'        => now(),                      // Yorum eklenme tarihi
            ]);

            
        return back()->with('success', 'Yorumunuz başarıyla eklendi.'); // İşlem başarılıysa, önceki sayfaya dönüp başarı mesajı göster
}

}