@php
    $journal = current_journal();
    $allDiscussions = $submission->discussions;

    // Map stage_id to stage key for Alpine state
    $stageMap = [
        1 => 'submission',
        2 => 'review',
        3 => 'copyediting',
        4 => 'production',
    ];
    $defaultStage = $stageMap[$submission->stage_id] ?? 'submission';
@endphp

<x-app-layout>
    <x-slot name="title">{{ $submission->title }}</x-slot>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

    <div x-data="submissionWorkflow({
        defaultStage: {{ json_encode($defaultStage) }},
        stageId: {{ $submission->stage_id ?? 1 }},
        currentReviewRound: {{ $submission->currentReviewRound()?->round ?? 1 }},
        maxReviewRound: {{ $submission->reviewRounds()->max('round') ?? 1 }},
        authorName: {{ json_encode($submission->author->name ?? 'Author') }},
        journalName: {{ json_encode($journal->name) }},
        submissionTitle: {{ json_encode($submission->title) }},
        promotableFilesUrl: {{ json_encode(route('journal.workflow.promotable-files', ['journal' => $journal->slug, 'submission' => $submission->slug])) }},
        revisionFilesUrl: {{ json_encode(route('journal.workflow.revision-files', ['journal' => $journal->slug, 'submission' => $submission->slug])) }},
        searchReviewersUrl: {{ json_encode(route('journal.workflow.reviewers.search', $journal->slug)) }},
        uploadImageUrl: {{ json_encode(route('journal.discussion.upload-image', ['journal' => $journal->slug])) }},
        uploadFileUrl: {{ json_encode(route('journal.discussion.upload-file', $journal->slug)) }},
        availableFilesUrl: {{ json_encode(route('journal.workflow.available-files', ['journal' => $journal->slug, 'submission' => $submission->slug])) }},
        reviewerAttachmentsUrl: {{ json_encode(route('journal.workflow.reviewer-attachments', ['journal' => $journal->slug, 'submission' => $submission->slug])) }},
        uploadDecisionFileUrl: {{ json_encode(route('journal.workflow.upload-decision-file', ['journal' => $journal->slug, 'submission' => $submission->slug])) }},
        csrfToken: {{ json_encode(csrf_token()) }},
        firstAuthorName: {{ json_encode($submission->authors?->first()?->name ?? 'Author') }},
        submissionCode: {{ json_encode($submission->submission_code ?? '') }},
        potentialEditors: {{ json_encode($potentialEditors) }}
    })">


        {{-- Header Section --}}
        <div class="mb-8">
            <nav class="flex items-center text-sm text-gray-500 mb-4 transition-colors">
                <a href="{{ route('journal.submissions.index', $journal->slug) }}"
                    class="group flex items-center hover:text-indigo-600">
                    <div
                        class="mr-2 p-1 rounded-md bg-gray-100 group-hover:bg-indigo-50 text-gray-500 group-hover:text-indigo-600 transition-colors">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                    </div>
                    Back to Submissions
                </a>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="flex-1 min-w-0 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-start gap-3">
                        <h1 class="text-2xl font-bold text-gray-900 leading-snug break-words">
                            {{ $submission->title }}
                        </h1>
                        @php
                            $stageColors = [
                                1 => 'bg-blue-100 text-blue-800 border-blue-200', // Submission
                                2 => 'bg-amber-100 text-amber-800 border-amber-200', // Review
                                3 => 'bg-teal-100 text-teal-800 border-teal-200', // Copyediting
                                4 => 'bg-emerald-100 text-emerald-800 border-emerald-200', // Production
                            ];
                            $stageNames = [
                                1 => 'Submission',
                                2 => 'Review',
                                3 => 'Copyediting',
                                4 => 'Production',
                            ];
                            
                            // Determine base stage color/name
                            $currentStageColor = $stageColors[$submission->stage_id] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            $currentStageName = $stageNames[$submission->stage_id] ?? 'Unknown';

                            // Override for special statuses
                             if ($submission->status == 3) { // Declined
                                $currentStageColor = 'bg-red-100 text-red-800 border-red-200';
                                $currentStageName = 'Declined';
                            } elseif ($submission->status == 2) { // Published
                                $currentStageColor = 'bg-indigo-100 text-indigo-800 border-indigo-200';
                                $currentStageName = 'Published';
                            }
                            
                            $isRejected = $submission->status == 3;
                        @endphp
                        
                        <div class="flex-shrink-0 pt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $currentStageColor }}">
                                {{ $currentStageName }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-y-2 text-sm text-gray-500">
                        <div class="flex items-center mr-6">
                            <span class="font-mono bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs mr-2 border border-gray-200">
                                {{ $submission->submission_code }}
                            </span>
                        </div>
                        
                        <div class="flex items-center mr-6">
                            <div class="w-6 h-6 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 mr-2">
                                <i class="fa-regular fa-user text-xs"></i>
                            </div>
                            <span class="text-gray-900 font-medium">
                                {{ $submission->authors?->first()?->name ?? 'Unknown Author' }}
                            </span>
                        </div>

                        <div class="flex items-center">
                            <i class="fa-regular fa-calendar text-gray-400 mr-2"></i>
                            <span>
                                {{ $submission->submitted_at?->format('M d, Y') ?? $submission->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Header Actions --}}
                <div class="flex items-center gap-3 pt-1">
                    <button @click="showActivityLog = true"
                        class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm">
                        <i class="fa-solid fa-clock-rotate-left mr-2 text-gray-500"></i>
                        Activity Log
                    </button>
                </div>
            </div>
        </div>

        {{-- WARNING BANNERS --}}
        @php
            $isUnassigned = $submission->editorialAssignments->where('is_active', true)->isEmpty();
            $isRejected = $submission->status == 3;
        @endphp

        {{-- REJECTED WARNING BANNER --}}
        @if ($isRejected)
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                <div class="flex items-center">
                    <i class="fa-solid fa-ban text-red-500 text-xl mr-3"></i>
                    <div>
                        <p class="text-red-800 font-semibold">This submission has been declined.</p>
                        <p class="text-red-600 text-sm">Workflow actions are disabled. This submission will appear
                            in
                            the Archives.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- UNASSIGNED WARNING BANNER --}}
        @if ($isUnassigned && !$isRejected)
            @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fa-solid fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-red-800 font-semibold">No editor has been assigned to this submission.
                                </p>
                                <p class="text-red-600 text-sm">Assign an editor to enable editorial decisions.</p>
                            </div>
                        </div>
                        <button @click="assignEditorModalOpen = true; resetEditorModal()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">
                            Assign Editor
                        </button>
                    </div>
                </div>
            @endjournalPermission
        @endif

        {{-- Main Navigation (Tabs) --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button @click="activeTab = 'workflow'"
                    :class="activeTab === 'workflow' ? 'border-indigo-600 text-indigo-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg focus:outline-none">
                    Workflow
                </button>
                <button @click="activeTab = 'publication'"
                    :class="activeTab === 'publication' ? 'border-indigo-600 text-indigo-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-lg focus:outline-none">
                    Publication
                </button>
            </nav>
        </div>

        {{-- Workflow Content --}}
        <div x-show="activeTab === 'workflow'">

            {{-- Stage Navigation (Blue Bar) --}}
            <div class="bg-gray-100 p-1 rounded-t-lg border-b border-gray-200 flex space-x-1">
                @foreach (['submission' => 1, 'review' => 2, 'copyediting' => 3, 'production' => 4] as $stageName => $stageId)
                    @can('accessStage', [$submission, $stageName])
                        <button
                            @click="activeStage = '{{ $stageName }}'; uploadStage = '{{ $stageName }}'; discussionStageId = {{ $stageId }}"
                            :class="activeStage === '{{ $stageName }}' ?
                                'bg-white text-indigo-600 border-t-4 border-indigo-600 shadow-sm' :
                                'text-gray-600 hover:bg-white/50'"
                            class="px-6 py-3 text-sm font-medium rounded-t-sm transition-all focus:outline-none flex-1 lg:flex-none">
                            {{ ucfirst($stageName) }}
                        </button>
                    @endcan
                @endforeach
            </div>

            {{-- Submission Stage Content --}}
            <div x-show="activeStage === 'submission'" class="bg-gray-50/50 min-h-screen pt-6">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                    {{-- Main Panel Area --}}
                    <div
                        class="{{ auth()->user()->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)? 'lg:col-span-3': 'lg:col-span-4' }} space-y-8">

                        {{-- Files Panel --}}
                        <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                            <div
                                class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                <h3 class="text-base font-bold text-gray-900">Submission Files</h3>
                                @if (auth()->user()->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id) ||
                                        !$submission->submitted_at)
                                    <button @click="fileModalOpen = true"
                                        class="text-sm text-indigo-600 font-medium hover:text-indigo-800">
                                        + Upload File
                                    </button>
                                @endif
                            </div>
                            {{-- Responsive wrapper for file table --}}
                            <div class="overflow-x-auto">
                                <table class="min-w-full table-fixed divide-y divide-gray-200">
                                    <colgroup>
                                        <col class="w-1/2 md:w-auto">
                                        <col class="w-32">
                                        <col class="w-32 flex-shrink-0">
                                    </colgroup>
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($submission->files->where('stage', 'submission') as $file)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <div
                                                            class="flex-shrink-0 h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                                                            @php
                                                                $extension = strtolower(
                                                                    pathinfo($file->file_name, PATHINFO_EXTENSION),
                                                                );
                                                                $iconClass = match ($extension) {
                                                                    'pdf' => 'fa-file-pdf text-red-500',
                                                                    'doc', 'docx' => 'fa-file-word text-blue-500',
                                                                    'xls', 'xlsx' => 'fa-file-excel text-green-500',
                                                                    'ppt',
                                                                    'pptx'
                                                                        => 'fa-file-powerpoint text-orange-500',
                                                                    default => 'fa-file-lines text-gray-500',
                                                                };
                                                            @endphp
                                                            <i class="fa-solid {{ $iconClass }} text-lg"></i>
                                                        </div>
                                                        <div class="ml-4 min-w-0 flex-1">
                                                            <div class="text-sm font-medium text-gray-900 truncate"
                                                                title="{{ $file->file_name }}">
                                                                {{ $file->file_name }}
                                                            </div>
                                                            <div class="text-xs text-gray-500">
                                                                {{ ucfirst($file->file_type) }} •
                                                                {{ number_format($file->file_size / 1024, 0) }} KB
                                                                •
                                                                v{{ $file->version ?? 1 }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $file->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end gap-2">
                                                        {{-- Preview Button --}}
                                                        @php
                                                            $viewableExtensions = [
                                                                'pdf',
                                                                'doc',
                                                                'docx',
                                                                'xls',
                                                                'xlsx',
                                                                'ppt',
                                                                'pptx',
                                                                'odt',
                                                                'ods',
                                                                'odp',
                                                            ];
                                                            $isViewable = in_array($extension, $viewableExtensions);
                                                        @endphp
                                                        @if ($isViewable)
                                                            <a href="{{ route('files.preview', $file) }}"
                                                                title="Preview"
                                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                                                                <i class="fa-solid fa-eye"></i>
                                                            </a>
                                                        @endif
                                                        {{-- Download Button --}}
                                                        <a href="{{ route('files.download', $file) }}" title="Download"
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 transition-colors">
                                                            <i class="fa-solid fa-download"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3"
                                                    class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                                    No files uploaded to this stage.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Discussions Panel - Stage 1: Submission --}}
                        <x-discussion-panel :submission="$submission" :stageId="1" stageName="submission" :discussions="$allDiscussions"
                            :participants="$participants" :journal="$journal" />




                    </div>







                    {{-- Active Stage Placeholder for other tabs --}}
                    @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                        <div class="lg:col-span-1 space-y-6">
                            {{-- Workflow Actions --}}
                            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Workflow
                                    Actions
                                </h4>
                                @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                    @php
                                        $decisionMade = $submission->stage_id > 1 || $submission->status == 3;
                                    @endphp

                                    <div x-data="{ changingDecision: false }">
                                        @if ($decisionMade)
                                            <div x-show="!changingDecision" class="space-y-3">
                                                @if ($submission->status == 3)
                                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                                        <div class="flex items-center">
                                                            <i class="fa-solid fa-ban text-red-500 mr-2"></i>
                                                            <span class="text-sm font-semibold text-red-800">Submission
                                                                Declined</span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                        <div class="flex items-center">
                                                            <i class="fa-solid fa-check text-blue-500 mr-2"></i>
                                                            <span class="text-sm font-semibold text-blue-800">Submission
                                                                accepted for review</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                <button @click="changingDecision = true"
                                                    class="text-sm text-indigo-600 font-medium hover:underline focus:outline-none">
                                                    Change Decision
                                                </button>
                                            </div>
                                        @endif

                                        <div x-show="!{{ $decisionMade ? 'true' : 'false' }} || changingDecision"
                                            class="{{ $decisionMade ? 'mt-4 pt-4 border-t border-gray-100' : '' }}"
                                            style="{{ $decisionMade ? 'display: none' : '' }}">

                                            @if ($isUnassigned && !$isRejected)
                                                {{-- BLUE INFO BOX: Disabled state for unassigned --}}
                                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                                    <div class="flex items-start">
                                                        <i class="fa-solid fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                                                        <p class="text-sm text-blue-700">
                                                            Assign an editor to enable the editorial decisions for this
                                                            stage.
                                                        </p>
                                                    </div>
                                                </div>
                                                {{-- DISABLED STATE: Show grayed out buttons for unassigned --}}
                                                <button disabled
                                                    class="w-full mb-2 px-4 py-2.5 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed text-sm font-medium">
                                                    <i class="fa-solid fa-arrow-right mr-2"></i>Send to Review
                                                </button>
                                                <button disabled
                                                    class="w-full px-4 py-2.5 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed text-sm font-medium">
                                                    <i class="fa-solid fa-ban mr-2"></i>Decline Submission
                                                </button>
                                            @else
                                                {{-- ACTIVE STATE: Enabled workflow actions --}}
                                                <button @click="openSendToReviewModal()"
                                                    class="w-full mb-2 px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                                                    <i class="fa-solid fa-arrow-right mr-2"></i>Send to Review
                                                </button>
                                                <button @click="openSkipReviewModal()"
                                                    class="w-full mb-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium transition-colors">
                                                    <i class="fa-solid fa-forward mr-2"></i>Accept & Skip Review
                                                </button>
                                                <button @click="declineModalOpen = true; resetDeclineModal()"
                                                    class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium transition-colors">
                                                    <i class="fa-solid fa-ban mr-2"></i>Decline Submission
                                                </button>
                                            @endif

                                            @if ($decisionMade)
                                                <div class="mt-3 text-center">
                                                    <button @click="changingDecision = false"
                                                        class="text-xs text-gray-400 hover:text-gray-600 underline focus:outline-none">
                                                        Cancel
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 italic">Actions available to Editors.</p>
                                @endjournalPermission
                            </div>

                            {{-- Participants (Modernized - OJS 3.3 Style) --}}
                            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Participants
                                    </h4>
                                    @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                        <button @click="assignEditorModalOpen = true; resetEditorModal()"
                                            class="text-xs font-medium px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors">
                                            <i class="fa-solid fa-plus text-xs mr-1"></i> Assign
                                        </button>
                                    @endjournalPermission
                                </div>

                                @php
                                    // Group participants by role
                                    $groupedParticipants = [
                                        'Journal Manager' => [],
                                        'Editor' => [],
                                        'Section Editor' => [],
                                        'Author' => [],
                                        'Reviewer' => [],
                                    ];

                                    // Add editorial assignments
                                    foreach (
                                        $submission->editorialAssignments->where('is_active', true)
                                        as $assignment
                                    ) {
                                        $roleName = ucfirst(str_replace('_', ' ', $assignment->role));
                                        if (!isset($groupedParticipants[$roleName])) {
                                            $groupedParticipants[$roleName] = [];
                                        }
                                        $groupedParticipants[$roleName][] = [
                                            'user' => $assignment->user,
                                            'role' => $roleName,
                                            'assignment_id' => $assignment->id,
                                            'type' => 'editorial',
                                        ];
                                    }

                                    // Add submitting author
                                    if ($submission->authors->first()) {
                                        $groupedParticipants['Author'][] = [
                                            'user' => $submission->authors->first(),
                                            'role' => 'Author',
                                            'type' => 'author',
                                        ];
                                    }

                                    // Role colors and initials
                                    $roleColors = [
                                        'Journal Manager' => 'bg-purple-500',
                                        'Editor' => 'bg-blue-500',
                                        'Section Editor' => 'bg-indigo-500',
                                        'Author' => 'bg-amber-500',
                                        'Reviewer' => 'bg-emerald-500',
                                    ];

                                    $userIsEditor = auth()
                                        ->user()
                                        ->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id);
                                    $userIsSuperAdmin = auth()
                                        ->user()
                                        ->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER], $journal->id);
                                @endphp

                                <div class="space-y-4">
                                    @foreach ($groupedParticipants as $role => $members)
                                        @if (count($members) > 0)
                                            {{-- Role Group Header --}}
                                            <div>
                                                <h5
                                                    class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                                                    {{ $role }}
                                                </h5>
                                                <div class="space-y-2">
                                                    @foreach ($members as $member)
                                                        @php
                                                            $user = $member['user'];
                                                            $isCurrentUser = $user->id === auth()->id();
                                                            $initials = strtoupper(substr($user->name, 0, 1));
                                                            if (str_contains($user->name, ' ')) {
                                                                $parts = explode(' ', $user->name);
                                                                $initials = strtoupper(
                                                                    substr($parts[0], 0, 1) .
                                                                        substr($parts[1] ?? '', 0, 1),
                                                                );
                                                            }
                                                            $avatarColor = $roleColors[$role] ?? 'bg-gray-500';
                                                        @endphp
                                                        {{-- User Item --}}
                                                        <div
                                                            class="flex items-center gap-3 py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                                            {{-- Avatar --}}
                                                            <div
                                                                class="w-9 h-9 rounded-full {{ $avatarColor }} flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                                                {{ $initials }}
                                                            </div>
                                                            {{-- Name --}}
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm font-semibold text-gray-900 truncate">
                                                                    {{ $user->name }}
                                                                    @if ($isCurrentUser)
                                                                        <span
                                                                            class="text-xs text-indigo-600 font-normal">(You)</span>
                                                                    @endif
                                                                </p>
                                                                <p class="text-xs text-gray-500 truncate">
                                                                    {{ $user->email }}
                                                                </p>
                                                            </div>

                                                            {{-- Editor View: Full Action Dropdown --}}
                                                            @if ($userIsEditor && !$isCurrentUser)
                                                                <div class="relative" x-data="{ openDropdown: false }">
                                                                    <button @click="openDropdown = !openDropdown"
                                                                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded transition-colors">
                                                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                                                    </button>

                                                                    {{-- Dropdown Menu --}}
                                                                    <div x-show="openDropdown"
                                                                        @click.away="openDropdown = false"
                                                                        x-transition:enter="transition ease-out duration-100"
                                                                        x-transition:enter-start="transform opacity-0 scale-95"
                                                                        x-transition:enter-end="transform opacity-100 scale-100"
                                                                        x-transition:leave="transition ease-in duration-75"
                                                                        x-transition:leave-start="transform opacity-100 scale-100"
                                                                        x-transition:leave-end="transform opacity-0 scale-95"
                                                                        class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
                                                                        style="display: none;">
                                                                        <div class="py-1">
                                                                            {{-- Notify Action --}}
                                                                            @if ($user->exists)
                                                                                <button type="button"
                                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                                                    <i
                                                                                        class="fa-solid fa-envelope text-indigo-500 w-4"></i>
                                                                                    <span>Send Email</span>
                                                                                </button>
                                                                            @endif

                                                                            {{-- Login As (Super Admin Only) --}}
                                                                            @if ($userIsSuperAdmin && $user->exists)
                                                                                <form
                                                                                    action="{{ route('journal.users.login-as', ['journal' => $journal->slug, 'user' => $user->id]) }}"
                                                                                    method="POST" class="inline">
                                                                                    @csrf
                                                                                    <button type="submit"
                                                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                                                        <i
                                                                                            class="fa-solid fa-user-shield text-purple-500 w-4"></i>
                                                                                        <span>Login As User</span>
                                                                                    </button>
                                                                                </form>
                                                                            @endif

                                                                            <div class="border-t border-gray-100"></div>

                                                                            {{-- Remove Action (Not for Authors) --}}
                                                                            @if ($member['type'] === 'editorial')
                                                                                <form method="POST"
                                                                                    action="{{ route('journal.workflow.remove-editor', ['journal' => $journal->slug, 'submission' => $submission->slug, 'assignment' => $member['assignment_id']]) }}"
                                                                                    onsubmit="return confirm('Are you sure you want to remove this participant? This action cannot be undone.');">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit"
                                                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                                                        <i
                                                                                            class="fa-solid fa-trash w-4"></i>
                                                                                        <span>Remove</span>
                                                                                    </button>
                                                                                </form>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    @if (array_sum(array_map('count', $groupedParticipants)) === 0)
                                        <p class="text-sm text-gray-400 italic text-center py-4">No participants
                                            assigned
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endjournalPermission

                </div>
            </div>

            {{-- ==================== REVIEW STAGE ==================== --}}
            <div x-show="activeStage === 'review'" class="bg-gray-50/50 min-h-screen pt-6">
            @if($submission->stage_id == 1)
                <div class="flex flex-col items-center justify-center min-h-[400px]">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-solid fa-lock text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">The review process has not yet been initiated.</h3>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                    {{-- ==================== AUTHOR VIEW (Blind Review Protocol) ==================== --}}
                    @if (isset($isAuthorView) && $isAuthorView)
                        @include('submissions.partials.review-author-view')

                        {{-- ==================== EDITOR/ADMIN VIEW (Full Access) ==================== --}}
                    @else
                        {{-- Main Panel Area --}}
                        <div class="lg:col-span-3 space-y-6">

                            {{-- ==================== ROUND TABS NAVIGATION ==================== --}}
                            @php
                                // Get all valid rounds (not duplicates)
                                $allRounds = $submission->reviewRounds()->orderBy('round')->get()->unique('round'); // Prevent duplicate round numbers

                                $currentRound = $submission->currentReviewRound();
                                $latestRoundNumber = $currentRound?->round ?? 1;

                                // Check if there's already a pending new round
$hasPendingNewRound = $allRounds->contains(function ($r) use ($currentRound) {
    return $r->status === 'pending' && $r->round > ($currentRound?->round ?? 0);
});

// Get selected round from URL or default to latest
$selectedRoundNumber = request()->query('round', $latestRoundNumber);
$selectedRound = $allRounds->firstWhere('round', $selectedRoundNumber) ?? $currentRound;

