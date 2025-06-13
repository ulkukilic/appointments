<!-- resources/views/layouts/sidebar-admin.blade.php -->
<nav class="side-nav bg-white vh-100 overflow-auto border-end">
  <div class="side-nav-inner">
    <ul class="side-nav-menu scrollable">

      {{-- GENEL BAKIŞ --}}
      <li class="nav-item">
        <a href="{{ route('dash.admin') }}"
           class="nav-link {{ request()->routeIs('dash.admin') ? 'active' : '' }}">
          <span class="icon-holder"><i class="bi bi-speedometer2"></i></span>
          <span class="title">Genel Bakış</span>
        </a>
      </li>

      {{-- RANDEVULAR --}}
      <li class="nav-item dropdown {{ request()->routeIs('admin.appointments*') ? 'open' : '' }}">
        <a href="#" class="nav-link menu-toggle d-flex align-items-center">
          <span class="icon-holder"><i class="bi bi-calendar-check"></i></span>
          <span class="title">Randevular</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul class="dropdown-menu">
          <li>
            <a href="{{ route('admin.appointments') }}"
               class="{{ request()->routeIs('admin.appointments') ? 'active' : '' }}">
              Tüm Randevular
            </a>
          </li>
        </ul>
      </li>

      {{-- ŞİRKET BİLGİLERİ --}}
      <li class="nav-item dropdown {{ request()->routeIs('admin.categories*') ? 'open' : '' }}">
        <a href="#" class="nav-link menu-toggle d-flex align-items-center">
          <span class="icon-holder"><i class="bi bi-building"></i></span>
          <span class="title">Şirket Bilgileri</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul class="dropdown-menu">
          <li>
            <a href="{{ route('admin.categories.index') }}"
               class="{{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
              Düzenle
            </a>
          </li>
        </ul>
      </li>

      {{-- ÇALIŞAN YÖNETİMİ --}}
      <li class="nav-item dropdown {{ request()->routeIs('admin.staff*') ? 'open' : '' }}">
        <a href="#" class="nav-link menu-toggle d-flex align-items-center">
          <span class="icon-holder"><i class="bi bi-people-fill"></i></span>
          <span class="title">Çalışan Yönetimi</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul class="dropdown-menu">
          <li>
            <a href="{{ route('admin.staff.index') }}"
               class="{{ request()->routeIs('admin.staff*') ? 'active' : '' }}">
              Çalışan Listesi
            </a>
          </li>
        </ul>
      </li>

      {{-- SERVİSLER --}}
      <li class="nav-item dropdown {{ request()->routeIs('admin.services*') ? 'open' : '' }}">
        <a href="#" class="nav-link menu-toggle d-flex align-items-center">
          <span class="icon-holder"><i class="bi bi-gear-fill"></i></span>
          <span class="title">Servisler</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul class="dropdown-menu">
          <li>
            <a href="{{ route('admin.services.index') }}"
               class="{{ request()->routeIs('admin.services.index') ? 'active' : '' }}">
              Servis Listesi
            </a>
          </li>
          <li>
            <a href="{{ route('admin.services.create') }}"
               class="{{ request()->routeIs('admin.services.create') ? 'active' : '' }}">
              Yeni Servis Ekle
            </a>
          </li>
        </ul>
      </li>

      {{-- YORUM YÖNETİMİ --}}
      <li class="nav-item">
        <a href="{{ route('admin.reviews.index') }}"
           class="nav-link {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
          <span class="icon-holder"><i class="bi bi-chat-dots"></i></span>
          <span class="title">Yorum Yönetimi</span>
        </a>
      </li>

      {{-- ÇIKIŞ --}}
      <li class="nav-item mt-auto px-3">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn btn-outline-danger w-100">Çıkış Yap</button>
        </form>
      </li>

    </ul>
  </div>
</nav>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.side-nav .menu-toggle').forEach(btn => {
      btn.addEventListener('click', e => {
        e.preventDefault();
        btn.closest('.nav-item.dropdown').classList.toggle('open');
      });
    });
  });
</script>
@endpush
