<x-mail::message>
# {{ $journalName }}

{{ $body }}

<x-mail::button :url="config('app.url')">
Visit Journal
</x-mail::button>

Best regards,
{{ $journalName }}
</x-mail::message>