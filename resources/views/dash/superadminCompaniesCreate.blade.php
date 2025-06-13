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
      <label>Kategori</label>
      <input name="category" class="form-control" required>
    </div>
    {{-- diğer alanlar --}}
    <button class="btn btn-primary">Kaydet</button>
    <a href="{{ route('superadmin.companies.index') }}" class="btn btn-secondary">İptal</a>
  </form>
@endsection
