<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'journal_id',
        'key',
        'name',
        'subject',
        'body',
        'description',
        'from_name',
        'from_email',
        'is_enabled',
        'is_custom',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_custom' => 'boolean',
        'can_edit' => 'boolean',
        'can_disable' => 'boolean',
        'is_active' => 'boolean',
        'variables' => 'array',
    ];

    // =====================================================
    // RELATIONSHIPS
    // =====================================================

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    // =====================================================
    // SCOPES
    // =====================================================

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    // =====================================================
    // STATIC: Default Templates
    // =====================================================

    /**
     * Get default OJS email templates
     */
    public static function getDefaultTemplates(): array
    {
        return [
            [
                'key' => 'SUBMISSION_ACK',
                'name' => 'Submission Acknowledgement',
                'subject' => 'Submission Acknowledgement',
                'body' => "Dear {\$authorName},\n\nThank you for submitting the manuscript, \"{\$submissionTitle}\" to {\$journalName}. With the online journal management system that we are using, you will be able to track its progress through the editorial process by logging in to the journal web site:\n\nSubmission URL: {\$submissionUrl}\n\nIf you have any questions, please contact me. Thank you for considering this journal as a venue for your work.\n\n{\$signature}",
                'description' => 'Sent to the author when a new submission is received.',
            ],
            [
                'key' => 'SUBMISSION_ACK_NOT_USER',
                'name' => 'Submission Acknowledgement (Co-Author)',
                'subject' => 'Submission Acknowledgement',
                'body' => "Dear {\$recipientName},\n\nYou have been named as a co-author on a manuscript submission to {\$journalName}.\n\nThe submitting author, {\$authorName}, has provided the following message:\n\nTitle: {\$submissionTitle}\n\nIf you have any questions, please contact the submitting author.\n\n{\$signature}",
                'description' => 'Sent to co-authors when a new submission is received.',
            ],
            [
                'key' => 'REVIEW_REQUEST',
                'name' => 'Review Request',
                'subject' => 'Article Review Request',
                'body' => "Dear {\$reviewerName},\n\nI believe that you would serve as an excellent reviewer of the manuscript, \"{\$submissionTitle},\" which has been submitted to {\$journalName}.\n\nPlease log into the journal website to indicate whether you will undertake the review or not, as well as to access the submission and guidelines.\n\nReview URL: {\$reviewUrl}\n\nThe review is due {\$reviewDueDate}.\n\nThank you for considering this request.\n\n{\$signature}",
                'description' => 'Sent to a reviewer when they are assigned to review a submission.',
            ],
            [
                'key' => 'REVIEW_REQUEST_SUBSEQUENT',
                'name' => 'Review Request (Resubmission)',
                'subject' => 'Article Review Request (Revised)',
                'body' => "Dear {\$reviewerName},\n\nThis regards the manuscript \"{\$submissionTitle},\" which has been resubmitted to {\$journalName}.\n\nAs you reviewed the original submission, we would appreciate if you could review this revised version as well.\n\nReview URL: {\$reviewUrl}\n\nThe review is due {\$reviewDueDate}.\n\n{\$signature}",
                'description' => 'Sent to a reviewer for resubmitted manuscripts.',
            ],
            [
                'key' => 'REVIEW_CONFIRM',
                'name' => 'Review Confirmed',
                'subject' => 'Review Confirmed',
                'body' => "Dear {\$reviewerName},\n\nThank you for agreeing to review the submission, \"{\$submissionTitle},\" for {\$journalName}.\n\nPlease make sure to complete the review by {\$reviewDueDate}.\n\nReview URL: {\$reviewUrl}\n\n{\$signature}",
                'description' => 'Sent to a reviewer when they accept a review request.',
            ],
            [
                'key' => 'REVIEW_DECLINE',
                'name' => 'Review Declined',
                'subject' => 'Unable to Review',
                'body' => "Dear {\$editorName},\n\nI am afraid that I am unable to review the submission, \"{\$submissionTitle},\" for {\$journalName} at this time.\n\nThank you for thinking of me, and please feel free to contact me in the future.\n\n{\$reviewerName}",
                'description' => 'Sent when a reviewer declines a review request.',
            ],
            [
                'key' => 'REVIEW_REMIND',
                'name' => 'Review Reminder',
                'subject' => 'Reminder: Review Due',
                'body' => "Dear {\$reviewerName},\n\nThis is a reminder that your review for \"{\$submissionTitle}\" is due on {\$reviewDueDate}.\n\nPlease log in to complete your review at your earliest convenience.\n\nReview URL: {\$reviewUrl}\n\n{\$signature}",
                'description' => 'Reminder sent to a reviewer for pending reviews.',
            ],
            [
                'key' => 'REVIEW_COMPLETE',
                'name' => 'Review Completed',
                'subject' => 'Review Completed',
                'body' => "Dear {\$editorName},\n\n{\$reviewerName} has completed the review of \"{\$submissionTitle}\" for {\$journalName}.\n\nPlease log in to view the review comments and make an editorial decision.\n\nSubmission URL: {\$submissionUrl}\n\n{\$signature}",
                'description' => 'Sent to editor when a reviewer completes their review.',
            ],
            [
                'key' => 'EDITOR_DECISION_ACCEPT',
                'name' => 'Editorial Decision: Accept',
                'subject' => 'Editor Decision: Accept',
                'body' => "Dear {\$authorName},\n\nWe have reached a decision regarding your submission to {\$journalName}, \"{\$submissionTitle}\".\n\nOur decision is to: Accept Submission\n\n{\$editorComments}\n\n{\$signature}",
                'description' => 'Sent to the author when their submission is accepted.',
            ],
            [
                'key' => 'EDITOR_DECISION_REVISIONS',
                'name' => 'Editorial Decision: Revisions Required',
                'subject' => 'Editor Decision: Revisions Required',
                'body' => "Dear {\$authorName},\n\nWe have reached a decision regarding your submission to {\$journalName}, \"{\$submissionTitle}\".\n\nOur decision is to: Request Revisions\n\nPlease address the following concerns and resubmit your revised manuscript:\n\n{\$editorComments}\n\n{\$signature}",
                'description' => 'Sent to the author when revisions are required.',
            ],
            [
                'key' => 'EDITOR_DECISION_DECLINE',
                'name' => 'Editorial Decision: Decline',
                'subject' => 'Editor Decision: Decline',
                'body' => "Dear {\$authorName},\n\nWe have reached a decision regarding your submission to {\$journalName}, \"{\$submissionTitle}\".\n\nOur decision is to: Decline Submission\n\n{\$editorComments}\n\nThank you for considering {\$journalName} as a venue for your work.\n\n{\$signature}",
                'description' => 'Sent to the author when their submission is declined.',
            ],
            [
                'key' => 'COPYEDIT_REQUEST',
                'name' => 'Copyediting Request',
                'subject' => 'Copyediting Assignment',
                'body' => "Dear {\$copyeditorName},\n\nYou have been assigned to copyedit the submission \"{\$submissionTitle}\" for {\$journalName}.\n\nPlease log in to access the submission and begin copyediting.\n\nSubmission URL: {\$submissionUrl}\n\n{\$signature}",
                'description' => 'Sent to a copyeditor when they are assigned.',
            ],
            [
                'key' => 'LAYOUT_REQUEST',
                'name' => 'Layout Request',
                'subject' => 'Layout Assignment',
                'body' => "Dear {\$layoutEditorName},\n\nYou have been assigned to create galleys for the submission \"{\$submissionTitle}\" for {\$journalName}.\n\nPlease log in to access the submission files.\n\nSubmission URL: {\$submissionUrl}\n\n{\$signature}",
                'description' => 'Sent to a layout editor when they are assigned.',
            ],
            [
                'key' => 'PUBLISH_NOTIFY',
                'name' => 'Publication Notification',
                'subject' => 'Your Article Has Been Published',
                'body' => "Dear {\$authorName},\n\nWe are pleased to inform you that your article \"{\$submissionTitle}\" has been published in {\$journalName}, {\$issueTitle}.\n\nYou can view your published article at:\n{\$articleUrl}\n\nThank you for your contribution.\n\n{\$signature}",
                'description' => 'Sent to authors when their article is published.',
            ],
        ];
    }

    /**
     * Seed default templates for a journal
     */
    public static function seedForJournal(string $journalId): void
    {
        foreach (self::getDefaultTemplates() as $template) {
            self::create([
                'journal_id' => $journalId,
                'key' => $template['key'],
                'name' => $template['name'],
                'subject' => $template['subject'],
                'body' => $template['body'],
                'description' => $template['description'],
                'is_enabled' => true,
                'is_custom' => false,
            ]);
        }
    }

    /**
     * Reset template to default
     */
    public function resetToDefault(): bool
    {
        $defaults = collect(self::getDefaultTemplates());
        $default = $defaults->firstWhere('key', $this->key);

        if ($default) {
            $this->update([
                'subject' => $default['subject'],
                'body' => $default['body'],
                'is_custom' => false,
            ]);
            return true;
        }

        return false;
    }
}
