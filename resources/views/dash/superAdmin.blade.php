<!-- resources/views/dash/admin.blade.php -->
@extends('layouts.superadmin')

@section('title','SuperAdmin Paneli')
@section('page_title','Hoşgeldin, ' . session('full_name') . '!')

@section('content')
  <div class="alert alert-success">
    SuperAdmin Paneline Hoşgeldiniz!
  </div>
@endsection
