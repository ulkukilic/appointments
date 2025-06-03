<!-- Ana şablon dosyasını genişletir -->
@extends('layouts.app')
<!-- Sayfa başlığını ayarlar -->
@section('title', 'Admin Dashboard')
<!-- Sayfa üstü başlık alanına hoşgeldin mesajı ekler -->
@section('page_title', 'Hoşgeldin Admin, ' . session('full_name') . '!')

<!-- İçerik bölümünü başlatır -->
@section('content')
  <!-- Bootstrap satırı ve boşluk sınıfını tanımlar -->
  <div class="row g-4">
    <!-- Kategori yönetimi kartı bölümü için başlatma div'i -->
    <div class="col-12 col-md-6 col-lg-4">
      <!-- Kart bileşeni kapsayıcı div'i -->
      <div class="card h-100">
        <!-- Kartın tıklanabilir kısmı: kategoriler sayfasına yönlendirir -->
        <a href="{{ route('admin.categories.index') }}">
          <!-- Kategori yönetimi resmi -->
          <img
            src="{{ asset('panel/assets/images/category-management.jpg') }}"
            class="card-img-top"
            alt="Kategori Yönetimi"
          >
        </a>
        <!-- Kart içeriği gövdesi, ortalanmış metin içerir -->
        <div class="card-body text-center">
          <!-- Kart başlığına tıklanabilir bağlantı eklenir -->
          <a
            href="{{ route('admin.categories.index') }}"
            class="stretched-link text-decoration-none text-dark"
          >
            <!-- Kart başlığı: Kategori Yönetimi -->
            <h5 class="card-title">Kategori Yönetimi</h5>
          </a>
        </div>
      </div>
    </div>

    <!-- Personel yönetimi kartı bölümü için başlatma div'i -->
    <div class="col-12 col-md-6 col-lg-4">
      <!-- Kart bileşeni kapsayıcı div'i -->
      <div class="card h-100">
        <!-- Kartın tıklanabilir kısmı: personel sayfasına yönlendirir -->
        <a href="{{ route('admin.staff.index') }}">
          <!-- Personel yönetimi resmi -->
          <img
            src="{{ asset('panel/assets/images/staff-management.jpg') }}"
            class="card-img-top"
            alt="Personel Yönetimi"
          >
        </a>
        <!-- Kart içeriği gövdesi, ortalanmış metin içerir -->
        <div class="card-body text-center">
          <!-- Kart başlığına tıklanabilir bağlantı eklenir -->
          <a
            href="{{ route('admin.staff.index') }}"
            class="stretched-link text-decoration-none text-dark"
          >
            <!-- Kart başlığı: Personel Yönetimi -->
            <h5 class="card-title">Personel Yönetimi</h5>
          </a>
        </div>
      </div>
    </div>

    <!-- Müsaitlik yönetimi kartı bölümü için başlatma div'i -->
    <div class="col-12 col-md-6 col-lg-4">
      <!-- Kart bileşeni kapsayıcı div'i -->
      <div class="card h-100">
        <!-- Kartın tıklanabilir kısmı: müsaitlik sayfasına yönlendirir -->
        <a href="{{ route('admin.availability.index') }}">
          <!-- Müsaitlik yönetimi resmi -->
          <img
            src="{{ asset('panel/assets/images/availability-management.jpg') }}"
            class="card-img-top"
            alt="Müsaitlik Yönetimi"
          >
        </a>
        <!-- Kart içeriği gövdesi, ortalanmış metin içerir -->
        <div class="card-body text-center">
          <!-- Kart başlığına tıklanabilir bağlantı eklenir -->
          <a
            href="{{ route('admin.availability.index') }}"
            class="stretched-link text-decoration-none text-dark"
          >
            <!-- Kart başlığı: Müsaitlik Yönetimi -->
            <h5 class="card-title">Müsaitlik Yönetimi</h5>
          </a>
        </div>
      </div>
    </div>

    <!-- Randevu yönetimi kartı bölümü için başlatma div'i -->
    <div class="col-12 col-md-6 col-lg-4">
      <!-- Kart bileşeni kapsayıcı div'i -->
      <div class="card h-100">
        <!-- Kartın tıklanabilir kısmı: randevular sayfasına yönlendirir -->
        <a href="{{ route('admin.appointments') }}">
          <!-- Randevu yönetimi resmi -->
          <img
            src="{{ asset('panel/assets/images/appointment-management.jpg') }}"
            class="card-img-top"
            alt="Randevu Yönetimi"
          >
        </a>
        <!-- Kart içeriği gövdesi, ortalanmış metin içerir -->
        <div class="card-body text-center">
          <!-- Kart başlığına tıklanabilir bağlantı eklenir -->
          <a
            href="{{ route('admin.appointments') }}"
            class="stretched-link text-decoration-none text-dark"
          >
            <!-- Kart başlığı: Randevu Yönetimi -->
            <h5 class="card-title">Randevu Yönetimi</h5>
          </a>
        </div>
      </div>
    </div>
  </div>
<!-- İçerik bölümünü kapatır -->
@endsection
