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

class BookingController extends Controller
{
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

    // Yalnızca kendi şirketini güncelleyebilir (admin)
    public function update(Request $request, $company_uni_id)
    {
        if (Auth::user()->company_uni_id != $company_uni_id) {
            abort(403); // hata sayfasına yönlendiriyor erişimi olmayanları
        }

        // Güncelleme yapılmış bilgileri almak ve güncellemek için
        DB::table('companies')->where('company_uni_id', $company_uni_id)
          ->update($request->only(['name','address','phone_number','description']));

        return back()->with('success','Company updated.');
    }

    // Kategori seçildiğinde o kategoriyi listelemek için kullanılacak fonksiyon
    public function showCategory($category)
    {
        // O şirketin tüm bilgilerini çekecek
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

        // SQL'den availability_slots tablosundan her personele ait uygun zamanları getir
        $staffData = $staffList->map(function($s) {
            // Personelin uygun olan, geleceğe dönük slotlarını çekiyoruz
            $slots = DB::table('availability_slots')
                ->where('staff_member_uni_id', $s->staff_member_uni_id)
                ->where('status', 'available')
                ->where('start_time', '>=', now())
                ->orderBy('start_time')
                ->get();

            // Slotlar ve personel bilgisi birlikte döndürülür
            return [
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

        // kategori, şirket, personeller ve hizmetler
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
            ->lockForUpdate()
            ->first();

        if (! $slot) {
            // senden önce başkası aldıysa artık müsait değil yazısı görür
            DB::rollBack();
            return back()->with('error', 'Seçilen slot artık müsait değil.');
        }

        // Slot durumunu booked olarak güncelle
        DB::table('availability_slots')
            ->where('slot_id', $request->slot_id)
            ->update(['status' => 'booked']);

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

        // Transaction onayla
        DB::commit();

        // Müşteriye onay e-postası gönder
        Mail::to(Auth::user()->email)
             ->send(new AppointmentRequestedMail($appointmentId));
        // Müşteri paneline yönlendir ve başarı mesajı göster
        return redirect()->route('dash.customer')
                         ->with('success', 'Randevu isteğiniz başarıyla alındı.');
    }

    // Admin: Şirkete ait personel listesini getir
    public function listStaff()
    {
        // Yalnızca kendi şirketinin personelini döner
        $companyId = session('company_uni_id');
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
          ->where('company_uni_id', session('company_uni_id'))
          ->delete();

        return back()->with('success','Çalışan silindi.');
    }

    public function adminAppointments()
    {
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
        $r->validate(['status'=>'required|in:pending,confirmed,cancelled']);

        // Admin: sadece kendi şirkete ait randevıya dokun
        $updated = DB::table('appointments')
            ->where('appointment_id', $id)
            ->where('company_uni_id', session('company_uni_id'))
            ->update(['status'=>$r->status]);

        if ($updated) {
            // Mail gönderirken Auth::user()->email kullanırsın
            Mail::to($r->email)->send(new AppointmentStatusMail($id,$r->status));
        }

        return back()->with('success','Durum güncellendi.');
    }
}
