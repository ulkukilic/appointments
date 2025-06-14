@extends('layouts.app')      

@section('title', 'Forgot Password')
@section('page_title', 'Reset Password')

@section('content')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">Registered Email</label> 
            <input
                type="email"
                id="email"
                name="email"
                class="form-control"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary">Send</button>
    </form>

    @if(session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    @if ($errors->has('email'))
        <div class="alert alert-danger">{{ $errors->first('email') }}</div>
    @endif
@endsection
