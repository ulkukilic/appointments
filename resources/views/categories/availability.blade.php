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
       
     @if($entry['slots']->isEmpty()) <!-- Eğer slot yoksa bilgi mesajı -->
     <p>Önümüzdeki {{ $days }} gün içinde müsait slot yok.</p>
       @else
        <ul> <!-- Slot listesini göster -->
        @foreach($entry['slots'] as $slot)
        <li><!-- Başlangıç ve bitiş zamanını biçimlendir -->
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
             <h4>Yorum Yap</h4>
              <!-- Kullanıcıdan puan ve yorum almak için form başlatılır -->
              <form method="POST" action="{{ route('reviews.store', $company->company_uni_id) }}">
                @csrf

                <!-- Puan (rating) alanı -->
             <div class="mb-3">
              <label for="rating" class="form-label">Puan (1–5)</label>
              <input 
               type="number" 
              name="rating" 
              id="rating" 
              class="form-control @error('rating') is-invalid @enderror" 
              min="1" 
              max="5" 
              required 
              value="{{ old('rating') }}">
            <!-- Puan alanı için validasyon hatası varsa göster -->
              @error('rating')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              </div>

              <!-- Yorum (comment) alanı -->
                <div class="mb-3">
                  <label for="comment" class="form-label">Yorum (opsiyonel)</label>
                  <textarea 
                    name="comment" 
                    id="comment" 
                    rows="3" 
                    class="form-control @error('comment') is-invalid @enderror"
                  >{{ old('comment') }}</textarea>
                  <!-- Yorum alanı için validasyon hatası varsa göster -->                  @error('comment')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Formu göndermek için buton -->
                <button type="submit" class="btn btn-primary">Gönder</button>
              </form>
              
          </ul>
        @endif
      </div>
    </div>
  @endforeach
@endsection
              