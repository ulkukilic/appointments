
@component('mail::message')
# Merhaba {{ $user->name }},

Vistula Booking’e kaydolduğun için teşekkürler!

@component('mail::button', ['url' => 'https://vistula.edu.pl'])
Siteye Git
@endcomponent

Teşekkürler,<br>
{{ config('mail.from.name') }}
@endcomponent
