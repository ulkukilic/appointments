@extends('layouts.superadmin')

@section('page_title','Yeni Şirket Ekle')
@section('content')
  <form action="{{ route('superadmin.companies.store') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label>Şirket Adı</label>
      <input name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Şirket Sahibi (Ad Soyad veya E-posta)</label>
      <input name="owner_identifier" class="form-control" 
             placeholder="E-posta veya tam ad" required>
      <div class="form-text">
        Sistemde kayıtlı değilse, yeni kullanıcı oluşturulur.
      </div>
    </div>
    {{-- diğer alanlar --}}
    <button class="btn btn-primary">Kaydet</button>
    <a href="{{ route('superadmin.companies.index') }}" class="btn btn-secondary">İptal</a>
  </form>
@endsection
