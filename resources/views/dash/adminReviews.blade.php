
@extends('layouts.admin')
@include('layouts.alerts')
@section('title','Yorum Yönetimi')
@section('page_title','Yorum Yönetimi')
@section('content')
<!-- Şirkete yapılan tüm yorumlar başlığı -->
<h4>Şirkete Ait Yorumlar</h4>

<!-- Eğer yorum yoksa kullanıcıya bilgi mesajı göster -->
@if($reviews->isEmpty())
  <p class="text-muted">Henüz bu şirkete ait yorum bulunmuyor.</p>
@else
  <!-- Yorumlar varsa tablo halinde göster -->
  <table class="table table-bordered table-hover">
    <thead class="table-light">
       <tr class="bg-white text-dark">
        <th>#</th>                  <!-- Yorumun sıra numarası -->
        <th>Müşteri Adı</th>        <!-- Yorumu yapan müşteri adı -->
        <th>Puan</th>               <!-- Verilen puan (1–5) -->
        <th>Yorum</th>              <!-- Yorumun kendisi (yoksa çizgi) -->
        <th>Tarih</th>              <!-- Yorum tarihi -->
      </tr>
    </thead>
    <tbody>
      <!-- Her bir yorum için tablo satırı oluştur -->
      @foreach($reviews as $rev)
        <tr class="bg-white text-dark">
          <td>{{ $loop->iteration }}</td>
          <td>{{ $rev->customer_name }}</td>
          <td>{{ $rev->rating }}/5</td>
          <td>{{ $rev->comment ?? '—' }}</td>
          <td>{{ \Carbon\Carbon::parse($rev->created_at)->format('d M Y H:i') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endif
@endsection