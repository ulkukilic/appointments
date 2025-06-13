@extends('layouts.admin')
@section('title', 'Kategori: ' . $category)
@section('page_title', 'Kategori: ' . ucwords(str_replace('-', ' ', $category)))
@section('content')
@include('layouts.alerts')
@if($companies->isEmpty())
    <p class="text-muted">Bu kategoriye ait şirket yok.</p>
@else
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Şirket Adı</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            @foreach($companies as $company)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $company->name }}</td>
                    <td>
                        <a href="{{ route('categories.company.availability', [$category, $company->company_uni_id]) }}" class="btn btn-info btn-sm">Müsaitlik</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection
