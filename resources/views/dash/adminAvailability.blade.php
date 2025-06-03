@extends('layouts.app')

@section('title', 'Availability Management')
@section('page_title', 'Personel Müsaitlik Yönetimi')

@section('content')
  <div class="alert alert-info">
    Personel müsaitlik durumlarını düzenleyebilirsiniz.
  </div>

  @foreach($availabilityData as $entry)
    <h5>{{ $entry['staff']->full_name }} ({{ $entry['staff']->experience_level }})</h5>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Slot ID</th>
          <th>Başlangıç</th>
          <th>Bitiş</th>
          <th>Durum</th>
          <th>Güncelle</th>
        </tr>
      </thead>
      <tbody>
        @foreach($entry['slots'] as $slot)
          <tr>
            <td>{{ $slot->slot_id }}</td>
            <td>{{ \Carbon\Carbon::parse($slot->start_time)->format('d M Y H:i') }}</td>
            <td>{{ \Carbon\Carbon::parse($slot->end_time)->format('d M Y H:i') }}</td>
            <td>{{ ucfirst($slot->status) }}</td>
            <td>
              <form method="POST" action="{{ route('admin.availability.update', $slot->slot_id) }}">
                @csrf
                <select name="status" class="form-select form-select-sm d-inline w-auto">
                  <option value="available"   {{ $slot->status === 'available'   ? 'selected' : '' }}>Available</option>
                  <option value="unavailable" {{ $slot->status === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Güncelle</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endforeach
@endsection
