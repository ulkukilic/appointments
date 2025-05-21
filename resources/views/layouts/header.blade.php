<!-- resources/views/layouts/header.blade.php -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login Page')</title>

    
    {{-- 1️⃣ Bootstrap grid + temel stiller --}}
     <link href="{{ asset('panel/assets/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet"> 
    
    {{-- 2️⃣ Panel’in kendi app.css’i (theme’in genel stilleri) --}}
   <link href="{{ asset('panel/assets/css/app.css?v=0.0.1') }}" rel="stylesheet"> 
    
    {{-- 3️⃣ Senin custom overrides (logo, header vb.) --}}
<link href="{{ asset('panel/assets/css/style.css?v=0.0.3') }}" rel="stylesheet"> 
</head>
