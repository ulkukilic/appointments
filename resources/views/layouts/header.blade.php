<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="{{ route('login.form') }}">
    <img src="{{ asset('panel/assets/images/logo/logo-fold.png') }}"
         alt="Logo"
         style="height: 40px; width: auto;">
  </a>
  <div class="collapse navbar-collapse">
    <ul class="navbar-nav ml-auto">
      @auth
        <li class="nav-item"><a class="nav-link" href="{{ route('dash.customer') }}">Customer</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('dash.admin') }}">Admin</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('dash.superadmin') }}">Superadmin</a></li>
        <li class="nav-item">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-link nav-link">Logout</button>
          </form>
        </li>
      @else
        <li class="nav-item"><a class="nav-link" href="{{ route('login.form') }}">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('register.form') }}">Register</a></li>
      @endauth
    </ul>
  </div>
</nav>
