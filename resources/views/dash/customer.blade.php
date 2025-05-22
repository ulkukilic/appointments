{{-- resources/views/dash/customer.blade.php --}}
@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('page_title', 'Welcome, ' . session('full_name') . '!')

@section('content')
  
  <div class="row g-4">
    @php
    
      $categories = [
        'hospital'   => ['Hospital',     'hospital.jpg'],
        'dentist'    => ['Dentist',      'dentist.jpg'], 
        'pharmacy'     => ['Pharmacy',      'pharmacy.jpg'], 
        'vet'          => ['Veterinary',    'vet.jpg'],   
        'hair-salon' => ['Hair Salon',   'hair-salon.jpg'],
        'barber'     => ['Barber',       'berber.jpg'],   
        'restaurant'   => ['Restaurant',    'restaurant.jpg'], 
        'cafe'       => ['Cafe',         'cafe.jpg'],   
        'gym'          => ['Gym',           'gym.jpg'],            
                
           
      ];
    @endphp
    
    @foreach($categories as $slug => [$label, $img])
      <!-- Her bir kategori için sütun tanımı; farklı ekran genişliklerinde responsive -->
      <div class="col-6 col-md-4 col-lg-3">
        <div class="card h-100">
          <!-- Kart resmi, tıklanınca o kategorinin show sayfasına gider -->
          <a href="{{ route('categories.show', $slug) }}">
            <img
              src="{{ asset('panel/assets/images/' . $img) }}"  
              class="card-img-top"                               
              alt="{{ $label }}"                                
            >
            <!-- asset() helper ile public dizininden resmi çekiyoruz -->
             <!-- Bootstrap kart resmi sınıfı -->
              <!-- Görsel yüklenmezse alternatif olarak etiket gösterir -->
          </a>
          <div class="card-body text-center">
            <!-- Kart başlığı, tüm kart tıklanabilir yapmak için stretched-link kullanıldı -->
            <a
              href="{{ route('categories.show', $slug) }}"
              class="stretched-link text-decoration-none text-dark"
            >
              <h5 class="card-title">{{ $label }}</h5>  <!-- Her kategorinin ekranda görünen adı -->
            </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <!-- Diğer müşteri özel içerik için boşluk bırakıldı -->
  <div class="mt-5">
    <p>Here you can display other customer-specific information.</p>
  </div>
@endsection
