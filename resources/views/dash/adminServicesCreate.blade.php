@extends('layouts.admin')
@section('page_title','Yeni Servis Ekle')
@section('content')
  <form action="{{ route('admin.services.store') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label>Servis Adı</label>
      <input name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Fiyat (₺)</label>
      <input name="price" type="number" step="0.01" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Süre (dakika)</label>
      <input name="duration" type="number" class="form-control" required>
    </div>
    <button class="btn btn-primary">Kaydet</button>
    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">İptal</a>
  </form>
@endsection
