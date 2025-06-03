{{-- Admin – Kategori Yönetimi Sayfası --}}
@extends('layouts.app')

@section('title', 'Admin - Kategori Yönetimi')
@section('page_title', 'Kategori Listesi')

@section('content')
    @include('layouts.alerts')

    @if($categories->isEmpty())
        <p class="text-muted">Henüz kayıtlı bir kategori yok.</p>
    @else
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Slug</th>
                    <th>Okunabilir İsim</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $slug)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $slug }}</td>
                    <td>{{ ucwords(str_replace('-', ' ', $slug)) }}</td>
                    <td>
                        {{-- Bu slug’a tıklayıp, customer tarafında o kategoriye ait şirketleri görebiliriz --}}
                        <a href="{{ route('categories.show', $slug) }}" class="btn btn-sm btn-primary">
                            Göster
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
