
@extends('layouts.app')

@section('title', 'Superadmin - Şirket Düzenle')
@section('page_title', 'Düzenle: ' . $company->name)


@section('content')
  <!-- Uyarı mesajlarını gösteren kısım -->
  @include('layouts.alerts')

  <!-- Kart bileşeni kapsayıcı div'i başlatır -->
  <div class="card">
    <!-- Kart başlık kısmı -->
    <div class="card-header">
      <!-- Başlık: Şirket Bilgilerini Güncelle -->
      <h3>Şirket Bilgilerini Güncelle</h3>
    </div>
    <!-- Kart gövde kısmı -->
    <div class="card-body">
      <!-- Form başlatılır, POST metodu ve güncelleme rotası belirtilir -->
      <form 
        method="POST" 
        action="{{ route('superadmin.company.update', $company->company_uni_id) }}"
      >
        <!-- CSRF koruması için alan -->
        @csrf

        <!-- Şirket adı alanı kapsayıcı div -->
        <div class="mb-3">
          <!-- Etiket: Şirket Adı -->
          <label for="name" class="form-label">Şirket Adı</label>
          <!-- Metin girişi: Şirket adı, eskiden gelen veya mevcut değeri gösterir -->
          <input 
            type="text" 
            name="name" 
            id="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $company->name) }}" 
            required
          >
          <!-- Hata mesajı gösterimi: 'name' alanı için -->
          @error('name')
            <!-- Geçersiz giriş mesajı -->
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Kategori alanı kapsayıcı div -->
        <div class="mb-3">
          <!-- Etiket: Kategori -->
          <label for="category" class="form-label">Kategori</label>
          <!-- Metin girişi: Kategori, eskiden gelen veya mevcut değeri gösterir -->
          <input 
            type="text" 
            name="category" 
            id="category"
            class="form-control @error('category') is-invalid @enderror"
            value="{{ old('category', $company->category) }}" 
            required
          >
          <!-- Hata mesajı gösterimi: 'category' alanı için -->
          @error('category')
            <!-- Geçersiz giriş mesajı -->
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <!-- Açıklama metni: Kategori örnekleri -->
          <div class="form-text">
            (Örn: hospital, barber, restaurant, vb.)
          </div>
        </div>

        <!-- E-posta alanı kapsayıcı div -->
        <div class="mb-3">
          <!-- Etiket: E-posta -->
          <label for="email" class="form-label">E-posta</label>
          <!-- E-posta girişi: Eski veya mevcut e-posta değeri gösterir -->
          <input 
            type="email" 
            name="email" 
            id="email"
            class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $company->email) }}" 
            required
          >
          <!-- Hata mesajı gösterimi: 'email' alanı için -->
          @error('email')
            <!-- Geçersiz giriş mesajı -->
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Adres alanı kapsayıcı div -->
        <div class="mb-3">
          <!-- Etiket: Adres -->
          <label for="address" class="form-label">Adres</label>
          <!-- Metin girişi: Adres, eski veya mevcut değeri gösterir -->
          <input 
            type="text" 
            name="address" 
            id="address"
            class="form-control @error('address') is-invalid @enderror"
            value="{{ old('address', $company->address) }}"
          >
          <!-- Hata mesajı gösterimi: 'address' alanı için -->
          @error('address')
            <!-- Geçersiz giriş mesajı -->
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Telefon numarası alanı kapsayıcı div -->
        <div class="mb-3">
          <!-- Etiket: Telefon Numarası -->
          <label for="phone_number" class="form-label">Telefon Numarası</label>
          <!-- Metin girişi: Telefon numarası, eski veya mevcut değeri gösterir -->
          <input 
            type="text" 
            name="phone_number" 
            id="phone_number"
            class="form-control @error('phone_number') is-invalid @enderror"
            value="{{ old('phone_number', $company->phone_number) }}"
          >
          <!-- Hata mesajı gösterimi: 'phone_number' alanı için -->
          @error('phone_number')
            <!-- Geçersiz giriş mesajı -->
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Açıklama alanı kapsayıcı div -->
        <div class="mb-3">
          <!-- Etiket: Açıklama -->
          <label for="description" class="form-label">Açıklama</label>
          <!-- Çok satırlı metin girişi: Açıklama, eskiden gelen veya mevcut değeri gösterir -->
          <textarea 
            name="description" 
            id="description" 
            rows="4"
            class="form-control @error('description') is-invalid @enderror"
          >{{ old('description', $company->description) }}</textarea>
          <!-- Hata mesajı gösterimi: 'description' alanı için -->
          @error('description')
            <!-- Geçersiz giriş mesajı -->
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Kaydet butonu: Formu gönderir -->
        <button type="submit" class="btn btn-primary">Kaydet</button>
        <!-- İptal butonu: Superadmin paneline geri döner -->
        <a href="{{ route('dash.superadmin') }}" class="btn btn-secondary">İptal</a>
      </form>
    </div>
  </div>

@endsection