// Filter revision files for the SELECTED round
// Revisions are files uploaded by author in response to that round's revision request
                                $authorRevisionFiles = $submission->files
                                    ->where('stage', 'revision')
                                    ->where('file_type', 'revision')
                                    ->filter(function ($file) use ($selectedRoundNumber, $allRounds) {
                                        $metadata = $file->metadata ?? [];

                                        // Exclude files promoted by editor
                                        if (isset($metadata['decision_type']) || isset($metadata['promoted_from'])) {
                                            return false;
                                        }

                                        // If file has revision_round metadata, use it
                                        if (isset($metadata['revision_for_round'])) {
                                            return $metadata['revision_for_round'] == $selectedRoundNumber;
                                        }

                                        // Fallback: Check upload time relative to round dates
                                        // Get the selected round and next round to determine time window
                                        $thisRound = $allRounds->firstWhere('round', $selectedRoundNumber);
                                        $nextRound = $allRounds->firstWhere('round', $selectedRoundNumber + 1);

                                        if ($thisRound) {
                                            $uploadedAt = $file->created_at;
                                            $roundCreatedAt = $thisRound->created_at;

                                            // File should be uploaded after this round was created
                                            if ($uploadedAt < $roundCreatedAt) {
                                                return false;
                                            }

                                            // If there's a next round, file should be uploaded before that
                                            if ($nextRound && $uploadedAt >= $nextRound->created_at) {
                                                return false;
                                            }

                                            return true;
                                        }

                                        // Default: show in Round 1 if no round data
                                        return $selectedRoundNumber == 1;
                                    });
                                $hasAuthorRevisions = $authorRevisionFiles->isNotEmpty();
                            @endphp
                            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                                <div class="border-b border-gray-200 bg-gray-50">
                                    <nav class="flex items-center justify-between px-4" aria-label="Tabs">
                                        <div class="flex -mb-px">
                                            @foreach ($allRounds as $round)
                                                @php
                                                    $isSelected = $round->round == $selectedRoundNumber;
                                                    $isCompleted = $round->status === 'completed';
                                                @endphp
                                                <a href="{{ request()->fullUrlWithQuery(['round' => $round->round]) }}"
                                                    class="whitespace-nowrap py-3 px-5 border-b-2 font-semibold text-sm transition-colors
                                                    {{ $isSelected
                                                        ? 'border-indigo-500 text-indigo-600 bg-white'
                                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                                    <i
                                                        class="fa-solid fa-rotate mr-1.5 {{ $isSelected ? 'text-indigo-500' : 'text-gray-400' }}"></i>
                                                    Round {{ $round->round }}
                                                    @if ($isCompleted)
                                                        <i class="fa-solid fa-check text-green-500 ml-1 text-xs"></i>
                                                    @elseif ($round->round == $latestRoundNumber)
                                                        <span
                                                            class="ml-1 text-xs font-normal {{ $isSelected ? 'text-indigo-500' : 'text-gray-400' }}">(Latest)</span>
                                                    @endif
                                                </a>
                                            @endforeach
                                            @if ($allRounds->isEmpty())
                                                <span class="py-3 px-5 text-sm text-gray-500 italic">No review
                                                    rounds
                                                    yet</span>
                                            @endif
                                        </div>
                                        {{-- New Review Round Button (only show if author has revisions AND no pending new round exists) --}}
                                        @if ($hasAuthorRevisions && $submission->status === 'revision_required' && !$hasPendingNewRound)
                                            <button type="button" @click="openNewRoundModal()"
                                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors my-2">
                                                <i class="fa-solid fa-plus mr-1.5"></i> New Review Round
                                            </button>
                                        @endif
                                    </nav>
                                </div>

                                {{-- Status Banner - Shows status for the SELECTED round --}}
                                <div class="p-4">
                                    @php
                                        $displayRoundStatus = $selectedRound?->status ?? 'pending';
                                        $displayRoundNumber = $selectedRoundNumber;

                                        // Get reviewers for selected round
                                        $roundReviewers = $submission->reviewAssignments
                                            ->where('round', $selectedRoundNumber);
                                        $reviewersCount = $roundReviewers->count();
                                        $completedReviews = $roundReviewers
                                            ->where('status', 'completed')
                                            ->count();

                                        // Determine the status message based on selected round state (Priority Chain)
                                        if ($displayRoundStatus === 'completed') {
                                            // Priority 5: Decision State (Completed)
                                            $statusConfig = [
                                                'class' => 'border-green-400 bg-green-50',
                                                'icon' => 'fa-check-circle text-green-500',
                                                'title' => 'Round Complete',
                                                'message' => 'This review round has been completed.',
                                            ];
                                        } elseif ($reviewersCount === 0) {
                                            // Priority 1: Awaiting Reviewers
                                            $statusConfig = [
                                                'class' => 'border-amber-400 bg-amber-50',
                                                'icon' => 'fa-user-plus text-amber-500',
                                                'title' => 'Awaiting Reviewers',
                                                'message' => 'No reviewers have been assigned yet. Add reviewers to begin the review process.',
                                            ];
                                        } elseif (
                                            $hasAuthorRevisions &&
                                            $submission->status === 'revision_required' &&
                                            $displayRoundNumber == $latestRoundNumber
                                        ) {
                                            // Priority 2: Revisions Submitted
                                            $statusConfig = [
                                                'class' => 'border-teal-400 bg-teal-50',
                                                'icon' => 'fa-file-circle-check text-teal-500',
                                                'title' => 'Revisions Submitted',
                                                'message' => 'The author has submitted revised files. You can create a new review round to send these revisions to reviewers.',
                                            ];
                                        } elseif ($completedReviews < $reviewersCount) {
                                            // Priority 3: Under Review
                                            $statusConfig = [
                                                'class' => 'border-blue-400 bg-blue-50',
                                                'icon' => 'fa-clock text-blue-500',
                                                'title' => 'Under Review',
                                                'message' => "{$completedReviews} of {$reviewersCount} reviewers have completed their review.",
                                            ];
                                        } elseif (
                                            $displayRoundStatus === 'revisions_requested' ||
                                            $submission->status === 'revision_required'
                                        ) {
                                            // Priority 5: Decision State (Revisions Requested)
                                            $statusConfig = [
                                                'class' => 'border-orange-400 bg-orange-50',
                                                'icon' => 'fa-pen text-orange-500',
                                                'title' => 'Revisions Requested',
                                                'message' => 'Waiting for author to submit revised manuscript.',
                                            ];
                                        } else {
                                            // Priority 4: Reviews Complete (reviewersCount > 0 AND completedReviews === reviewersCount)
                                            $statusConfig = [
                                                'class' => 'border-green-400 bg-green-50',
                                                'icon' => 'fa-check-circle text-green-500',
                                                'title' => 'Reviews Complete',
                                                'message' => 'All reviewers have completed their review. A decision can now be made.',
                                            ];
                                        }
                                    @endphp
                                    <div class="border-l-4 {{ $statusConfig['class'] }} p-4 rounded-r-lg">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <i class="fa-solid {{ $statusConfig['icon'] }} text-lg"></i>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-semibold text-gray-900">
                                                    Round {{ $displayRoundNumber }} Status:
                                                    {{ $statusConfig['title'] }}
                                                </h3>
                                                <p class="mt-1 text-sm text-gray-600">
                                                    {{ $statusConfig['message'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Reviewers Panel --}}
                            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                                <div
                                    class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                    <h3 class="text-base font-bold text-gray-900">
                                        <i class="fa-solid fa-user-check text-indigo-500 mr-2"></i>Reviewers
                                    </h3>
                                    @if (auth()->user()->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id))
                                        <a href="{{ route('journal.workflow.assign-reviewer-page', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                            class="text-sm text-indigo-600 font-medium hover:text-indigo-800 flex items-center">
                                            <i class="fa-solid fa-plus mr-1"></i> Add Reviewer
                                        </a>
                                    @endif
                                </div>
                                <div class="divide-y divide-gray-200">
                                    @forelse($submission->reviewAssignments->where('round', $selectedRoundNumber) as $assignment)
                                        <div x-data="{ expanded: false }" class="group transition-all duration-200">
                                            {{-- Summary Row --}}
                                            <div @click="expanded = !expanded" 
                                                 class="px-6 py-4 flex items-center justify-between cursor-pointer hover:bg-gray-50 transition-colors">
                                                <div class="flex items-center flex-1 min-w-0">
                                                    <i class="fa-solid fa-chevron-right text-xs text-gray-400 mr-4 transition-transform duration-200"
                                                       :class="expanded ? 'rotate-90' : ''"></i>
                                                    
                                                    <div class="flex items-center">
                                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-50 to-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm border border-indigo-200 shadow-sm">
                                                            {{ strtoupper(mb_substr($assignment->reviewer->name ?? 'R', 0, 1)) }}
                                                        </div>
                                                        <div class="ml-4 truncate">
                                                            <div class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                                                {{ $assignment->reviewer->name ?? 'Unknown' }}
                                                            </div>
                                                            <div class="flex items-center gap-2 mt-0.5">
                                                                @php
                                                                    $statusColors = [
                                                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                                        'accepted' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                                        'completed' => 'bg-green-100 text-green-800 border-green-200',
                                                                        'declined' => 'bg-red-100 text-red-800 border-red-200',
                                                                        'cancelled' => 'bg-gray-100 text-gray-600 border-gray-200',
                                                                    ];
                                                                @endphp
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusColors[$assignment->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                                                    {{ $assignment->status_label }}
                                                                </span>
                                                                @if($assignment->due_date)
                                                                    <span class="text-[11px] {{ $assignment->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                                                        {{ $assignment->isOverdue() ? 'Overdue: ' : 'Due: ' }}{{ $assignment->due_date->format('M d') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-4">
                                                    @if ($assignment->recommendation)
                                                        <div class="hidden sm:block text-right">
                                                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Recommendation</span>
                                                            <span class="text-xs font-bold text-{{ $assignment->recommendation_color }}-600">
                                                                {{ $assignment->recommendation_label }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div class="hidden sm:block text-right">
                                                            <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Method</span>
                                                            <span class="text-xs font-medium text-gray-600">
                                                                {{ ucfirst($assignment->review_method) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Expanded Actions Area --}}
                                            <div x-show="expanded" x-collapse x-cloak>
                                                <div class="px-6 py-5 bg-gray-50/80 border-t border-gray-100">
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                                        {{-- Primary Action: Login As --}}
                                                        <form action="{{ route('journal.users.login-as', ['journal' => $journal->slug, 'user' => $assignment->reviewer_id]) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="journal_id" value="{{ $journal->id }}">
                                                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                                                                <i class="fa-solid fa-user-secret mr-2"></i> Login As Reviewer
                                                            </button>
                                                        </form>

                                                        {{-- Review Details --}}
                                                        @if ($assignment->status === 'completed' || $assignment->recommendation)
                                                            <button type="button" @click='openReviewDetailsModal({{ json_encode($assignment) }})'
                                                                class="flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                                                                <i class="fa-solid fa-eye text-indigo-500 mr-2"></i> Review Details
                                                            </button>
                                                        @endif

                                                        {{-- Edit Assignment --}}
                                                        <button type="button" @click='openEditReviewModal({{ json_encode($assignment) }})'
                                                            class="flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                                                            <i class="fa-solid fa-calendar-check text-indigo-500 mr-2"></i> Edit Assignment
                                                        </button>

                                                        {{-- Unassign --}}
                                                        @if (!in_array($assignment->status, ['completed', 'declined', 'cancelled']))
                                                            <form action="{{ route('journal.workflow.unassign-reviewer', ['journal' => $journal->slug, 'submission' => $submission->slug, 'assignment' => $assignment->id]) }}"
                                                                method="POST" onsubmit="return confirm('Remove this reviewer?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 bg-white border border-red-100 text-red-600 text-xs font-bold rounded-lg hover:bg-red-50 transition-colors shadow-sm">
                                                                    <i class="fa-solid fa-user-minus mr-2"></i> Unassign
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-6 py-12 text-center">
                                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-50 mb-3">
                                                <i class="fa-solid fa-user-slash text-gray-300"></i>
                                            </div>
                                            <p class="text-sm text-gray-500 italic">No reviewers assigned to this round yet.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Review Files Panel --}}
                            @php
                                // Filter review files by selected round
                                $reviewFiles = $submission->files
                                    ->where('stage', 'review')
                                    ->filter(function ($file) use ($selectedRoundNumber) {
                                        $metadata = $file->metadata ?? [];
                                        // File with review_round metadata - show only for matching round
                                        if (isset($metadata['review_round'])) {
                                            return $metadata['review_round'] == $selectedRoundNumber;
                                        }
                                        // Original files (no round metadata) - show for Round 1 only
                                        return $selectedRoundNumber == 1;
                                    });
                            @endphp
                            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                                <div
                                    class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                    <h3 class="text-base font-bold text-gray-900">
                                        <i class="fa-solid fa-file-lines text-indigo-500 mr-2"></i>Review Files
                                        <span class="text-xs text-gray-500 font-normal ml-2">(Round
                                            {{ $selectedRoundNumber }})</span>
                                    </h3>
                                </div>
                                <div class="p-6 overflow-x-auto">
                                    @forelse($reviewFiles as $file)
                                        <div
                                            class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors min-w-max">
                                            <div class="flex items-center">
                                                @php
                                                    $extension = strtolower(
                                                        pathinfo($file->file_name, PATHINFO_EXTENSION),
                                                    );
                                                    $iconClass = match ($extension) {
                                                        'pdf' => 'fa-file-pdf text-red-500',
                                                        'doc', 'docx' => 'fa-file-word text-blue-500',
                                                        'xls', 'xlsx' => 'fa-file-excel text-green-500',
                                                        'ppt', 'pptx' => 'fa-file-powerpoint text-orange-500',
                                                        default => 'fa-file-lines text-gray-500',
                                                    };
                                                    $viewableExtensions = [
                                                        'pdf',
                                                        'doc',
                                                        'docx',
                                                        'xls',
                                                        'xlsx',
                                                        'ppt',
                                                        'pptx',
                                                        'odt',
                                                    ];
                                                    $isViewable = in_array($extension, $viewableExtensions);
                                                @endphp
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center mr-3">
                                                    <i class="fa-solid {{ $iconClass }}"></i>
                                                </div>
                                                <div>
                                                    <span
                                                        class="text-sm font-medium text-gray-700">{{ $file->file_name }}</span>
                                                    <p class="text-xs text-gray-500">
                                                        @if (isset($file->metadata['promoted_from']))
                                                            <span class="text-purple-600"><i
                                                                    class="fa-solid fa-arrow-up-from-bracket mr-1"></i>Promoted</span>
                                                            •
                                                        @endif
                                                        {{ number_format($file->file_size / 1024, 0) }} KB
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                @if ($isViewable)
                                                    <a href="{{ route('files.preview', $file) }}" title="Preview"
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-indigo-600 hover:bg-indigo-50">
                                                        <i class="fa-solid fa-eye text-sm"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('files.download', $file) }}" title="Download"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded text-gray-400 hover:text-emerald-600 hover:bg-emerald-50">
                                                    <i class="fa-solid fa-download text-sm"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 italic text-center py-4">No review files
                                            for Round {{ $selectedRoundNumber }}.
                                        </p>
                                    @endforelse
                                </div>
                            </div>

                            {{-- ==================== REVISIONS GRID (Author's Uploaded Revisions) ==================== --}}
                            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                                <div
                                    class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-teal-50 to-emerald-50">
                                    <h3 class="text-base font-bold text-gray-900">
                                        <i class="fa-solid fa-file-circle-check text-teal-500 mr-2"></i>Revisions
                                        <span class="text-xs text-gray-500 font-normal ml-2">(Round
                                            {{ $selectedRoundNumber }})</span>
                                        @if ($authorRevisionFiles->isNotEmpty())
                                            <span
                                                class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-700">
                                                {{ $authorRevisionFiles->count() }} file(s)
                                            </span>
                                        @endif
                                    </h3>
                                    @if ($authorRevisionFiles->isNotEmpty())
                                        <button type="button" @click="openNewRoundModal()"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-600 bg-white border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">
                                            <i class="fa-solid fa-arrow-right-to-bracket mr-1.5"></i> Send to
                                            Review
                                        </button>
                                    @endif
                                </div>
                                <div class="p-6">
                                    @forelse($authorRevisionFiles as $file)
                                        <div
                                            class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 rounded-lg px-3 -mx-3 transition-colors">
                                            <div class="flex items-center gap-3">
                                                @php
                                                    $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                                                    $iconClass = match ($ext) {
                                                        'pdf' => 'fa-file-pdf text-red-500',
                                                        'doc', 'docx' => 'fa-file-word text-blue-500',
                                                        default => 'fa-file text-gray-500',
                                                    };
                                                @endphp
                                                <div
                                                    class="w-9 h-9 rounded-lg bg-teal-50 flex items-center justify-center">
                                                    <i class="fa-solid {{ $iconClass }}"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $file->file_name }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        Uploaded by <span
                                                            class="font-medium">{{ $file->uploader?->name ?? 'Author' }}</span>
                                                        • {{ $file->created_at->format('M d, Y - H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="{{ route('files.download', $file) }}"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                                <i class="fa-solid fa-download mr-1.5"></i> Download
                                            </a>
                                        </div>
                                    @empty
                                        <div class="text-center py-8">
                                            <i class="fa-solid fa-clock-rotate-left text-gray-300 text-3xl"></i>
                                            <p class="text-sm text-gray-500 mt-3">No revisions for Round
                                                {{ $selectedRoundNumber }}.
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">The author will upload revised
                                                files
                                                after revisions are requested.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- Review Discussions - Stage 2 --}}
                            <x-discussion-panel :submission="$submission" :stageId="2" stageName="review"
                                :discussions="$allDiscussions" :participants="$participants" :journal="$journal" />
                        </div>

                        {{-- Sidebar --}}
                        <div class="lg:col-span-1 space-y-6">
                            {{-- Editor Decision Panel --}}
                            @if (auth()->user()->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id))
                                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">
                                        Editor
                                        Decision</h4>

                                    @if ($submission->stage_id == 2 && $submission->status != 3)
                                        {{-- Active - Stage 2 and not declined --}}
                                        <div class="space-y-3">
                                            <button type="button" @click="openAcceptModal()"
                                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                                <i class="fa-solid fa-check mr-2"></i> Accept Submission
                                            </button>
                                            {{-- Request Revisions - Opens Modal --}}
                                            <button type="button" @click="openRevisionModal()"
                                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-yellow-300 shadow-sm text-sm font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100">
                                                <i class="fa-solid fa-pen mr-2"></i> Request Revisions
                                            </button>
                                            <form
                                                action="{{ route('journal.workflow.record-decision', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                                method="POST"
                                                onsubmit="return confirm('This will decline the submission. Continue?')">
                                                @csrf
                                                <input type="hidden" name="decision" value="decline">
                                                <button type="submit"
                                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                                    <i class="fa-solid fa-xmark mr-2"></i> Decline
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        {{-- Disabled - Stage has passed or submission declined --}}
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-4">
                                            <p class="text-sm text-gray-600 flex items-center">
                                                <i class="fa-solid fa-check-circle text-gray-400 mr-2"></i>
                                                @if ($submission->status == 3)
                                                    Submission has been declined.
                                                @elseif($submission->stage_id > 2)
                                                    Review stage complete. Moved to
                                                    <strong
                                                        class="ml-1">{{ ucfirst($stageNames[$submission->stage_id] ?? 'next stage') }}</strong>.
                                                @else
                                                    Awaiting review stage.
                                                @endif
                                            </p>
                                        </div>
                                        <button disabled
                                            class="w-full mb-2 px-4 py-2 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed text-sm font-medium">
                                            <i class="fa-solid fa-check mr-2"></i> Accept Submission
                                        </button>
                                        <button disabled
                                            class="w-full mb-2 px-4 py-2 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed text-sm font-medium">
                                            <i class="fa-solid fa-pen mr-2"></i> Request Revisions
                                        </button>
                                        <button disabled
                                            class="w-full px-4 py-2 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed text-sm font-medium">
                                            <i class="fa-solid fa-xmark mr-2"></i> Decline
                                        </button>
                                    @endif
                                </div>
                            @endif

                            {{-- Participants (Modernized - OJS 3.3 Style) --}}
                            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Participants
                                    </h4>
                                    @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                        <button @click="assignEditorModalOpen = true; resetEditorModal()"
                                            class="text-xs font-medium px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors">
                                            <i class="fa-solid fa-plus text-xs mr-1"></i> Assign
                                        </button>
                                    @endjournalPermission
                                </div>

                                @php
                                    // Group participants by role
                                    $groupedParticipants = [
                                        'Journal Manager' => [],
                                        'Editor' => [],
                                        'Section Editor' => [],
                                        'Author' => [],
                                        'Reviewer' => [],
                                    ];

                                    // Add editorial assignments
                                    foreach (
                                        $submission->editorialAssignments->where('is_active', true)
                                        as $assignment
                                    ) {
                                        $roleName = ucfirst(str_replace('_', ' ', $assignment->role));
                                        if (!isset($groupedParticipants[$roleName])) {
                                            $groupedParticipants[$roleName] = [];
                                        }
                                        $groupedParticipants[$roleName][] = [
                                            'user' => $assignment->user,
                                            'role' => $roleName,
                                            'assignment_id' => $assignment->id,
                                            'type' => 'editorial',
                                        ];
                                    }

                                    // Add submitting author
                                    if ($submission->authors->first()) {
                                        $groupedParticipants['Author'][] = [
                                            'user' => $submission->authors->first(),
                                            'role' => 'Author',
                                            'type' => 'author',
                                        ];
                                    }

                                    // Role colors and initials
                                    $roleColors = [
                                        'Journal Manager' => 'bg-purple-500',
                                        'Editor' => 'bg-blue-500',
                                        'Section Editor' => 'bg-indigo-500',
                                        'Author' => 'bg-amber-500',
                                        'Reviewer' => 'bg-emerald-500',
                                    ];

                                    $userIsEditor = auth()
                                        ->user()
                                        ->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id);
                                    $userIsSuperAdmin = auth()
                                        ->user()
                                        ->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER], $journal->id);
                                @endphp

                                <div class="space-y-4">
                                    @foreach ($groupedParticipants as $role => $members)
                                        @if (count($members) > 0)
                                            {{-- Role Group Header --}}
                                            <div>
                                                <h5
                                                    class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                                                    {{ $role }}
                                                </h5>
                                                <div class="space-y-2">
                                                    @foreach ($members as $member)
                                                        @php
                                                            $user = $member['user'];
                                                            $isCurrentUser = $user->id === auth()->id();
                                                            $initials = strtoupper(substr($user->name, 0, 1));
                                                            if (str_contains($user->name, ' ')) {
                                                                $parts = explode(' ', $user->name);
                                                                $initials = strtoupper(
                                                                    substr($parts[0], 0, 1) .
                                                                        substr($parts[1] ?? '', 0, 1),
                                                                );
                                                            }
                                                            $avatarColor = $roleColors[$role] ?? 'bg-gray-500';
                                                        @endphp
                                                        {{-- User Item --}}
                                                        <div
                                                            class="flex items-center gap-3 py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                                            {{-- Avatar --}}
                                                            <div
                                                                class="w-9 h-9 rounded-full {{ $avatarColor }} flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                                                {{ $initials }}
                                                            </div>
                                                            {{-- Name --}}
                                                            <div class="flex-1 min-w-0">
                                                                <p
                                                                    class="text-sm font-semibold text-gray-900 truncate">
                                                                    {{ $user->name }}
                                                                    @if ($isCurrentUser)
                                                                        <span
                                                                            class="text-xs text-indigo-600 font-normal">(You)</span>
                                                                    @endif
                                                                </p>
                                                                <p class="text-xs text-gray-500 truncate">
                                                                    {{ $user->email }}
                                                                </p>
                                                            </div>

                                                            {{-- Editor View: Full Action Dropdown --}}
                                                            @if ($userIsEditor && !$isCurrentUser)
                                                                <div class="relative" x-data="{ openDropdown: false }">
                                                                    <button @click="openDropdown = !openDropdown"
                                                                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded transition-colors">
                                                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                                                    </button>

                                                                    {{-- Dropdown Menu --}}
                                                                    <div x-show="openDropdown"
                                                                        @click.away="openDropdown = false"
                                                                        x-transition:enter="transition ease-out duration-100"
                                                                        x-transition:enter-start="transform opacity-0 scale-95"
                                                                        x-transition:enter-end="transform opacity-100 scale-100"
                                                                        x-transition:leave="transition ease-in duration-75"
                                                                        x-transition:leave-start="transform opacity-100 scale-100"
                                                                        x-transition:leave-end="transform opacity-0 scale-95"
                                                                        class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
                                                                        style="display: none;">
                                                                        <div class="py-1">
                                                                            {{-- Notify Action --}}
                                                                            @if ($user->exists)
                                                                                <button type="button"
                                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                                                    <i
                                                                                        class="fa-solid fa-envelope text-indigo-500 w-4"></i>
                                                                                    <span>Send Email</span>
                                                                                </button>
                                                                            @endif

                                                                            {{-- Login As (Super Admin Only) --}}
                                                                            @if ($userIsSuperAdmin && $user->exists)
                                                                                <form
                                                                                    action="{{ route('journal.users.login-as', ['journal' => $journal->slug, 'user' => $user->id]) }}"
                                                                                    method="POST" class="inline">
                                                                                    @csrf
                                                                                    <button type="submit"
                                                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                                                        <i
                                                                                            class="fa-solid fa-user-shield text-purple-500 w-4"></i>
                                                                                        <span>Login As User</span>
                                                                                    </button>
                                                                                </form>
                                                                            @endif

                                                                            <div class="border-t border-gray-100">
                                                                            </div>

                                                                            {{-- Remove Action (Not for Authors) --}}
                                                                            @if ($member['type'] === 'editorial')
                                                                                <form method="POST"
                                                                                    action="{{ route('journal.workflow.remove-editor', ['journal' => $journal->slug, 'submission' => $submission->slug, 'assignment' => $member['assignment_id']]) }}"
                                                                                    onsubmit="return confirm('Are you sure you want to remove this participant? This action cannot be undone.');">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit"
                                                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                                                        <i
                                                                                            class="fa-solid fa-trash w-4"></i>
                                                                                        <span>Remove</span>
                                                                                    </button>
                                                                                </form>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    @if (array_sum(array_map('count', $groupedParticipants)) === 0)
                                        <p class="text-sm text-gray-400 italic text-center py-4">No participants
                                            assigned
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif {{-- End of isAuthorView conditional --}}
                </div>
            @endif
            </div>

            {{-- ==================== COPYEDITING STAGE ==================== --}}
            <div x-show="activeStage === 'copyediting'" class="bg-gray-50/50 min-h-screen pt-6">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    {{-- Main Panel --}}
                    <div class="lg:col-span-3 space-y-6">

                        {{-- Draft Files (from Review Stage) - EDITOR ONLY --}}
                        @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-base font-bold text-gray-900">
                                                <i class="fa-solid fa-file-import text-blue-500 mr-2"></i>Draft Files
                                            </h3>
                                            <p class="text-xs text-gray-600 mt-1">Files from the review stage, ready for
                                                copyediting</p>
                                        </div>
                                        <button @click="draftFilesModalOpen = true"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="fa-solid fa-plus mr-1.5"></i>
                                            Upload/Select Files
                                        </button>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    File
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Type
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Date Added
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($submission->files->where('stage', 'copyedit_draft') as $file)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center">
                                                            @php
                                                                $extension = strtolower(
                                                                    pathinfo($file->file_name, PATHINFO_EXTENSION),
                                                                );
                                                                $iconClass = match ($extension) {
                                                                    'pdf' => 'fa-file-pdf text-red-500',
                                                                    'doc', 'docx' => 'fa-file-word text-blue-500',
                                                                    'xls', 'xlsx' => 'fa-file-excel text-green-500',
                                                                    'ppt',
                                                                    'pptx'
                                                                        => 'fa-file-powerpoint text-orange-500',
                                                                    default => 'fa-file-lines text-gray-500',
                                                                };
                                                            @endphp
                                                            <div
                                                                class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center mr-3 flex-shrink-0">
                                                                <i class="fa-solid {{ $iconClass }} text-lg"></i>
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <div class="text-sm font-medium text-gray-900 truncate">
                                                                    {{ $file->file_name }}
                                                                </div>
                                                                <div class="text-xs text-gray-500">
                                                                    {{ number_format($file->file_size / 1024, 0) }} KB
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ $file->file_type_label }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $file->created_at->format('M d, Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <div class="flex items-center justify-end gap-2">
                                                            @php
                                                                $viewableExtensions = [
                                                                    'pdf',
                                                                    'doc',
                                                                    'docx',
                                                                    'xls',
                                                                    'xlsx',
                                                                    'ppt',
                                                                    'pptx',
                                                                    'odt',
                                                                ];
                                                                $isViewable = in_array($extension, $viewableExtensions);
                                                            @endphp
                                                            @if ($isViewable)
                                                                <a href="{{ route('files.preview', $file) }}"
                                                                    title="Preview"
                                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                                                                    <i class="fa-solid fa-eye"></i>
                                                                </a>
                                                            @endif
                                                            <a href="{{ route('files.download', $file) }}"
                                                                title="Download"
                                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 transition-colors">
                                                                <i class="fa-solid fa-download"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4"
                                                        class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                                        No draft files available. Files will appear here after acceptance
                                                        from the Review stage.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endjournalPermission

                        {{-- Copyedited Files (Final Edited Versions) --}}
                        <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 bg-teal-50">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">
                                            <i class="fa-solid fa-file-pen text-teal-500 mr-2"></i>Copyedited
                                        </h3>
                                        <p class="text-xs text-gray-600 mt-1">Final files that have undergone
                                            copyediting, ready for Production</p>
                                    </div>
                                    {{-- Authors can also upload copyedited files --}}
                                    @if (auth()->user()->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id) ||
                                            $submission->user_id === auth()->id())
                                        <button @click="fileModalOpen = true; uploadStage = 'copyedited'"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                            <i class="fa-solid fa-upload mr-1.5"></i>
                                            Upload Copyedited File
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Type
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date Added
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($submission->files->where('stage', 'copyedited') as $file)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        @php
                                                            $extension = strtolower(
                                                                pathinfo($file->file_name, PATHINFO_EXTENSION),
                                                            );
                                                            $iconClass = match ($extension) {
                                                                'pdf' => 'fa-file-pdf text-red-500',
                                                                'doc', 'docx' => 'fa-file-word text-blue-500',
                                                                'xls', 'xlsx' => 'fa-file-excel text-green-500',
                                                                'ppt', 'pptx' => 'fa-file-powerpoint text-orange-500',
                                                                default => 'fa-file-lines text-gray-500',
                                                            };
                                                        @endphp
                                                        <div
                                                            class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center mr-3 flex-shrink-0">
                                                            <i class="fa-solid {{ $iconClass }} text-lg"></i>
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <div class="text-sm font-medium text-gray-900 truncate">
                                                                {{ $file->file_name }}
                                                            </div>
                                                            <div class="text-xs text-gray-500">
                                                                {{ number_format($file->file_size / 1024, 0) }} KB
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                                        {{ $file->file_type_label }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $file->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end gap-2">
                                                        @php
                                                            $viewableExtensions = [
                                                                'pdf',
                                                                'doc',
                                                                'docx',
                                                                'xls',
                                                                'xlsx',
                                                                'ppt',
                                                                'pptx',
                                                                'odt',
                                                            ];
                                                            $isViewable = in_array($extension, $viewableExtensions);
                                                        @endphp
                                                        @if ($isViewable)
                                                            <a href="{{ route('files.preview', $file) }}"
                                                                title="Preview"
                                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                                                                <i class="fa-solid fa-eye"></i>
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('files.download', $file) }}"
                                                            title="Download"
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 transition-colors">
                                                            <i class="fa-solid fa-download"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4"
                                                    class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                                    No copyedited files yet. Upload the final edited versions here.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Copyediting Discussions - Stage 3 --}}
                        <x-discussion-panel :submission="$submission" :stageId="3" stageName="copyediting"
                            :discussions="$allDiscussions" :participants="$participants" :journal="$journal" />
                    </div>

                    {{-- Sidebar --}}
                    <div class="lg:col-span-1 space-y-6">
                        @if (auth()->user()->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id))
                            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Workflow
                                    Actions
                                </h4>
                                @if ($submission->stage_id == 3 && $submission->status != 3)
                                    {{-- Info Box --}}
                                    <div class="bg-teal-50 border border-teal-200 rounded-lg p-3 mb-4">
                                        <div class="flex items-start">
                                            <i class="fa-solid fa-info-circle text-teal-500 mt-0.5 mr-2"></i>
                                            <p class="text-xs text-teal-700">
                                                Promote <strong>Copyedited</strong> files to Production stage when
                                                editing is complete.
                                            </p>
                                        </div>
                                    </div>
                                    <button @click="openSendToProductionModal()"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-teal-600 hover:bg-teal-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                        <i class="fa-solid fa-arrow-right mr-2"></i> Send to Production
                                    </button>
                                @else
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-4">
                                        <p class="text-sm text-gray-600 flex items-center">
                                            <i class="fa-solid fa-check-circle text-gray-400 mr-2"></i>
                                            @if ($submission->status == 3)
                                                Submission has been declined.
                                            @elseif($submission->stage_id > 3)
                                                Copyediting complete. Moved to Production.
                                            @else
                                                Awaiting copyediting stage.
                                            @endif
                                        </p>
                                    </div>
                                    <button disabled
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed text-sm font-medium">
                                        <i class="fa-solid fa-arrow-right mr-2"></i> Send to Production
                                    </button>
                                @endif
                            </div>
                        @endif

                        {{-- Participants (Modernized - OJS 3.3 Style) --}}
                        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Participants
                                </h4>
                                @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                    <button @click="assignEditorModalOpen = true; resetEditorModal()"
                                        class="text-xs font-medium px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors">
                                        <i class="fa-solid fa-plus text-xs mr-1"></i> Assign
                                    </button>
                                @endjournalPermission
                            </div>

                            @php
                                // Group participants by role
                                $groupedParticipants = [
                                    'Journal Manager' => [],
                                    'Editor' => [],
                                    'Section Editor' => [],
                                    'Author' => [],
                                    'Reviewer' => [],
                                ];

                                // Add editorial assignments
                                foreach ($submission->editorialAssignments->where('is_active', true) as $assignment) {
                                    $roleName = ucfirst(str_replace('_', ' ', $assignment->role));
                                    if (!isset($groupedParticipants[$roleName])) {
                                        $groupedParticipants[$roleName] = [];
                                    }
                                    $groupedParticipants[$roleName][] = [
                                        'user' => $assignment->user,
                                        'role' => $roleName,
                                        'assignment_id' => $assignment->id,
                                        'type' => 'editorial',
                                    ];
                                }

                                // Add submitting author
                                if ($submission->authors->first()) {
                                    $groupedParticipants['Author'][] = [
                                        'user' => $submission->authors->first(),
                                        'role' => 'Author',
                                        'type' => 'author',
                                    ];
                                }

                                // Role colors and initials
                                $roleColors = [
                                    'Journal Manager' => 'bg-purple-500',
                                    'Editor' => 'bg-blue-500',
                                    'Section Editor' => 'bg-indigo-500',
                                    'Author' => 'bg-amber-500',
                                    'Reviewer' => 'bg-emerald-500',
                                ];

                                $userIsEditor = auth()
                                    ->user()
                                    ->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id);
                                $userIsSuperAdmin = auth()
                                    ->user()
                                    ->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER], $journal->id);
                            @endphp

                            <div class="space-y-4">
                                @foreach ($groupedParticipants as $role => $members)
                                    @if (count($members) > 0)
                                        {{-- Role Group Header --}}
                                        <div>
                                            <h5
                                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                                                {{ $role }}
                                            </h5>
                                            <div class="space-y-2">
                                                @foreach ($members as $member)
                                                    @php
                                                        $user = $member['user'];
                                                        $isCurrentUser = $user->id === auth()->id();
                                                        $initials = strtoupper(substr($user->name, 0, 1));
                                                        if (str_contains($user->name, ' ')) {
                                                            $parts = explode(' ', $user->name);
                                                            $initials = strtoupper(
                                                                substr($parts[0], 0, 1) . substr($parts[1] ?? '', 0, 1),
                                                            );
                                                        }
                                                        $avatarColor = $roleColors[$role] ?? 'bg-gray-500';
                                                    @endphp
                                                    {{-- User Item --}}
                                                    <div
                                                        class="flex items-center gap-3 py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                                        {{-- Avatar --}}
                                                        <div
                                                            class="w-9 h-9 rounded-full {{ $avatarColor }} flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                                            {{ $initials }}
                                                        </div>
                                                        {{-- Name --}}
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-semibold text-gray-900 truncate">
                                                                {{ $user->name }}
                                                                @if ($isCurrentUser)
                                                                    <span
                                                                        class="text-xs text-indigo-600 font-normal">(You)</span>
                                                                @endif
                                                            </p>
                                                            <p class="text-xs text-gray-500 truncate">
                                                                {{ $user->email }}
                                                            </p>
                                                        </div>

                                                        {{-- Editor View: Full Action Dropdown --}}
                                                        @if ($userIsEditor && !$isCurrentUser)
                                                            <div class="relative" x-data="{ openDropdown: false }">
                                                                <button @click="openDropdown = !openDropdown"
                                                                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded transition-colors">
                                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                                </button>

                                                                {{-- Dropdown Menu --}}
                                                                <div x-show="openDropdown"
                                                                    @click.away="openDropdown = false"
                                                                    x-transition:enter="transition ease-out duration-100"
                                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                                    x-transition:leave="transition ease-in duration-75"
                                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                                    x-transition:leave-end="transform opacity-0 scale-95"
                                                                    class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
                                                                    style="display: none;">
                                                                    <div class="py-1">
                                                                        {{-- Notify Action --}}
                                                                        @if ($user->exists)
                                                                            <button type="button"
                                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                                                <i
                                                                                    class="fa-solid fa-envelope text-indigo-500 w-4"></i>
                                                                                <span>Send Email</span>
                                                                            </button>
                                                                        @endif

                                                                        {{-- Login As (Super Admin Only) --}}
                                                                        @if ($userIsSuperAdmin && $user->exists)
                                                                            <form
                                                                                action="{{ route('journal.users.login-as', ['journal' => $journal->slug, 'user' => $user->id]) }}"
                                                                                method="POST" class="inline">
                                                                                @csrf
                                                                                <button type="submit"
                                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                                                    <i
                                                                                        class="fa-solid fa-user-shield text-purple-500 w-4"></i>
                                                                                    <span>Login As User</span>
                                                                                </button>
                                                                            </form>
                                                                        @endif

                                                                        <div class="border-t border-gray-100"></div>

                                                                        {{-- Remove Action (Not for Authors) --}}
                                                                        @if ($member['type'] === 'editorial')
                                                                            <form method="POST"
                                                                                action="{{ route('journal.workflow.remove-editor', ['journal' => $journal->slug, 'submission' => $submission->slug, 'assignment' => $member['assignment_id']]) }}"
                                                                                onsubmit="return confirm('Are you sure you want to remove this participant? This action cannot be undone.');">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                                                    <i
                                                                                        class="fa-solid fa-trash w-4"></i>
                                                                                    <span>Remove</span>
                                                                                </button>
                                                                            </form>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                @if (array_sum(array_map('count', $groupedParticipants)) === 0)
                                    <p class="text-sm text-gray-400 italic text-center py-4">No participants
                                        assigned
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $issueOptions = ($issues ?? collect())->map(function ($i) {
                    $title = $i->title ? " - {$i->title}" : '';
                    $status = $i->is_published ? ' [Published]' : ' [Future]';
                    return [
                        'id' => $i->id,
                        'label' => "Vol {$i->volume}, No {$i->number} ({$i->year}){$title}{$status}",
                        'is_published' => $i->is_published,
                    ];
                });
            @endphp
            {{-- ==================== PRODUCTION STAGE ==================== --}}
            <div x-show="activeStage === 'production'" class="bg-gray-50/50 min-h-screen pt-6"
                x-data="{
                    scheduleModalOpen: false,
                    issues: {{ json_encode($issueOptions) }},
                    selectedIssueId: '{{ $submission->issue_id ?? '' }}',
                    isLoadingIssues: false,
                
                    openScheduleModal() {
                        this.scheduleModalOpen = true;
                    }
                }">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                    {{-- Main Panel Area --}}
                    <div class="lg:col-span-3 space-y-6">


                        {{-- ====== PRODUCTION READY FILES PANEL ====== --}}
                        <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                            <div
                                class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">
                                        <i class="fa-solid fa-file-circle-check text-emerald-500 mr-2"></i>Production
                                        Ready Files
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Edited files promoted from Copyediting.
                                        Download these to create final galleys (PDF/HTML).</p>
                                </div>
                            </div>

                            {{-- Production Ready Files Table --}}
                            @php
                                $productionReadyFiles = $submission->files->whereIn('stage', [
                                    'production',
                                    'production_ready',
                                ]);
                            @endphp
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                File Name</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Size</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                Uploaded</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($productionReadyFiles as $file)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <div
                                                            class="flex-shrink-0 w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center mr-3">
                                                            <i class="fa-solid fa-file-lines text-emerald-600"></i>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $file->file_name }}
                                                            </p>
                                                            <p class="text-xs text-gray-500">
                                                                {{ $file->file_type_label ?? ucfirst($file->file_type ?? 'Document') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ number_format($file->file_size / 1024, 0) }} KB
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $file->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <a href="{{ route('files.download', $file) }}" target="_blank"
                                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors"
                                                        title="Download">
                                                        <i class="fa-solid fa-download mr-1.5"></i> Download
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-12 text-center">
                                                    <div class="flex flex-col items-center">
                                                        <div
                                                            class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                                            <i
                                                                class="fa-solid fa-folder-open text-gray-400 text-xl"></i>
                                                        </div>
                                                        <p class="text-sm font-medium text-gray-900">No
                                                            production-ready
                                                            files yet</p>
                                                        <p class="text-xs text-gray-500 mt-1 max-w-xs">Files will
                                                            appear
                                                            here after the "Send to Production" action from Copyediting
                                                            stage.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        {{-- Production Discussions - Stage 4 --}}
                        <x-discussion-panel :submission="$submission" :stageId="4" stageName="production"
                            :discussions="$allDiscussions" :participants="$participants" :journal="$journal" />

                    </div>

                    {{-- ====== SIDEBAR ====== --}}
                    <div class="lg:col-span-1 space-y-6">

                        {{-- Publication Status Card --}}
                        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Publication
                            </h4>

                            @if ($submission->status === 'published')
                                {{-- Published State --}}
                                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-check-circle text-emerald-500 text-xl mr-3"></i>
                                        <div>
                                            <p class="text-sm font-semibold text-emerald-800">Published</p>
                                            <p class="text-xs text-emerald-600">
                                                {{ $submission->published_at?->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @if ($submission->issue)
                                    <p class="text-sm text-gray-600 mb-4">
                                        <i class="fa-solid fa-book text-gray-400 mr-2"></i>
                                        {{ $submission->issue->identifier }}
                                    </p>
                                @endif
                                @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                    <form
                                        action="{{ route('journal.workflow.unpublish', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to unpublish this submission?')"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-200 text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 transition-colors">
                                            <i class="fa-solid fa-eye-slash mr-2"></i> Unpublish
                                        </button>
                                    </form>
                                @endjournalPermission
                            @elseif($submission->issue_id)
                                {{-- Scheduled State --}}
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-calendar-check text-blue-500 text-xl mr-3"></i>
                                        <div>
                                            <p class="text-sm font-semibold text-blue-800">Scheduled</p>
                                            <p class="text-xs text-blue-600">{{ $submission->issue->identifier }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                    @if (!$submission->hasGalleys())
                                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                                            <p class="text-xs text-amber-700">
                                                <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                                Upload at least one galley to publish.
                                            </p>
                                        </div>
                                    @endif

                                    <div class="space-y-2">
                                        <form
                                            action="{{ route('journal.workflow.publish', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" {{ !$submission->hasGalleys() ? 'disabled' : '' }}
                                                class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white {{ $submission->hasGalleys() ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-gray-300 cursor-not-allowed' }} transition-colors">
                                                <i class="fa-solid fa-rocket mr-2"></i> Publish Now
                                            </button>
                                        </form>

                                        <form
                                            action="{{ route('journal.workflow.unschedule', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-600 bg-white hover:bg-gray-50 transition-colors">
                                                <i class="fa-solid fa-calendar-xmark mr-2"></i> Unschedule
                                            </button>
                                        </form>
                                    </div>
                                @endjournalPermission
                            @else
                                {{-- Not Scheduled State --}}
                                <p class="text-sm text-gray-500 mb-4">
                                    This submission is not scheduled for publication yet.
                                </p>

                                @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                    <button @click="activeTab = 'publication'"
                                        class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors">
                                        <i class="fa-solid fa-arrow-right mr-2"></i> Go to Publication Tab
                                    </button>
                                    <p class="text-xs text-gray-500 mt-2 text-center">
                                        Manage galleys and schedule publication in the Publication tab.
                                    </p>
                                @endjournalPermission
                            @endif
                        </div>

                        {{-- Participants (Modernized - OJS 3.3 Style) --}}
                        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Participants
                                </h4>
                                @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                    <button @click="assignEditorModalOpen = true; resetEditorModal()"
                                        class="text-xs font-medium px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors">
                                        <i class="fa-solid fa-plus text-xs mr-1"></i> Assign
                                    </button>
                                @endjournalPermission
                            </div>

                            @php
                                // Group participants by role
                                $groupedParticipants = [
                                    'Journal Manager' => [],
                                    'Editor' => [],
                                    'Section Editor' => [],
                                    'Author' => [],
                                    'Reviewer' => [],
                                ];

                                // Add editorial assignments
                                foreach ($submission->editorialAssignments->where('is_active', true) as $assignment) {
                                    $roleName = ucfirst(str_replace('_', ' ', $assignment->role));
                                    if (!isset($groupedParticipants[$roleName])) {
                                        $groupedParticipants[$roleName] = [];
                                    }
                                    $groupedParticipants[$roleName][] = [
                                        'user' => $assignment->user,
                                        'role' => $roleName,
                                        'assignment_id' => $assignment->id,
                                        'type' => 'editorial',
                                    ];
                                }

                                // Add submitting author
                                if ($submission->authors->first()) {
                                    $groupedParticipants['Author'][] = [
                                        'user' => $submission->authors->first(),
                                        'role' => 'Author',
                                        'type' => 'author',
                                    ];
                                }

                                // Role colors and initials
                                $roleColors = [
                                    'Journal Manager' => 'bg-purple-500',
                                    'Editor' => 'bg-blue-500',
                                    'Section Editor' => 'bg-indigo-500',
                                    'Author' => 'bg-amber-500',
                                    'Reviewer' => 'bg-emerald-500',
                                ];

                                $userIsEditor = auth()
                                    ->user()
                                    ->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id);
                                $userIsSuperAdmin = auth()
                                    ->user()
                                    ->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER], $journal->id);
                            @endphp

                            <div class="space-y-4">
                                @foreach ($groupedParticipants as $role => $members)
                                    @if (count($members) > 0)
                                        {{-- Role Group Header --}}
                                        <div>
                                            <h5
                                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                                                {{ $role }}
                                            </h5>
                                            <div class="space-y-2">
                                                @foreach ($members as $member)
                                                    @php
                                                        $user = $member['user'];
                                                        $isCurrentUser = $user->id === auth()->id();
                                                        $initials = strtoupper(substr($user->name, 0, 1));
                                                        if (str_contains($user->name, ' ')) {
                                                            $parts = explode(' ', $user->name);
                                                            $initials = strtoupper(
                                                                substr($parts[0], 0, 1) . substr($parts[1] ?? '', 0, 1),
                                                            );
                                                        }
                                                        $avatarColor = $roleColors[$role] ?? 'bg-gray-500';
                                                    @endphp
                                                    {{-- User Item --}}
                                                    <div
                                                        class="flex items-center gap-3 py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                                        {{-- Avatar --}}
                                                        <div
                                                            class="w-9 h-9 rounded-full {{ $avatarColor }} flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                                            {{ $initials }}
                                                        </div>
                                                        {{-- Name --}}
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-semibold text-gray-900 truncate">
                                                                {{ $user->name }}
                                                                @if ($isCurrentUser)
                                                                    <span
                                                                        class="text-xs text-indigo-600 font-normal">(You)</span>
                                                                @endif
                                                            </p>
                                                            <p class="text-xs text-gray-500 truncate">
                                                                {{ $user->email }}
                                                            </p>
                                                        </div>

                                                        {{-- Editor View: Full Action Dropdown --}}
                                                        @if ($userIsEditor && !$isCurrentUser)
                                                            <div class="relative" x-data="{ openDropdown: false }">
                                                                <button @click="openDropdown = !openDropdown"
                                                                    class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded transition-colors">
                                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                                </button>

                                                                {{-- Dropdown Menu --}}
                                                                <div x-show="openDropdown"
                                                                    @click.away="openDropdown = false"
                                                                    x-transition:enter="transition ease-out duration-100"
                                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                                    x-transition:leave="transition ease-in duration-75"
                                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                                    x-transition:leave-end="transform opacity-0 scale-95"
                                                                    class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
                                                                    style="display: none;">
                                                                    <div class="py-1">
                                                                        {{-- Notify Action --}}
                                                                        @if ($user->exists)
                                                                            <button type="button"
                                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                                                <i
                                                                                    class="fa-solid fa-envelope text-indigo-500 w-4"></i>
                                                                                <span>Send Email</span>
                                                                            </button>
                                                                        @endif

                                                                        {{-- Login As (Super Admin Only) --}}
                                                                        @if ($userIsSuperAdmin && $user->exists)
                                                                            <form
                                                                                action="{{ route('journal.users.login-as', ['journal' => $journal->slug, 'user' => $user->id]) }}"
                                                                                method="POST" class="inline">
                                                                                @csrf
                                                                                <button type="submit"
                                                                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                                                    <i
                                                                                        class="fa-solid fa-user-shield text-purple-500 w-4"></i>
                                                                                    <span>Login As User</span>
                                                                                </button>
                                                                            </form>
                                                                        @endif

                                                                        <div class="border-t border-gray-100"></div>

                                                                        {{-- Remove Action (Not for Authors) --}}
                                                                        @if ($member['type'] === 'editorial')
                                                                            <form method="POST"
                                                                                action="{{ route('journal.workflow.remove-editor', ['journal' => $journal->slug, 'submission' => $submission->slug, 'assignment' => $member['assignment_id']]) }}"
                                                                                onsubmit="return confirm('Are you sure you want to remove this participant? This action cannot be undone.');">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                                                    <i
                                                                                        class="fa-solid fa-trash w-4"></i>
                                                                                    <span>Remove</span>
                                                                                </button>
                                                                            </form>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                @if (array_sum(array_map('count', $groupedParticipants)) === 0)
                                    <p class="text-sm text-gray-400 italic text-center py-4">No participants
                                        assigned
                                    </p>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ====== ADD GALLEY MODAL ====== --}}
                <div x-show="galleyModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto" role="dialog"
                    aria-modal="true">
                    <div
                        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div x-show="galleyModalOpen" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                            @click="galleyModalOpen = false"></div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                        <div x-show="galleyModalOpen" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fa-solid fa-file-arrow-up text-indigo-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                    <h3 class="text-lg leading-6 font-semibold text-gray-900">Add Publication
                                        Galley
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">Upload a final file for readers to
                                        download.
                                    </p>
                                </div>
                            </div>

                            <form
                                action="{{ route('journal.workflow.galley.store', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                method="POST" enctype="multipart/form-data" class="mt-5 space-y-4">
                                @csrf

                                <div>
                                    <label for="galley-label" class="block text-sm font-medium text-gray-700">
                                        Galley Label <span class="text-red-500">*</span>
                                    </label>
                                    <select name="label" id="galley-label" required
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="PDF">PDF</option>
                                        <option value="HTML">HTML</option>
                                        <option value="EPUB">EPUB</option>
                                        <option value="XML">XML (JATS)</option>
                                        <option value="MP3">MP3 (Audio)</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Choose the format type for this file.</p>
                                </div>

                                <div>
                                    <label for="galley-locale"
                                        class="block text-sm font-medium text-gray-700">Language</label>
                                    <select name="locale" id="galley-locale"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="en">English</option>
                                        <option value="id">Indonesian</option>
                                        <option value="ar">Arabic</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="galley-file" class="block text-sm font-medium text-gray-700">
                                        File <span class="text-red-500">*</span>
                                    </label>
                                    <input type="file" name="file" id="galley-file" required
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="mt-1 text-xs text-gray-500">Maximum file size: 50MB</p>
                                </div>

                                <div class="mt-5 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                                        <i class="fa-solid fa-upload mr-2"></i> Upload Galley
                                    </button>
                                    <button type="button" @click="galleyModalOpen = false"
                                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ====== SCHEDULE FOR PUBLICATION MODAL ====== --}}
                <div x-show="scheduleModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto" role="dialog"
                    aria-modal="true">
                    <div
                        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div x-show="scheduleModalOpen" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                            @click="scheduleModalOpen = false"></div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                        <div x-show="scheduleModalOpen" x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fa-solid fa-calendar-plus text-indigo-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                    <h3 class="text-lg leading-6 font-semibold text-gray-900">Schedule for
                                        Publication
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">Assign this submission to an issue.</p>
                                </div>
                            </div>

                            <form
                                action="{{ route('journal.workflow.assign-issue', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                method="POST" class="mt-5 space-y-4">
                                @csrf

                                <div>
                                    <label for="issue-select" class="block text-sm font-medium text-gray-700">
                                        Select Issue <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1 relative">
                                        <template x-if="isLoadingIssues">
                                            <div class="flex items-center justify-center py-4">
                                                <i class="fa-solid fa-spinner fa-spin text-gray-400 mr-2"></i>
                                                <span class="text-sm text-gray-500">Loading issues...</span>
                                            </div>
                                        </template>
                                        <template x-if="!isLoadingIssues && issues.length === 0">
                                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                                <p class="text-sm text-amber-700">
                                                    <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                                    No issues found. Please create an issue first.
                                                </p>
                                            </div>
                                        </template>
                                        <template x-if="!isLoadingIssues && issues.length > 0">
                                            <select name="issue_id" id="issue-select" required
                                                x-model="selectedIssueId"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <option value="">-- Select an Issue --</option>
                                                <template x-for="issue in issues" :key="issue.id">
                                                    <option :value="issue.id" x-text="issue.label"></option>
                                                </template>
                                            </select>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" id="permissions-confirmed"
                                            name="permissions_confirmed"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="permissions-confirmed" class="font-medium text-gray-700">Copyright
                                            Confirmed</label>
                                        <p class="text-gray-500">I confirm that all copyright and permissions are
                                            in
                                            order.</p>
                                    </div>
                                </div>

                                <div class="mt-5 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                    <button type="submit" :disabled="!selectedIssueId"
                                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed sm:col-start-2 sm:text-sm">
                                        <i class="fa-solid fa-calendar-check mr-2"></i> Save Schedule
                                    </button>
                                    <button type="button" @click="scheduleModalOpen = false"
                                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- ==================== PUBLICATION TAB ==================== --}}
        @php
            $publication = $submission->currentPublication ?? $submission->getOrCreatePublication();
            $pubStatus = $publication->status ?? 1;
            $pubAuthors =
                ($publication->authors?->isNotEmpty()
                    ? $publication->authors->map(
                        fn($a) => [
                            'id' => $a->id,
                            'name' => $a->name,
                            'email' => $a->email ?? '',
                            'orcid' => $a->orcid ?? '',
                            'orcid_url' => $a->orcid_url ?? '',
                            'affiliation' => $a->affiliation ?? '',
                            'country' => $a->country ?? '',
                            'is_corresponding' => $a->is_corresponding ?? false,
                            'include_in_browse' => $a->include_in_browse ?? true,
                            'given_name' => $a->given_name ?? '',
                            'family_name' => $a->family_name ?? '',
                        ],
                    )
                    : $submission->authors->map(
                        fn($a) => [
                            'id' => $a->id,
                            'name' => $a->name,
                            'email' => $a->email ?? '',
                            'orcid' => $a->orcid ?? '',
                            'orcid_url' => $a->orcid_url ?? '',
                            'affiliation' => $a->affiliation ?? '',
                            'country' => $a->country ?? '',
                            'is_corresponding' => $a->is_corresponding ?? false,
                            'include_in_browse' => $a->include_in_browse ?? true,
                            'given_name' => $a->given_name ?? '',
                            'family_name' => $a->family_name ?? '',
                        ],
                    )) ?? [];
        @endphp
        <div x-show="activeTab === 'publication'" x-cloak x-data="{
            pubTab: (new URLSearchParams(window.location.search)).get('subtab') || 'title',
            contributorModalOpen: false,
            editingContributor: null,
            issues: {{ json_encode($issueOptions) }},
            sections: [],
            isLoadingIssues: false,
            isLoadingSections: false,
        
            async loadSections() {
                this.isLoadingSections = true;
                try {
                    const res = await fetch('{{ route('journal.workflow.publication.sections.list', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}');
                    this.sections = await res.json();
                } catch (e) { console.error(e); }
                this.isLoadingSections = false;
            },
        
            openContributorModal(contributor = null) {
                this.editingContributor = contributor;
                this.contributorModalOpen = true;
            },
        
            init() {
                this.loadSections();
            },
        
            // Reordering Logic
            reorderModalOpen: false,
            reorderList: [],
            allAuthors: {{ json_encode($pubAuthors) }},
            isSavingOrder: false,
        
            openReorderModal() {
                // Clone authors to local list for manipulation
                this.reorderList = JSON.parse(JSON.stringify(this.allAuthors));
                this.reorderModalOpen = true;
            },
        
            moveUp(index) {
                if (index > 0) {
                    const temp = this.reorderList[index];
                    this.reorderList[index] = this.reorderList[index - 1];
                    this.reorderList[index - 1] = temp;
                }
            },
        
            moveDown(index) {
                if (index < this.reorderList.length - 1) {
                    const temp = this.reorderList[index];
                    this.reorderList[index] = this.reorderList[index + 1];
                    this.reorderList[index + 1] = temp;
                }
            },
        
            async saveOrder() {
                this.isSavingOrder = true;
                const order = this.reorderList.map(a => a.id);
                try {
                    const response = await fetch('{{ route('journal.workflow.publication.contributors.reorder', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order })
                    });
        
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Failed to save order. Please try again.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('An error occurred.');
                }
                this.isSavingOrder = false;
            }
        }">

            {{-- Status Bar Header --}}
            <div class="bg-white border-b border-gray-200 -mx-6 -mt-6 px-6 py-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <h2 class="text-lg font-semibold text-gray-900">Publication</h2>
                        @php
                            $statusColors = [
                                1 => 'bg-gray-100 text-gray-700',
                                2 => 'bg-blue-100 text-blue-700',
                                3 => 'bg-emerald-100 text-emerald-700',
                                4 => 'bg-orange-100 text-orange-700',
                            ];
                            $statusLabels = [
                                1 => 'Unscheduled',
                                2 => 'Scheduled',
                                3 => 'Published',
                                4 => 'Unpublished',
                            ];
                        @endphp
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$pubStatus] ?? 'bg-gray-100 text-gray-700' }}">
                            <span
                                class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $pubStatus == 3 ? 'bg-emerald-500' : ($pubStatus == 2 ? 'bg-blue-500' : 'bg-gray-400') }}"></span>
                            {{ $statusLabels[$pubStatus] ?? 'Unknown' }}
                        </span>
                        @if ($publication && $publication->issue)
                            <span class="text-sm text-gray-500">
                                <i class="fa-solid fa-book text-gray-400 mr-1"></i>
                                {{ $publication->issue->identifier }}
                            </span>
                        @endif
                    </div>

                    {{-- Main Action Button --}}
                    @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                        <div class="flex items-center gap-2">
                            @if ($pubStatus == 3)
                                <form
                                    action="{{ route('journal.workflow.publication.unpublish', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Are you sure?')"
                                        class="inline-flex items-center px-4 py-2 border border-orange-200 text-sm font-medium rounded-lg text-orange-700 bg-white hover:bg-orange-50">
                                        <i class="fa-solid fa-eye-slash mr-2"></i> Unpublish
                                    </button>
                                </form>
                            @elseif($pubStatus == 2)
                                <form
                                    action="{{ route('journal.workflow.publication.publish', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm">
                                        <i class="fa-solid fa-rocket mr-2"></i> Publish
                                    </button>
                                </form>
                            @else
                                <button @click="pubTab = 'issue'" :disabled="!issues.length"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fa-solid fa-calendar-plus mr-2"></i> Schedule for Publication
                                </button>
                            @endif
                        </div>
                    @endjournalPermission
                </div>
            </div>

            {{-- Split Layout: Sidebar + Content --}}
            <div class="flex gap-8">

                {{-- Left Vertical Sidebar Navigation --}}
                <nav class="w-56 flex-shrink-0">
                    <div class="sticky top-24 space-y-1">
                        <button @click="pubTab = 'title'"
                            :class="pubTab === 'title' ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600' :
                                'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="w-full text-left px-4 py-2.5 text-sm font-medium rounded-r-lg transition-colors">
                            <i class="fa-solid fa-heading w-5 mr-2 text-center"></i> Title & Abstract
                        </button>
                        <button @click="pubTab = 'contributors'"
                            :class="pubTab === 'contributors' ?
                                'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600' :
                                'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="w-full text-left px-4 py-2.5 text-sm font-medium rounded-r-lg transition-colors">
                            <i class="fa-solid fa-users w-5 mr-2 text-center"></i> Contributors
                            <span
                                class="ml-auto text-xs bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded">{{ $pubAuthors->count() }}</span>
                        </button>
                        <button @click="pubTab = 'metadata'"
                            :class="pubTab === 'metadata' ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600' :
                                'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="w-full text-left px-4 py-2.5 text-sm font-medium rounded-r-lg transition-colors">
                            <i class="fa-solid fa-tags w-5 mr-2 text-center"></i> Metadata
                        </button>
                        <button @click="pubTab = 'references'"
                            :class="pubTab === 'references' ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600' :
                                'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="w-full text-left px-4 py-2.5 text-sm font-medium rounded-r-lg transition-colors">
                            <i class="fa-solid fa-quote-left w-5 mr-2 text-center"></i> References
                        </button>

                        <button @click="pubTab = 'galleys'"
                            :class="pubTab === 'galleys' ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600' :
                                'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="w-full text-left px-4 py-2.5 text-sm font-medium rounded-r-lg transition-colors">
                            <i class="fa-solid fa-file-pdf w-5 mr-2 text-center"></i> Galleys
                            <span
                                class="ml-auto text-xs bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded">{{ $submission->galleys->count() }}</span>
                        </button>
                        <button @click="pubTab = 'license'"
                            :class="pubTab === 'license' ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600' :
                                'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="w-full text-left px-4 py-2.5 text-sm font-medium rounded-r-lg transition-colors">
                            <i class="fa-solid fa-scale-balanced w-5 mr-2 text-center"></i> Permissions & Disclosure
                        </button>
                        <button @click="pubTab = 'issue'"
                            :class="pubTab === 'issue' ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600' :
                                'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="w-full text-left px-4 py-2.5 text-sm font-medium rounded-r-lg transition-colors">
                            <i class="fa-solid fa-book-open w-5 mr-2 text-center"></i> Issue
                        </button>
                        <button @click="pubTab = 'identifiers'"
                            :class="pubTab === 'identifiers' ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600' :
                                'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="w-full text-left px-4 py-2.5 text-sm font-medium rounded-r-lg transition-colors">
                            <i class="fa-solid fa-fingerprint w-5 mr-2 text-center"></i> Identifiers
                        </button>
                      
                        <button @click="pubTab = 'seo'"
                            :class="pubTab === 'seo' ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600' :
                                'text-gray-600 hover:bg-gray-50 border-l-4 border-transparent'"
                            class="w-full text-left px-4 py-2.5 text-sm font-medium rounded-r-lg transition-colors">
                            <i class="fa-brands fa-google-scholar w-5 mr-2 text-center"></i> Google Scholar Forecaster
                        </button>
                    </div>
                </nav>

                {{-- Right Content Area --}}
                <div class="flex-1 min-w-0">

                    {{-- WARNING: PUBLISHED --}}
                    @if ($pubStatus == 3)
                        <div class="bg-red-600 border-l-4 border-red-800 p-4 mb-6 rounded-r-lg shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fa-solid fa-triangle-exclamation text-white text-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-white">
                                        <span class="font-bold block mb-0.5">This version has been published and can
                                            not be edited.</span>
                                        You must unpublish this version before making any changes.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ====== TITLE & ABSTRACT ====== --}}
                    <div x-show="pubTab === 'title'"
                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-base font-bold text-gray-900">Title & Abstract</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Edit the publication title and abstract.</p>
                        </div>
                        <form
                            action="{{ route('journal.workflow.publication.title.update', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                            method="POST" class="p-6">
                            @csrf
                            <fieldset class="space-y-5" @if ($pubStatus == 3) disabled @endif>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="title"
                                        value="{{ old('title', $publication->title ?? $submission->title) }}"
                                        required
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-lg font-medium">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                                    <input type="text" name="subtitle"
                                        value="{{ old('subtitle', $publication->subtitle ?? $submission->subtitle) }}"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Abstract</label>
                                    <textarea name="abstract" id="publicationAbstract" rows="8"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('abstract', $publication->abstract ?? $submission->abstract) }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">HTML formatting is allowed.</p>
                                </div>

                                <div class="flex justify-end pt-4 border-t border-gray-100">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fa-solid fa-save mr-2"></i> Save
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>

                    {{-- ====== CONTRIBUTORS ====== --}}
                    <div x-show="pubTab === 'contributors'"
                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Contributors</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Manage authors and contributors for this
                                    publication.</p>
                            </div>
                            @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                <button @click="openContributorModal()"
                                    :disabled="{{ $pubStatus == 3 ? 'true' : 'false' }}"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fa-solid fa-plus mr-1.5"></i> Add Contributor
                                </button>
                                <button @click="openReorderModal()"
                                    :disabled="allAuthors.length < 2 || {{ $pubStatus == 3 ? 'true' : 'false' }}"
                                    class="ml-2 inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fa-solid fa-arrow-down-short-wide mr-1.5"></i> Order
                                </button>
                            @endjournalPermission
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            #
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Affiliation</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                            Primary</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                            In Browse</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($pubAuthors as $index => $author)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $index + 1 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold mr-3">
                                                        {{ strtoupper(substr($author['name'], 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $author['name'] }}
                                                        </p>
                                                        @if ($author['orcid'])
                                                            <a href="{{ $author['orcid_url'] }}" target="_blank"
                                                                class="text-xs text-green-600 hover:underline">
                                                                <i class="fa-brands fa-orcid mr-0.5"></i> ORCID
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $author['email'] }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                                {{ $author['affiliation'] ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if ($author['is_corresponding'])
                                                    <i class="fa-solid fa-check-circle text-emerald-500"></i>
                                                @else
                                                    <span class="text-gray-300">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if ($author['include_in_browse'] ?? true)
                                                    <i class="fa-solid fa-check-circle text-emerald-500"></i>
                                                @else
                                                    <span class="text-gray-300">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                                    <div class="flex items-center justify-end gap-1">
                                                        <button type="button"
                                                            :disabled="{{ $pubStatus == 3 ? 'true' : 'false' }}"
                                                            @click="openContributorModal({
                                                                id: '{{ $author['id'] }}',
                                                                given_name: '{{ $author['given_name'] ?? '' }}',
                                                                family_name: '{{ $author['family_name'] ?? '' }}',
                                                                email: '{{ $author['email'] }}',
                                                                affiliation: '{{ $author['affiliation'] ?? '' }}',
                                                                country: '{{ $author['country'] ?? '' }}',
                                                                orcid: '{{ $author['orcid'] ?? '' }}',
                                                                is_corresponding: {{ $author['is_corresponding'] ? 'true' : 'false' }},
                                                                include_in_browse: {{ $author['include_in_browse'] ?? true ? 'true' : 'false' }}
                                                            })"
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </button>
                                                        <form
                                                            action="{{ route('journal.workflow.publication.contributor.destroy', ['journal' => $journal->slug, 'submission' => $submission->slug, 'author' => $author['id']]) }}"
                                                            method="POST" class="inline"
                                                            onsubmit="return confirm('Remove this contributor?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                @if ($pubStatus == 3) disabled @endif
                                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endjournalPermission
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center">
                                                    <div
                                                        class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                                        <i class="fa-solid fa-user-plus text-gray-400 text-xl"></i>
                                                    </div>
                                                    <p class="text-sm font-medium text-gray-900">No contributors
                                                        yet
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">Add authors and
                                                        contributors
                                                        to this publication.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ====== METADATA ====== --}}
                    <div x-show="pubTab === 'metadata'"
                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-base font-bold text-gray-900">Metadata</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Keywords and other metadata for indexing.</p>
                        </div>
                        <form
                            action="{{ route('journal.workflow.publication.metadata.update', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                            method="POST" class="p-6">
                            @csrf
                            <fieldset class="space-y-5" @if ($pubStatus == 3) disabled @endif>
                                <div x-data="keywordInputShow({{ json_encode($submission->keywords->pluck('content')->toArray()) }})">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keywords</label>
                                    <input type="text" x-ref="keywordInput"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Type keyword and press Enter">
                                    <p class="mt-1 text-xs text-gray-500">Press Enter or comma to add keywords. Start
                                        typing to see suggestions.</p>
                                </div>

                                <div class="flex justify-end pt-4 border-t border-gray-100">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fa-solid fa-save mr-2"></i> Save
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>

                    {{-- ====== REFERENCES ====== --}}
                    <div x-show="pubTab === 'references'"
                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-base font-bold text-gray-900">References</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Manage article references for indexing.</p>
                        </div>
                        <form
                            action="{{ route('journal.workflow.publication.references.update', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                            method="POST" class="p-6">
                            @csrf
                            <fieldset class="space-y-5" @if ($pubStatus == 3) disabled @endif>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">References</label>
                                    <textarea name="references" rows="15"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm leading-6"
                                        placeholder="Paste your references here...">{{ old('references', $publication->references ?? $submission->references) }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Provide a list of references for your work.
                                    </p>
                                </div>

                                <div class="flex justify-end pt-4 border-t border-gray-100">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fa-solid fa-save mr-2"></i> Save References
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>


                    {{-- ====== SEO CHECK (Google Scholar) ====== --}}
                    <div x-show="pubTab === 'seo'" class="h-full">
                        <x-google-scholar-seo :analysis="$seoAnalysis" />
                    </div>

                    {{-- ====== ISSUE (SCHEDULING) ====== --}}
                    <div x-show="pubTab === 'issue'"
                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-base font-bold text-gray-900">Issue</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Schedule this publication to an issue.</p>
                        </div>
                        <form
                            action="{{ route('journal.workflow.publication.issue.assign', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                            method="POST" enctype="multipart/form-data" class="p-6">
                            @csrf
                            <fieldset class="space-y-5" @if ($pubStatus == 3) disabled @endif>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Issue <span
                                                class="text-red-500">*</span></label>
                                        <template x-if="isLoadingIssues">
                                            <div class="flex items-center py-2 text-sm text-gray-500">
                                                <i class="fa-solid fa-spinner fa-spin mr-2"></i> Loading...
                                            </div>
                                        </template>
                                        <template x-if="!isLoadingIssues">
                                            <select name="issue_id" required
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">-- Select Issue --</option>
                                                <template x-for="issue in issues" :key="issue.id">
                                                    <option :value="issue.id"
                                                        :selected="issue.id === '{{ $publication->issue_id ?? '' }}'"
                                                        x-text="issue.label">
                                                    </option>
                                                </template>
                                            </select>
                                        </template>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                                        <template x-if="isLoadingSections">
                                            <div class="flex items-center py-2 text-sm text-gray-500">
                                                <i class="fa-solid fa-spinner fa-spin mr-2"></i> Loading...
                                            </div>
                                        </template>
                                        <template x-if="!isLoadingSections">
                                            <select name="section_id"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">-- Select Section --</option>
                                                <template x-for="section in sections" :key="section.id">
                                                    <option :value="section.id"
                                                        :selected="section
                                                            .id ==
                                                            '{{ $publication->section_id ?? ($submission->section_id ?? '') }}'"
                                                        x-text="section.name"></option>
                                                </template>
                                            </select>
                                        </template>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Pages</label>
                                        <input type="text" name="pages"
                                            value="{{ old('pages', $publication->pages ?? '') }}"
                                            placeholder="e.g., 1-12"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date
                                            Published</label>
                                        <input type="date" name="date_published"
                                            value="{{ old('date_published', $publication->date_published?->format('Y-m-d') ?? '') }}"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>

                                {{-- URL Path & Cover Image --}}
                                <div class="grid grid-cols-1 gap-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">URL Path <span
                                                class="text-gray-400 font-normal">(Optional)</span></label>
                                        <div class="flex items-center">
                                            <span
                                                class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm h-[42px]">
                                                /article/
                                            </span>
                                            <input type="text" name="url_path"
                                                value="{{ old('url_path', $publication->url_path ?? '') }}"
                                                placeholder="custom-slug"
                                                class="flex-1 rounded-r-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">An optional path to use in the URL
                                            instead
                                            of the ID.</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Cover
                                            Image</label>

                                        @if ($publication->cover_image_path)
                                            <div class="mb-3 flex items-start gap-4 p-3 bg-gray-50 border rounded-lg">
                                                <img src="{{ Storage::url($publication->cover_image_path) }}"
                                                    alt="Cover Image"
                                                    class="h-20 w-auto rounded shadow-sm object-cover">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">Current Cover</div>
                                                    <div class="text-xs text-gray-500 mt-0.5">Upload a new image to
                                                        replace.</div>
                                                </div>
                                            </div>
                                        @endif

                                        <input type="file" name="cover_image" accept="image/*"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-colors">
                                        <p class="mt-1 text-xs text-gray-500">Formats: JPG, PNG, WEBP.</p>
                                    </div>
                                </div>

                                @if ($publication->issue_id)
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <i class="fa-solid fa-calendar-check text-blue-500 mr-3"></i>
                                            <div>
                                                <p class="text-sm font-medium text-blue-800">Currently Scheduled</p>
                                                <p class="text-xs text-blue-600">
                                                    {{ $publication->issue->identifier ?? 'Unknown Issue' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex justify-between pt-4 border-t border-gray-100">
                                    @if ($publication->issue_id)
                                        <form
                                            action="{{ route('journal.workflow.publication.unschedule', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-600 bg-white hover:bg-gray-50">
                                                <i class="fa-solid fa-calendar-xmark mr-2"></i> Unschedule
                                            </button>
                                        </form>
                                    @else
                                        <div></div>
                                    @endif
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fa-solid fa-calendar-check mr-2"></i>
                                        {{ $publication->issue_id ? 'Save' : 'Save' }}
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>

                    {{-- ====== GALLEYS (Publication Formats) ====== --}}
                    <div x-show="pubTab === 'galleys'"
                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-bold text-gray-900">
                                    <i class="fa-solid fa-file-pdf text-red-500 mr-2"></i>Publication Galleys
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Final formats available to readers (PDF,
                                    HTML, EPUB).</p>
                            </div>
                            @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                <button @click="openAddGalley()" @if ($pubStatus == 3) disabled @endif
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fa-solid fa-plus mr-2"></i> Add Galley
                                </button>
                            @endjournalPermission
                        </div>

                        {{-- Galleys Table --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Format</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            File / URL</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Language</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Type</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($submission->galleys as $galley)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold {{ $galley->label_color }}">
                                                    <i class="fa-solid {{ $galley->label_icon }} mr-1.5"></i>
                                                    {{ $galley->label }}
                                                </span>
                                                @if ($galley->url_path)
                                                    <span class="block text-xs text-gray-400 mt-1">
                                                        /{{ $galley->url_path }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div>
                                                        @if ($galley->is_remote)
                                                            <p class="text-sm font-medium text-gray-900 truncate max-w-xs"
                                                                title="{{ $galley->url_remote }}">
                                                                {{ Str::limit($galley->url_remote, 40) }}
                                                            </p>
                                                        @else
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $galley->file->file_name ?? 'No file' }}
                                                            </p>
                                                            @if ($galley->file)
                                                                <p class="text-xs text-gray-500">
                                                                    {{ number_format($galley->file->file_size / 1024, 0) }}
                                                                    KB
                                                                </p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="text-sm text-gray-600">{{ $galley->locale_name }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($galley->is_remote)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                        <i class="fa-solid fa-link mr-1"></i> Remote
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                        <i class="fa-solid fa-hdd mr-1"></i> Local
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="flex items-center justify-end gap-1">
                                                    @if ($galley->download_url)
                                                        <a href="{{ $galley->download_url }}" target="_blank"
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors"
                                                            title="Download / View">
                                                            <i
                                                                class="fa-solid fa-{{ $galley->is_remote ? 'external-link' : 'download' }}"></i>
                                                        </a>
                                                    @endif
                                                    @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                                        <button type="button"
                                                            @click='openEditGalley({{ json_encode([
                                                                'id' => $galley->id,
                                                                'label' => $galley->label,
                                                                'locale' => $galley->locale,
                                                                'url_path' => $galley->url_path,
                                                                'url_remote' => $galley->url_remote,
                                                                'is_remote' => $galley->is_remote,
                                                            ]) }})'
                                                            @if ($pubStatus == 3) disabled @endif
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                            title="Edit">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </button>
                                                        <form
                                                            action="{{ route('journal.workflow.galley.destroy', ['journal' => $journal->slug, 'submission' => $submission->slug, 'galley' => $galley->id]) }}"
                                                            method="POST" class="inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this galley?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                @if ($pubStatus == 3) disabled @endif
                                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                                title="Delete">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endjournalPermission
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center">
                                                    <div
                                                        class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mb-4">
                                                        <i class="fa-solid fa-file-pdf text-indigo-400 text-2xl"></i>
                                                    </div>
                                                    <p class="text-sm font-semibold text-gray-900">No galleys uploaded
                                                        yet</p>
                                                    <p class="text-xs text-gray-500 mt-1 max-w-xs">Upload PDF, HTML,
                                                        or
                                                        EPUB files so readers can access the article.</p>
                                                    @journalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id)
                                                        <button @click="openAddGalley()"
                                                            @if ($pubStatus == 3) disabled @endif
                                                            class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                            <i class="fa-solid fa-plus mr-2"></i> Add your first galley
                                                        </button>
                                                    @endjournalPermission
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Info Box --}}
                        @if (!$submission->hasGalleys())
                            <div class="bg-amber-50 border-t border-amber-200 px-6 py-4">
                                <div class="flex">
                                    <i
                                        class="fa-solid fa-exclamation-triangle text-amber-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                    <div>
                                        <p class="text-sm font-medium text-amber-800">Required for Publication</p>
                                        <p class="text-xs text-amber-700 mt-0.5">
                                            At least one galley (e.g., PDF) must be uploaded before the article can be
                                            published.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif


                    </div>

                    {{-- ====== LICENSE & DOI ====== --}}
                    <div x-show="pubTab === 'license'"
                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-base font-bold text-gray-900">Permissions & Disclosure</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Copyright and License Information
                            </p>
                        </div>
                        <form
                            action="{{ route('journal.workflow.publication.license.update', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                            method="POST" class="p-6">
                            @csrf
                            <fieldset class="space-y-5" @if ($pubStatus == 3) disabled @endif>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Copyright
                                            Holder</label>
                                        <input type="text" name="copyright_holder"
                                            value="{{ old('copyright_holder', $publication->copyright_holder ?? '') }}"
                                            placeholder="e.g., The Author(s)"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Copyright
                                            Year</label>
                                        <input type="number" name="copyright_year"
                                            value="{{ old('copyright_year', $publication->copyright_year ?? date('Y')) }}"
                                            min="1900" max="2100"
                                            class="block w-32 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">License URL</label>
                                    <input type="url" name="license_url"
                                        value="{{ old('license_url', $publication->license_url ?? '') }}"
                                        placeholder="https://creativecommons.org/licenses/by/4.0/"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <p class="mt-1 text-xs text-gray-500">Common licenses: CC BY 4.0, CC BY-SA 4.0, CC
                                        BY-NC 4.0</p>
                                </div>

                                <div class="flex justify-end pt-4 border-t border-gray-100">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fa-solid fa-save mr-2"></i> Save
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>

                    {{-- ====== IDENTIFIERS (DOI) ====== --}}
                    <div x-show="pubTab === 'identifiers'"
                        class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden"
                        x-data="{
                            isAssigning: false,
                            isClearing: false,
                            currentDoi: '{{ $publication->doi ?? '' }}',
                            doiEnabled: {{ $journal->doi_enabled ? 'true' : 'false' }},
                        
                            async assignDoi() {
                                if (!confirm('Are you sure you want to assign a DOI to this publication?')) return;
                                this.isAssigning = true;
                                try {
                                    const res = await fetch('{{ route('journal.workflow.publication.doi.assign', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    });
                                    const data = await res.json();
                                    if (data.success) {
                                        this.currentDoi = data.doi;
                                    } else {
                                        alert(data.message || 'Failed to assign DOI');
                                    }
                                } catch (e) {
                                    console.error(e);
                                    alert('An error occurred while assigning DOI');
                                }
                                this.isAssigning = false;
                            },
                        
                            async clearDoi() {
                                if (!confirm('Are you sure you want to clear the DOI? This action cannot be undone if the DOI has been registered.')) return;
                                this.isClearing = true;
                                try {
                                    const res = await fetch('{{ route('journal.workflow.publication.doi.clear', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    });
                                    const data = await res.json();
                                    if (data.success) {
                                        this.currentDoi = '';
                                    } else {
                                        alert(data.message || 'Failed to clear DOI');
                                    }
                                } catch (e) {
                                    console.error(e);
                                    alert('An error occurred while clearing DOI');
                                }
                                this.isClearing = false;
                            }
                        }">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-base font-bold text-gray-900">Identifiers</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Digital Object Identifier (DOI) for this
                                publication.</p>
                        </div>
                        <div class="p-6 space-y-5">
                            {{-- DOI Status Banner (if disabled) --}}
                            <template x-if="!doiEnabled">
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                    <div class="flex">
                                        <i class="fa-solid fa-exclamation-triangle text-amber-500 mt-0.5 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-medium text-amber-800">DOI Assignment Disabled</p>
                                            <p class="text-xs text-amber-700 mt-1">
                                                DOI assignment is not enabled for this journal.
                                                <a href="{{ route('journal.settings.doi.edit', $journal->slug) }}"
                                                    class="underline hover:no-underline">Configure DOI Settings</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- DOI Input and Buttons --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">DOI</label>
                                <div class="flex items-center gap-3">
                                    {{-- DOI Input (Disabled) --}}
                                    <div
                                        class="flex-1 flex items-center bg-gray-50 border border-gray-300 rounded-lg overflow-hidden">
                                        <span
                                            class="px-3 py-2.5 text-sm text-gray-500 bg-gray-100 border-r border-gray-300 whitespace-nowrap">https://doi.org/</span>
                                        <input type="text" :value="currentDoi" disabled
                                            :placeholder="currentDoi ? '' : 'No DOI assigned'"
                                            class="flex-1 px-3 py-2.5 text-sm text-gray-700 bg-gray-50 border-0 focus:ring-0 cursor-not-allowed">
                                    </div>

                                    {{-- Assign Button (shown when no DOI) --}}
                                    <button type="button" x-show="!currentDoi" @click="assignDoi()"
                                        :disabled="isAssigning || !doiEnabled || {{ $pubStatus == 3 ? 'true' : 'false' }}"
                                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                                        <template x-if="isAssigning">
                                            <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                                        </template>
                                        <template x-if="!isAssigning">
                                            <i class="fa-solid fa-check mr-2"></i>
                                        </template>
                                        <span x-text="isAssigning ? 'Assigning...' : 'Assign'"></span>
                                    </button>

                                    {{-- Clear Button (shown when DOI exists) --}}
                                    <button type="button" x-show="currentDoi" @click="clearDoi()"
                                        :disabled="isClearing || {{ $pubStatus == 3 ? 'true' : 'false' }}"
                                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                                        <template x-if="isClearing">
                                            <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                                        </template>
                                        <template x-if="!isClearing">
                                            <i class="fa-solid fa-times mr-2"></i>
                                        </template>
                                        <span x-text="isClearing ? 'Clearing...' : 'Clear'"></span>
                                    </button>
                                </div>

                                {{-- DOI Link (shown when DOI exists) --}}
                                <template x-if="currentDoi">
                                    <p class="mt-2 text-xs text-gray-500">
                                        <a :href="'https://doi.org/' + currentDoi" target="_blank"
                                            class="text-indigo-600 hover:underline">
                                            <i class="fa-solid fa-external-link mr-1"></i> View on doi.org
                                        </a>
                                    </p>
                                </template>
                                <template x-if="!currentDoi">
                                    <p class="mt-2 text-xs text-gray-500">Click "Assign" to generate a DOI for this
                                        publication.</p>
                                </template>
                            </div>

                            {{-- Help Box --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                                <div class="flex">
                                    <i class="fa-solid fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                                    <div class="text-xs text-blue-700">
                                        <p class="font-medium mb-1">About DOIs</p>
                                        <p>A DOI (Digital Object Identifier) is a persistent identifier used to uniquely
                                            identify scholarly articles. Once assigned, the DOI should not be changed as
                                            it may have been registered with external services like Crossref.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ====== ADD/EDIT CONTRIBUTOR MODAL ====== --}}
            <div x-show="contributorModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto" role="dialog"
                aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="contributorModalOpen" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                        @click="contributorModalOpen = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div x-show="contributorModalOpen" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                        <div class="sm:flex sm:items-start mb-5">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-user-plus text-indigo-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-semibold text-gray-900"
                                    x-text="editingContributor ? 'Edit Contributor' : 'Add Contributor'"></h3>
                                <p class="mt-1 text-sm text-gray-500">Enter the contributor's information.</p>
                            </div>
                        </div>

                        <form
                            :action="editingContributor
                                ?
                                '{{ url('/' . $journal->slug . '/workflow') }}/' + '{{ $submission->slug }}' +
                                '/publication/contributor/' + editingContributor.id :
                                '{{ route('journal.workflow.publication.contributor.store', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}'"
                            method="POST" class="space-y-4">
                            @csrf
                            <template x-if="editingContributor">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="given_name" required
                                        :value="editingContributor?.given_name || ''"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="family_name" required
                                        :value="editingContributor?.family_name || ''"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                        class="text-red-500">*</span></label>
                                <input type="email" name="email" required
                                    :value="editingContributor?.email || ''"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Affiliation</label>
                                <input type="text" name="affiliation"
                                    :value="editingContributor?.affiliation || ''"
                                    placeholder="e.g., Harvard University"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                    <input type="text" name="country" :value="editingContributor?.country || ''"
                                        placeholder="e.g., United States"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ORCID iD</label>
                                    <input type="text" name="orcid" :value="editingContributor?.orcid || ''"
                                        placeholder="0000-0000-0000-0000"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <div class="space-y-3 pt-2">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="is_corresponding" value="1"
                                            :checked="editingContributor?.is_corresponding"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label class="font-medium text-gray-700">Principal contact for editorial
                                            correspondence</label>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="include_in_browse" value="1"
                                            :checked="editingContributor?.include_in_browse ?? true"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label class="font-medium text-gray-700">Include this contributor in
                                            browse
                                            lists</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:col-start-2 sm:text-sm">
                                    <i class="fa-solid fa-save mr-2"></i> <span
                                        x-text="editingContributor ? 'Update' : 'Add'"></span>
                                </button>
                                <button type="button"
                                    @click="contributorModalOpen = false; editingContributor = null"
                                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ====== REORDER CONTRIBUTORS MODAL ====== --}}
            <div x-show="reorderModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto" role="dialog"
                aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="reorderModalOpen" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                        @click="reorderModalOpen = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div x-show="reorderModalOpen" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">

                        <div class="sm:flex sm:items-start mb-5">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-arrow-down-short-wide text-indigo-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-semibold text-gray-900">Order Contributors</h3>
                                <p class="mt-1 text-sm text-gray-500">Drag and drop or use arrows to change the
                                    order.
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-2 text-sm text-gray-500 bg-gray-50 border border-gray-200 rounded-lg p-1 max-h-[300px] overflow-y-auto">
                            <ul class="space-y-1">
                                <template x-for="(author, index) in reorderList" :key="author.id">
                                    <li
                                        class="flex items-center justify-between p-2 bg-white border border-gray-200 rounded shadow-sm">
                                        <span class="font-medium text-gray-900 truncate flex-1 mr-2"
                                            x-text="author.name"></span>
                                        <div class="flex items-center space-x-1">
                                            <button type="button" @click="moveUp(index)" :disabled="index === 0"
                                                class="p-1 text-gray-400 hover:text-indigo-600 disabled:opacity-30 disabled:hover:text-gray-400">
                                                <i class="fa-solid fa-arrow-up"></i>
                                            </button>
                                            <button type="button" @click="moveDown(index)"
                                                :disabled="index === reorderList.length - 1"
                                                class="p-1 text-gray-400 hover:text-indigo-600 disabled:opacity-30 disabled:hover:text-gray-400">
                                                <i class="fa-solid fa-arrow-down"></i>
                                            </button>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <div class="mt-5 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="button" @click="saveOrder()" :disabled="isSavingOrder"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none disabled:opacity-75 sm:col-start-2 sm:text-sm">
                                <i class="fa-solid fa-spinner fa-spin mr-2" x-show="isSavingOrder"></i>
                                <span x-text="isSavingOrder ? 'Saving...' : 'Done'"></span>
                            </button>
                            <button type="button" @click="reorderModalOpen = false"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- NEW DISCUSSION MODAL --}}
        <div x-show="discussionModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="discussionModalOpen = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <div
                    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                    <div class="mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Add Discussion</h3>
                    </div>

                    <form id="discussion-form"
                        action="{{ route('journal.discussion.create', ['journal' => $journal->slug, 'submission' => $submission]) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="stage_id" x-model="discussionStageId">

                        {{-- Hidden inputs for attached files --}}
                        <template x-for="(file, index) in discussionFiles" :key="file.id">
                            <div>
                                <input type="hidden" :name="'attached_files[' + index + '][id]'"
                                    :value="file.id">
                                <input type="hidden" :name="'attached_files[' + index + '][name]'"
                                    :value="file.name">
                            </div>
                        </template>

                        <div class="space-y-4">
                            <div>
                                <label for="subject"
                                    class="block text-sm font-medium text-gray-700">Subject</label>
                                <input type="text" name="subject" id="subject"
                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    required>
                            </div>

                            @if (auth()->user()->hasJournalPermission([\App\Models\Role::LEVEL_MANAGER, \App\Models\Role::LEVEL_SECTION_EDITOR], $journal->id))
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Notify
                                        Participants</label>
                                    <div
                                        class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                                        @forelse($participants as $participant)
                                            <label
                                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-white cursor-pointer transition-colors">
                                                <input type="checkbox" name="participants[]"
                                                    value="{{ $participant->id }}" checked
                                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                                    {{-- Avatar --}}
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                        {{ strtoupper(substr($participant->name, 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <span
                                                            class="text-sm font-medium text-gray-900 block truncate">{{ $participant->name }}</span>
                                                        <span
                                                            class="text-xs text-gray-500 block truncate">{{ $participant->email }}</span>
                                                    </div>
                                                </div>
                                                {{-- Role Badge --}}
                                                @php
                                                    $role =
                                                        $participant->id === $submission->user_id ? 'Author' : 'Editor';
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $role === 'Author' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                                                    {{ $role }}
                                                </span>
                                            </label>
                                        @empty
                                            <p class="text-sm text-gray-500 italic text-center py-2">No other
                                                participants to notify.</p>
                                        @endforelse
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label for="discussion-editor"
                                    class="block text-sm font-medium text-gray-700">Message</label>
                                <div class="mt-1">
                                    <textarea name="body" id="discussion-editor"></textarea>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900">Attached Files</h4>
                                    <button type="button" @click="fileWizardOpen = true"
                                        class="text-sm text-indigo-600 font-medium hover:underline">
                                        + Attach File
                                    </button>
                                </div>
                                <ul class="mt-3 space-y-2">
                                    <template x-for="file in discussionFiles" :key="file.id">
                                        <li
                                            class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded border border-gray-200">
                                            <div class="flex items-center">
                                                <i class="fa-regular fa-file text-gray-400 mr-2"></i>
                                                <span class="text-sm text-gray-700" x-text="file.name"></span>
                                                <span class="ml-2 text-xs text-gray-500"
                                                    x-text="(file.size / 1024).toFixed(0) + ' KB'"></span>
                                            </div>
                                            <button type="button" class="text-xs text-red-600 hover:text-red-800"
                                                @click="discussionFiles = discussionFiles.filter(f => f.id !== file.id)">Remove</button>
                                        </li>
                                    </template>
                                    <template x-if="discussionFiles.length === 0">
                                        <li class="text-sm text-gray-500 italic">No files attached.</li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="button" @click="submitDiscussion()"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:col-start-2 sm:text-sm">
                                OK
                            </button>
                            <button type="button" @click="discussionModalOpen = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- FILE WIZARD MODAL (Nested/Overlay) --}}
        <div x-show="fileWizardOpen" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto"
            aria-labelledby="wizard-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity" aria-hidden="true">
                </div>

                <div
                    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="mb-4 border-b border-gray-200 pb-2">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="wizard-title">
                            Add File to Discussion
                        </h3>
                        <div class="flex space-x-2 mt-2">
                            <span :class="wizardStep >= 1 ? 'text-indigo-600 font-bold' : 'text-gray-400'"
                                class="text-xs">1. Upload</span>
                            <span :class="wizardStep >= 2 ? 'text-indigo-600 font-bold' : 'text-gray-400'"
                                class="text-xs">2. Metadata</span>
                            <span :class="wizardStep >= 3 ? 'text-indigo-600 font-bold' : 'text-gray-400'"
                                class="text-xs">3. Confirm</span>
                        </div>
                    </div>

                    {{-- Step 1: Upload --}}
                    <div x-show="wizardStep === 1">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Article
                                    Component</label>
                                <select
                                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option>Article Text</option>
                                    <option>Other</option>
                                </select>
                            </div>
                            <div
                                class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md relative hover:bg-gray-50">
                                <div class="space-y-1 text-center">
                                    <i class="fa-solid fa-cloud-arrow-up text-gray-400 text-3xl"></i>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label
                                            class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input type="file" class="sr-only" @change="handleFileUpload">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">Drag and drop or select file</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:flex sm:flex-row-reverse">
                            <button type="button" @click="fileWizardOpen = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                        </div>
                    </div>

                    {{-- Step 2: Metadata --}}
                    <template x-if="wizardStep === 2">
                        <div>
                            <div class="space-y-4">
                                <p class="text-sm text-gray-500">File uploaded. Please review metadata.</p>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Filename</label>
                                    <input type="text" x-model="tempUploadedFile.name"
                                        class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            <div class="mt-5 sm:flex sm:flex-row-reverse">
                                <button type="button" @click="wizardStep = 3"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Continue</button>
                                <button type="button" @click="fileWizardOpen = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                            </div>
                        </div>
                    </template>

                    {{-- Step 3: Confirm --}}
                    <div x-show="wizardStep === 3">
                        <div class="text-center py-4">
                            <div
                                class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-3">
                                <i class="fa-solid fa-check text-green-600 text-lg"></i>
                            </div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">File Added</h3>
                            <p class="text-sm text-gray-500 mt-2">The file <span class="font-bold"
                                    x-text="tempUploadedFile && tempUploadedFile.name"></span> is ready to be
                                attached.
                            </p>
                        </div>
                        <div class="mt-5 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="button" @click="completeWizard()"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:col-start-2 sm:text-sm">
                                Complete
                            </button>
                            <button type="button" @click="addAnotherFile()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm">
                                Add Another File
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Original File Modal for main submission files (kept for reference or reuse) --}}
        <div x-show="fileModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="fileModalOpen = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <div
                    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                    <div class="mb-5">
                        <h3 class="text-lg leading-6 font-bold text-gray-900">Upload Submission File</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Uploading file to <strong class="text-indigo-600"><span
                                    x-text="uploadStage.charAt(0).toUpperCase() + uploadStage.slice(1)"></span></strong>
                            stage.
                        </p>
                    </div>

                    <form
                        action="{{ route('journal.workflow.file.store', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="stage" x-model="uploadStage">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select File</label>
                                <div
                                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 hover:border-indigo-400 transition-colors cursor-pointer relative group">
                                    <div class="space-y-1 text-center">
                                        <i
                                            class="fa-solid fa-cloud-arrow-up text-gray-400 text-3xl mb-3 group-hover:text-indigo-500 transition-colors"></i>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span
                                                class="relative bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Upload a file</span>
                                            </span>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PDF, DOC, DOCX, XLS up to 10MB
                                        </p>
                                        <p x-ref="fileNameDisplay"
                                            class="text-sm text-indigo-600 font-medium mt-2 min-h-[20px]"></p>
                                    </div>
                                    <input type="file" name="file"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required
                                        @change="$refs.fileNameDisplay.innerText = $event.target.files[0].name">
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:col-start-2 sm:text-sm">
                                <i class="fa-solid fa-upload mr-2 mt-0.5"></i> Upload
                            </button>
                            <button type="button" @click="fileModalOpen = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        {{-- ==================== ASSIGN EDITOR MODAL ==================== --}}
        @include('submissions.partials.modal-assign-editor')

        {{-- ==================== SEND TO REVIEW MODAL ==================== --}}
        <div x-show="sendToReviewModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="send-review-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="sendToReviewModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                    @click="sendToReviewModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="sendToReviewModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fa-solid fa-arrow-right text-indigo-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900" id="send-review-modal-title">
                                Send to Review
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Select files to promote to the Review stage. Original files will remain in the
                                Submission stage.
                            </p>
                        </div>
                    </div>

                    <form
                        action="{{ route('journal.workflow.promote-review', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                        method="POST" class="mt-5">
                        @csrf

                        {{-- File Selection --}}
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900">
                                    <i class="fa-solid fa-file-lines text-gray-400 mr-2"></i>
                                    Select Files to Promote
                                </h4>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                <template x-if="isLoadingFiles">
                                    <div class="px-4 py-8 text-center">
                                        <i class="fa-solid fa-spinner fa-spin text-gray-400 text-xl"></i>
                                        <p class="text-sm text-gray-500 mt-2">Loading files...</p>
                                    </div>
                                </template>
                                <template x-if="!isLoadingFiles && availableFiles.length === 0">
                                    <div class="px-4 py-8 text-center">
                                        <i class="fa-solid fa-folder-open text-gray-300 text-2xl"></i>
                                        <p class="text-sm text-gray-500 mt-2">No files available for promotion.
                                        </p>
                                    </div>
                                </template>
                                <template x-if="!isLoadingFiles && availableFiles.length > 0">
                                    <div>
                                        {{-- Hidden inputs: only render for selected files --}}
                                        <template x-for="(file, idx) in selectedFilesForPromotion" :key="file.id">
                                            <div>
                                                <input type="hidden" :name="'selected_files[' + idx + '][id]'" :value="file.id">
                                                <input type="hidden" :name="'selected_files[' + idx + '][type]'" :value="file.type">
                                            </div>
                                        </template>
                                        <ul class="divide-y divide-gray-100">
                                            <template x-for="file in availableFiles" :key="file.id">
                                                <li class="px-4 py-3 hover:bg-gray-50 cursor-pointer"
                                                    @click="toggleFileSelection(file)">
                                                    <label class="flex items-center cursor-pointer">
                                                        {{-- Checkbox is visual-only, no name binding --}}
                                                        <input type="checkbox" :checked="isFileSelected(file)"
                                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                            @click.prevent="toggleFileSelection(file)">
                                                        <div class="ml-3 flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate"
                                                                x-text="file.name"></p>
                                                            <p class="text-xs text-gray-500">
                                                                <span x-text="file.source"></span> •
                                                                <span
                                                                    x-text="(file.size / 1024).toFixed(0) + ' KB'"></span>
                                                                •
                                                                <span x-text="file.created_at"></span>
                                                            </p>
                                                        </div>
                                                    </label>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </template>
                            </div>
                            <div class="bg-gray-50 px-4 py-2 border-t border-gray-200">
                                <p class="text-xs text-gray-500">
                                    <span x-text="selectedFilesForPromotion.length"></span> file(s) selected
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                                <i class="fa-solid fa-arrow-right mr-2"></i> Send to Review
                            </button>
                            <button type="button" @click="sendToReviewModalOpen = false"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================== ACCEPT & SKIP REVIEW MODAL ==================== --}}
        <div x-show="skipReviewModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="skip-review-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="skipReviewModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                    @click="skipReviewModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="skipReviewModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fa-solid fa-forward text-emerald-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900" id="skip-review-modal-title">
                                Accept & Skip Review
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                This will bypass the Review stage and move the submission directly to Copyediting.
                            </p>
                        </div>
                    </div>

                    <form
                        action="{{ route('journal.workflow.skip-review', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                        method="POST" class="mt-5">
                        @csrf

                        {{-- Warning Banner --}}
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex">
                                <i class="fa-solid fa-exclamation-triangle text-yellow-500 mt-0.5 mr-3"></i>
                                <div class="text-sm text-yellow-700">
                                    <p class="font-medium">Are you sure?</p>
                                    <p class="mt-1">This action will accept the submission without peer review.
                                        Use
                                        this only for trusted authors or special cases.</p>
                                </div>
                            </div>
                        </div>

                        {{-- File Selection --}}
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900">
                                    <i class="fa-solid fa-file-lines text-gray-400 mr-2"></i>
                                    Select Files to Promote to Copyediting
                                </h4>
                            </div>
                            <div class="max-h-48 overflow-y-auto">
                                <template x-if="isLoadingFiles">
                                    <div class="px-4 py-6 text-center">
                                        <i class="fa-solid fa-spinner fa-spin text-gray-400"></i>
                                        <p class="text-sm text-gray-500 mt-2">Loading files...</p>
                                    </div>
                                </template>
                                <template x-if="!isLoadingFiles && availableFiles.length > 0">
                                    <div>
                                        {{-- Hidden inputs: only render for selected files --}}
                                        <template x-for="(file, idx) in selectedFilesForPromotion" :key="file.id">
                                            <div>
                                                <input type="hidden" :name="'selected_files[' + idx + '][id]'" :value="file.id">
                                                <input type="hidden" :name="'selected_files[' + idx + '][type]'" :value="file.type">
                                            </div>
                                        </template>
                                        <ul class="divide-y divide-gray-100">
                                            <template x-for="file in availableFiles" :key="file.id">
                                                <li class="px-4 py-3 hover:bg-gray-50 cursor-pointer"
                                                    @click="toggleFileSelection(file)">
                                                    <label class="flex items-center cursor-pointer">
                                                        {{-- Checkbox is visual-only, no name binding --}}
                                                        <input type="checkbox" :checked="isFileSelected(file)"
                                                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded"
                                                            @click.prevent="toggleFileSelection(file)">
                                                        <div class="ml-3 flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate"
                                                                x-text="file.name"></p>
                                                            <p class="text-xs text-gray-500"
                                                                x-text="file.source + ' • ' + (file.size / 1024).toFixed(0) + ' KB'">
                                                            </p>
                                                        </div>
                                                    </label>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Notes (Optional) --}}
                        <div class="mt-4">
                            <label for="skip-review-notes" class="block text-sm font-medium text-gray-700">Notes
                                (Optional)</label>
                            <textarea id="skip-review-notes" name="notes" rows="2" x-model="skipReviewNotes"
                                placeholder="Reason for skipping review..."
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm"></textarea>
                        </div>

                        <div class="mt-5 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:col-start-2 sm:text-sm">
                                <i class="fa-solid fa-check mr-2"></i> Accept & Skip Review
                            </button>
                            <button type="button" @click="skipReviewModalOpen = false"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================== ACCEPT SUBMISSION MODAL ==================== --}}
        <div x-show="acceptModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="accept-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="acceptModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                    @click="acceptModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="acceptModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">

                    <!-- Form -->
                    <form
                        action="{{ route('journal.workflow.record-decision', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="decision" value="accept">

                        <!-- Modal Header -->
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-green-50">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900" id="accept-modal-title">
                                    Accept Submission
                                </h3>
                                <button type="button" @click="acceptModalOpen = false"
                                    class="text-gray-400 hover:text-gray-600">
                                    <i class="fa-solid fa-times text-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="px-6 py-5 space-y-6 max-h-[70vh] overflow-y-auto">
                            <!-- Send Email Options -->
                            <div class="space-y-3">
                                <h4 class="text-sm font-semibold text-gray-900">Send Email Notification</h4>
                                <div class="space-y-2">
                                    <label
                                        class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer"
                                        :class="{ 'bg-emerald-50 border-emerald-500': acceptSendEmail }">
                                        <input type="radio" name="send_email" value="1"
                                            x-model="acceptSendEmail" :value="true"
                                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                        <div class="flex-1">
                                            <span class="block text-sm font-medium text-gray-900">Send email to
                                                author</span>
                                            <span
                                                class="block text-xs text-gray-500">{{ $submission->authors->first()->name ?? 'Author' }}</span>
                                        </div>
                                    </label>
                                    <label
                                        class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer"
                                        :class="{ 'bg-emerald-50 border-emerald-500': !acceptSendEmail }">
                                        <input type="radio" name="send_email" value="0"
                                            x-model="acceptSendEmail" :value="false"
                                            class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                        <div class="flex-1">
                                            <span class="block text-sm font-medium text-gray-900">Do not send
                                                email</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Email Content -->
                            <div x-show="acceptSendEmail" class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Email Content</label>
                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                    <textarea name="email_body" id="accept-email-editor" rows="6" class="hidden"></textarea>
                                </div>
                            </div>

                            <!-- Files Selection -->
                            <div class="space-y-3">
                                <h4 class="text-sm font-medium text-gray-900">Select Revised Files to move to Copyediting
                                </h4>
                                <p class="text-xs text-gray-500">Choose files from author revisions and reviewer attachments to
                                    promote to the next stage.</p>

                                <div class="border rounded-md overflow-hidden bg-white">
                                    <div x-show="acceptIsLoading" class="p-4 text-center text-gray-500">
                                        <i class="fa-solid fa-spinner fa-spin mr-2"></i> Loading files...
                                    </div>

                                    <ul x-show="!acceptIsLoading"
                                        class="divide-y divide-gray-200 max-h-48 overflow-y-auto">
                                        <template x-for="file in acceptFiles" :key="file.id">
                                            <li class="px-4 py-3 flex items-center hover:bg-gray-50 cursor-pointer"
                                                @click="toggleAcceptFile(file.id)">
                                                <input type="checkbox" name="selected_files[]"
                                                    :value="file.id"
                                                    :checked="acceptSelectedFiles.includes(file.id)"
                                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900" x-text="file.name">
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        <span x-text="file.source"></span> | <span
                                                            x-text="file.created_at"></span>
                                                    </p>
                                                </div>
                                            </li>
                                        </template>
                                        <li x-show="acceptFiles.length === 0"
                                            class="px-4 py-4 text-sm text-yellow-700 bg-yellow-50 rounded-b-md border-t border-yellow-200 italic text-center">
                                            No revised files found. Please ensure the author has uploaded a revision before promoting.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                            <button type="button" @click="acceptModalOpen = false"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 bg-emerald-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-emerald-700">
                                Record Decision
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================== DECLINE SUBMISSION MODAL ==================== --}}
        <div x-show="declineModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="decline-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="declineModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                    @click="declineModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="declineModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fa-solid fa-ban text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900" id="decline-modal-title">
                                Decline Submission
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                This will reject the submission. Please provide a reason for declining.
                            </p>
                        </div>
                    </div>

                    <form
                        action="{{ route('journal.workflow.decline', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                        method="POST" class="mt-5">
                        @csrf

                        {{-- Reason Textarea --}}
                        <div>
                            <label for="decline-reason" class="block text-sm font-medium text-gray-700">
                                Reason for Declining <span class="text-red-500">*</span>
                            </label>
                            <textarea id="decline-reason" name="reason" rows="4" required minlength="10" x-model="declineReason"
                                placeholder="Please explain why this submission is being declined. This will be logged for future reference."
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
                            <p class="mt-1 text-xs text-gray-500">Minimum 10 characters required.</p>
                        </div>

                        {{-- Notify Author Checkbox --}}
                        <div class="mt-4 flex items-start">
                            <div class="flex items-center h-5">
                                <input id="notify-author" name="notify_author" type="checkbox"
                                    x-model="notifyAuthor"
                                    class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="notify-author" class="font-medium text-gray-700">Notify the
                                    Author</label>
                                <p class="text-gray-500">Send an email notification to the author about this
                                    decision.
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="submit" :disabled="declineReason.length < 10"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed sm:col-start-2 sm:text-sm">
                                <i class="fa-solid fa-ban mr-2"></i> Decline Submission
                            </button>
                            <button type="button" @click="declineModalOpen = false"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:col-start-1 sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================== REQUEST REVISIONS MODAL (OJS 3.3 Style) ==================== --}}
        <div x-show="revisionModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="revision-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="revisionModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                    @click="resetRevisionModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="revisionModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">

                    {{-- Modal Header --}}
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-amber-50 to-yellow-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-yellow-100">
                                    <i class="fa-solid fa-pen-to-square text-yellow-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900" id="revision-modal-title">
                                        Request Revisions
                                    </h3>
                                    <p class="text-sm text-gray-500">Configure revision request for the author</p>
                                </div>
                            </div>
                            <button type="button" @click="resetRevisionModal()"
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fa-solid fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Modal Body --}}
                    <form
                        action="{{ route('journal.workflow.request-revisions', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                        method="POST" @submit="revisionIsSubmitting = true">
                        @csrf

                        <div class="px-6 py-5 space-y-6 max-h-[70vh] overflow-y-auto">

                            {{-- Section A: New Review Round --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">
                                    <i class="fa-solid fa-rotate text-indigo-500 mr-2"></i>
                                    Require New Review Round
                                </h4>
                                <div class="space-y-3">
                                    <label
                                        class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 bg-white hover:border-indigo-300 transition-colors"
                                        :class="{ 'border-indigo-500 ring-2 ring-indigo-100': !revisionNewRound }">
                                        <input type="radio" name="new_review_round" value="0"
                                            x-model="revisionNewRound" :value="false"
                                            class="mt-0.5 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">No new review
                                                round</span>
                                            <p class="text-xs text-gray-500 mt-0.5">Revisions will not be subject
                                                to a
                                                new round of peer reviews.</p>
                                        </div>
                                    </label>
                                    <label
                                        class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 bg-white hover:border-indigo-300 transition-colors"
                                        :class="{ 'border-indigo-500 ring-2 ring-indigo-100': revisionNewRound }">
                                        <input type="radio" name="new_review_round" value="1"
                                            x-model="revisionNewRound" :value="true"
                                            class="mt-0.5 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">Require new review
                                                round</span>
                                            <p class="text-xs text-gray-500 mt-0.5">Revisions will be subject to a
                                                new
                                                round of peer reviews.</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Section B: Send Email --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">
                                    <i class="fa-solid fa-envelope text-blue-500 mr-2"></i>
                                    Send Email
                                </h4>
                                <div class="space-y-3">
                                    <label
                                        class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 bg-white hover:border-blue-300 transition-colors"
                                        :class="{ 'border-blue-500 ring-2 ring-blue-100': revisionSendEmail }">
                                        <input type="radio" name="send_email" value="1"
                                            x-model="revisionSendEmail" :value="true"
                                            class="mt-0.5 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">Send email
                                                notification</span>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                Notify author(s): <span
                                                    class="font-medium text-gray-700">{{ $submission->authors->first()?->name ?? 'Author' }}</span>
                                            </p>
                                        </div>
                                    </label>
                                    <label
                                        class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 bg-white hover:border-blue-300 transition-colors"
                                        :class="{ 'border-blue-500 ring-2 ring-blue-100': !revisionSendEmail }">
                                        <input type="radio" name="send_email" value="0"
                                            x-model="revisionSendEmail" :value="false"
                                            class="mt-0.5 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">Do not send
                                                email</span>
                                            <p class="text-xs text-gray-500 mt-0.5">No email notification will be
                                                sent.</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Section C: Email Content --}}
                            <div x-show="revisionSendEmail" x-transition
                                class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">
                                    <i class="fa-solid fa-file-lines text-teal-500 mr-2"></i>
                                    Email Content
                                </h4>
                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                    <textarea name="email_body" id="revision-email-editor" class="hidden"></textarea>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    Include reviewer feedback and specific revision requirements.
                                </p>
                            </div>

                            {{-- Section D: Review Attachments --}}
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-gray-900">
                                        <i class="fa-solid fa-paperclip text-purple-500 mr-2"></i>
                                        Review Attachments
                                    </h4>
                                    <label
                                        class="inline-flex items-center text-xs font-medium text-indigo-600 hover:text-indigo-800 cursor-pointer transition-colors">
                                        <i class="fa-solid fa-upload mr-1"></i>
                                        Upload File
                                        <input type="file" class="sr-only"
                                            @change="uploadRevisionFile($event)">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mb-3">Select files to share with the author(s).
                                </p>

                                {{-- Hidden inputs for selected files --}}
                                <template x-for="(fileId, index) in revisionSelectedFiles" :key="fileId">
                                    <input type="hidden" :name="'selected_files[' + index + ']'"
                                        :value="fileId">
                                </template>

                                {{-- File List --}}
                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                    <template x-if="revisionIsLoadingFiles">
                                        <div class="px-4 py-6 text-center">
                                            <i class="fa-solid fa-spinner fa-spin text-gray-400 text-lg"></i>
                                            <p class="text-sm text-gray-500 mt-2">Loading files...</p>
                                        </div>
                                    </template>

                                    <template
                                        x-if="!revisionIsLoadingFiles && revisionAttachments.length === 0 && revisionUploadedFiles.length === 0">
                                        <div class="px-4 py-6 text-center">
                                            <i class="fa-solid fa-folder-open text-gray-300 text-2xl"></i>
                                            <p class="text-sm text-gray-500 mt-2">No reviewer attachments
                                                available.
                                            </p>
                                        </div>
                                    </template>

                                    <template
                                        x-if="!revisionIsLoadingFiles && (revisionAttachments.length > 0 || revisionUploadedFiles.length > 0)">
                                        <ul class="divide-y divide-gray-100 max-h-48 overflow-y-auto">
                                            {{-- Existing Reviewer Files --}}
                                            <template x-for="file in revisionAttachments" :key="file.id">
                                                <li class="px-4 py-3 hover:bg-gray-50 cursor-pointer"
                                                    @click="toggleRevisionFile(file.id)">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="checkbox"
                                                            :checked="isRevisionFileSelected(file.id)"
                                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                        <div class="ml-3 flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate"
                                                                x-text="file.name"></p>
                                                            <p class="text-xs text-gray-500">
                                                                <span
                                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700 mr-1">
                                                                    <i class="fa-solid fa-user-check mr-1"></i>
                                                                    <span x-text="file.uploader"></span>
                                                                </span>
                                                                <span x-text="file.uploaded_at"></span>
                                                            </p>
                                                        </div>
                                                    </label>
                                                </li>
                                            </template>

                                            {{-- Newly Uploaded Files --}}
                                            <template x-for="file in revisionUploadedFiles" :key="file.id">
                                                <li class="px-4 py-3 bg-green-50 border-l-4 border-green-400">
                                                    <label class="flex items-center">
                                                        <input type="checkbox" checked disabled
                                                            class="h-4 w-4 text-green-600 border-gray-300 rounded cursor-not-allowed">
                                                        <div class="ml-3 flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate"
                                                                x-text="file.name"></p>
                                                            <p class="text-xs text-green-600">
                                                                <i class="fa-solid fa-check-circle mr-1"></i>
                                                                Just uploaded
                                                            </p>
                                                        </div>
                                                    </label>
                                                </li>
                                            </template>
                                        </ul>
                                    </template>
                                </div>

                                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                    <span>
                                        <span x-text="revisionSelectedFiles.length"></span> file(s) selected
                                    </span>
                                </div>
                            </div>

                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                            <button type="button" @click="resetRevisionModal()"
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                :disabled="revisionIsSubmitting || (revisionSendEmail && !revisionEmailBody)"
                                class="inline-flex items-center px-4 py-2.5 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <i class="fa-solid fa-gavel mr-2"></i>
                                <span
                                    x-text="revisionIsSubmitting ? 'Submitting...' : 'Record Editorial Decision'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================== DECISION NOTIFICATION MODAL (Author View) ==================== --}}
        <div x-show="showDecisionModal" x-cloak class="fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="decision-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDecisionModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                    @click="showDecisionModal = false; selectedDecision = null"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showDecisionModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                    {{-- Modal Header --}}
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100">
                                    <i class="fa-solid fa-envelope-open-text text-indigo-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900" id="decision-modal-title"
                                        x-text="selectedDecision?.type_label || 'Editorial Decision'"></h3>
                                    <p class="text-sm text-gray-500" x-show="selectedDecision?.made_at"
                                        x-text="selectedDecision?.made_at ? new Date(selectedDecision.made_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : ''">
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="showDecisionModal = false; selectedDecision = null"
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fa-solid fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Modal Body --}}
                    <div class="px-6 py-6 max-h-[60vh] overflow-y-auto">
                        {{-- Email Content --}}
                        <div x-show="selectedDecision?.email_body" class="prose prose-sm max-w-none">
                            <div x-html="selectedDecision?.email_body || ''"></div>
                        </div>

                        {{-- No Email Content --}}
                        <div x-show="!selectedDecision?.email_body" class="text-center py-8">
                            <i class="fa-solid fa-envelope-circle-check text-gray-300 text-4xl"></i>
                            <p class="text-gray-500 mt-4">No detailed message was included with this decision.</p>
                            <p class="text-sm text-gray-400 mt-1">Please contact the editor if you need more
                                information.</p>
                        </div>

                        {{-- New Round Notice --}}
                        <div x-show="selectedDecision?.new_review_round" class="mt-6 border-t border-gray-200 pt-4">
                            <div class="flex items-center gap-2 text-amber-600 bg-amber-50 px-4 py-3 rounded-lg">
                                <i class="fa-solid fa-rotate"></i>
                                <span class="text-sm font-medium">Your revised manuscript will undergo a new round
                                    of
                                    peer review.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                        <button type="button" @click="showDecisionModal = false; selectedDecision = null"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== REVISION UPLOAD MODAL (Author View) ==================== --}}
        <div x-show="revisionUploadModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="revision-upload-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="revisionUploadModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                    @click="revisionUploadModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="revisionUploadModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    {{-- Modal Header --}}
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-teal-50 to-emerald-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-teal-100">
                                    <i class="fa-solid fa-file-arrow-up text-teal-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900"
                                        id="revision-upload-modal-title">
                                        Upload Revised Manuscript
                                    </h3>
                                    <p class="text-sm text-gray-500">Submit your revised files</p>
                                </div>
                            </div>
                            <button type="button" @click="revisionUploadModalOpen = false"
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fa-solid fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Modal Body --}}
                    <form
                        action="{{ route('journal.submissions.files.store', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="stage" value="revision">
                        <input type="hidden" name="file_type" value="revision">

                        <div class="px-6 py-6 space-y-4">
                            <div
                                class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-teal-400 transition-colors">
                                <input type="file" name="file" id="revisionFileInput" class="sr-only"
                                    accept=".doc,.docx,.pdf,.odt,.rtf" required>
                                <label for="revisionFileInput" class="cursor-pointer">
                                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 mb-4 block"></i>
                                    <p class="text-sm font-medium text-gray-700">Click to upload your revised
                                        manuscript</p>
                                    <p class="text-xs text-gray-500 mt-1">DOC, DOCX, PDF, ODT (Max 10MB)</p>
                                </label>
                            </div>

                            {{-- File Selected Preview --}}
                            <div id="revisionFilePreview" class="hidden bg-teal-50 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-file-check text-teal-600 text-xl"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900" id="revisionFileName"></p>
                                        <p class="text-xs text-gray-500" id="revisionFileSize"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-xs text-blue-700">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    Please ensure your revised manuscript addresses all reviewer comments before
                                    uploading.
                                </p>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                            <button type="button" @click="revisionUploadModalOpen = false"
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2.5 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition-colors">
                                <i class="fa-solid fa-upload mr-2"></i> Upload Revision
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            // Preview selected file for revision upload
            document.getElementById('revisionFileInput')?.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById('revisionFilePreview');
                const fileName = document.getElementById('revisionFileName');
                const fileSize = document.getElementById('revisionFileSize');

                if (file) {
                    preview.classList.remove('hidden');
                    fileName.textContent = file.name;
                    fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                } else {
                    preview.classList.add('hidden');
                }
            });
        </script>

        {{-- ==================== NEW REVIEW ROUND MODAL (Editor View) ==================== --}}
        <div x-show="newRoundModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="new-round-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="newRoundModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                    @click="resetNewRoundModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="newRoundModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    {{-- Modal Header --}}
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100">
                                    <i class="fa-solid fa-rotate text-indigo-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900" id="new-round-modal-title">
                                        Create New Review Round
                                    </h3>
                                    <p class="text-sm text-gray-500">Round
                                        {{ ($submission->currentReviewRound()?->round ?? 0) + 1 }}
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="resetNewRoundModal()"
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fa-solid fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Modal Body --}}
                    <form method="POST"
                        action="{{ route('journal.workflow.create-new-round', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                        @submit="newRoundIsSubmitting = true">
                        @csrf
                        <div class="px-6 py-6 space-y-4">
                            <p class="text-sm text-gray-600">
                                You are about to create a new review round for this submission. Select the revision
                                files you want to send to reviewers.
                            </p>

                            {{-- File Selection --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Select Files for Review
                                </label>

                                {{-- Loading State --}}
                                <div x-show="newRoundIsLoading" class="flex items-center justify-center py-8">
                                    <i class="fa-solid fa-spinner fa-spin text-indigo-500 text-2xl"></i>
                                    <span class="ml-3 text-sm text-gray-500">Loading revision files...</span>
                                </div>

                                {{-- File List --}}
                                <div x-show="!newRoundIsLoading"
                                    class="border border-gray-200 rounded-lg divide-y divide-gray-100 max-h-64 overflow-y-auto">
                                    <template x-if="newRoundFiles.length === 0">
                                        <div class="text-center py-6 px-4">
                                            <i class="fa-solid fa-folder-open text-gray-300 text-2xl"></i>
                                            <p class="text-sm text-gray-500 mt-2">No revision files available.</p>
                                        </div>
                                    </template>
                                    <template x-for="file in newRoundFiles" :key="file.id">
                                        <label class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" name="selected_files[]" :value="file.id"
                                                :checked="isNewRoundFileSelected(file.id)"
                                                @change="toggleNewRoundFile(file.id)"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            <div class="ml-3 flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate"
                                                    x-text="file.name"></p>
                                                <p class="text-xs text-gray-500">
                                                    <span x-text="file.uploader"></span> •
                                                    <span x-text="file.uploaded_at"></span>
                                                </p>
                                            </div>
                                        </label>
                                    </template>
                                </div>

                                <p class="text-xs text-gray-500 flex items-center gap-1.5 mt-2">
                                    <i class="fa-solid fa-info-circle text-gray-400"></i>
                                    <span x-text="newRoundSelectedFiles.length"></span> file(s) selected for
                                    promotion
                                </p>
                            </div>

                            {{-- Info Box --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-xs text-blue-700">
                                    <i class="fa-solid fa-lightbulb mr-1"></i>
                                    Selected files will be copied to the new review round as review files. You can
                                    then
                                    assign new reviewers.
                                </p>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                            <button type="button" @click="resetNewRoundModal()"
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                :disabled="newRoundIsSubmitting || newRoundSelectedFiles.length === 0"
                                :class="(newRoundIsSubmitting || newRoundSelectedFiles.length === 0) ?
                                'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                                class="flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fa-solid fa-rotate mr-2"
                                    :class="newRoundIsSubmitting ? 'fa-spin' : ''"></i>
                                <span
                                    x-text="newRoundIsSubmitting ? 'Creating...' : 'Create New Review Round'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================== ACTIVITY LOG MODAL ==================== --}}
        <div x-show="showActivityLog" x-cloak class="fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="activity-log-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showActivityLog" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="showActivityLog = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showActivityLog" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">

                    {{-- Modal Header --}}
                    <div class="bg-indigo-600 px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 h-10 w-10 rounded-lg bg-indigo-500 flex items-center justify-center">
                                    <i class="fa-solid fa-clock-rotate-left text-white text-lg"></i>
                                </div>
                                <h3 class="ml-3 text-xl font-bold text-white" id="activity-log-modal-title">
                                    Submission Activity Log
                                </h3>
                            </div>
                            <button @click="showActivityLog = false" type="button"
                                class="bg-indigo-500 hover:bg-indigo-700 rounded-lg p-2 text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white">
                                <i class="fa-solid fa-times text-lg"></i>
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-indigo-50">
                            Complete timeline of actions and events for <span
                                class="font-semibold text-white">{{ $submission->submission_code }}</span>
                        </p>
                    </div>

                    {{-- Modal Body --}}
                    <div class="bg-white px-6 py-6 max-h-[calc(100vh-16rem)] overflow-y-auto">
                        @php
                            $allLogs = $submission->logs()->with('user')->orderBy('created_at', 'desc')->get();
                        @endphp

                        @if ($allLogs->count() > 0)
                            {{-- Timeline Layout --}}
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    @foreach ($allLogs as $log)
                                        @php
                                            $isLast = $loop->last;
                                            $colorMap = [
                                                'indigo' => [
                                                    'bg' => 'bg-indigo-500',
                                                    'text' => 'text-indigo-700',
                                                    'ring' => 'ring-indigo-100',
                                                ],
                                                'purple' => [
                                                    'bg' => 'bg-purple-500',
                                                    'text' => 'text-purple-700',
                                                    'ring' => 'ring-purple-100',
                                                ],
                                                'blue' => [
                                                    'bg' => 'bg-blue-500',
                                                    'text' => 'text-blue-700',
                                                    'ring' => 'ring-blue-100',
                                                ],
                                                'emerald' => [
                                                    'bg' => 'bg-emerald-500',
                                                    'text' => 'text-emerald-700',
                                                    'ring' => 'ring-emerald-100',
                                                ],
                                                'amber' => [
                                                    'bg' => 'bg-amber-500',
                                                    'text' => 'text-amber-700',
                                                    'ring' => 'ring-amber-100',
                                                ],
                                                'green' => [
                                                    'bg' => 'bg-green-500',
                                                    'text' => 'text-green-700',
                                                    'ring' => 'ring-green-100',
                                                ],
                                                'red' => [
                                                    'bg' => 'bg-red-500',
                                                    'text' => 'text-red-700',
                                                    'ring' => 'ring-red-100',
                                                ],
                                                'gray' => [
                                                    'bg' => 'bg-gray-400',
                                                    'text' => 'text-gray-700',
                                                    'ring' => 'ring-gray-100',
                                                ],
                                            ];
                                            $colors = $colorMap[$log->color] ?? $colorMap['gray'];
                                        @endphp
                                        <li x-data="{ showEmail: false }">
                                            <div class="relative pb-8">
                                                @if (!$isLast)
                                                    <span
                                                        class="absolute left-5 top-5 -ml-px h-full w-0.5 bg-gray-200"
                                                        aria-hidden="true"></span>
                                                @endif

                                                <div class="relative flex items-start space-x-3">
                                                    {{-- Icon --}}
                                                    <div class="relative">
                                                        <span
                                                            class="h-10 w-10 rounded-full {{ $colors['bg'] }} flex items-center justify-center ring-8 ring-white shadow-sm">
                                                            <i
                                                                class="fa-solid {{ $log->icon }} text-white text-sm"></i>
                                                        </span>
                                                    </div>

                                                    {{-- Content --}}
                                                    <div class="min-w-0 flex-1">
                                                        <div class="text-sm">
                                                            <p class="font-semibold text-gray-900 mb-1">
                                                                {{ $log->title }}
                                                            </p>
                                                            <p class="text-gray-600 leading-relaxed">
                                                                {{ $log->description }}
                                                            </p>
                                                        </div>

                                                        <div
                                                            class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                                            <div class="flex items-center">
                                                                <i
                                                                    class="fa-solid fa-calendar mr-1.5 text-gray-400"></i>
                                                                <time
                                                                    datetime="{{ $log->created_at->toIso8601String() }}">
                                                                    {{ $log->created_at->format('M d, Y') }}
                                                                </time>
                                                            </div>
                                                            <div class="flex items-center">
                                                                <i class="fa-solid fa-clock mr-1.5 text-gray-400"></i>
                                                                {{ $log->created_at->format('H:i') }}
                                                            </div>
                                                            @if ($log->user)
                                                                <div class="flex items-center">
                                                                    <i
                                                                        class="fa-solid fa-user mr-1.5 text-gray-400"></i>
                                                                    <span
                                                                        class="font-medium text-gray-700">{{ $log->user->name }}</span>
                                                                    <span
                                                                        class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                                        {{ $log->user->primary_role_label }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        {{-- Email Preview Toggle --}}
                                                        @if ($log->email_body)
                                                            <div class="mt-3">
                                                                <button @click="showEmail = !showEmail" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                                    <i class="fa-solid fa-envelope mr-1.5 text-gray-500"></i>
                                                                    <span x-text="showEmail ? 'Hide Email' : 'View Email'"></span>
                                                                </button>
                                                                <div x-show="showEmail" x-collapse class="mt-3 bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden text-sm">
                                                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex flex-col gap-1">
                                                                        <div class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Subject</div>
                                                                        <div class="font-medium text-gray-900">{{ $log->email_subject ?? 'No Subject' }}</div>
                                                                    </div>
                                                                    <div class="p-4 prose prose-sm max-w-none text-gray-700">
                                                                        {!! clean($log->email_body) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($log->files && $log->files->count() > 0)
                                                            <div class="mt-3 flex flex-col gap-2">
                                                                @foreach ($log->files as $file)
                                                                    @php
                                                                        $fileUrl = isset($file->metadata['copied_from_discussion'])
                                                                            ? route('journal.discussion.file.download', ['journal' => $journal->slug, 'file' => $file->metadata['copied_from_discussion']])
                                                                            : route('files.download', $file);
                                                                    @endphp
                                                                    <a href="{{ $fileUrl }}" class="inline-flex items-center px-3 py-1.5 border border-gray-200 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:text-indigo-600 transition-colors max-w-[20rem]">
                                                                        <i class="fa-solid fa-paperclip text-gray-400 mr-2 flex-shrink-0"></i>
                                                                        <span class="truncate">{{ $file->file_name }}</span>
                                                                        <span class="ml-2 text-xs text-gray-400 flex-shrink-0">({{ $file->file_size_formatted }})</span>
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            {{-- Empty State --}}
                            <div class="text-center py-12">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                    <i class="fa-solid fa-timeline text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">No Activity Yet</h3>
                                <p class="text-sm text-gray-500">Activity will appear here as actions are taken on
                                    this submission.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-200 gap-2">
                        <button @click="showActivityLog = false" type="button"
                            class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto">
                            Close
                        </button>
                        @if ($submission->status == \App\Models\Submission::STATUS_PUBLISHED)
                            <a href="{{ route('journal.correspondence.download', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                target="_blank"
                                class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto">
                                <i class="fa-solid fa-file-pdf text-red-500 mr-2"></i> Download Correspondence Proof
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== SEND TO PRODUCTION MODAL (OJS 3.3 Style) ==================== --}}
        <div x-show="sendToProductionModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="send-production-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="sendToProductionModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="resetProductionModal()"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="sendToProductionModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">

                    {{-- Modal Header --}}
                    <div class="bg-white px-6 py-5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 h-10 w-10 rounded-lg bg-teal-500 flex items-center justify-center">
                                    <i class="fa-solid fa-cogs text-white text-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-xl font-bold text-green-500" id="send-production-modal-title">
                                        Send to Production
                                    </h3>
                                    <p class="text-sm text-teal-100 mt-0.5">Record your editorial decision</p>
                                </div>
                            </div>
                            <button @click="resetProductionModal()" type="button"
                                class="bg-teal-500 hover:bg-teal-700 rounded-lg p-2 text-white transition-colors focus:outline-none focus:ring-2 focus:ring-white">
                                <i class="fa-solid fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Modal Body --}}
                    <form
                        action="{{ route('journal.workflow.send-production', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                        method="POST" @submit="productionIsSubmitting = true">
                        @csrf

                        <div class="bg-white px-6 py-6 max-h-[calc(100vh-16rem)] overflow-y-auto">
                            {{-- Section A: Email Notification --}}
                            <div class="mb-6">
                                <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                                    <i class="fa-solid fa-envelope text-teal-500 mr-2"></i>
                                    Send Email
                                </h4>
                                <div class="space-y-3 bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <label class="flex items-start cursor-pointer group">
                                        <input type="radio" name="send_email" value="1"
                                            x-model="productionSendEmail" @change="productionSendEmail = true"
                                            class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 mt-0.5">
                                        <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                                            Send an email notification to the author(s):
                                            <span
                                                class="font-semibold text-teal-700">{{ $submission->author->name ?? ($submission->authors->first()?->name ?? 'Author') }}</span>
                                        </span>
                                    </label>
                                    <label class="flex items-start cursor-pointer group">
                                        <input type="radio" name="send_email" value="0"
                                            x-model="productionSendEmail" @change="productionSendEmail = false"
                                            class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 mt-0.5">
                                        <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                                            Do not send an email notification
                                        </span>
                                    </label>
                                </div>
                            </div>

                            {{-- Section B: Email Body (Rich Text) --}}
                            <div x-show="productionSendEmail" x-transition class="mb-6">
                                <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                                    <i class="fa-solid fa-file-lines text-teal-500 mr-2"></i>
                                    Email Content
                                </h4>
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <textarea id="production-email-editor" name="email_body" x-model="productionEmailBody"
                                        class="w-full min-h-[200px]"></textarea>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    This email will be sent to notify the author that their submission is moving to
                                    production.
                                </p>
                            </div>

                            {{-- Section C: Files to be Auto-Forwarded --}}
                            <div class="mb-4">
                                <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                                    <i class="fa-solid fa-files text-teal-500 mr-2"></i>
                                    Files to be Forwarded Automatically
                                </h4>
                                <p class="text-xs text-gray-600 mb-3">
                                    The following draft files will be automatically forwarded to the Production stage:
                                </p>

                                @php
                                    $copyeditedFiles = $submission->files->where('stage', 'copyedited');
                                    $draftFiles      = $submission->files->where('stage', 'copyedit_draft');
                                    $hasAnyFiles     = $copyeditedFiles->count() > 0 || $draftFiles->count() > 0;
                                @endphp

                                <div class="border border-gray-200 rounded-lg overflow-hidden divide-y divide-gray-200">
                                    {{-- Copyedited Files Section --}}
                                    @if ($copyeditedFiles->count() > 0)
                                        <div class="bg-teal-50">
                                            <div class="px-4 py-2 border-b border-teal-100">
                                                <span class="text-xs font-bold uppercase tracking-wider text-teal-700">
                                                    <i class="fa-solid fa-check-circle mr-1"></i>
                                                    Copyedited ({{ $copyeditedFiles->count() }})
                                                </span>
                                            </div>
                                            @foreach ($copyeditedFiles as $file)
                                                <div class="flex items-center px-4 py-3 border-b border-teal-100 last:border-b-0 bg-white">
                                                    <i class="fa-regular fa-file-lines text-teal-500 flex-shrink-0 mr-3"></i>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $file->file_name }}</p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $file->created_at->format('M d, Y') }} •
                                                            {{ number_format($file->file_size / 1024, 0) }} KB
                                                        </p>
                                                    </div>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-teal-100 text-teal-800 flex-shrink-0 ml-2">
                                                        Copyedited
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Draft Files Section --}}
                                    @if ($draftFiles->count() > 0)
                                        <div class="bg-blue-50">
                                            <div class="px-4 py-2 border-b border-blue-100">
                                                <span class="text-xs font-bold uppercase tracking-wider text-blue-700">
                                                    <i class="fa-solid fa-file-import mr-1"></i>
                                                    Draft Files ({{ $draftFiles->count() }})
                                                </span>
                                            </div>
                                            @foreach ($draftFiles as $file)
                                                <div class="flex items-center px-4 py-3 border-b border-blue-100 last:border-b-0 bg-white">
                                                    <i class="fa-regular fa-file text-blue-500 flex-shrink-0 mr-3"></i>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $file->file_name }}</p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $file->created_at->format('M d, Y') }} •
                                                            {{ number_format($file->file_size / 1024, 0) }} KB
                                                        </p>
                                                    </div>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 flex-shrink-0 ml-2">
                                                        Draft
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Empty State --}}
                                    @if (!$hasAnyFiles)
                                        <div class="px-6 py-10 text-center bg-gray-50">
                                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-100 mb-3">
                                                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-xl"></i>
                                            </div>
                                            <p class="text-sm font-medium text-gray-900">No draft files found</p>
                                            <p class="text-xs text-gray-500 mt-1">No draft or copyedited files exist in Copyediting. You may proceed, but no files will be forwarded to Production.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Info Notice --}}
                            @if ($hasAnyFiles)
                                <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                                    <div class="flex">
                                        <i class="fa-solid fa-circle-info text-teal-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                        <div>
                                            <p class="text-sm font-medium text-teal-800">Automatic File Forwarding</p>
                                            <p class="text-xs text-teal-700 mt-1">
                                                All {{ $copyeditedFiles->count() + $draftFiles->count() }} file(s) listed above will be automatically copied to the Production stage.
                                                The submission status will change to "In Production".
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Modal Footer --}}
                        <div
                            class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-gray-200">
                            <button @click="resetProductionModal()" type="button"
                                class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                Cancel
                            </button>
                            <button type="submit" :disabled="productionIsSubmitting"
                                :class="productionIsSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                                class="inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-teal-600 text-sm font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                <template x-if="productionIsSubmitting">
                                    <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                                </template>
                                <template x-if="!productionIsSubmitting">
                                    <i class="fa-solid fa-gavel mr-2"></i>
                                </template>
                                <span
                                    x-text="productionIsSubmitting ? 'Recording...' : 'Record Editorial Decision'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        {{-- ==================== MANAGE DRAFT FILES MODAL (Copyediting) ==================== --}}
        <div x-show="draftFilesModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto"
            aria-labelledby="draft-files-modal-title" role="dialog" aria-modal="true" x-data="{
                draftTab: 'upload',
                reviewFiles: [],
                selectedReviewFiles: [],
                isLoadingReviewFiles: false,
                isSubmittingSelection: false,
            
                async loadReviewFiles() {
                    this.isLoadingReviewFiles = true;
                    try {
                        const res = await fetch('{{ route('journal.workflow.review-stage-files', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}');
                        const data = await res.json();
                        this.reviewFiles = data.files || [];
                    } catch (e) {
                        console.error('Failed to load review files:', e);
                    }
                    this.isLoadingReviewFiles = false;
                },
            
                toggleReviewFile(fileId) {
                    const index = this.selectedReviewFiles.indexOf(fileId);
                    if (index > -1) {
                        this.selectedReviewFiles.splice(index, 1);
                    } else {
                        this.selectedReviewFiles.push(fileId);
                    }
                },
            
                async copySelectedFiles() {
                    if (this.selectedReviewFiles.length === 0) {
                        alert('Please select at least one file to copy.');
                        return;
                    }
            
                    this.isSubmittingSelection = true;
                    try {
                        const res = await fetch('{{ route('journal.workflow.copy-review-to-draft', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                file_ids: this.selectedReviewFiles
                            })
                        });
            
                        const data = await res.json();
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Failed to copy files. Please try again.');
                        }
                    } catch (e) {
                        console.error('Failed to copy files:', e);
                        alert('An error occurred. Please try again.');
                    }
                    this.isSubmittingSelection = false;
                },
            
                init() {
                    this.$watch('draftTab', (tab) => {
                        if (tab === 'select' && this.reviewFiles.length === 0) {
                            this.loadReviewFiles();
                        }
                    });
                }
            }">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="draftFilesModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="draftFilesModalOpen = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="draftFilesModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">

                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <i class="fa-solid fa-file-import mr-2"></i>
                                Manage Draft Files
                            </h3>
                            <button @click="draftFilesModalOpen = false"
                                class="text-blue-100 hover:text-white transition-colors">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Tabs --}}
                    <div class="border-b border-gray-200 bg-gray-50">
                        <nav class="flex -mb-px">
                            <button @click="draftTab = 'upload'"
                                :class="draftTab === 'upload' ? 'border-blue-600 text-blue-600' :
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                                <i class="fa-solid fa-upload mr-2"></i>
                                Upload New File
                            </button>
                            <button @click="draftTab = 'select'"
                                :class="draftTab === 'select' ? 'border-blue-600 text-blue-600' :
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors">
                                <i class="fa-solid fa-check-square mr-2"></i>
                                Select from Review
                            </button>
                        </nav>
                    </div>

                    {{-- Tab Content --}}
                    <div class="px-6 py-6">
                        {{-- Upload New File Tab --}}
                        <div x-show="draftTab === 'upload'">
                            <form method="POST"
                                action="{{ route('journal.submissions.files.store', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="stage" value="copyedit_draft">
                                <input type="hidden" name="file_type" value="manuscript">

                                <div class="space-y-4">
                                    <div
                                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                                        <input type="file" name="file" id="draftFileInput"
                                            accept=".doc,.docx,.pdf,.odt,.rtf" required class="hidden">
                                        <label for="draftFileInput" class="cursor-pointer">
                                            <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 mb-3"></i>
                                            <p class="text-sm text-gray-600 mb-1">Click to upload or drag and drop</p>
                                            <p class="text-xs text-gray-500">DOC, DOCX, PDF, ODT, RTF (max 20MB)</p>
                                        </label>
                                    </div>

                                    <div id="draftFilePreview"
                                        class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <i class="fa-solid fa-file-word text-blue-600 text-2xl mr-3"></i>
                                                <div>
                                                    <p id="draftFileName" class="text-sm font-medium text-gray-900">
                                                    </p>
                                                    <p id="draftFileSize" class="text-xs text-gray-500"></p>
                                                </div>
                                            </div>
                                            <button type="button"
                                                onclick="document.getElementById('draftFileInput').value = ''; document.getElementById('draftFilePreview').classList.add('hidden');"
                                                class="text-red-500 hover:text-red-700">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end gap-3">
                                    <button type="button" @click="draftFilesModalOpen = false"
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                                        <i class="fa-solid fa-upload mr-2"></i>
                                        Upload File
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Select from Review Tab --}}
                        <div x-show="draftTab === 'select'">
                            <div x-show="isLoadingReviewFiles" class="text-center py-8">
                                <i class="fa-solid fa-spinner fa-spin text-3xl text-blue-600 mb-3"></i>
                                <p class="text-sm text-gray-600">Loading review files...</p>
                            </div>

                            <div x-show="!isLoadingReviewFiles && reviewFiles.length === 0"
                                class="text-center py-8">
                                <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-3"></i>
                                <p class="text-sm text-gray-600">No files available from the Review stage.</p>
                            </div>

                            <div x-show="!isLoadingReviewFiles && reviewFiles.length > 0">
                                <p class="text-sm text-gray-600 mb-4">
                                    Select files from the Review stage to copy to Draft Files. The original files will
                                    remain in the Review stage.
                                </p>

                                <div class="space-y-2 max-h-96 overflow-y-auto">
                                    <template x-for="file in reviewFiles" :key="file.id">
                                        <label
                                            class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                            <input type="checkbox" :value="file.id"
                                                @change="toggleReviewFile(file.id)"
                                                :checked="selectedReviewFiles.includes(file.id)"
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900" x-text="file.name">
                                                    </p>
                                                    <span class="text-xs text-gray-500"
                                                        x-text="(file.size / 1024).toFixed(0) + ' KB'"></span>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    Uploaded by <span x-text="file.uploader"></span> on <span
                                                        x-text="file.uploaded_at"></span>
                                                </p>
                                            </div>
                                        </label>
                                    </template>
                                </div>

                                <div class="mt-6 flex justify-between items-center">
                                    <p class="text-sm text-gray-600">
                                        <span x-text="selectedReviewFiles.length"></span> file(s) selected
                                    </p>
                                    <div class="flex gap-3">
                                        <button type="button" @click="draftFilesModalOpen = false"
                                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Cancel
                                        </button>
                                        <button type="button" @click="copySelectedFiles()"
                                            :disabled="selectedReviewFiles.length === 0 || isSubmittingSelection"
                                            :class="selectedReviewFiles.length === 0 || isSubmittingSelection ?
                                                'opacity-50 cursor-not-allowed' : ''"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                                            <i class="fa-solid fa-copy mr-2"
                                                :class="isSubmittingSelection ? 'fa-spinner fa-spin' : 'fa-copy'"></i>
                                            <span
                                                x-text="isSubmittingSelection ? 'Copying...' : 'Copy to Draft Files'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Review Details Modal --}}
        <div x-show="reviewDetailsModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="reviewDetailsModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity"
                    aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="reviewDetailsModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-clipboard-check text-indigo-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Review Details
                                </h3>
                                <template x-if="selectedReview">
                                    <div class="mt-4">
                                        <div class="mb-4">
                                            <h4 class="text-sm font-semibold text-gray-700">Recommendation</h4>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                x-text="selectedReview?.recommendation_label || selectedReview?.recommendation"></span>
                                        </div>

                                        <div class="mb-4">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-1">Comments for Author</h4>
                                            <div class="bg-gray-50 p-3 rounded-md text-sm text-gray-600 prose prose-sm max-w-none"
                                                x-html="selectedReview?.comments_for_author || '<em>No comments provided.</em>'">
                                            </div>
                                        </div>

                                        @if (!$isAuthorView)
                                            <div class="mb-4">
                                                <h4 class="text-sm font-semibold text-gray-700 mb-1">Comments for Editor
                                                    (Internal)</h4>
                                                <div class="bg-yellow-50 p-3 rounded-md text-sm text-gray-600 prose prose-sm max-w-none border border-yellow-200"
                                                    x-html="selectedReview?.comments_for_editor || '<em>No confidential comments provided.</em>'">
                                                </div>
                                            </div>

                                            {{-- Reviewer Rating Section --}}
                                            <div class="mt-6 pt-6 border-t border-gray-100" x-data="{ 
                                                hoverRating: 0,
                                                isSaving: false,
                                                showSaved: false,
                                                async saveRating(val) {
                                                    this.isSaving = true;
                                                    try {
                                                        const response = await fetch(`{{ route('journal.workflow.review-assignment.rate', ['journal' => $journal->slug, 'reviewAssignment' => '__ID__']) }}`.replace('__ID__', selectedReview.id), {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'Accept': 'application/json',
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                            },
                                                            body: JSON.stringify({ quality_rating: val })
                                                        });
                                                        if (response.ok) {
                                                            this.showSaved = true;
                                                            selectedReview.quality_rating = val;
                                                            setTimeout(() => this.showSaved = false, 2000);
                                                        }
                                                    } catch (e) {
                                                        console.error(e);
                                                    }
                                                    this.isSaving = false;
                                                }
                                            }">
                                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Reviewer Quality Rating</h4>
                                                <div class="flex items-center gap-1">
                                                    <template x-for="i in 5">
                                                        <button @click="saveRating(i)" 
                                                                @mouseenter="hoverRating = i" 
                                                                @mouseleave="hoverRating = 0"
                                                                type="button"
                                                                class="text-2xl transition-all duration-150 focus:outline-none hover:scale-110"
                                                                :class="(hoverRating || selectedReview?.quality_rating || 0) >= i ? 'text-yellow-400' : 'text-gray-300'">
                                                            <i class="fa-solid fa-star"></i>
                                                        </button>
                                                    </template>
                                                    <span x-show="isSaving" class="ml-2 text-xs text-gray-400">
                                                        <i class="fa-solid fa-spinner fa-spin"></i>
                                                    </span>
                                                    <span x-show="showSaved" x-cloak x-transition class="ml-2 text-xs text-green-600 font-medium bg-green-50 px-2 py-1 rounded-full border border-green-100">
                                                        <i class="fa-solid fa-check"></i> Saved!
                                                    </span>
                                                </div>
                                                <p class="mt-1 text-xs text-gray-400">Rate the quality of this review for internal editorial records.</p>
                                            </div>
                                        @endif

                                        <div class="mt-2 text-xs text-gray-400">
                                            Completed at: <span
                                                x-text="new Date(selectedReview?.completed_at).toLocaleDateString()"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="closeReviewDetailsModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Review Assignment Modal --}}
        <div x-show="editReviewModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="edit-review-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editReviewModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="editReviewModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="editReviewModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 py-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900" id="edit-review-modal-title">
                                <i class="fa-solid fa-edit text-indigo-500 mr-2"></i>Edit Review Assignment
                            </h3>
                            <button @click="editReviewModalOpen = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                        <form @submit.prevent="submitEditReview()" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Review Method</label>
                                <select x-model="editReviewMethod" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="blind">Blind</option>
                                    <option value="double_blind">Double Blind</option>
                                    <option value="open">Open</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Response Due Date</label>
                                    <input type="date" x-model="editResponseDueDate" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Review Due Date</label>
                                    <input type="date" x-model="editReviewDueDate" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                                <button type="button" @click="editReviewModalOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                                <button type="submit" :disabled="editIsSubmitting" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50 flex items-center transition-colors">
                                    <i x-show="editIsSubmitting" class="fa-solid fa-spinner fa-spin mr-2"></i>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    {{-- ========== ADD/EDIT GALLEY MODAL (Extracted Component) ========== --}}
    <x-submissions.galley-modal :journal="$journal" :submission="$submission" :pubStatus="$pubStatus" />
    </div>
    <style>
        .ck-editor__editable {
            min-height: 250px !important;
        }

        .ck-editor__editable:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
        }
    </style>
    <script>
        // File preview for Draft Files upload
        document.addEventListener('DOMContentLoaded', function() {
            const draftFileInput = document.getElementById('draftFileInput');
            if (draftFileInput) {
                draftFileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const preview = document.getElementById('draftFilePreview');
                    const fileName = document.getElementById('draftFileName');
                    const fileSize = document.getElementById('draftFileSize');

                    if (file) {
                        preview.classList.remove('hidden');
                        fileName.textContent = file.name;
                        fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    } else {
                        preview.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const abstractTextarea = document.querySelector('#publicationAbstract');
            if (abstractTextarea) {
                ClassicEditor
                    .create(abstractTextarea, {
                        toolbar: {
                            items: [
                                'heading', '|',
                                'bold', 'italic', '|',
                                'bulletedList', 'numberedList', '|',
                                'outdent', 'indent', '|',
                                'link', 'blockQuote', 'insertTable', '|',
                                'undo', 'redo'
                            ]
                        }
                    })
                    .catch(error => {
                        console.error('CKEditor initialization failed:', error);
                    });
            }
        });
    </script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('submissionWorkflow', (config) => ({
                activeTab: (new URLSearchParams(window.location.search)).get('tab') || 'workflow',
                activeStage: config.defaultStage,

                init() {
                    // Check for URL parameters and clean them up after initialization
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('tab') || urlParams.has('subtab')) {
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }
                },

                fileModalOpen: false,
                discussionModalOpen: false,
                fileWizardOpen: false,
                uploadStage: config.defaultStage,
                discussionStageId: config.stageId,

                // Author Review View State
                selectedAuthorRound: config.currentReviewRound,
                showDecisionModal: false,
                selectedDecision: null,
                revisionUploadModalOpen: false,

                // Editor Review View State (Multi-Round)
                selectedEditorRound: config.maxReviewRound,
                newRoundModalOpen: false,
                newRoundFiles: [],
                newRoundSelectedFiles: [],
                newRoundIsLoading: false,
                newRoundIsSubmitting: false,

                // Accept Submission Modal State
                acceptModalOpen: false,
                acceptSendEmail: true,
                acceptEmailBody: '',
                acceptFiles: [],
                acceptSelectedFiles: [],
                acceptIsLoading: false,
                acceptIsSubmitting: false,
                acceptEditorInstance: null,

                // Assign Editor Modal State
                assignEditorModalOpen: false,
                editorSearch: '',
                editorRoleFilter: '',
                allEditors: config.potentialEditors || [],
                selectedEditor: null,
                editorRole: 'editor',
                isSearchingEditors: false, // kept for compatibility if needed

                get filteredEditors() {
                    let editors = this.allEditors;

                    // Text Search
                    if (this.editorSearch) {
                        const search = this.editorSearch.toLowerCase();
                        editors = editors.filter(e =>
                            e.name.toLowerCase().includes(search) ||
                            e.email.toLowerCase().includes(search)
                        );
                    }

                    // Role Filter
                    if (this.editorRoleFilter) {
                        editors = editors.filter(e =>
                            e.role_names && e.role_names.includes(this.editorRoleFilter)
                        );
                    }

                    return editors;
                },

                // Deprecated AJAX search (but kept just in case we need it later, or aliases to local filter)
                async searchEditors() {
                    // No-op: filtering is now computed local
                },

                selectEditor(editor) {
                    this.selectedEditor = editor;
                    // Dont clear search so list remains stable
                },

                resetEditorModal() {
                    this.selectedEditor = null;
                    this.editorSearch = '';
                    this.editorRoleFilter = '';
                },
                selectedReviewer: null,
                reviewerSearch: '',
                reviewerResults: [],
                reviewMethod: 'double_blind',
                responseDueDate: '',
                reviewDueDate: '',
                isSearching: false,

                // Discussion Wizard State
                discussionFiles: [],
                wizardStep: 1,
                tempUploadedFile: null,

                // CKEditor
                editorInstance: null,
                messageBody: '',

                // Editorial Decision Modals
                sendToReviewModalOpen: false,
                availableFiles: [],
                selectedFilesForPromotion: [],
                isLoadingFiles: false,

                // Copyediting Stage Modals
                draftFilesModalOpen: false,
                sendToProductionModalOpen: false,
                productionSendEmail: true,
                productionEmailBody: '',
                productionSelectedFiles: [],
                productionIsLoading: false,
                productionIsSubmitting: false,
                productionEditorInstance: null,

                // Accept Skip Review Modal
                skipReviewModalOpen: false,
                skipReviewNotes: '',

                // Decline Modal
                declineModalOpen: false,
                declineReason: '',
                showActivityLog: false,
                notifyAuthor: true,

                // Request Revisions Modal (OJS 3.3 Style)
                revisionModalOpen: false,
                revisionNewRound: false,
                revisionSendEmail: true,
                revisionEmailBody: '',
                revisionAttachments: [],
                revisionSelectedFiles: [],
                revisionIsLoadingFiles: false,
                revisionIsSubmitting: false,
                revisionEditorInstance: null,
                revisionUploadedFiles: [],
                
                // Galley Modal State
                galleyModalOpen: false,
                isSubmitting: false,
                editingGalley: null,
                galleyLabel: '',
                galleyLocale: 'en',
                galleyUrlPath: '',
                isRemote: false,
                remoteUrl: '',
                selectedFile: null,
                selectedFileName: '',
                errors: {},
                
                // Edit Review Assignment State
                editReviewModalOpen: false,
                editReviewAssignment: null,
                editReviewMethod: 'double_blind',
                editResponseDueDate: '',
                editReviewDueDate: '',
                editIsSubmitting: false,

                openEditReviewModal(assignment) {
                    this.editReviewAssignment = assignment;
                    this.editReviewMethod = assignment.review_method;
                    this.editResponseDueDate = assignment.response_due_date ? new Date(assignment.response_due_date).toISOString().split('T')[0] : '';
                    this.editReviewDueDate = assignment.due_date ? new Date(assignment.due_date).toISOString().split('T')[0] : '';
                    this.editReviewModalOpen = true;
                },

                async submitEditReview() {
                    this.editIsSubmitting = true;
                    try {
                        const response = await fetch(`{{ route('journal.workflow.review-assignment.update', ['journal' => $journal->slug, 'reviewAssignment' => '__ID__']) }}`.replace('__ID__', this.editReviewAssignment.id), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': config.csrfToken
                            },
                            body: JSON.stringify({
                                due_date: this.editReviewDueDate,
                                response_due_date: this.editResponseDueDate,
                                review_method: this.editReviewMethod
                            })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            alert(data.message || 'Failed to update review assignment.');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('An error occurred while updating the review assignment.');
                    }
                    this.editIsSubmitting = false;
                },

                init() {
                    this.$watch('discussionModalOpen', value => {
                        if (value) setTimeout(() => this.initEditor(), 100);
                    });
                },

                toggleAcceptFile(fileId) {
                    const idx = this.acceptSelectedFiles.indexOf(fileId);
                    if (idx > -1) {
                        this.acceptSelectedFiles.splice(idx, 1);
                    } else {
                        this.acceptSelectedFiles.push(fileId);
                    }
                },

                openAcceptModal() {
                    this.acceptModalOpen = true;
                    this.acceptSendEmail = true;
                    this.acceptEmailBody = this.getAcceptEmailTemplate();
                    this.loadAcceptFiles();
                    this.$nextTick(() => this.initAcceptEditor());
                },

                getAcceptEmailTemplate() {
                    return `<p>Dear ${config.authorName},</p>
                            <p>We are pleased to inform you that your submission <strong>"${config.submissionTitle}"</strong> has been accepted for publication in <strong>${config.journalName}</strong>.</p>
                            <p>We will now proceed to the Copyediting/Production stage.</p>
                            <p>Thank you for submitting your work to us.</p>
                            <p>Best regards,<br>The Editorial Team</p>`;
                },

                async loadAcceptFiles() {
                    this.acceptIsLoading = true;
                    try {
                        const res = await fetch(config.availableFilesUrl);
                        const data = await res.json();
                        this.acceptFiles = data.files || [];
                        this.acceptSelectedFiles = this.acceptFiles.map(f => f.id);
                    } catch (e) {
                        console.error(e);
                    }
                    this.acceptIsLoading = false;
                },

                initAcceptEditor() {
                    if (this.acceptEditorInstance) {
                        this.acceptEditorInstance.setData(this.acceptEmailBody);
                        return;
                    }
                    const editorEl = document.querySelector('#accept-email-editor');
                    if (!editorEl) return;

                    ClassicEditor
                        .create(editorEl, {
                            simpleUpload: {
                                uploadUrl: config.uploadImageUrl,
                                headers: {
                                    'X-CSRF-TOKEN': config.csrfToken
                                }
                            }
                        })
                        .then(editor => {
                            this.acceptEditorInstance = editor;
                            editor.setData(this.acceptEmailBody);
                            editor.model.document.on('change:data', () => {
                                this.acceptEmailBody = editor.getData();
                            });
                        })
                        .catch(err => console.error(err));
                },

                async loadRevisionFilesForNewRound() {
                    this.newRoundIsLoading = true;
                    try {
                        const res = await fetch(config.revisionFilesUrl);
                        this.newRoundFiles = await res.json();
                        this.newRoundSelectedFiles = this.newRoundFiles.map(f => f.id);
                    } catch (e) {
                        console.error('Failed to load revision files:', e);
                    }
                    this.newRoundIsLoading = false;
                },

                toggleNewRoundFile(fileId) {
                    const idx = this.newRoundSelectedFiles.indexOf(fileId);
                    if (idx > -1) {
                        this.newRoundSelectedFiles.splice(idx, 1);
                    } else {
                        this.newRoundSelectedFiles.push(fileId);
                    }
                },

                isNewRoundFileSelected(fileId) {
                    return this.newRoundSelectedFiles.includes(fileId);
                },

                openNewRoundModal() {
                    this.newRoundModalOpen = true;
                    this.loadRevisionFilesForNewRound();
                },

                resetNewRoundModal() {
                    this.newRoundModalOpen = false;
                    this.newRoundFiles = [];
                    this.newRoundSelectedFiles = [];
                    this.newRoundIsSubmitting = false;
                },

                // ==================== SEND TO PRODUCTION MODAL ====================
                openSendToProductionModal() {
                    this.sendToProductionModalOpen = true;
                    this.productionSendEmail = true;
                    this.productionEmailBody = this.getProductionEmailTemplate();
                    this.$nextTick(() => this.initProductionEditor());
                },

                getProductionEmailTemplate() {
                    const submissionUrl = window.location.href;
                    return `<p>Dear ${config.authorName},</p>
                            <p>The editing of your submission, <strong>"${config.submissionTitle},"</strong> is complete. We are now sending it to production.</p>
                            <p><strong>Submission URL:</strong> <a href="${submissionUrl}">${submissionUrl}</a></p>
                            <p>If you have any questions, please contact us.</p>
                            <p>Best regards,<br>The ${config.journalName} Editorial Team</p>`;
                },

                initProductionEditor() {
                    if (this.productionEditorInstance) {
                        this.productionEditorInstance.setData(this.productionEmailBody);
                        return;
                    }
                    const editorEl = document.querySelector('#production-email-editor');
                    if (!editorEl) return;

                    ClassicEditor
                        .create(editorEl, {
                            simpleUpload: {
                                uploadUrl: config.uploadImageUrl,
                                headers: {
                                    'X-CSRF-TOKEN': config.csrfToken
                                }
                            }
                        })
                        .then(editor => {
                            this.productionEditorInstance = editor;
                            editor.setData(this.productionEmailBody);
                            editor.model.document.on('change:data', () => {
                                this.productionEmailBody = editor.getData();
                            });
                        })
                        .catch(err => console.error(err));
                },

                toggleProductionFile(fileId) {
                    const idx = this.productionSelectedFiles.indexOf(fileId);
                    if (idx > -1) {
                        this.productionSelectedFiles.splice(idx, 1);
                    } else {
                        this.productionSelectedFiles.push(fileId);
                    }
                },

                isProductionFileSelected(fileId) {
                    return this.productionSelectedFiles.includes(fileId);
                },

                resetProductionModal() {
                    this.sendToProductionModalOpen = false;
                    this.productionSendEmail = true;
                    this.productionEmailBody = '';
                    this.productionSelectedFiles = [];
                    this.productionIsSubmitting = false;
                    if (this.productionEditorInstance) {
                        this.productionEditorInstance.destroy();
                        this.productionEditorInstance = null;
                    }
                },

                // Review Details Modal State
                reviewDetailsModalOpen: false,
                selectedReview: null,

                openReviewDetailsModal(review) {
                    this.selectedReview = review;
                    this.reviewDetailsModalOpen = true;
                },

                closeReviewDetailsModal() {
                    this.reviewDetailsModalOpen = false;
                    this.selectedReview = null;
                },


                async searchEditors() {
                    if (this.editorSearch.length < 2) {
                        this.editorResults = [];
                        return;
                    }
                    this.isSearchingEditors = true;
                    try {
                        const url = new URL(config.searchReviewersUrl);
                        url.searchParams.append('q', this.editorSearch);
                        url.searchParams.append('role', 'editor');
                        const res = await fetch(url.toString());
                        this.editorResults = await res.json();
                    } catch (e) {
                        console.error(e);
                    }
                    this.isSearchingEditors = false;
                },

                selectEditor(editor) {
                    this.selectedEditor = editor;
                    this.editorSearch = editor.name;
                    this.editorResults = [];
                },

                resetEditorModal() {
                    this.selectedEditor = null;
                    this.editorSearch = '';
                    this.editorResults = [];
                    this.editorRole = 'editor';
                },

                async searchReviewers() {
                    if (this.reviewerSearch.length < 2) {
                        this.reviewerResults = [];
                        return;
                    }
                    this.isSearching = true;
                    try {
                        const url = new URL(config.searchReviewersUrl);
                        url.searchParams.append('q', this.reviewerSearch);
                        const res = await fetch(url.toString());
                        this.reviewerResults = await res.json();
                    } catch (e) {
                        console.error(e);
                    }
                    this.isSearching = false;
                },

                selectReviewer(reviewer) {
                    this.selectedReviewer = reviewer;
                    this.reviewerSearch = reviewer.name;
                    this.reviewerResults = [];
                },

                resetReviewerModal() {
                    this.selectedReviewer = null;
                    this.reviewerSearch = '';
                    this.reviewerResults = [];
                    this.reviewMethod = 'double_blind';
                    this.responseDueDate = '';
                    this.reviewDueDate = '';
                },

                initEditor() {
                    if (this.editorInstance) return;
                    ClassicEditor.create(document.querySelector('#discussion-editor'), {
                            simpleUpload: {
                                uploadUrl: config.uploadImageUrl,
                                headers: {
                                    'X-CSRF-TOKEN': config.csrfToken
                                }
                            }
                        }).then(editor => {
                            this.editorInstance = editor;
                            editor.model.document.on('change:data', () => {
                                this.messageBody = editor.getData();
                            });
                        })
                        .catch(error => {
                            console.error(error);
                        });
                },

                resetDiscussionForm() {
                    this.discussionFiles = [];
                    this.messageBody = '';
                    if (this.editorInstance) {
                        this.editorInstance.setData('');
                    }
                },

                submitDiscussion() {
                    if (!this.messageBody || this.messageBody.trim() === '') {
                        alert('Message is required');
                        return;
                    }
                    document.querySelector('#discussion-form').submit();
                },

                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    let formData = new FormData();
                    formData.append('file', file);

                    fetch(config.uploadFileUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': config.csrfToken
                            },
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            this.tempUploadedFile = data;
                            this.wizardStep = 2;
                        })
                        .catch(err => alert('Upload failed'));
                },

                completeWizard() {
                    if (this.tempUploadedFile) {
                        this.discussionFiles.push(this.tempUploadedFile);
                    }
                    this.wizardStep = 1;
                    this.tempUploadedFile = null;
                    this.fileWizardOpen = false;
                },

                addAnotherFile() {
                    if (this.tempUploadedFile) {
                        this.discussionFiles.push(this.tempUploadedFile);
                    }
                    this.wizardStep = 1;
                    this.tempUploadedFile = null;
                },

                async loadAvailableFiles() {
                    this.isLoadingFiles = true;
                    try {
                        const res = await fetch(config.availableFilesUrl);
                        const data = await res.json();
                        this.availableFiles = data.files;
                        // Pre-select all files by default
                        this.selectedFilesForPromotion = data.files.map(f => ({
                            id: f.id,
                            type: f.type
                        }));
                    } catch (e) {
                        console.error('Failed to load files:', e);
                    }
                    this.isLoadingFiles = false;
                },

                toggleFileSelection(file) {
                    const index = this.selectedFilesForPromotion.findIndex(f => f.id === file.id);
                    if (index > -1) {
                        this.selectedFilesForPromotion.splice(index, 1);
                    } else {
                        this.selectedFilesForPromotion.push({
                            id: file.id,
                            type: file.type
                        });
                    }
                },

                isFileSelected(file) {
                    return this.selectedFilesForPromotion.some(f => f.id === file.id);
                },

                openSendToReviewModal() {
                    this.sendToReviewModalOpen = true;
                    this.loadAvailableFiles();
                },

                openSkipReviewModal() {
                    this.skipReviewModalOpen = true;
                    this.loadAvailableFiles();
                },

                resetDeclineModal() {
                    this.declineReason = '';
                    this.notifyAuthor = true;
                },

                openRevisionModal() {
                    this.revisionModalOpen = true;
                    this.revisionNewRound = false;
                    this.revisionSendEmail = true;
                    this.revisionSelectedFiles = [];
                    this.revisionUploadedFiles = [];
                    this.loadRevisionAttachments();
                    this.$nextTick(() => this.initRevisionEditor());
                },

                async loadRevisionAttachments() {
                    this.revisionIsLoadingFiles = true;
                    try {
                        const res = await fetch(config.reviewerAttachmentsUrl);
                        const data = await res.json();
                        this.revisionAttachments = data.files || [];
                    } catch (e) {
                        console.error('Failed to load reviewer attachments:', e);
                    }
                    this.revisionIsLoadingFiles = false;
                },

                initRevisionEditor() {
                    if (this.revisionEditorInstance) return;
                    const editorEl = document.querySelector('#revision-email-editor');
                    if (!editorEl) return;

                    ClassicEditor
                        .create(editorEl, {
                            simpleUpload: {
                                uploadUrl: config.uploadImageUrl,
                                headers: {
                                    'X-CSRF-TOKEN': config.csrfToken
                                }
                            }
                        })
                        .then(editor => {
                            this.revisionEditorInstance = editor;
                            // Pre-fill with default template
                            const defaultBody = this.getDefaultRevisionEmailTemplate();
                            editor.setData(defaultBody);
                            this.revisionEmailBody = defaultBody;
                            editor.model.document.on('change:data', () => {
                                this.revisionEmailBody = editor.getData();
                            });
                        })
                        .catch(err => console.error(err));
                },

                getDefaultRevisionEmailTemplate() {
                    return `<p>Dear ${config.firstAuthorName},</p>
                    <p>We have reached a decision regarding your submission to <strong>${config.journalName}</strong>:</p>
                    <p><strong>Submission:</strong> ${config.submissionTitle}</p>
                    <p><strong>Manuscript ID:</strong> ${config.submissionCode}</p>
                    <hr>
                    <p><strong>Our Decision: Revisions Required</strong></p>
                    <p>Based on the reviewers' feedback, we request that you revise your manuscript. Please address each
                        reviewer comment carefully and submit your revised manuscript through the journal portal.</p>
                    <p>The reviewer comments are attached or included below for your reference.</p>
                    <hr>
                    <p>If you have any questions, please do not hesitate to contact us.</p>
                    <p>Best regards,<br>The Editorial Team</p>`;
                },

                toggleRevisionFile(fileId) {
                    const index = this.revisionSelectedFiles.indexOf(fileId);
                    if (index > -1) {
                        this.revisionSelectedFiles.splice(index, 1);
                    } else {
                        this.revisionSelectedFiles.push(fileId);
                    }
                },

                isRevisionFileSelected(fileId) {
                    return this.revisionSelectedFiles.includes(fileId);
                },

                async uploadRevisionFile(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    let formData = new FormData();
                    formData.append('file', file);

                    try {
                        const res = await fetch(config.uploadDecisionFileUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': config.csrfToken
                            },
                            body: formData
                        });
                        const data = await res.json();
                        this.revisionUploadedFiles.push(data);
                        this.revisionSelectedFiles.push(data.id);
                    } catch (err) {
                        alert('Upload failed');
                    }
                    event.target.value = '';
                },

                resetRevisionModal() {
                    this.revisionModalOpen = false;
                    this.revisionNewRound = false;
                    this.revisionSendEmail = true;
                    this.revisionEmailBody = '';
                    this.revisionSelectedFiles = [];
                    this.revisionUploadedFiles = [];
                    if (this.revisionEditorInstance) {
                        this.revisionEditorInstance.destroy();
                        this.revisionEditorInstance = null;
                    }
                },

                // Galley Methods
                openAddGalley() {
                    this.resetGalleyForm();
                    this.galleyModalOpen = true;
                },

                openEditGalley(galley) {
                    this.resetGalleyForm();
                    this.editingGalley = galley;
                    this.galleyLabel = galley.label;
                    this.galleyLocale = galley.locale || 'en';
                    this.galleyUrlPath = galley.url_path || '';
                    this.isRemote = galley.is_remote || false;
                    this.remoteUrl = galley.url_remote || '';
                    this.galleyModalOpen = true;
                },

                resetGalleyForm() {
                    this.editingGalley = null;
                    this.galleyLabel = '';
                    this.galleyLocale = 'en';
                    this.galleyUrlPath = '';
                    this.isRemote = false;
                    this.remoteUrl = '';
                    this.selectedFile = null;
                    this.selectedFileName = '';
                    this.errors = {};
                    this.isSubmitting = false;
                },

                handleGalleyFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.selectedFile = file;
                        this.selectedFileName = file.name;
                    }
                },

                async submitGalley() {
                    if (this.isSubmitting) return;
                    this.isSubmitting = true;
                    this.errors = {};

                    const formData = new FormData();
                    formData.append('label', this.galleyLabel);
                    formData.append('locale', this.galleyLocale);
                    formData.append('url_path', this.galleyUrlPath);
                    formData.append('is_remote', this.isRemote ? '1' : '0');

                    if (this.isRemote) {
                        formData.append('url_remote', this.remoteUrl);
                    } else if (this.selectedFile) {
                        formData.append('file', this.selectedFile);
                    }

                    try {
                        const url = this.editingGalley ?
                            '{{ route('journal.workflow.galley.update', ['journal' => $journal->slug, 'submission' => $submission->slug, 'galley' => '__GALLEY_ID__']) }}'.replace('__GALLEY_ID__', this.editingGalley.id) :
                            '{{ route('journal.workflow.galley.store', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}';

                        if (this.editingGalley) {
                            formData.append('_method', 'PUT');
                        }

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        if (response.ok) {
                            window.location.href = window.location.pathname + '?tab=publication&subtab=galleys';
                        } else {
                            const data = await response.json();
                            if (data.errors) {
                                this.errors = data.errors;
                            } else {
                                alert(data.message || 'An error occurred');
                            }
                        }
                    } catch (e) {
                        console.error(e);
                        alert('An error occurred while saving');
                    }

                    this.isSubmitting = false;
                }
            }));
        });
    </script>

    {{-- Reviewer Selector Script --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reviewerSelector', (journalId, submissionId, assignUrl) => ({
                showModal: false,
                view: 'list', // list, config
                search: '',
                reviewers: [],
                isLoading: false,
                // Config
                selectedReviewer: null,
                reviewMethod: 'double_blind',
                responseDueDate: '',
                reviewDueDate: '',

                openModal() {
                    this.showModal = true;
                    this.view = 'list';
                    this.fetchReviewers();
                },

                fetchReviewers() {
                    this.isLoading = true;
                    fetch(`/api/journal/${journalId}/reviewers?q=${this.search}`)
                        .then(res => res.json())
                        .then(data => {
                            this.reviewers = data;
                            this.isLoading = false;
                        });
                },

                selectReviewer(reviewer) {
                    this.selectedReviewer = reviewer;
                    // Default Dates
                    const today = new Date();
                    const response = new Date(today);
                    response.setDate(today.getDate() + 7);
                    const review = new Date(today);
                    review.setDate(today.getDate() + 28);

                    this.responseDueDate = response.toISOString().split('T')[0];
                    this.reviewDueDate = review.toISOString().split('T')[0];
                    this.reviewMethod = 'double_blind';

                    this.view = 'config';
                },

                confirmAssignment() {
                    if (!this.selectedReviewer) return;

                    fetch(assignUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify({
                                reviewer_id: this.selectedReviewer.id,
                                review_method: this.reviewMethod,
                                response_due_date: this.responseDueDate,
                                review_due_date: this.reviewDueDate
                            })
                        })
                        .then(res => {
                            if (res.ok) {
                                window.location.reload();
                            } else {
                                res.json().then(data => {
                                    alert(data.message || 'Failed to assign reviewer.');
                                }).catch(() => {
                                    alert('Failed to assign reviewer.');
                                });
                            }
                        });
                }
            }));
        });
    </script>

    {{-- Keyword Input (Tagify) for Publication Metadata --}}
    <script>
        function keywordInputShow(initialKeywords = []) {
            return {
                tagify: null,

                init() {
                    // Load Tagify CSS and JS
                    if (!document.querySelector('link[href*="tagify"]')) {
                        const link = document.createElement('link');
                        link.rel = 'stylesheet';
                        link.href = 'https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css';
                        document.head.appendChild(link);
                    }

                    if (!window.Tagify) {
                        const script = document.createElement('script');
                        script.src = 'https://cdn.jsdelivr.net/npm/@yaireo/tagify';
                        script.onload = () => this.initializeTagify(initialKeywords);
                        document.head.appendChild(script);
                    } else {
                        this.initializeTagify(initialKeywords);
                    }
                },

                initializeTagify(initialKeywords) {
                    const input = this.$refs.keywordInput;

                    this.tagify = new Tagify(input, {
                        delimiters: ",|Enter",
                        maxTags: 20,
                        dropdown: {
                            enabled: 1,
                            maxItems: 10,
                            classname: "tagify__dropdown",
                            closeOnSelect: true
                        },
                        whitelist: [],
                        enforceWhitelist: false,
                        editTags: {
                            clicks: 1,
                            keepInvalid: false
                        }
                    });

                    // Add initial keywords
                    if (initialKeywords && initialKeywords.length > 0) {
                        this.tagify.addTags(initialKeywords);
                    }

                    // Fetch autocomplete suggestions
                    let controller;
                    this.tagify.on('input', (e) => {
                        const value = e.detail.value;
                        this.tagify.settings.whitelist.length = 0;

                        if (value.length < 2) return;

                        // Cancel previous request
                        controller && controller.abort();
                        controller = new AbortController();

                        fetch(`/api/keywords?query=${encodeURIComponent(value)}`, {
                                signal: controller.signal
                            })
                            .then(response => response.json())
                            .then(data => {
                                this.tagify.settings.whitelist = data.map(k => k.content);
                                this.tagify.dropdown.show(value);
                            })
                            .catch(err => {
                                if (err.name !== 'AbortError') {
                                    console.error('Keyword fetch error:', err);
                                }
                            });
                    });

                    // Create hidden inputs for form submission
                    this.tagify.on('add remove', () => {
                        this.updateHiddenInputs();
                    });

                    // Initial hidden inputs
                    this.updateHiddenInputs();
                },

                updateHiddenInputs() {
                    // Remove existing hidden keyword inputs
                    const existingInputs = document.querySelectorAll('input[name^="keywords["]');
                    existingInputs.forEach(input => input.remove());

                    // Add new hidden inputs for each tag
                    const tags = this.tagify.value;
                    const form = this.$refs.keywordInput.closest('form');

                    tags.forEach((tag, index) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `keywords[${index}]`;
                        input.value = tag.value;
                        form.appendChild(input);
                    });
                }
            }
        }
    </script>


</x-app-layout>
