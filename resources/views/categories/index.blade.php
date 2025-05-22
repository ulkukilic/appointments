@extends('layouts.app')

@section('content')
  <!-- Admin sadece kendi şirketini görebilsin -->
  @php $company = DB::table('companies')->where('company_uni_id', auth()->user()->company_uni_id)->first(); @endphp

  <h1>My Company</h1>
  <p>{{ $company->name }}</p>
  <a href="{{ route('admin.companies.edit', $company->company_uni_id) }}">Edit</a>
@endsection
