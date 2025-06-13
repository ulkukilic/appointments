@extends('layouts.superadmin')

@section('title','SuperAdmin – Kullanıcılar')
@section('page_title','Tüm Kullanıcılar')

@section('content')
  @if($users->isEmpty())
    <div class="alert alert-info">Henüz kayıtlı kullanıcı yok.</div>
  @else
    <table class="table table-hover">
      <thead>
        <tr><th>#</th><th>Ad Soyad</th><th>Email</th><th>İşlem</th></tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $u->full_name }}</td>
            <td>{{ $u->email }}</td>
            <td>
              <form method="POST" action="{{ route('superadmin.user.delete',$u->user_uni_id) }}">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Sil</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
@endsection
