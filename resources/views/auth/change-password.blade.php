@extends('layouts.app')         

@section('title', 'Change Password')
@section('page_title', 'Reset Your Password')

@section('content')
    <form method="POST" action="{{ route('password.reset') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email">E-posta</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Yeni Şifre</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                required
            >
        </div>

        <div class="form-group">
            <label for="password_confirmation">Şifre (Tekrar)</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-control"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary">Şifreyi Güncelle</button>
    </form>
@endsection
