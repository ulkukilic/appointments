@extends('layouts.app')
@section('title','Admin Paneli')
@section('page_title', 'Hoşgeldin Admin, ' . session('full_name') . '!')

@section('content')
<!-- Sekmeler -->
<div class="mb-4">
  <button onclick="showSection('appointments')" class="btn btn-outline-success">Randevu Yönetimi</button>
  <button onclick="showSection('categories')" class="btn btn-outline-primary">Kategori Yönetimi</button>
  <button onclick="showSection('staff')" class="btn btn-outline-secondary">Personel Yönetimi</button>
  <button onclick="showSection('availability')" class="btn btn-outline-warning">Müsaitlik Yönetimi</button>
</div>

<!--  Randevu -->
<div id="appointments-section">
  @include('dash.adminAppointments')
</div>

<!--  Kategori -->
<div id="categories-section" style="display:none">
  @include('dash.adminCategories')
</div>

<!--  Personel -->
<div id="staff-section" style="display:none">
  @include('dash.adminStaff')
</div>

<!--  Müsaitlik -->
<div id="availability-section" style="display:none">
  @include('dash.adminAvailability')
</div>

<script>
  function showSection(section) {
    document.getElementById('appointments-section').style.display = 'none';
    document.getElementById('categories-section').style.display = 'none';
    document.getElementById('staff-section').style.display = 'none';
    document.getElementById('availability-section').style.display = 'none';
    document.getElementById(section + '-section').style.display = 'block';
  }
</script>
@endsection
