<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','Müşteri Paneli')</title>
  <link href="{{ asset('panel/assets/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
  <style>
    .page-layout { display: grid; grid-template-columns: 200px 1fr; }
    .sidebar  { background:#fff; border-right:1px solid #ddd; height:100vh; position:fixed; width:200px; }
    .content  { margin-left:200px; padding:1rem; }
    .sidebar .nav-link.active { font-weight:bold; }
  </style>
</head>
<body>
  <div class="page-layout">
    <nav class="sidebar p-3">
      <h5>Müşteri Paneli</h5>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a href="{{ route('dash.customer') }}"
             class="nav-link {{ request()->routeIs('dash.customer') ? 'active' : '' }}">
            Şirketler
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('dash.customer.appointments') }}"
             class="nav-link {{ request()->routeIs('dash.customer.appointments') ? 'active' : '' }}">
            Randevularım
          </a>
        </li>
        <li class="nav-item mt-3">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-danger w-100">Çıkış Yap</button>
          </form>
        </li>
      </ul>
    </nav>

    <main class="content">
      <h1>@yield('page_title')</h1>
      @include('layouts.alerts')
      @yield('content')
    </main>
  </div>
  <script src="{{ asset('panel/assets/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
