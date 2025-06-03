{{-- Admin – Müsaitlik Yönetimi Sayfası --}}
@extends('layouts.app')

@section('title', 'Admin - Müsaitlik Yönetimi')
@section('page_title', 'Personel Müsaitlik Yönetimi')

@section('content')
  @include('layouts.alerts')

  @foreach($availabilityData as $entry)
    {{-- Personel Bilgisi --}}
    <h5>{{ $entry['staff']->full_name }} 
        ({{ $entry['staff']->experience_level }})
    </h5>

    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>Slot ID</th>
          <th>Başlangıç</th>
          <th>Bitiş</th>
          <th>Servis Adı</th>
          <th>Süre (dk)</th>
          <th>Durum</th>
          <th>Güncelle</th>
        </tr>
      </thead>
      <tbody>
        @if($entry['slots']->isEmpty())
          <tr>
            <td colspan="7" class="text-center text-muted">
              Bu personelin kayıtlı slotu yok.
            </td>
          </tr>
        @else
          @foreach($entry['slots'] as $slot)
            <tr>
              <td>{{ $slot->slot_id }}</td>
              <td>{{ \Carbon\Carbon::parse($slot->start_time)->format('d M Y H:i') }}</td>
              <td>{{ \Carbon\Carbon::parse($slot->end_time)->format('d M Y H:i') }}</td>
              <td>{{ $slot->service_name ?? '—' }}</td>
              <td>{{ $slot->standard_duration ?? '—' }}</td>
              <td>{{ ucfirst($slot->status) }}</td>
              <td>
                <form 
                  method="POST" 
                  action="{{ route('admin.availability.update', $slot->slot_id) }}"
                  class="d-flex align-items-center"
                >
                  @csrf
                  <select name="status" class="form-select form-select-sm me-2">
                    <option value="available"   {{ $slot->status === 'available'   ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ $slot->status === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    <option value="booked"      {{ $slot->status === 'booked'      ? 'selected' : '' }}>Booked</option>
                  </select>
                  <button type="submit" class="btn btn-sm btn-primary">Güncelle</button>
                </form>
              </td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
  @endforeach
@endsection
