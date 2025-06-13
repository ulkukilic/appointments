@extends('layouts.customer')
@section('title','Şirketler')
@section('page_title','Kategori Seçin')
@section('content')
  <div class="row g-4">
    @foreach($categories as $category)
      <div class="col-6 col-md-4 col-lg-3">
        <div class="card">
          <a href="{{ route('categories.show', $category) }}">
            <img src="{{ asset('panel/assets/images/'.$category.'.jpg') }}"
                 class="card-img-top" alt="{{ $category }}">
          </a>
          <div class="card-body text-center">
            <a href="{{ route('categories.show', $category) }}"
               class="text-decoration-none stretched-link text-dark">
              <h5>{{ ucfirst($category) }}</h5>
            </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection
