{{--
    Author View for Review Stage (OJS 3.3 Style)
    This partial is shown when $isAuthorView is true.
    It hides all internal workflow details (reviewers, internal files)
    and only shows:
    - Round Status
    - Notifications (Decision History)
    - Shared/Promoted Attachments
    - Revisions Upload Area
    - Review Discussions
--}}

<div class="lg:col-span-3 space-y-6">

    {{-- Section A: Round Tabs & Status --}}
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
        {{-- Round Tabs - Only show rounds that have activity (not pending new rounds) --}}
        @php
            // Filter rounds to only show ones that the author should see:
            // - Completed rounds (revisions_requested, resubmit_for_review, approved, declined)
            // - OR the first/current round
            $visibleRounds = $authorReviewData['reviewRounds']->filter(function ($round) use ($authorReviewData) {
                // Always show round 1
                if ($round->round === 1) {
                    return true;
                }
                // Show if round has decisions/activity (not just created as pending)
                return !in_array($round->status, ['pending']);
            });
        @endphp
        @if ($visibleRounds->count() > 1)
            <div class="border-b border-gray-200 bg-gray-50">
                <nav class="flex -mb-px" aria-label="Tabs">
                    @foreach ($visibleRounds as $round)
                        <button type="button" @click="selectedAuthorRound = {{ $round->round }}"
                            :class="selectedAuthorRound === {{ $round->round }} ?
                                'border-indigo-500 text-indigo-600 bg-white' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-3 px-6 border-b-2 font-medium text-sm transition-colors">
                            Round {{ $round->round }}
                        </button>
                    @endforeach
                </nav>
            </div>
        @endif

        {{-- Status Banner --}}
        <div class="p-6">
            @php
                $currentRound = $authorReviewData['currentRound'];
                $hasUploadedRevision = $authorReviewData['revisionFiles']->isNotEmpty();

                $statusMessages = [
                    'pending' => [
                        'class' => 'border-blue-400 bg-blue-50',
                        'icon' => 'fa-clock text-blue-500',
                        'title' => 'Under Review',
                        'message' =>
                            'Your submission is currently under review. You will be notified when a decision is made.',
                    ],
                    'revisions_requested' => [
                        'class' => 'border-amber-400 bg-amber-50',
                        'icon' => 'fa-pen text-amber-500',
                        'title' => 'Revisions Requested',
                        'message' =>
                            'The reviewers have requested revisions to your manuscript. Please upload your revised manuscript below.',
                    ],
                    'resubmit_for_review' => [
                        'class' => 'border-orange-400 bg-orange-50',
                        'icon' => 'fa-rotate text-orange-500',
                        'title' => 'Resubmit for Review',
                        'message' =>
                            'The submission must be resubmitted for another review round. Please address the feedback and upload your revised manuscript.',
                    ],
                    'revision_submitted' => [
                        'class' => 'border-teal-400 bg-teal-50',
                        'icon' => 'fa-check text-teal-500',
                        'title' => 'Revision Submitted',
                        'message' => 'Your revised manuscript has been submitted. The editor will review your changes.',
                    ],
                    'approved' => [
                        'class' => 'border-green-400 bg-green-50',
                        'icon' => 'fa-check-circle text-green-500',
                        'title' => 'Review Complete',
                        'message' => 'The review process has been completed. Your submission has been approved.',
                    ],
                    'declined' => [
                        'class' => 'border-red-400 bg-red-50',
                        'icon' => 'fa-times-circle text-red-500',
                        'title' => 'Submission Declined',
                        'message' => 'Unfortunately, your submission has been declined.',
                    ],
                ];

                $roundStatus = $currentRound?->status ?? 'pending';

                // If author has uploaded revision and round status is still "revisions_requested" or "resubmit_for_review",
                // show "revision_submitted" status
                if ($hasUploadedRevision && in_array($roundStatus, ['revisions_requested', 'resubmit_for_review'])) {
                    $roundStatus = 'revision_submitted';
                }

                $statusInfo = $statusMessages[$roundStatus] ?? $statusMessages['pending'];
            @endphp

            <div class="border-l-4 {{ $statusInfo['class'] }} p-4 rounded-r-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fa-solid {{ $statusInfo['icon'] }} text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-semibold text-gray-900">
                            Round {{ $currentRound?->round ?? 1 }} Status: {{ $statusInfo['title'] }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">{{ $statusInfo['message'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section B: Notifications (Decision History) --}}
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-base font-bold text-gray-900">
                <i class="fa-solid fa-bell text-indigo-500 mr-2"></i>Notifications
            </h3>
        </div>
        <div class="p-6">
            @if ($authorReviewData['decisionHistory']->isEmpty())
                <div class="text-center py-6">
                    <i class="fa-solid fa-envelope-open text-gray-300 text-3xl"></i>
                    <p class="text-sm text-gray-500 mt-3">No editorial decisions have been communicated yet.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($authorReviewData['decisionHistory'] as $index => $decision)
                        <div class="py-3 flex items-center justify-between hover:bg-gray-50 rounded-lg px-3 -mx-3 cursor-pointer transition-colors"
                            @click="showDecisionModal = true; selectedDecision = {{ json_encode($decision) }}">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center
                                    {{ $decision['type'] === 'revision_request' ? 'bg-amber-100' : ($decision['type'] === 'decline' ? 'bg-red-100' : 'bg-blue-100') }}">
                                    <i
                                        class="fa-solid {{ $decision['type'] === 'revision_request' ? 'fa-pen text-amber-600' : ($decision['type'] === 'decline' ? 'fa-times text-red-600' : 'fa-check text-blue-600') }}"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $decision['type_label'] }}</p>
                                    <p class="text-xs text-gray-500">
                                        Round {{ $decision['round'] }} •
                                        @if ($decision['made_at'])
                                            {{ \Carbon\Carbon::parse($decision['made_at'])->format('M d, Y - H:i') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-gray-400"></i>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Section C: Reviewer's Attachments (Shared Files) --}}
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-base font-bold text-gray-900">
                <i class="fa-solid fa-paperclip text-purple-500 mr-2"></i>Reviewer's Attachments
            </h3>
        </div>
        <div class="p-6">
            @if ($authorReviewData['promotedFiles']->isEmpty())
                <div class="text-center py-6">
                    <i class="fa-solid fa-folder-open text-gray-300 text-3xl"></i>
                    <p class="text-sm text-gray-500 mt-3">No files have been attached for you to review.</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($authorReviewData['promotedFiles'] as $file)
                        <div
                            class="flex items-center justify-between py-3 px-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-3">
                                @php
                                    $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                                    $iconClass = match ($ext) {
                                        'pdf' => 'fa-file-pdf text-red-500',
                                        'doc', 'docx' => 'fa-file-word text-blue-500',
                                        default => 'fa-file text-gray-500',
                                    };
                                @endphp
                                <i class="fa-solid {{ $iconClass }} text-lg"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $file->file_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $file->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <a href="{{ route('files.download', $file) }}"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                                <i class="fa-solid fa-download mr-1.5"></i> Download
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Section D: Revisions (Upload Area) --}}
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-base font-bold text-gray-900">
                <i class="fa-solid fa-file-arrow-up text-teal-500 mr-2"></i>Revisions
            </h3>
            @if ($submission->status === 'revision_required')
                <button type="button" @click="revisionUploadModalOpen = true"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 transition-colors">
                    <i class="fa-solid fa-upload mr-1.5"></i> Upload File
                </button>
            @endif
        </div>
        <div class="p-6">
            @if ($authorReviewData['revisionFiles']->isEmpty())
                <div class="text-center py-6">
                    <i class="fa-solid fa-cloud-arrow-up text-gray-300 text-3xl"></i>
                    <p class="text-sm text-gray-500 mt-3">No revision files uploaded yet.</p>
                    @if ($submission->status === 'revision_required')
                        <button type="button" @click="revisionUploadModalOpen = true"
                            class="mt-3 inline-flex items-center px-4 py-2 text-sm font-medium text-teal-600 bg-teal-50 rounded-lg hover:bg-teal-100 transition-colors">
                            <i class="fa-solid fa-upload mr-2"></i> Upload Your Revised Manuscript
                        </button>
                    @endif
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($authorReviewData['revisionFiles'] as $file)
                        <div
                            class="flex items-center justify-between py-3 px-4 rounded-lg bg-teal-50 hover:bg-teal-100 transition-colors">
                            <div class="flex items-center gap-3">
                                @php
                                    $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                                    $iconClass = match ($ext) {
                                        'pdf' => 'fa-file-pdf text-red-500',
                                        'doc', 'docx' => 'fa-file-word text-blue-500',
                                        default => 'fa-file text-gray-500',
                                    };
                                @endphp
                                <i class="fa-solid {{ $iconClass }} text-lg"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $file->file_name }}</p>
                                    <p class="text-xs text-gray-500">Uploaded {{ $file->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('files.download', $file) }}"
                                    class="p-2 text-gray-400 hover:text-indigo-600 transition-colors">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Section E: Review Discussions --}}
    <x-discussion-panel :submission="$submission" :stageId="2" stageName="review" :discussions="$submission->discussions->where('stage_id', 2)" :participants="$participants"
        :journal="$journal" />

