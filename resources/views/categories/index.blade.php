@extends('layouts.app') {{-- müşteri tarafı, sidebar yok --}}

@section('content')
  <h2>{{ ucwords(str_replace('-',' ',$category)) }}</h2>
  @if($companies->isEmpty())
    <p class="text-muted">Bu kategoriye ait şirket yok.</p>
  @else
    <div class="row g-4">
      @foreach($companies as $company)
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card">
            <a href="{{ route('categories.company.availability',[$category,$company->company_uni_id]) }}">
              <img src="{{ asset('panel/assets/images/'.$category.'/'.Str::slug($company->name).'.jpg') }}"
                   class="card-img-top" alt="">
            </a>
            <div class="card-body text-center">
              <a href="{{ route('categories.company.availability',[$category,$company->company_uni_id]) }}"
                 class="text-decoration-none stretched-link text-dark">
                <h5>{{ $company->name }}</h5>
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
@endsection
