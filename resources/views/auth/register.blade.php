
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
     <link rel="stylesheet" href="{{ asset('css/app.css') }}">
<!--   css dosyasinin konumu belirtlendi HTML DE  "href=style.css" oldugu gibi-->
</head>
<body>
    <div class="container" >  <!--  kullanici kayit ol formunu gorucek -->
        <h1> Please Register </h1>
        @if ($errors->any())  <!-- en az bir hata varsa any() den dolayi direk $error degiskeni doner  -->
        <div class= " alert alert-danger">
            <ul>
                @foreach($errors->all() as $error) <!-- iceride hata veren tum errorlari alir ve liste halinde yazdirir -->
                <li>{{$error}} </li> 
                @endforeach
            </ul>
        </div> 
        @endif
        <form method="POST" action="{{route('register.submit')}}"> <!-- POST kullanilir cunku url ye mail adres vs ciksin istenmez  -->
@csrf  <!--Larabel , from tabanli saldirila karsi  korumak icin kullanilir  . sonucuya gelen POST istenildiginde burdan kontrol edilir gelen form bizdem mi gelmis kontrol edilir -->
<div class ="form-group">
    <label for="name"> Name </label>
    <input   
    type="text"
    id="name"  
    name="name"   
    class="form-control" 
    value="{{ old('name') }}"
     required >
    <!-- class-form-controller kismi css ekleme ve duzenlemede kolaylik olmlasi icin kullanilmis bir classdir 
     {{old('name')}} ise 
     name="name" $reqire->input('name')  yaparken cagrim yapilmasi icin isimlendirilmistir -->
</div>
    <div class="form-group">
        <label for="password"> Password </label>
        <input
         type="password"
         name="password"
         id="password"
         class="form-control"
         required>
    </div>
       
     <div class="form-group">
        <label for="password_confirmation">Password Again </label>
        <input
         type="password"
         id="password_confirmation"
         name="password_confirmation"
         class="form-control"
         required>
      </div>
      <button type="submit" class="btn btn-primary">Register</button>

      <div class="form-group">
      <p>Already have an account? <a class="text-primary" href="{{route('login.form')}}">Sign in</a></p>
      </div>
</div>
</body>
</html>