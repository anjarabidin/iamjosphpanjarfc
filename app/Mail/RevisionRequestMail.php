<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RevisionRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The submission instance.
     */
    public Submission $submission;

    /**
     * The decision body/message from the editor.
     */
    public string $decisionBody;

    /**
     * The attachment file paths to include.
     */
    public array $attachmentFiles;

    /**
     * Whether a new review round is required.
     */
    public bool $newRoundRequired;

    /**
     * The journal instance.
     */
    public $journal;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Submission $submission,
        string $decisionBody,
        array $attachmentFiles = [],
        bool $newRoundRequired = false
    ) {
        $this->submission = $submission;
        $this->decisionBody = $decisionBody;
        $this->attachmentFiles = $attachmentFiles;
        $this->newRoundRequired = $newRoundRequired;
        $this->journal = $submission->journal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $journalName = $this->journal?->name ?? config('app.name');

        return new Envelope(
            subject: "Revisions Required: {$this->submission->title}",
            replyTo: $this->journal?->email ? [$this->journal->email] : [],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.revision-request',
            with: [
                'submission' => $this->submission,
                'decisionBody' => $this->decisionBody,
                'newRoundRequired' => $this->newRoundRequired,
                'journal' => $this->journal,
                'authorName' => $this->submission->authors->first()?->name ?? $this->submission->author?->name ?? 'Author',
                'submissionUrl' => route('journal.submissions.show', [
                    'journal' => $this->journal?->slug ?? 'default',
                    'submission' => $this->submission->slug,
                ]),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->attachmentFiles as $file) {
            if (isset($file['path']) && Storage::disk('local')->exists($file['path'])) {
                $attachments[] = Attachment::fromStorageDisk('local', $file['path'])
                    ->as($file['name'] ?? basename($file['path']))
                    ->withMime($file['mime'] ?? 'application/octet-stream');
            }
        }

        return $attachments;
    }
}