</div>

{{-- Sidebar for Author (Simplified) --}}
<div class="lg:col-span-1 space-y-6">
    {{-- Review Round Info --}}
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Review Round</h4>
        @php $currentRound = $submission->currentReviewRound(); @endphp
        @if ($currentRound)
            <div class="text-center">
                <span class="text-3xl font-bold text-indigo-600">{{ $currentRound->round }}</span>
                <p class="text-sm text-gray-500 mt-1">{{ $currentRound->status_label }}</p>
            </div>
        @else
            <p class="text-sm text-gray-500 italic text-center">No review round started.</p>
        @endif
    </div>

    {{-- Submission Status --}}
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Status</h4>
        @php
            $statusBadges = [
                'submitted' => 'bg-blue-100 text-blue-800',
                'under_review' => 'bg-yellow-100 text-yellow-800',
                'in_review' => 'bg-yellow-100 text-yellow-800',
                'revision_required' => 'bg-orange-100 text-orange-800',
                'accepted' => 'bg-green-100 text-green-800',
                'rejected' => 'bg-red-100 text-red-800',
                'published' => 'bg-emerald-100 text-emerald-800',
            ];
        @endphp
        <span
            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusBadges[$submission->status] ?? 'bg-gray-100 text-gray-800' }}">
            {{ $submission->status_label }}
        </span>
    </div>

    {{-- Need Help Card --}}
    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-5 rounded-xl border border-indigo-100 shadow-sm">
        <h4 class="text-sm font-semibold text-indigo-900 mb-2">
            <i class="fa-solid fa-circle-question mr-2"></i>Need Help?
        </h4>
        <p class="text-xs text-indigo-700 mb-3">
            If you have questions about your submission or the review process, start a discussion with the editor.
        </p>
        <button type="button" @click="discussionModalOpen = true"
            class="w-full inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-indigo-600 bg-white rounded-lg border border-indigo-200 hover:bg-indigo-50 transition-colors">
            <i class="fa-solid fa-comments mr-2"></i> Contact Editor
        </button>
    </div>
</div>
