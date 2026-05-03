<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Submission;
use App\Services\WaGateway;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\SubmissionReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewSubmissionNotification;

class SendSubmissionNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $submission;
    protected $author;

    /**
     * Create a new job instance.
     *
     * @param Submission $submission
     * @param User $author
     */
    public function __construct(Submission $submission, User $author)
    {
        $this->submission = $submission;
        $this->author = $author;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // 1. Notify the author that submission was received
            $this->author->notify(new SubmissionReceived($this->submission));

            // 2. Notify Journal Managers and Editors about the new submission
            $editorsAndManagers = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['Journal Manager', 'Editor', 'Admin', 'Super Admin']);
            })->get();

            Notification::send($editorsAndManagers, new NewSubmissionNotification($this->submission));

            // 3. Send WhatsApp notification to author
            WaGateway::sendTemplate($this->author, 'submission_received', [
                'name' => $this->author->name,
                'title' => $this->submission->title,
            ], $this->submission->journal_id);

            // 4. Send WhatsApp notification to Journal Managers and Editors
            foreach ($editorsAndManagers as $editor) {
                WaGateway::sendTemplate($editor, 'new_submission_notification', [
                    'name' => $editor->name,
                    'title' => $this->submission->title,
                    'author' => $this->author->name,
                ], $this->submission->journal_id);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send submission notifications', [
                'submission_id' => $this->submission->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Optionally release the job to try again later
            // $this->release(60); 
        }
    }
}
