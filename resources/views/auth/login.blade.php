<!DOCTYPE html>
<html lang="en">
@include('layouts.header')

<body>
      <div class="container">
        <h1>Please Login</h1>
         @if(session('success')) <!--  eger basarili bir sekilde regoster yapilip donduryse kayit basarili mesajini alir-->
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

         @if ($errors->any()) <!--eger sayfanin acilisinda any() herhangi bir hata olursa hata mesaji dondurur  -->
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $msg)
                        <li>{{ $msg }}</li> <!-- ve bu hata mesajini listeli bir sekilde yazdirir -->
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('login.submit') }}">
         <!--Post ile sbasitirilcak degerleri  body icinde gonderir URL de login.submit gpuzkur-->
           @csrf

            <div class="form-group">  <!-- grup yapar ve duzenlemerli kolaylastirir-->
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email') }}"
                    required 
                >
                   <!-- old onceki gonderide input degerli alir ve kullanici girisi kaybolmaz -->
            
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
        </form> <!-- button ile gonderim yapilir--> 

          <p class="form-group">Don’t have an account?
        <a href="{{ route('register.form') }}">Register here</a>
        </p> <!--eger bir hesabi yoksa direk web.php den gelen register.form sayfasina  yonlendirme yapilmaktadir -->
      </div>
    </body>
</html>