@extends('layouts.app')

@section('content')
<div class="row">
  {{-- Sidebar --}}
  <nav class="col-2">
    <ul class="nav flex-column mb-4">
      <li class="nav-item">
        <a href="{{ route('dash.customer') }}" 
           class="nav-link {{ request()->routeIs('dash.customer') ? 'fw-bold' : '' }}">
          Şirketler
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('dash.customer.appointments') }}" 
           class="nav-link {{ request()->routeIs('dash.customer.appointments') ? 'fw-bold' : '' }}">
          Randevularım
        </a>
      </li>
    </ul>
  </nav>

  {{-- Main content --}}
  <div class="col-10">
    @if(request()->routeIs('dash.customer'))
      <div class="row g-4">
        @foreach($categories as $slug => [$label, $img])
          <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100">
              <a href="{{ route('categories.show', $slug) }}">
                <img src="{{ asset('panel/assets/images/' . $img) }}"
                     class="card-img-top" alt="{{ $label }}">
              </a>
              <div class="card-body text-center">
                <a href="{{ route('categories.show', $slug) }}"
                   class="stretched-link text-decoration-none text-dark">
                  <h5 class="card-title">{{ $label }}</h5>
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      {{-- if not on companies list, leave blank --}}
    @endif
  </div>
</div>
@endsection
