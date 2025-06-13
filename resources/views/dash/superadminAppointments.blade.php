@extends('layouts.superadmin')

@section('title','SuperAdmin Randevular')
@section('page_title','Tüm Randevular')

@section('content')
  @if($list->isEmpty())
    <div class="alert alert-info">Henüz hiç randevu yok.</div>
  @else
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Şirket</th>
          <th>Müşteri</th>
          <th>Email</th>
          <th>Hizmet</th>
          <th>Personel</th>
          <th>Tarih/Saat</th>
          <th>Durum</th>
          <th>İşlem</th>
        </tr>
      </thead>
      <tbody>
        @foreach($list as $item)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->company_name }}</td>
            <td>{{ $item->customer_name }}</td>
            <td>{{ $item->email }}</td>
            <td>{{ $item->service_name }}</td>
            <td>{{ $item->staff_name }}</td>
            <td>{{ \Carbon\Carbon::parse($item->scheduled_time)->format('d M Y H:i') }}</td>
            <td class="text-capitalize">{{ $item->status }}</td>
            <td>
              <form method="POST" action="{{ route('superadmin.appointments.update', $item->appointment_id) }}">
                @csrf
                <button name="status" value="confirmed" class="btn btn-success btn-sm">Onayla</button>
                <button name="status" value="cancelled" class="btn btn-danger btn-sm">İptal</button>
                <button name="status" value="pending" class="btn btn-warning btn-sm">Beklemede</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
@endsection
