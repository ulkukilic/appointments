@extends('layouts.customer')
@section('title','Randevularım')
@section('page_title','Randevularım')
@section('content')
  @if($list->isEmpty())
    <p class="text-muted">Henüz hiç randevu almadınız.</p>
  @else
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Şirket</th>
          <th>Servis</th>
          <th>Personel</th>
          <th>Durum</th>
        </tr>
      </thead>
      <tbody>
        @foreach($list as $item)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->company_name }}</td>
            <td>{{ $item->service_name }}</td>
            <td>{{ $item->staff_name }}</td>
            <td>
              @if($item->status == 'pending')
                <button class="btn btn-sm btn-warning" disabled>Beklemede</button>
              @elseif($item->status == 'confirmed')
                <button class="btn btn-sm btn-success" disabled>Onaylandı</button>
              @elseif($item->status == 'cancelled')
                <button class="btn btn-sm btn-danger" disabled>İptal</button>
              @else
                <span>{{ ucfirst($item->status) }}</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
@endsection
