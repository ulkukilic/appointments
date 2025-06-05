@include('layouts.alerts')

@foreach($availabilityData as $entry)
  {{-- Personel Bilgisi --}}
  <h5>{{ $entry['staff']->full_name }} 
      ({{ $entry['staff']->experience_level }})
  </h5>

  {{-- Yeni Slot Ekleme Formu --}}
  <form 
    method="POST" 
    action="{{ route('admin.availability.add') }}" 
    class="row row-cols-lg-auto g-3 align-items-center mb-3"
  >
    @csrf
    <input type="hidden" name="staff_member_uni_id" value="{{ $entry['staff']->staff_member_uni_id }}">

    <div class="col">
      <label class="form-label">Başlangıç</label>
      <input type="datetime-local" name="start_time" class="form-control" required>
    </div>

    <div class="col">
      <label class="form-label">Bitiş</label>
      <input type="datetime-local" name="end_time" class="form-control" required>
    </div>

     <div class="col">
         <label class="form-label">Durum</label>
         <select name="status" class="form-select" required>
          <option value="">Seçiniz</option>
          <option value="available">Available</option>
          <option value="booked">Booked</option>
          <option value="unavailable">Unavailable</option>
        </select>
    </div>
    
    <div class="col">
      <label class="form-label d-block">&nbsp;</label>
      <button type="submit" class="btn btn-success">Yeni Slot Ekle</button>
    </div>
  </form>
@endforeach