<!-- resources/views/layouts/sidebar-staff.blade.php -->
<div class="bg-white border-end vh-100 position-fixed" style="width:220px; top:0; left:0;">
  <div class="p-4 border-bottom"><strong>Personel Paneli</strong></div>
  <ul class="nav flex-column px-2">
    <li class="nav-item mb-2">
      <a href="{{ route('dash.staff') }}" 
         class="nav-link {{ request()->routeIs('dash.staff') ? 'active' : '' }}">
         Genel Bakış
      </a>
    </li>
    
  </ul>
  <form method="POST" action="{{ route('logout') }}" class="px-3 mt-auto">
    @csrf
    <button class="btn btn-outline-danger w-100">Çıkış</button>
  </form>
</div>
