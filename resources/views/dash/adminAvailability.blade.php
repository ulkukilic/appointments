@extends('layouts.admin')

@section('title','Musaitlik Yonetimi')
@section('page_title','Musaitlik Yonetimi')

@section('content')

@foreach($availabilityData as $entry)
  <h5>{{ $entry['staff']->full_name }} ({{ $entry['staff']->experience_level }})</h5>

  <form method="POST" action="{{ route('admin.availability.add') }}"
        class="row row-cols-lg-auto g-3 mb-3 align-items-end">
    @csrf
    <input type="hidden" name="staff_member_uni_id"
           value="{{ $entry['staff']->staff_member_uni_id }}">
    <div class="col">
      <label class="form-label">Başlangıç</label>
      <input type="datetime-local" name="start_time" class="form-control" required>
    </div>
    <div class="col">
      <label class="form-label">Bitiş</label>
      <input type="datetime-local" name="end_time" class="form-control" required>
    </div>
    <div class="col">
      <label class="form-label">Servis</label>
      <select name="service_id" class="form-select" required>
        <option disabled selected>Servis Seçiniz</option>
        @foreach(DB::table('staff_services as ss')
                  ->join('services as s','ss.service_id','=','s.service_id')
                  ->where('ss.staff_member_uni_id',$entry['staff']->staff_member_uni_id)
                  ->select('s.service_id','s.name','s.standard_duration')->get() as $svc)
          <option value="{{ $svc->service_id }}">
            {{ $svc->name }} ({{ $svc->standard_duration }} dk)
          </option>
        @endforeach
      </select>
      @error('service_id') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="col">
      <label class="form-label">Durum</label>
      <select name="status" class="form-select" required>
        <option disabled selected>Seçiniz</option>
        <option value="available">Available</option>
        <option value="unavailable">Unavailable</option>
      </select>
    </div>
    <div class="col">
      <button type="submit" class="btn btn-success">Yeni Slot Ekle</button>
    </div>
  </form>

  <table class="table table-bordered table-hover mb-5">
    <thead class="table-light">
      <tr>
        <th>Slot ID</th><th>Başlangıç</th><th>Bitiş</th>
        <th>Servis</th><th>Süre</th><th>Durum</th><th>Güncelle</th>
      </tr>
    </thead>
    <tbody>
      @forelse($entry['slots'] as $slot)
        <tr>
          <td>{{ $slot->slot_id }}</td>
          <td>{{ \Carbon\Carbon::parse($slot->start_time)->format('d M Y H:i') }}</td>
          <td>{{ \Carbon\Carbon::parse($slot->end_time)->format('d M Y H:i') }}</td>
          <td>{{ $slot->service_name ?? '—' }}</td>
          <td>{{ $slot->standard_duration ?? '—' }}</td>
          <td>{{ ucfirst($slot->status) }}</td>
          <td>
            <form method="POST"
                  action="{{ route('admin.availability.update',$slot->slot_id) }}"
                  class="d-flex align-items-center">
              @csrf
              <select name="status" class="form-select form-select-sm me-2">
                <option value="available"   {{ $slot->status=='available'? 'selected':'' }}>Available</option>
                <option value="unavailable" {{ $slot->status=='unavailable'? 'selected':'' }}>Unavailable</option>
                <option value="booked"      {{ $slot->status=='booked'? 'selected':'' }}>Booked</option>
              </select>
              <button class="btn btn-sm btn-primary">Güncelle</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-center text-muted">Bu personelin kayıtlı slotu yok.</td></tr>
      @endforelse
    </tbody>
  </table>
@endforeach
@endsection