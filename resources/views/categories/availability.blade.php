@extends('layouts.app')

@section('content')
  <!-- Başlık: Seçilen şirketin adı ve müsaitlik sayfası -->
  <h2>{{ $company->name }} — Müsaitlik</h2>

  <!-- Hizmetler listesi -->
  <h4>Hizmetler</h4>
  <ul>
    <!-- Her hizmet için isim, süre ve fiyat bilgisi -->
    @foreach($services as $svc)
      <li>{{ $svc->name }} ({{ $svc->standard_duration }} dk) — ₺{{ $svc->price }}</li>
    @endforeach
  </ul>

  <!-- Personel ve onların müsaitlik slotları -->
  <h4>Personel ve Slotlar</h4>
  @foreach($staffData as $entry)
    <!-- Personel kartı -->
    <div class="card mb-3">
      <!-- Kart başlığı: personel adı ve deneyim seviyesi -->
      <div class="card-header">
        {{ $entry['staff']->full_name }} ({{ $entry['staff']->experience_level }})
      </div>
      <div class="card-body">
        <!-- Eğer slot yoksa bilgi mesajı -->
        @if($entry['slots']->isEmpty())
          <p>Önümüzdeki {{ $days }} gün içinde müsait slot yok.</p>
        @else
          <!-- Slot listesini göster -->
          <ul>
            @foreach($entry['slots'] as $slot)
              <li>
                <!-- Başlangıç ve bitiş zamanını biçimlendir -->
               {{ \Carbon\Carbon::parse($slot->start_time)->format('d M Y H:i') }}
             
                 {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}

                        <!-- randevu formu-->
                <form method="POST" action="{{ route('appointment.book') }}" class="d-inline-block ms-2">
                @csrf
                <input type="hidden" name="slot_id" value="{{ $slot->slot_id }}">

                  <!-- Hizmet Seçimi -->
                  <select name="service_id" required class="form-select form-select-sm d-inline-block w-auto">
                  @foreach($services as $svc)
                    <option value="{{ $svc->service_id }}">{{ $svc->name }}</option>
                  @endforeach
                 </select>


                  <button type="submit" class="btn btn-sm btn-success">Randevu Al</button>
                </form>
                

              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  @endforeach
@endsection
              