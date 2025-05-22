<x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

@component('mail::message')
# Merhaba {{ $user->name }},

Vistula Booking’e kaydolduğun için teşekkürler!

@component('mail::button', ['url' => 'https://vistula.edu.pl'])
Siteye Git
@endcomponent

Teşekkürler,<br>
{{ config('mail.from.name') }}
@endcomponent
