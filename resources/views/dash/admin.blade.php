@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Hoşgeldin Admin, ' . session('full_name') . '!')

@section('content')
<div class="list-group shadow-sm">

    <a href="{{ route('admin.categories.index') }}"
       class="list-group-item list-group-item-action fs-5 fw-semibold text-primary">
        Kategori Yönetimi
    </a>

    <a href="{{ route('admin.staff.index') }}"
       class="list-group-item list-group-item-action fs-5 fw-semibold text-primary">
        Personel Yönetimi
    </a>

    <a href="{{ route('admin.availability.index') }}"
       class="list-group-item list-group-item-action fs-5 fw-semibold text-primary">
        Müsaitlik Yönetimi
    </a>

    <a href="{{ route('admin.appointments') }}"
       class="list-group-item list-group-item-action fs-5 fw-semibold text-primary">
        Randevu Yönetimi
    </a>

</div>
@endsection
