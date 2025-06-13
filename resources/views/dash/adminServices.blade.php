@extends('layouts.admin')
@section('page_title','Servis Yönetimi')
@section('content')
  <a href="{{ route('admin.services.create') }}" class="btn btn-success mb-3">Yeni Servis Ekle</a>
  <table class="table table-striped">
    <thead><tr><th>#</th><th>Servis</th><th>Fiyat</th><th>Süre (dk)</th></tr></thead>
    <tbody>
      @foreach($services as $s)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $s->name }}</td>
          <td>{{ $s->price }} ₺</td>
          <td>{{ $s->duration }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection
