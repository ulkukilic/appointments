{{-- resources/views/layouts/sidebar-superadmin.blade.php --}}
<nav class="sidebar bg-white vh-100 overflow-auto border-end">
  <div class="side-nav-inner p-3">

    {{-- Randevular --}}
    <li class="nav-item mb-2">
      <a class="nav-link d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse"
         href="#appointmentsMenu"
         role="button"
         aria-expanded="{{ request()->is('dash/superadmin/appointments*') ? 'true' : 'false' }}"
         aria-controls="appointmentsMenu">
        <span><i class="bi bi-calendar-check me-2"></i>Randevular</span>
        <i class="bi bi-chevron-down"></i>
      </a>
      <div class="collapse {{ request()->is('dash/superadmin/appointments*') ? 'show' : '' }}"
           id="appointmentsMenu">
        <ul class="nav flex-column ms-3">
          <li class="nav-item">
            <a href="{{ route('superadmin.appointments') }}"
               class="nav-link {{ request()->routeIs('superadmin.appointments') ? 'active' : '' }}">
              Tüm Randevular
            </a>
          </li>
        </ul>
      </div>
    </li>

    {{-- Kullanıcılar --}}
    <li class="nav-item mb-2">
      <a class="nav-link d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse"
         href="#usersMenu"
         role="button"
         aria-expanded="{{ request()->is('dash/superadmin/users*') ? 'true' : 'false' }}"
         aria-controls="usersMenu">
        <span><i class="bi bi-people me-2"></i>Kullanıcılar</span>
        <i class="bi bi-chevron-down"></i>
      </a>
      <div class="collapse {{ request()->is('dash/superadmin/users*') ? 'show' : '' }}"
           id="usersMenu">
        <ul class="nav flex-column ms-3">
          <li class="nav-item">
            <a href="{{ route('superadmin.users.admins') }}"
               class="nav-link {{ request()->routeIs('superadmin.users.admins') ? 'active' : '' }}">
              Admin Kullanıcıları
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('superadmin.users.customers') }}"
               class="nav-link {{ request()->routeIs('superadmin.users.customers') ? 'active' : '' }}">
              Müşteri Kullanıcıları
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('superadmin.users.staff') }}"
               class="nav-link {{ request()->routeIs('superadmin.users.staff') ? 'active' : '' }}">
              Çalışan Kullanıcıları
            </a>
          </li>
        </ul>
      </div>
    </li>

    {{-- Şirketler --}}
    <li class="nav-item mb-2">
      <a class="nav-link d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse"
         href="#companiesMenu"
         role="button"
         aria-expanded="{{ request()->is('dash/superadmin/companies*') ? 'true' : 'false' }}"
         aria-controls="companiesMenu">
        <span><i class="bi bi-building me-2"></i>Şirketler</span>
        <i class="bi bi-chevron-down"></i>
      </a>
      <div class="collapse {{ request()->is('dash/superadmin/companies*') ? 'show' : '' }}"
           id="companiesMenu">
        <ul class="nav flex-column ms-3">
          <li class="nav-item">
            <a href="{{ route('superadmin.companies.index') }}"
               class="nav-link {{ request()->routeIs('superadmin.companies.index') ? 'active' : '' }}">
              Şirket Listele
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('superadmin.companies.create') }}"
               class="nav-link {{ request()->routeIs('superadmin.companies.create') ? 'active' : '' }}">
              Şirket Ekle
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('superadmin.companies.index') }}"
               class="nav-link">
              Şirket Düzenle
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('superadmin.companies.index') }}"
               class="nav-link">
              Şirket Sil
            </a>
          </li>
        </ul>
      </div>
    </li>

    {{-- Diğer sabit menüler --}}
    <li class="nav-item mb-2">
      <a href="{{ route('dash.superadmin') }}"
         class="nav-link {{ request()->routeIs('dash.superadmin') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 me-2"></i>Genel Bakış
      </a>
    </li>
    <li class="nav-item mb-2">
      <a href="{{ route('superadmin.reviews.index') }}"
         class="nav-link {{ request()->routeIs('superadmin.reviews.*') ? 'active' : '' }}">
        <i class="bi bi-chat-dots me-2"></i>Yorumlar
      </a>
    </li>

    {{-- Çıkış --}}
    <li class="nav-item mt-auto px-3">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-outline-danger w-100">Çıkış Yap</button>
      </form>
    </li>
  </div>
</nav>
