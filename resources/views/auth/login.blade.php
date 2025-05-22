@extends('layouts.app')

@section('title', 'Login')
@section('page_title', 'Please Login')

@section('content')
    <!-- eger basarili bir sekilde regoster yapilip donduryse kayit basarili mesajini alir -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- eger sayfanin acilisinda any() herhangi bir hata olursa hata mesaji dondurur -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $msg)
                    <li>{{ $msg }}</li> <!-- ve bu hata mesajini listeli bir sekilde yazdirir -->
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        <!-- Post ile sabitlencek degerleri body icinde gonderir, URL de login.submit gozukecek -->
        @csrf

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
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    {{-- Reset password kismi --}}
    <p class="mt-2">
        <a href="{{ route('password.request') }}">Forgot your password?</a>
    </p>

    <p class="form-group">
        Don’t have an account?
        <a href="{{ route('register.form') }}">Register here</a>
    </p> 
@endsection
