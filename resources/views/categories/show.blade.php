@extends('layouts.app')

@section('content')
  <!-- Başlık: Kategoriyi okunabilir hale çevirip gösteriyoruz -->
  <h2>{{ ucwords(str_replace('-', ' ', $category)) }}</h2>

  <!-- Eğer bu kategoriye ait hiç şirket yoksa bilgi mesajı göster -->
  @if ($companies->isEmpty())
    <p>Bu kategoride şirket bulunamadı.</p>
  @else
    <!-- Şirket kartları için responsive grid -->
    <div class="row g-4">
      @foreach($companies as $company)
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card h-100">
            <!-- Kart tıklanınca uygun availability sayfasına yönlendir -->
            <a href="{{ route('categories.company.availability', ['category' => $category, 'company' => $company->company_uni_id]) }}">
              <img
                src="{{ asset('panel/assets/images/' . $category . '/' . Str::slug($company->name) . '.jpg') }}"
                class="card-img-top"
                alt="{{ $company->name }}"
              >
            </a>

            <!-- Kart başlığı: şirket adı -->
            <div class="card-body text-center">
              <a
                href="{{ route('categories.company.availability', ['category' => $category, 'company' => $company->company_uni_id]) }}"
                class="stretched-link text-decoration-none text-dark"
              >
                <h5 class="card-title">{{ $company->name }}</h5>
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
@endsection
