<!-- resources/views/layouts/sidebar-superadmin.blade.php -->
<nav class="side-nav bg-white vh-100 overflow-auto border-end">
  <div class="side-nav">
    <div class="side-nav-inner">
      <ul class="side-nav-menu scrollable">

        {{-- Genel Bakış --}}
        <li class="nav-item">
          <a href="{{ route('dash.superadmin') }}"
             class="nav-link {{ request()->routeIs('dash.superadmin') ? 'active' : '' }}">
            <span class="icon-holder"><i class="bi bi-speedometer2"></i></span>
            <span class="title">Genel Bakış</span>
          </a>
        </li>

        {{-- Randevular --}}
        <li class="nav-item dropdown {{ request()->is('dash/superadmin/appointments*') ? 'open' : '' }}">
          <a href="javascript:void(0);" class="dropdown-toggle nav-link">
            <span class="icon-holder"><i class="bi bi-calendar-check"></i></span>
            <span class="title">Randevular</span>
            <span class="arrow"><i class="arrow-icon"></i></span>
          </a>
          <ul class="dropdown-menu">
            <li>
              <a href="{{ route('superadmin.appointments') }}"
                 class="{{ request()->routeIs('superadmin.appointments') ? 'active' : '' }}">
                Tüm Randevular
              </a>
            </li>
          </ul>
        </li>

        {{-- Kullanıcılar --}}
        <li class="nav-item dropdown {{ request()->is('dash/superadmin/users*') ? 'open' : '' }}">
          <a href="javascript:void(0);" class="dropdown-toggle nav-link">
            <span class="icon-holder"><i class="bi bi-people"></i></span>
            <span class="title">Kullanıcılar</span>
            <span class="arrow"><i class="arrow-icon"></i></span>
          </a>
          <ul class="dropdown-menu">
            <li>
              <a href="{{ route('superadmin.users.admins') }}"
                 class="{{ request()->routeIs('superadmin.users.admins') ? 'active' : '' }}">
                Admin Kullanıcıları
              </a>
            </li>
            <li>
              <a href="{{ route('superadmin.users.customers') }}"
                 class="{{ request()->routeIs('superadmin.users.customers') ? 'active' : '' }}">
                Müşteri Kullanıcıları
              </a>
            </li>
            <li>
              <a href="{{ route('superadmin.users.staff') }}"
                 class="{{ request()->routeIs('superadmin.users.staff') ? 'active' : '' }}">
                Çalışan Kullanıcıları
              </a>
            </li>
          </ul>
        </li>

        {{-- Yorumlar --}}
        <li class="nav-item">
          <a href="{{ route('superadmin.reviews.index') }}"
             class="nav-link {{ request()->routeIs('superadmin.reviews.*') ? 'active' : '' }}">
            <span class="icon-holder"><i class="bi bi-chat-dots"></i></span>
            <span class="title">Yorumlar</span>
          </a>
        </li>

        {{-- Şirketler --}}
        <li class="nav-item dropdown {{ request()->is('dash/superadmin/companies*') ? 'open' : '' }}">
          <a href="javascript:void(0);" class="dropdown-toggle nav-link">
            <span class="icon-holder"><i class="bi bi-building"></i></span>
            <span class="title">Şirketler</span>
            <span class="arrow"><i class="arrow-icon"></i></span>
          </a>
          <ul class="dropdown-menu">
            <li>
              <a href="{{ route('superadmin.companies.index') }}"
                 class="{{ request()->routeIs('superadmin.companies.index') ? 'active' : '' }}">
                Şirket Listele
              </a>
            </li>
            <li>
              <a href="{{ route('superadmin.companies.create') }}"
                 class="{{ request()->routeIs('superadmin.companies.create') ? 'active' : '' }}">
                Şirket Ekle
              </a>
            </li>
            <li>
              <a href="{{ route('superadmin.companies.index') }}">
                Şirket Düzenle
              </a>
            </li>
            <li>
              <a href="{{ route('superadmin.companies.index') }}">
                Şirket Sil
              </a>
            </li>
          </ul>
        </li>

        {{-- Çıkış --}}
        <li class="nav-item mt-auto">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-danger w-100">Çıkış Yap</button>
          </form>
        </li>

      </ul>
    </div>
  </div>
</nav>
<script>
  document.querySelectorAll('.sidebar .dropdown-toggle').forEach(toggle => {
    toggle.addEventListener('click', () => {
      const li = toggle.closest('.dropdown');
      li.classList.toggle('open');
    });
  });
</script>
