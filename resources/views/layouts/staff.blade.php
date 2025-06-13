<!-- resources/views/layouts/staff.blade.php -->
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>@yield('title','Personel Paneli')</title>
  <link href="{{ asset('panel/assets/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
 <style>
      body {
        margin: 0;
        padding: 0;
        background: #f8f9fa;
      }
      .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 220px;
        height: 100vh;
        background: #ffffff;
        border-right: 1px solid #ddd;
        z-index: 1000; 
      }
      main {
        margin-left: 220px;    
        padding: 1rem;
      }
      .nav-link.active {
        font-weight: bold;
      }
      </style>  
</head>
<body>
  @include('layouts.sidebar-staff')
  <main>
    <h1>@yield('page_title')</h1>
    @include('layouts.alerts')
    @yield('content')
  </main>
</body>
</html>
