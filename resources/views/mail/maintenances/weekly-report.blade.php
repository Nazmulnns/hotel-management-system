<x-mail::message>
# Weekly maintenance report

Report for **{{ $periodStart->toFormattedDateString() }}** to **{{ $periodEnd->toFormattedDateString() }}**.

<x-mail::table>
| Status | Count |
|:-------|------:|
| Pending | {{ $pending->count() }} |
| In progress | {{ $inProgress->count() }} |
| Resolved this week | {{ $resolved->count() }} |
</x-mail::table>

The full list is in the attached PDF.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>