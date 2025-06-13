@extends('layouts.admin')
@include('layouts.alerts')
@section('title','Yorum Yönetimi')
@section('page_title','Yorum Yönetimi')
@section('content')

<h4>Şirkete Ait Yorumlar</h4>

@if($reviews->isEmpty())
  <p class="text-muted">Henüz bu şirkete ait yorum bulunmuyor.</p>
@else
  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Müşteri</th>
        <th>Personel</th>
        <th>Puan</th>
        <th>Yorum</th>
        <th>Tarih</th>
        <th>İşlem</th>
      </tr>
    </thead>
    <tbody>
      @foreach($reviews as $rev)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $rev->customer_name }}</td>
          <td>{{ $rev->staff_name }}</td>
          <td>{{ $rev->rating }}/5</td>
          <td>{{ $rev->comment ?? '—' }}</td>
          <td>{{ \Carbon\Carbon::parse($rev->created_at)->format('d M Y H:i') }}</td>
          <td>
            <form method="POST"
                  action="{{ route('admin.reviews.delete', $rev->review_id) }}"
                  onsubmit="return confirm('Bu yorumu silmek istediğinizden emin misiniz?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger">Sil</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endif

@endsection
