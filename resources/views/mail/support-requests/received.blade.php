<x-mail::message>
# {{ __('We received your request') }}

{{ __('Dear :name,', ['name' => $supportRequest->guest->full_name]) }}

@if ($roomLabel)
{{ __('We hope you are enjoying your stay in room :room.', ['room' => $roomLabel]) }}
@endif

{{ __('We have received your request:') }}

<x-mail::panel>
{{ $supportRequest->issue }}
</x-mail::panel>

{{ __('Our team will look into it as soon as possible.') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>