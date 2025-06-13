<!-- resources/views/dash/superadminCompanyEdit.blade.php -->
@extends('layouts.superadmin')

@section('title','Superadmin - Şirket Düzenle')
@section('page_title','Düzenle: ' . $company->name)

@section('content')
  @include('layouts.alerts')

  <div class="card">
    <div class="card-header">
      <h3>Şirket Bilgilerini Güncelle</h3>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('superadmin.company.update', $company->company_uni_id) }}">
        @csrf

        <div class="mb-3">
          <label for="name" class="form-label">Şirket Adı</label>
          <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $company->name) }}" required>
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label for="category" class="form-label">Kategori</label>
          <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $company->category) }}" required>
          @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <div class="form-text">(Örn: hospital, barber, restaurant, vb.)</div>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">E-posta</label>
          <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $company->email) }}" required>
          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label for="address" class="form-label">Adres</label>
          <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $company->address) }}">
          @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label for="phone_number" class="form-label">Telefon Numarası</label>
          <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $company->phone_number) }}">
          @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Açıklama</label>
          <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $company->description) }}</textarea>
          @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary">Kaydet</button>
        <a href="{{ route('dash.superadmin') }}" class="btn btn-secondary">İptal</a>
      </form>
    </div>
  </div>
@endsection
