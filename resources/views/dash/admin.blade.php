{{-- resources/views/dash/admin.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('page_title', 'Welcome Admin, ' . session('full_name') . '!')

@section('content')
  <!--  Kategori yönetim kartları grid -->
  <div class="row g-4">
    @php
      $categories = [
        'hospital'   => ['Hospital',     'hospital.jpg'],
        'hair-salon' => ['Hair Salon',   'hair-salon.jpg'],
        'barber'     => ['Barber',       'berber.jpg'],
        'dentist'    => ['Dentist',      'dentist.jpg'],
        'cafe'       => ['Cafe',         'cafe.jpg'],
        'gym'        => ['Gym',          'gym.jpg'],
        'pharmacy'   => ['Pharmacy',     'pharmacy.jpg'],
        'vet'        => ['Veterinary',   'vet.jpg'],
        'restaurant' => ['Restaurant',   'restaurant.jpg'],
      ];
    @endphp

    @foreach($categories as $slug => [$label, $img])
      <div class="col-6 col-md-4 col-lg-3">
        <div class="card h-100">
          <!-- Yönetici kategori kartı; tıklayınca kategori detay yönetim sayfasına gider -->
           <a href="{{ route('admin.categories.index') }}">
            <img
              src="{{ asset('panel/assets/images/' . $img) }}"
              class="card-img-top"
              alt="{{ $label }}"
            >
          </a>
          <div class="card-body text-center">
            <a
              href="{{ route ('admin.categories.index')  }}"
              class="stretched-link text-decoration-none text-dark"
            >
              <h5 class="card-title">{{ $label }}</h5>
            </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  <div class="mt-5">
    <h4>Site Management</h4>
    <ul class="list-group">
      <li class="list-group-item">
        <a href="{{ route('admin.appointments') }}" class="text-decoration-none">
          Manage All Appointments
        </a>
      </li>
         <li class="list-group-item">
      <a href="{{ route('admin.staff.index') }}" class="text-decoration-none">
        Manage Staff Members
      </a>
    </li>
      
    </ul>
  </div>
@endsection
