{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app') <!-- Extends the main layout: resources/views/layouts/app.blade.php -->

@section('title', 'Login') <!-- Sets the page title -->

@section('content')
<div class="container-fluid">
    <!-- Full-height flex container for centering -->
    <div class="d-flex full-height p-v-20 flex-column justify-content-between">

        {{-- Sol üst logo --}}
        <div class="d-none d-md-flex p-h-40">
            <!-- Main logo: public/panel/assets/images/logo/logo.png -->
            <img src="{{ asset('panel/assets/images/logo/logo.png') }}" alt="logo">
        </div>

        {{-- Orta bölüm --}}
        <div class="container">
            <div class="row align-items-center">

                {{-- FORM KARTI --}}
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">

                            <!-- Heading and subheading -->
                            <h2 class="m-t-20">Sign In</h2>
                            <p class="m-b-30">Enter your credential to get access</p>

                            <!-- Alerts include: resources/views/layouts/alerts.blade.php -->
                            @include('layouts.alerts')

                            <!-- Login form -->
                            <form method="POST" action="{{ route('login.submit') }}">
                                @csrf <!-- CSRF protection -->

                                <!-- Email input -->
                                <div class="form-group">
                                    <label class="font-weight-semibold" for="email">Email:</label>
                                    <div class="input-affix">
                                        <i class="prefix-icon anticon anticon-mail"></i>
                                        <input type="email"
                                               name="email"
                                               id="email"
                                               value="{{ old('email') }}"
                                               class="form-control"
                                               placeholder="Email"
                                               required>
                                    </div>
                                </div>

                                <!-- Password input -->
                                <div class="form-group">
                                    <label class="font-weight-semibold" for="password">Password:</label>
                                    <a class="float-right font-size-13 text-muted" href="{{ route('password.request') }}">
                                        Forgot Password?
                                    </a>
                                    <div class="input-affix m-b-10">
                                        <i class="prefix-icon anticon anticon-lock"></i>
                                        <input type="password"
                                               name="password"
                                               id="password"
                                               class="form-control"
                                               placeholder="Password"
                                               required>
                                    </div>
                                </div>

                                <!-- Signup link & submit button -->
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="font-size-13 text-muted">
                                            Don’t have an account?
                                            <a class="small" href="{{ route('register.form') }}">Signup</a>
                                        </span>
                                        <button class="btn btn-primary">Sign In</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                {{-- SAĞ RESİM --}}
                <div class="offset-md-1 col-md-6 d-none d-md-block">
                   
                    <img class="img-fluid" src="{{ asset('panel/assets/images/logo/logo-fold.png') }}" alt="Login illustration">
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="d-none d-md-flex p-h-40 justify-content-between">
            <!-- Footer comes from layouts: resources/views/layouts/footer.blade.php -->
            <span>© 2019 ThemeNate</span>
            <ul class="list-inline mb-0">
                <li class="list-inline-item"><a class="text-dark text-link" href="">Legal</a></li>
                <li class="list-inline-item"><a class="text-dark text-link" href="">Privacy</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection
