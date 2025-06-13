@extends('layouts.superadmin')

@section('title','SuperAdmin – Şirketler')
@section('page_title','Tüm Şirketler')

@section('content')
  @if($companies->isEmpty())
    <div class="alert alert-info">Henüz hiç şirket yok.</div>
  @else
    <table class="table table-bordered">
      <thead>
        <tr><th>#</th><th>Şirket Adı</th><th>Kategori</th><th>İşlem</th></tr>
      </thead>
      <tbody>
        @foreach($companies as $c)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $c->name }}</td>
            <td>{{ $c->category }}</td>
            <td>
              <a href="{{ route('superadmin.company.edit', $c->company_uni_id) }}"
                 class="btn btn-sm btn-primary">Düzenle</a>
              <form method="POST" action="{{ route('superadmin.company.delete', $c->company_uni_id) }}" class="d-inline">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Sil</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
@endsection
