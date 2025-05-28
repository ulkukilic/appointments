@extends('layouts.app')

@section('title', 'Edit Company')

@section('page_title', 'Düzenle: ' . $company->name)

@section('content')
  <div class="card">
    <div class="card-header">
      <h3>Şirket Bilgilerini Düzenle</h3>
    </div>
    <div class="card-body">
      <form action="{{ route('superadmin.company.update', $company->company_uni_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label for="name" class="form-label">Şirket Adı</label>
          <input type="text" name="name" id="name"
                 class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name', $company->name) }}" required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="address" class="form-label">Adres</label>
          <input type="text" name="address" id="address"
                 class="form-control @error('address') is-invalid @enderror"
                 value="{{ old('address', $company->address) }}">
          @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="phone_number" class="form-label">Telefon Numarası</label>
          <input type="text" name="phone_number" id="phone_number"
                 class="form-control @error('phone_number') is-invalid @enderror"
                 value="{{ old('phone_number', $company->phone_number) }}">
          @error('phone_number')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Açıklama</label>
          <textarea name="description" id="description" rows="4"
                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $company->description) }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="btn btn-primary">Kaydet</button>
        <a href="{{ route('dash.superadmin') }}" class="btn btn-secondary">İptal</a>
      </form>
    </div>
  </div>
@endsection
