<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','Admin Paneli')</title>

  <link href="{{ asset('panel/assets/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('panel/assets/css/app.css') }}" rel="stylesheet">

  <style>
   /* --- SAYFA DÜZENİ: GRID İLE --- */
.page-layout {
  display: grid;
  grid-template-columns: 220px 1fr; /* sidebar 220px, içerik geri kalan */
}
.sidebar {
  position: fixed;
  top: 0; left: 0;
  width: 220px;
  height: 100vh;
  background: #fff;
  border-right: 1px solid #ddd;
  z-index: 1000;
}
.content-wrapper {
  /* grid’te 2. kolonda yer alır; zaten otomatik 220px’in sağına konulur */
  padding: 1rem 0;
  margin-left: 220px;  /* sabit bir “düşme” oluşturmuyoruz, ama grid sayesinde yine 220px kaydırma uygulanacak */
}
.container-inner {
  max-width: 1200px;   /* Enlink ile aynı maksimum genişlik */
  margin: 0 auto;      /* ortala */
  padding: 0 1rem;     /* içerik kenar boşlukları */
}
.page-title {
  margin: 1rem 0;
}

  </style>
</head>
<body>

  {{-- 1. Sabit sidebar --}}
  <aside class="sidebar">
    @include('layouts.sidebar-admin')
  </aside>

  {{-- 2. İçerik --}}
  <div class="content-wrapper">
    <h1 class="page-title">@yield('page_title')</h1>
    @include('layouts.alerts')
    @yield('content')
  </div>

  <script src="{{ asset('panel/assets/vendors/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('panel/assets/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
 <script>
    // Sidebar içindeki collapsible toggle
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.sidebar .dropdown-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() {
          var li = btn.closest('.nav-item.dropdown');
          li.classList.toggle('open');
        });
      });
    });
  </script>
  @stack('scripts')
</body>
</html>
