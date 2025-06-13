
@extends('layouts.admin')
@extends('layouts.admin')

@section('title', isset($staff) 
    ? 'Admin – Personel Düzenle: ' . $staff->full_name 
    : 'Admin – Yeni Personel Ekle'
)
@section('page_title', isset($staff) 
    ? 'Personel Düzenle: ' . $staff->full_name 
    : 'Yeni Personel Ekle'
)

@section('content')
  @include('layouts.alerts')

  <div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
      <h5 class="mb-0">
        {{ isset($staff) ? 'Personel Düzenle' : 'Yeni Personel Ekle' }}
      </h5>
    </div>
       <!-- Personel güncelleme veya ekleme formu -->
    <div class="card-body bg-white">
      <form 
        method="POST"
        action="{{ isset($staff) 
                   ? route('admin.staff.update', $staff->staff_member_uni_id) 
                   : route('admin.staff.add') 
                 }}">
        @csrf
        <!-- AD Soyad girisi -->
        <div class="mb-3">
          <label for="full_name" class="form-label">Ad Soyad</label>
          <input 
            type="text" 
            name="full_name" 
            id="full_name"
            class="form-control @error('full_name') is-invalid @enderror"
            value="{{ old('full_name', $staff->full_name ?? '') }}" 
            required
          >
          @error('full_name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        
        <!-- Deneyim seviyesi girişi -->
        <div class="mb-3">
          <label for="experience_level" class="form-label">Deneyim Seviyesi</label>
          <input 
            type="text" 
            name="experience_level" 
            id="experience_level"
            class="form-control @error('experience_level') is-invalid @enderror"
            value="{{ old('experience_level', $staff->experience_level ?? '') }}" 
            required
          >
          @error('experience_level')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
    </div> {{-- /.card-body --}}
    <div class="card-footer bg-white d-flex justify-content-between">
      <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">
        İptal
      </a>
      <button type="submit" class="btn btn-primary">
        {{ isset($staff) ? 'Güncelle' : 'Ekle' }}
      </button>
    </div>
      </form>
  </div>
@endsection
