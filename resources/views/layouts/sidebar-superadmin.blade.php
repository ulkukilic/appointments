<!-- resources/views/layouts/sidebar-superadmin.blade.php -->

<nav class="side-nav bg-white vh-100 overflow-auto border-end">
    <div class="side-nav-inner">
        <ul class="side-nav-menu scrollable">

            {{-- GENEL BAKIŞ --}}
            <li class="nav-item">
                <a href="{{ route('dash.superadmin') }}"
                   class="nav-link {{ request()->routeIs('dash.superadmin') ? 'active' : '' }}">
                    <span class="icon-holder"><i class="bi bi-speedometer2"></i></span>
                    <span class="title">Genel Bakış</span>
                </a>
            </li>

            {{-- RANDEVULAR --}}
            <li class="nav-item dropdown {{ request()->is('dash/superadmin/appointments*') ? 'open' : '' }}">
                <a href="#" class="nav-link menu-toggle d-flex align-items-center">
                    <span class="icon-holder"><i class="bi bi-calendar-check"></i></span>
                    <span class="title">Randevular</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
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

            {{-- KULLANICILAR --}}
            <li class="nav-item dropdown {{ request()->is('dash/superadmin/users*') ? 'open' : '' }}">
                <a href="#" class="nav-link menu-toggle d-flex align-items-center">
                    <span class="icon-holder"><i class="bi bi-people"></i></span>
                    <span class="title">Kullanıcılar</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
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

            {{-- YORUMLAR --}}
            <li class="nav-item">
                <a href="{{ route('superadmin.reviews.index') }}"
                   class="nav-link {{ request()->routeIs('superadmin.reviews.*') ? 'active' : '' }}">
                    <span class="icon-holder"><i class="bi bi-chat-dots"></i></span>
                    <span class="title">Yorumlar</span>
                </a>
            </li>

            {{-- ŞİRKETLER --}}
            <li class="nav-item dropdown {{ request()->is('dash/superadmin/companies*') ? 'open' : '' }}">
                <a href="#" class="nav-link menu-toggle d-flex align-items-center">
                    <span class="icon-holder"><i class="bi bi-building"></i></span>
                    <span class="title">Şirketler</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
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
                
                
                </ul>
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
            e.preventDefault();                           // Sayfa atlamasın
            btn.closest('.nav-item.dropdown').classList.toggle('open');
        });
    });
});
</script>
@endpush
