{{-- resources/views/layouts/superadmin.blade.php --}}
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','SuperAdmin Paneli')</title>

  <link href="{{ asset('panel/assets/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('panel/assets/css/app.css') }}" rel="stylesheet">

  <style>
    /* Sabit header yüksekliği */
    .header {
      height: 70px;
      position: fixed;
      top: 0; left: 0;
      right: 0;
      background: #fff;
      border-bottom: 1px solid #edf2f9;
      z-index: 1030;
    }

    /* Layout: sidebar + content */
    .page-layout {
      display: flex;
      margin-top: 70px; /* header kadar boşluk */
    }

    /* Sidebar */
    .sidebar {
      width: 220px;
      background: #fff;
      border-right: 1px solid #ddd;
      position: fixed;
      top: 70px;      /* header’ın hemen altı */
      bottom: 0;
      overflow-y: auto;
      z-index: 1000;
    }

    /* İçerik wrapper */
    .content-wrapper {
      margin-left: 220px; /* sidebar genişliği */
      padding: 1rem;
      flex: 1;
    }
  </style>
</head>
<body>
  {{-- 1. Sabit header --}}
  @include('layouts.header')

  <div class="page-layout">
    {{-- 2. Sidebar --}}
    <aside class="sidebar">
      @include('layouts.sidebar-superadmin')
    </aside>

    {{-- 3. Ana içerik --}}
    <main class="content-wrapper">
      <div class="container-inner">
        <h1 class="page-title">@yield('page_title')</h1>
        @include('layouts.alerts')
        @yield('content')
      </div>
    </main>
  </div>

  <script src="{{ asset('panel/assets/vendors/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('panel/assets/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded',()=>{
  /* artık .side-nav içinde arıyoruz */
  document.querySelectorAll('.side-nav .dropdown-toggle').forEach(btn=>{
     btn.addEventListener('click',e=>{
         e.preventDefault();           // link atlamasın
         btn.closest('.nav-item.dropdown').classList.toggle('open');
     });
  });
});
</script>


  @stack('scripts')
</body>
</html>