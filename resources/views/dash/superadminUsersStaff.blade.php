@extends('layouts.superadmin')

@section('title','SuperAdmin – Çalışan Kullanıcıları')
@section('page_title','Çalışan Kullanıcıları')

@section('content')
  @if($staff->isEmpty())
    <div class="alert alert-info">Henüz hiç çalışan kaydı yok.</div>
  @else
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Ad Soyad</th>
          <th>Deneyim Seviyesi</th>
          <th>İşlem</th>
        </tr>
      </thead>
      <tbody>
        @foreach($staff as $s)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $s->full_name }}</td>
            <td>{{ $s->experience_level }}</td>
            <td>
              <a href="{{ route('admin.staff.edit', $s->staff_member_uni_id) }}"
                 class="btn btn-sm btn-primary">Düzenle</a>
              <form method="POST"
                    action="{{ route('admin.staff.delete',$s->staff_member_uni_id) }}"
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
