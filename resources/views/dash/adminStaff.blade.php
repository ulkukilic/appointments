{{-- Admin – Personel Listesi & Ekleme --}}
@extends('layouts.app')

@section('title', 'Admin - Personel Yönetimi')
@section('page_title', 'Personel Listesi')

@section('content')
  @include('layouts.alerts')

  {{-- Personel Ekleme Butonu --}}
  <div class="mb-3">
    <a href="{{ route('admin.staff.edit', 0) }}" class="btn btn-success">
      Yeni Personel Ekle
    </a>
  </div>

  @if($staff->isEmpty())
    <p class="text-muted">Henüz bu şirkete ait kişisel veri yok.</p>
  @else
    <table class="table table-striped table-hover">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Ad Soyad</th>
          <th>Deneyim Seviyesi</th>
          <th>İşlemler</th>
        </tr>
      </thead>
      <tbody>
        @foreach($staff as $s)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $s->full_name }}</td>
            <td>{{ $s->experience_level }}</td>
            <td>
              {{-- Düzenle Butonu --}}
              <a href="{{ route('admin.staff.edit', $s->staff_member_uni_id) }}" class="btn btn-sm btn-warning me-1">
                Düzenle
              </a>

              {{-- Sil Butonu --}}
              <form 
                method="POST" 
                action="{{ route('admin.staff.delete', $s->staff_member_uni_id) }}" 
                class="d-inline-block"
                onsubmit="return confirm('Bu çalışanı silmek istediğinizden emin misiniz?');"
              >
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Sil</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
@endsection
