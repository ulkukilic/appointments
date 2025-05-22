{{-- debugging: --}}
@if (false)
    {{ dump('here') }}
@endif
@php
    // Hata öncesi çalıştı mı?
    file_put_contents(storage_path('logs/debug.txt'), 'app.blade loaded', FILE_APPEND);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', config('app.name'))</title>


  <link href="{{ asset('panel/assets/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('panel/assets/css/app.css?v=0.0.1') }}" rel="stylesheet">
  <link href="{{ asset('panel/assets/css/style.css?v=0.0.3') }}" rel="stylesheet">
</head>
<body>
   <!--header'i dahil etme islemi goruyor -->
  @include('layouts.header')

   <!--olasi bir hatayi gosteriyor -->
  @include('layouts.alerts')

  
  <main class="container mt-4">
  <!-- sayfanin basligi-->
    <h1>@yield('page_title')</h1>

     <!--sayfanin icerigini gosteriyor -->
    @yield('content')
  </main>

 <!--footer'i dahil ediyor -->
  @include('layouts.footer')

 <!-- javascript dosyalari-->
  <script src="{{ asset('panel/assets/vendors/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('panel/assets/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('panel/assets/js/app.js?v=0.0.1') }}"></script>
  @stack('scripts')
</body>
</html>
