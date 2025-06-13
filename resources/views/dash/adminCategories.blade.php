<!-- resources/views/dash/adminCategories.blade.php -->
@extends('layouts.admin')

@section('title','Kategori Yönetimi')
@section('page_title','Kategori Yönetimi')

@section('content')
  @if($companies->isEmpty())
    <p class="text-muted">Henüz kayıtlı şirket yok.</p>
  @else
    <table class="table table-bordered">
      <thead><tr>
        <th>#</th><th>Şirket Adı</th><th>İşlem</th>
      </tr></thead>
      <tbody>
        @foreach($companies as $company)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $company->name }}</td>
            <td>
              <a href="{{ route('categories.show',$company->category) }}"
                 class="btn btn-sm btn-primary">Göster</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
@endsection
