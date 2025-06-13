<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'SuperAdmin Paneli')</title>
  <!-- Bootstrap CSS -->
  <link href="{{ asset('panel/assets/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    /* sidebar + main layout */
    .sidebar {
      width: 220px;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      overflow-y: auto;
      background: #fff;
      border-right: 1px solid #dee2e6;
      padding-bottom: 3rem;
    }
    .content {
      margin-left: 220px;
      padding: 1rem 2rem;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    @include('layouts.sidebar-superadmin')
  </div>

  <div class="content">
    <div class="container-fluid">
      <h1 class="h3 mb-4">@yield('page_title')</h1>
      @include('layouts.alerts')
      @yield('content')
    </div>
  </div>

  <!-- Bootstrap Bundle JS (optional, e.g. dropdowns) -->
  <script src="{{ asset('panel/assets/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
