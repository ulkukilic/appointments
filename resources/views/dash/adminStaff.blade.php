{{-- resources/views/dash/adminStaff.blade.php --}}
@include('layouts.alerts')

<!-- Sayfa başlığı: Çalışan Listesi ve Hizmet Atamaları -->
<h4>Çalışan Listesi ve Hizmet Atamaları</h4>

<!-- Eğer şirkete ait personel yoksa bilgi mesajı göster -->
@if($staff->isEmpty())
  <p class="text-muted">Bu şirkete ait personel bulunmuyor.</p>
@else
  <!-- Şirkete ait personellerin ve hizmetlerinin listelendiği tablo -->
  <table class="table table-striped table-hover">
    <thead class="table-light">
      <tr>
        <th>#</th> <!-- Personel sıra numarası -->
        <th>Ad Soyad</th> <!-- Personelin adı soyadı -->
        <th>Deneyim Seviyesi</th> <!-- Personelin deneyim seviyesi -->
        <th>Hizmetler (Süre)</th> <!-- Personele atanmış hizmetler -->
        <th>İşlem</th> <!-- Silme işlemi sütunu -->
      </tr>
    </thead>
    <tbody>
      <!-- Her bir personel için satır -->
      @foreach($staff as $item)
        <tr class="bg-white text-dark">
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->full_name }}</td>
          <td>{{ $item->experience_level ?? '—' }}</td>
          <td>
            @php
              // Bu personelin bağlı olduğu hizmetleri alıyoruz
              $assigned = DB::table('staff_services as ss')
                            ->join('services as s', 'ss.service_id', '=', 's.service_id')
                            ->where('ss.staff_member_uni_id', $item->staff_member_uni_id)
                            ->select('s.name', 's.standard_duration')
                            ->get();
            @endphp

            <!-- Eğer hizmet atanmadıysa bilgi ver, varsa hizmetleri listele -->
            @if($assigned->isEmpty())
              <span class="text-muted">Hiç hizmet atanmadı</span>
            @else
              <ul class="mb-0">
                @foreach($assigned as $svc)
                  <li>{{ $svc->name }} ({{ $svc->standard_duration }} dk)</li>
                @endforeach
              </ul>
            @endif
          </td>
          <td>
            <!-- Personeli silmek için form ve sil butonu -->
            <form
              method="POST"
              action="{{ route('admin.staff.delete', $item->staff_member_uni_id) }}"
              onsubmit="return confirm('Bu çalışan silinsin mi?');"
              class="d-inline"
            >
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger">Sil</button>
            </form>
          </td>
        </>
      @endforeach
    </tbody>
  </table>
@endif

<hr>
<!-- Yeni personel ekleme ve hizmet atama formu başlığı -->
<h4>Yeni Personel Ekle &amp; Hizmet Atama</h4>
<!-- Yeni personel ekleme ve hizmet seçimi formu -->
<form method="POST" action="{{ route('admin.staff.add') }}">
  @csrf

  <!-- Personelin adı soyadı alanı -->
  <div class="mb-3">
    <label for="full_name" class="form-label">Ad Soyad</label>
    <input
      type="text"
      name="full_name"
      id="full_name"
      class="form-control @error('full_name') is-invalid @enderror"
      value="{{ old('full_name') }}"
      required
    >
    <!-- Ad Soyad için validasyon hatası -->
    @error('full_name')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <!-- Deneyim seviyesi alanı -->
  <div class="mb-3">
    <label for="experience_level" class="form-label">Deneyim Seviyesi</label>
    <input
      type="text"
      name="experience_level"
      id="experience_level"
      class="form-control @error('experience_level') is-invalid @enderror"
      value="{{ old('experience_level') }}"
      required
    >
    <!-- Deneyim seviyesi için validasyon hatası -->
    @error('experience_level')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <!-- Hizmet seçimi alanı (checkbox) -->
  <div class="mb-3">
    <label class="form-label">Hizmet Seçimi (Süre):</label>
    <div class="row">
      <!-- Mevcut tüm hizmetler için seçim kutusu -->
      @foreach($services as $svc)
        <div class="col-6 col-md-4">
          <div class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              name="service_ids[]"
              id="svc{{ $svc->service_id }}"
              value="{{ $svc->service_id }}"
            >
            <label class="form-check-label" for="svc{{ $svc->service_id }}">
              {{ $svc->name }} ({{ $svc->standard_duration }} dk)
            </label>
          </div>
        </div>
      @endforeach
    </div>
    <!-- Hizmet seçimi için validasyon hatası -->
    @error('service_ids')
      <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
  </div>

  <!-- Yeni personel ekle ve hizmet ata butonu -->
  <button type="submit" class="btn btn-success">Personel ve Hizmetleri Ekle</button>
</form>
