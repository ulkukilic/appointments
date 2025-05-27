@extends('layouts.app')

@section('title', 'Admin - Randevu Yönetimi')
@section('page_title', 'Randevu Listesi')

@section('content')

<!-- Başarılı / hata mesajlarını göstermek için alerts bileşeni -->
@include('layouts.alerts')

<!-- Randevu listesi tablosu -->
<table class="table table-striped">
  <thead>
    <tr>
      <th>#</th> <!-- Satır numarası -->
      <th>Müşteri</th> <!-- Randevu alan kullanıcı -->
      <th>Email</th> <!-- Müşteri e-posta -->
      <th>Hizmet</th> <!-- Alınan hizmet -->
      <th>Personel</th> <!-- Hangi çalışandan alıyor -->
      <th>Şirket</th> <!-- Hangi şirkette -->
      <th>Tarih / Saat</th> <!-- Randevu zamanı -->
      <th>Durum</th> <!-- Randevu durumu -->
      <th>İşlem</th> <!-- Onayla / Reddet / Beklet -->
    </tr>
  </thead>
  <tbody>
    @foreach($list as $item)
      <tr>
        <!-- Satır numarası -->
        <td>{{ $loop->iteration }}</td>

        <!-- Müşteri bilgileri -->
        <td>{{ $item->full_name }}</td>
        <td>{{ $item->email }}</td>

        <!-- Alınan hizmet ismini services tablosundan çeker -->
        <td>{{ optional(DB::table('services')->where('service_id', $item->service_id)->first())->name }}</td>

        <!-- Personel adı: staff_members tablosundan -->
        <td>
          {{ optional(DB::table('staff_members')->where('staff_member_uni_id', $item->staff_member_uni_id)->first())->full_name }}
        </td>

        <!-- Şirket bilgisi: admin yalnızca kendi şirketini görür ama yine de gösterilir -->
        <td>
          {{ optional(DB::table('companies')->where('company_uni_id', $item->company_uni_id)->first())->name }}
        </td>

        <!-- Randevu tarihi/saatini biçimli göster -->
        <td>{{ \Carbon\Carbon::parse($item->scheduled_time)->format('d M Y H:i') }}</td>

        <!-- Durum: pending / confirmed / cancelled -->
        <td class="text-capitalize">{{ $item->status }}</td>

        <!-- İşlem butonları: form ile gönderilir -->
        <td>
          <form method="POST" action="{{ route('admin.appointments.update', $item->appointment_id) }}">
            @csrf

            <!-- Email gizli alan: e-posta bildirimleri için kullanılabilir -->
            <input type="hidden" name="email" value="{{ $item->email }}">

            <!-- Onayla butonu -->
            <button
              name="status"
              value="confirmed"
              type="submit"
              class="btn btn-success btn-sm"
            >Onayla</button>

            <!-- Reddet butonu -->
            <button
              name="status"
              value="cancelled"
              type="submit"
              class="btn btn-danger btn-sm"
            >İptal Et</button>

            <!-- Beklet butonu -->
            <button
              name="status"
              value="pending"
              type="submit"
              class="btn btn-warning btn-sm"
            >Beklet</button>
          </form>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>

<!-- Eğer randevu yoksa kullanıcıyı bilgilendir -->
@if($list->isEmpty())
  <p>Henüz randevu alınmamış.</p>
@endif

@endsection
