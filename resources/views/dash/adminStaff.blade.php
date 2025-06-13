<!-- resources/views/dash/adminStaff.blade.php -->
@extends('layouts.admin')

@section('title','Personel Yönetimi')
@section('page_title','Personel Yönetimi')

@section('content')
  @if($staff->isEmpty())
    <p class="text-muted">Hiç personel yok.</p>
  @else
    <table class="table table-striped">
      <thead><tr>
        <th>#</th><th>Ad Soyad</th><th>Deneyim</th><th>İşlem</th>
      </tr></thead>
      <tbody>
        @foreach($staff as $item)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->full_name }}</td>
            <td>{{ $item->experience_level }}</td>
            <td>
              <a href="{{ route('admin.staff.edit',$item->staff_member_uni_id) }}"
                 class="btn btn-sm btn-warning">Düzenle</a>
              <form method="POST"
                    action="{{ route('admin.staff.delete',$item->staff_member_uni_id) }}"
                    class="d-inline" onsubmit="return confirm('Silinsin mi?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Sil</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  <hr>
  <a href="{{ route('admin.staff.edit',0) }}" class="btn btn-success">Yeni Personel Ekle</a>
@endsection
