@extends('layouts.app')

@section('title', 'Superadmin Dashboard')

@section('page_title', 'Welcome Superadmin, ' . session('full_name') . '!')

@section('content')

  <!-- Bilgilendirme mesajı: Superadmin arayüzünün amacı hakkında bilgi verir -->
  <div class="alert alert-info">
    From here, you can manage all companies, users, and appointments.
  </div>

  <!-- Şirket Yönetimi Başlığı: Şirketlerle ilgili işlemleri görüntüler -->
  <h3>Company Management</h3>
  @php
    $companies = DB::table('companies')->get();
  @endphp

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Name</th>
        <th>categories</th>
        <th>Email</th>
        <th>Operations</th>
      </tr>
    </thead>
    <tbody>
      @foreach($companies as $c)
        <tr>
          <td>{{ $c->name }}</td>
          <td>{{ $c->category }}</td>
          <td>{{ $c->email }}</td>
          <td>
            <!-- Düzenle butonu: Şirket bilgilerini güncelleme sayfasına yönlendirir -->
            <a href="{{ route('admin.companies.edit', $c->company_uni_id) }}" class="btn btn-sm btn-warning">Düzenle</a>
            <!-- Silme formu: Şirket kaydını silme işlemi yapar -->
            <form method="POST" action="{{ route('superadmin.company.delete', $c->company_uni_id) }}" style="display:inline-block">
              @csrf
              @method('DELETE')
              <button onclick="return confirm('Şirket silinsin mi?')" class="btn btn-sm btn-danger">Sil</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <!-- Kullanıcı Yönetimi Başlığı: Sistemdeki kullanıcıları listeler ve silme işlemi sağlar -->
  <h3 class="mt-5">User Management</h3>
  @php
    $users = DB::table('users')
               ->join('user_types', 'users.user_type_id', '=', 'user_types.user_type_id')
               ->select('users.*', 'user_types.user_type_name')
               ->get();
  @endphp

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Ad Soyad</th>
        <th>Email</th>
        <th>Rol</th>
        <th>İşlemler</th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $u)
        <tr>
          <td>{{ $u->full_name }}</td>
          <td>{{ $u->email }}</td>
          <td>{{ $u->user_type_name }}</td>
          <td>
            <!-- Silme formu: Kullanıcıyı sistemden kaldırır -->
            <form method="POST" action="{{ route('superadmin.user.delete', $u->user_uni_id) }}">
              @csrf
              @method('DELETE')
              <button onclick="return confirm('Kullanıcı silinsin mi?')" class="btn btn-sm btn-danger">Sil</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <!-- Randevu Yönetimi Başlığı: Son oluşturulan randevuları listeler ve durum güncelleme imkanı sunar -->
  <h3 class="mt-5">Appointment Management</h3>
  @php
    $appointments = DB::table('appointments')
      ->join('users', 'appointments.user_uni_id', '=', 'users.user_uni_id')
      ->join('companies', 'appointments.company_uni_id', '=', 'companies.company_uni_id')
      ->join('services', 'appointments.service_id', '=', 'services.service_id')
      ->select('appointments.*', 'users.full_name as customer_name', 'companies.name as company_name', 'services.name as service_name')
      ->orderBy('appointments.created_at', 'desc')
      ->limit(20)
      ->get();
  @endphp

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Müşteri</th>
        <th>Şirket</th>
        <th>Hizmet</th>
        <th>Tarih</th>
        <th>Durum</th>
        <th>İşlem</th>
      </tr>
    </thead>
    <tbody>
      @foreach($appointments as $a)
        <tr>
          <td>{{ $a->customer_name }}</td>
          <td>{{ $a->company_name }}</td>
          <td>{{ $a->service_name }}</td>
          <td>{{ \Carbon\Carbon::parse($a->scheduled_time)->format('d.m.Y H:i') }}</td>
          <td>{{ ucfirst($a->status) }}</td>
          <td>
            <!-- Durum güncelleme formu: Açılır listeden yeni durumu seçip gönderir -->
            <form method="POST" action="{{ route('superadmin.appointment.update', $a->appointment_id) }}">
              @csrf
              @method('PUT')
              <select name="status" class="form-select form-select-sm d-inline w-auto">
                <option value="pending" {{ $a->status === 'pending' ? 'selected' : '' }}>Beklemede</option>
                <option value="confirmed" {{ $a->status === 'confirmed' ? 'selected' : '' }}>Onaylandı</option>
                <option value="cancelled" {{ $a->status === 'cancelled' ? 'selected' : '' }}>İptal</option>
              </select>
              <button type="submit" class="btn btn-sm btn-primary">Güncelle</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

@endsection
