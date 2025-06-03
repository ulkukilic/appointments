{{-- Admin – Personel Ekleme / Düzenleme Formu --}}
@extends('layouts.app')

@section('title', 'Admin - Personel Düzenleme')
@section('page_title', isset($staff) 
    ? 'Personel Düzenle: ' . $staff->full_name 
    : 'Yeni Personel Ekle'
)

@section('content')
  @include('layouts.alerts')

  <form 
    method="POST"
    action="{{ isset($staff) 
               ? route('admin.staff.update', $staff->staff_member_uni_id) 
               : route('admin.staff.add') 
             }}"
  >
    @csrf

    {{-- Düzenleme modunda PUT methodu gerekiyor --}}
    @if(isset($staff))
      @method('POST') {{-- Model kullanmıyoruz, direkt POST->update metodu --}}
    @endif

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

    <button type="submit" class="btn btn-primary">
      {{ isset($staff) ? 'Güncelle' : 'Ekle' }}
    </button>
    <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">İptal</a>
  </form>
@endsection
