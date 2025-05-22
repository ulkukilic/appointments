{{-- resources/views/categories/show.blade.php --}}
@extends('layouts.app')

@section('title', ucwords(str_replace('-', ' ', $category)))
@section('page_title', ucwords(str_replace('-', ' ', $category)))

@section('content')
  <!-- Eğer $items dizisi boşsa bu mesaj gösterilecek -->
  @if (empty($items))
    <p>No items found in this category.</p>
  @else
    <div class="row g-4">
      @foreach($items as $item)
        @php
          use Illuminate\Support\Str; <!-- Str sınıfını kullanarak başlığı URL uyumlu hale getiriyoruz -->
          $slug    = Str::slug($item);
          $imgPath = "panel/assets/images/{$category}/{$slug}.jpg"; <!-- Resim dosyasının yolunu oluşturuyoruz -->
        @endphp

        <div class="col-6 col-md-4 col-lg-3">
          <div class="card h-100">
            <img
              src="{{ asset($imgPath) }}" <!-- asset() helper ile public klasöründen resmi çağırıyoruz -->
              class="card-img-top"        <!-- Bootstrap sınıfı; kartın üst kısmına resmi yerleştirir -->
              alt="{{ $item }}"          <!-- Alt metin olarak işletme adını kullanır -->
            >
            <div class="card-body text-center">
              <h5 class="card-title">{{ $item }}</h5> <!-- Kart başlığında işletme adını gösteriyoruz -->
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
@endsection
