@extends('layouts.app')

@section('content')
  <!-- Başlık: Seçilen şirketin adı ve müsaitlik sayfası -->
   <div class="d-flex justify-content-between align-items-center mb-4">
     <h2 class="mb-0">{{ $company->name }} — Müsaitlik</h2>
     <span class="badge bg-secondary">{{ strtoupper($category) }}</span>
   </div>

  <!-- Hizmetler listesi -->
  <h4>Hizmetler</h4>
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 mb-5">
    @foreach($services as $svc)
      <div class="col">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">{{ $svc->name }}</h5>
            <p class="card-text mb-1">
              <strong>Süre:</strong> {{ $svc->standard_duration }} dk
            </p>
            <p class="card-text">
              <strong>Fiyat:</strong> ₺{{ number_format($svc->price, 2) }}
            </p>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <!-- Personel ve onların müsaitlik slotları -->
  <h4>Personel ve Slotlar</h4>
  <div class="row row-cols-1 row-cols-md-2 g-4">
    @foreach($staffData as $entry)
      <!-- Personel kartı -->
      <div class="col">
        <div class="card shadow-sm h-100">
          <!-- Kart başlığı: personel adı ve deneyim seviyesi -->
          <div class="card-header bg-light">
            {{ $entry['staff']->full_name }}
            <span class="badge bg-info ms-2 text-dark">{{ ucfirst($entry['staff']->experience_level) }}</span>
          </div>
          <div class="card-body">
            @if($entry['slots']->isEmpty()) <!-- Eğer slot yoksa bilgi mesajı -->
              <p class="text-muted">Önümüzdeki {{ $days }} gün içinde müsait slot yok.</p>
            @else
              <ul class="list-group list-group-flush">
                @foreach($entry['slots'] as $slot)
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <!-- Başlangıç ve bitiş zamanını biçimlendir -->
                    <div>
                      <i class="bi bi-clock-history me-2"></i>
                      <strong>{{ \Carbon\Carbon::parse($slot->start_time)->format('d M Y H:i') }}</strong> —
                      <strong>{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</strong>
                      <span class="badge bg-secondary ms-2">{{ ucfirst($slot->status) }}</span>
                    </div>
                    <!-- randevu formu-->
                    <div class="d-flex">
                      <form method="POST" action="{{ route('appointment.book') }}" class="d-flex">
                        @csrf
                        <input type="hidden" name="slot_id" value="{{ $slot->slot_id }}">
                        <!-- Hizmet Seçimi -->
                        <select name="service_id" required class="form-select form-select-sm me-2">
                          <option value="" disabled selected>Servis Seçiniz</option>
                          @foreach($services as $svc)
                            <option value="{{ $svc->service_id }}">{{ $svc->name }}</option>
                          @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-success">Randevu Al</button>
                      </form>
                    </div>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <!-- Yorum Yap: Sayfanın En Altında -->
  <div class="card mt-5 shadow-sm">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Yorum Yap</h5>
    </div>
    <div class="card-body">
      <!-- Kullanıcıdan puan ve yorum almak için form başlatılır -->
      <form method="POST" action="{{ route('reviews.store', $company->company_uni_id) }}">
        @csrf

        <!-- Çalışan Seçimi -->
        <div class="mb-3">
          <label for="staff_member_uni_id" class="form-label">Hangi Çalışana Yorum Yapmak İstersiniz?</label>
          <select
            name="staff_member_uni_id"
            id="staff_member_uni_id"
            class="form-select @error('staff_member_uni_id') is-invalid @enderror"
            required
          >
            <option value="" disabled selected>Çalışan Seçiniz</option>
            @foreach($staffData as $entry)
              <option
                value="{{ $entry['staff']->staff_member_uni_id }}"
                {{ old('staff_member_uni_id') == $entry['staff']->staff_member_uni_id ? 'selected' : '' }}
              >
                {{ $entry['staff']->full_name }} ({{ ucfirst($entry['staff']->experience_level) }})
              </option>
            @endforeach
          </select>
          @error('staff_member_uni_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

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
            value="{{ old('rating') }}"
          >
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
            rows="4"
            class="form-control @error('comment') is-invalid @enderror"
          >{{ old('comment') }}</textarea>
          @error('comment')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Formu göndermek için buton -->
        <button type="submit" class="btn btn-success">Gönder</button>
      </form>
    </div>
  </div>
@endsection
