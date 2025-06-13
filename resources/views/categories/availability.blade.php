@extends('layouts.admin')
@section('title', $company->name . ' Müsaitlik')
@section('page_title', $company->name . ' Personel Müsaitlikleri')
@section('content')
@include('layouts.alerts')
@if($staffData->isEmpty())
    <p class="text-muted">Bu şirkete ait personel bulunmuyor.</p>
@else
    @foreach($staffData as $entry)
        <div class="mb-3">
            <h5>{{ $entry['staff']->full_name }} ({{ $entry['staff']->experience_level }})</h5>
            @if($entry['slots']->isEmpty())
                <p class="text-muted">Personelin kayıtlı slotu yok.</p>
            @else
                <ul>
                    @foreach($entry['slots'] as $slot)
                        <li>
                            {{ \Carbon\Carbon::parse($slot->start_time)->format('d M Y H:i') }}
                            - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                            ({{ ucfirst($slot->status) }})
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endforeach
@endif
@endsection
