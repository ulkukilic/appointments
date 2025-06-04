@include('layouts.alerts')

<p>Session company ID: {{ session('company_uni_id') }}</p>

@if($list->isEmpty())
  <p class="text-muted">Henüz bu şirkete ait randevu yok.</p>
  @else
    <table class="table table-striped table-hover">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Müşteri</th>
          <th>Email</th>
          <th>Hizmet</th>
          <th>Personel</th>
          <th>Tarih / Saat</th>
          <th>Durum</th>
          <th>İşlem</th>
        </tr>
      </thead>
      <tbody>
        @foreach($list as $item)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->customer_name ?? '(Tanımsız)' }}</td>
            <td>{{ $item->email         ?? '(Tanımsız)' }}</td>
            <td>{{ $item->service_name   ?? '(Tanımsız)' }}</td>
            <td>{{ $item->staff_name     ?? '(Tanımsız)' }}</td>
            <td>{{ \Carbon\Carbon::parse($item->scheduled_time)->format('d M Y H:i') }}</td>
            <td class="text-capitalize">{{ $item->status }}</td>
            <td>
             <form method="POST" action="{{ route('admin.appointments.update', $item->appointment_id) }}">

                @csrf
                <input type="hidden" name="email" value="{{ $item->email }}">
                <select name="status" class="form-select form-select-sm me-2">
                  <option value="pending"   {{ $item->status === 'pending'   ? 'selected' : '' }}>Beklemede</option>
                  <option value="confirmed" {{ $item->status === 'confirmed' ? 'selected' : '' }}>Onaylandı</option>
                  <option value="cancelled" {{ $item->status === 'cancelled' ? 'selected' : '' }}>İptal</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Güncelle</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

