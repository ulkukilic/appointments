{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app') <!-- Extends the main layout: resources/views/layouts/app.blade.php -->

@section('title', 'Register')

@section('content')

  

    <div class="container-fluid">
        <!-- Full-height flex container for centering -->
        <div class="d-flex full-height p-v-20 flex-column justify-content-between">
<!-- Sol ust tarafta bulunan logo -->
   <div class="d-none d-md-flex p-h-40">
     <img src="{{ asset('panel/assets/images/logo/logo.png') }}" alt="logo">
    </div> 

            {{-- Orta bölüm --}}
            <div class="container">
                <div class="row align-items-center">

                    {{-- SOL RESİM --}}
                    <div class="col-md-6 d-none d-md-block">
                        <img class="img-fluid" src="{{ asset('panel/assets/images/logo/logo.png') }}" alt="Signup illustration">
                    </div>

                    {{-- FORM KARTI --}}
                    <div class="m-l-auto col-md-5">
                        <div class="card">
                            <div class="card-body">

                                <!-- Heading and subheading -->
                                <h2 class="m-t-20">Sign In</h2>
                                <p class="m-b-30">Enter your credential to get access</p>

                                <!-- Alerts include: resources/views/layouts/alerts.blade.php -->
                                @include('layouts.alerts')

                                <!-- Registration form -->
                                <form method="POST" action="{{ route('register.submit') }}">
                                    @csrf <!-- CSRF protection -->

                                    <!-- Name input -->
                                    <div class="form-group">
                                        <label class="font-weight-semibold" for="name">Name:</label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" placeholder="Name" required>
                                    </div>

                                    <!-- Surname input -->
                                    <div class="form-group">
                                        <label class="font-weight-semibold" for="surname">Last Name:</label>
                                        <input type="text" name="surname" id="surname" value="{{ old('surname') }}" class="form-control" placeholder="Surname" required>
                                    </div>

                                    <!-- Email input -->
                                    <div class="form-group">
                                        <label class="font-weight-semibold" for="email">Email:</label>
                                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" placeholder="Email" required>
                                    </div>

                                    <!-- Password input -->
                                    <div class="form-group">
                                        <label class="font-weight-semibold" for="password">Password:</label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                    </div>

                                    <!-- Confirm password input -->
                                    <div class="form-group">
                                        <label class="font-weight-semibold" for="password_confirmation">Confirm Password:</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                                    </div>

                                    <!-- Agreement checkbox & submit -->
                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between p-t-15">
                                            <div class="checkbox">
                                                <input id="agreement" type="checkbox" required>
                                                <label for="agreement">
                                                    <span>I have read the <a href="">agreement</a></span>
                                                </label>
                                            </div>
                                            <button class="btn btn-primary">Sign In</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    @include('layouts.footer')

@endsection