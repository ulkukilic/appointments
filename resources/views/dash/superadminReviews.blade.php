@extends('layouts.superadmin')

@section('title','SuperAdmin – Yorumlar')
@section('page_title','Tüm Yorumlar')

@section('content')
  @if($reviews->isEmpty())
    <div class="alert alert-info">Henüz hiç yorum yok.</div>
  @else
    <table class="table table-striped">
      <thead>
        <tr><th>#</th><th>Kullanıcı</th><th>Puan</th><th>Yorum</th><th>Tarih</th></tr>
      </thead>
      <tbody>
        @foreach($reviews as $r)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $r->user_name }}</td>
            <td>{{ $r->rating }}</td>
            <td>{{ $r->comment }}</td>
            <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
@endsection

