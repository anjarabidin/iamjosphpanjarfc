<x-mail::message>
    # Submission Accepted

    Dear {{ $authorName }},

    {!! $body !!}

    We are pleased to inform you that your submission **"{{ $submissionTitle }}"** has been accepted for publication.

    You will be contacted shortly regarding the copyediting process.

    <x-mail::button :url="route('submissions.show', $submission)">
        View Submission
    </x-mail::button>

    Best regards,<br>
    {{ config('app.name') }}
</x-mail::message>
