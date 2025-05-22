@extends('layouts.app') 
@section('title','Register')
@section('page_title','Please Register')
@section('content')

 @if ($errors->any())  <!-- en az bir hata varsa any() den dolayi direk $error degiskeni doner  -->
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error) <!-- iceride hata veren tum errorlari alir ve liste halinde yazdirir -->
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.submit') }}"> <!-- POST kullanilir cunku URL’de hassas veri gostermek istemeyiz -->
        @csrf  <!-- form-tabanli saldirilara karsi koruma -->

        <div class="form-group">
            <label for="name"> Name </label>
            <input   
                type="text"
                id="name"  
                name="name"   
                class="form-control" 
                value="{{ old('name') }}"
                required
            > <!-- {{old('name')}} önceki gonderide input degerini korur -->
        </div>

        <div class="form-group">
            <label for="surname">Last Name</label>
            <input
                type="text"
                id="surname"
                name="surname"
                class="form-control"
                value="{{ old('surname') }}"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                value="{{ old('email') }}"
                required
            >
        </div>

        <div class="form-group">
            <label for="password"> Password </label>
            <input
                type="password"
                name="password"
                id="password"
                class="form-control"
                required
            >
        </div>

        <div class="form-group">
            <label for="password_confirmation">Password Again </label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-control"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary">Register</button>

        <p class="mt-2">
            Already have an account?
            <a class="text-primary" href="{{ route('login.form') }}">Sign in</a>
        </p>
    </form>
@endsection