<!-- resources/views/dash/adminAppointments.blade.php -->
@extends('layouts.admin')

@section('title','Randevu Yönetimi')
@section('page_title','Randevu Yönetimi')

@section('content')
  @if($list->isEmpty())
    <p class="text-muted">Henüz bu şirkete ait randevu yok.</p>
  @else
    <table class="table table-striped">
      <thead><tr>
        <th>#</th><th>Müşteri</th><th>Email</th>
        <th>Hizmet</th><th>Personel</th><th>Tarih/Saat</th>
        <th>Durum</th><th>İşlem</th>
      </tr></thead>
      <tbody>
        @foreach($list as $item)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->customer_name }}</td>
            <td>{{ $item->email }}</td>
            <td>{{ $item->service_name }}</td>
            <td>{{ $item->staff_name }}</td>
            <td>{{ \Carbon\Carbon::parse($item->scheduled_time)->format('d M Y H:i') }}</td>
            <td>
              <span class="badge 
                {{ $item->status=='pending'   ? 'bg-warning text-dark' 
                : ($item->status=='confirmed' ? 'bg-success' 
                : ($item->status=='cancelled' ? 'bg-danger':'bg-secondary')) }}">
                {{ ucfirst($item->status) }}
              </span>
            </td>
            <td>
              <form method="POST" action="{{ route('admin.appointments.update',$item->appointment_id) }}" class="d-flex">
                @csrf
                <select name="status" class="form-select form-select-sm me-2">
                  <option value="pending"   {{ $item->status=='pending'? 'selected':'' }}>Beklemede</option>
                  <option value="confirmed" {{ $item->status=='confirmed'? 'selected':'' }}>Onaylandı</option>
                  <option value="cancelled" {{ $item->status=='cancelled'? 'selected':'' }}>İptal</option>
                </select>
                <button class="btn btn-sm btn-primary">Güncelle</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
@endsection
