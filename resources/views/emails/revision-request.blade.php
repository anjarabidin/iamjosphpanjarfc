<x-mail::message>
    # Revisions Required

    Dear {{ $authorName }},

    Thank you for submitting your manuscript titled **"{{ $submission->title }}"** ({{ $submission->submission_code }})
    to **{{ $journal?->name ?? config('app.name') }}**.

    After careful consideration by our editorial team and reviewers, we have reached a decision regarding your
    submission.

    ---

    ## Editorial Decision: Revisions Required

    {!! $decisionBody !!}

    @if ($newRoundRequired)
        <x-mail::panel>
            **Important:** Once you submit your revised manuscript, it will undergo a new round of peer review.
        </x-mail::panel>
    @else
        <x-mail::panel>
            Your revisions will be reviewed by the editor. A new peer review round is not required.
        </x-mail::panel>
    @endif

    ---

    ## Next Steps

    1. Review the feedback provided above carefully.
    2. Prepare your revised manuscript addressing all comments.
    3. Log in to your author dashboard to submit your revisions.

    <x-mail::button :url="$submissionUrl" color="primary">
        View Submission
    </x-mail::button>

    If you have any questions regarding this decision, please do not hesitate to contact us.

    ---

    Best regards,<br>
    **{{ $journal?->name ?? config('app.name') }}** Editorial Team

    <x-mail::subcopy>
        This is an automated message from the {{ config('app.name') }} journal management system.
    </x-mail::subcopy>
</x-mail::message>
