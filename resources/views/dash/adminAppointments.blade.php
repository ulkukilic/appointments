@extends('layouts.app')

@section('title', 'Admin - Randevu Yönetimi')
@section('page_title', 'Randevu Listesi')

@section('content')
@include('layouts.alerts')
 <table class="table table-striped">
  <thead>
    <tr>
      <th>Customer Name</th>
      <th>Email</th>
      <th>Service</th>
      <th>Appointment Time</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
      @foreach($list as $item)
        <tr>
          <!-- Sıra numarası ile bu satırın kaçıncı döngü olduğunu alır, tablo içinde satır numarası olarak gösterir -->
          <td>{{ $loop->iteration }}</td>

          <!-- Kullanıcı bilgileri  $item den full_name ve emil bilgilerini alir ve tabloya ekelr-->
          <td>{{ $item->full_name }}</td>
          <td>{{ $item->email }}</td>

          <!-- Hizmet bilgisi hangi servis uygulanicak vs gibi-->
          <td>{{ optional(DB::table('services')->where('service_id', $item->service_id)->first())->name }}</td>

          <!-- Tarih saat gösterimi -->
          <td>{{ \Carbon\Carbon::parse($item->scheduled_time)->format('d M Y H:i') }}</td>

          <!-- Durum: pending/confirmed/cancelled -->
          <td class="text-capitalize">{{ $item->status }}</td>
             <td>
              <form method="POST" action="{{ route('admin.appointments.update', $item->appointment_id) }}">
                  @csrf

                   <!-- Gizli alan: Email adresi backend’de e-posta bildiriminde kullanılmak üzere form verisine eklenir -->
                <input type="hidden" name="email" value="{{ $item->email }}">
                         <!-- Onayla -->
              <button
                name="status"
                value="confirmed"
                type="submit"
                class="btn btn-success btn-sm"
              >Confirm</button>

              <!-- Reddet -->
              <button
                name="status"
                value="cancelled"
                type="submit"
                class="btn btn-danger btn-sm"
              >Cancel</button>

              <!-- Beklet -->
              <button
                name="status"
                value="pending"
                type="submit"
                class="btn btn-warning btn-sm"
              >Pending</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
<!-- henuz randevu bulamamissa ve bos donuyorsa bu bilgigi gec  -->
  @if($list->isEmpty())
    <p>   No appointments found yet.</p> 
  @endif
@endsection