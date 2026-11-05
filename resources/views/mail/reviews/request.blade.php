<x-mail::message>
# Thank you for staying with us

Dear {{ $booking->guest->full_name }},

We hope you enjoyed your {{ $nights }} {{ Str::plural('night', $nights) }} with us ({{ $booking->check_in->toDateString() }} to {{ $booking->check_out->toDateString() }}, booking **{{ $booking->reference }}**).

We would love to hear about your stay. It only takes a minute.

<x-mail::button :url="$reviewUrl">
Leave a review
</x-mail::button>

<x-mail::panel>
Rate your stay from 1 to 5 stars and tell us what we can do better.
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>