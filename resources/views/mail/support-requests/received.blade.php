<x-mail::message>
# We received your request

Dear {{ $supportRequest->guest->full_name }},

We hope you are enjoying your stay in room **{{ $roomLabel }}**.

We have received your request:

<x-mail::panel>
{{ $supportRequest->issue }}
</x-mail::panel>

Our team will look into it as soon as possible.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>