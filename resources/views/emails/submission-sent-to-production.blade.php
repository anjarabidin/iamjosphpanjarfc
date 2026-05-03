<x-mail::message>
    # Your Submission is Now in Production

    Dear {{ $authorName }},

    {!! $body !!}

    Your submission **"{{ $submissionTitle }}"** to **{{ $journalName }}** has completed the copyediting stage and is now in production.

    The production team will prepare your article for publication. You may be contacted if any clarifications are needed.

    <x-mail::button :url="$submissionUrl">
        View Submission
    </x-mail::button>

    Best regards,<br>
    {{ $journalName }} Editorial Team
</x-mail::message>