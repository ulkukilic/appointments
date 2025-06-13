@extends('layouts.superadmin')

@section('title','SuperAdmin – Müşteri Kullanıcıları')
@section('page_title','Müşteri Kullanıcıları')

@section('content')
  @if($users->isEmpty())
    <div class="alert alert-info">Henüz hiç müşteri kullanıcı yok.</div>
  @else
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Ad Soyad</th>
          <th>Email</th>
          <th>İşlem</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $u->full_name }}</td>
            <td>{{ $u->email }}</td>
            <td>
              {{-- Düzenle / Sil butonları yine aynı şekilde --}}
              <a href="{{ route('superadmin.users.edit', $u->user_uni_id) }}"
                 class="btn btn-sm btn-primary">Düzenle</a>
              <form method="POST"
                    action="{{ route('superadmin.user.delete',$u->user_uni_id) }}"
                    class="d-inline">
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
