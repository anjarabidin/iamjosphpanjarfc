@php
    $journal = current_journal();
@endphp

<x-app-layout>
    <x-slot name="title">{{ $submission->title }} - Editorial</x-slot>

    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('journal.editorial.queue', ['journal' => $journal->slug]) }}"
                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900 line-clamp-1">{{ $submission->title }}</h1>
                <p class="mt-1 text-sm text-gray-500">Editorial Workflow</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        activeTab: 'details',
        showDecisionModal: false,
        showAssignModal: false,
        decision: '',
        comments: ''
    }">
        <!-- Status Bar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <!-- Status Badge -->
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium {{ $submission->status_color === 'yellow' ? 'bg-yellow-100 text-yellow-800' : ($submission->status_color === 'green' ? 'bg-green-100 text-green-800' : ($submission->status_color === 'red' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ $submission->status_label }}
                    </span>

                    <!-- Stage -->
                    <span class="text-sm text-gray-500">
                        Stage: <span
                            class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $submission->stage)) }}</span>
                    </span>
                </div>

                <!-- Quick Actions -->
                <div class="flex items-center space-x-2">
                    @if (in_array($submission->status, ['submitted', 'under_review', 'revision_required']))
                        <button @click="showDecisionModal = true"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Record Decision
                        </button>
                    @endif

                    <button @click="showAssignModal = true"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Assign Reviewer
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button @click="activeTab = 'details'"
                        :class="activeTab === 'details' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Submission Details
                    </button>
                    <button @click="activeTab = 'reviews'"
                        :class="activeTab === 'reviews' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center">
                        Review Results
                        @if ($reviewSummary['completed'] > 0)
                            <span
                                class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-green-100 text-green-800 rounded-full">
                                {{ $reviewSummary['completed'] }}
                            </span>
                        @endif
                    </button>
                    <button @click="activeTab = 'files'"
                        :class="activeTab === 'files' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Files
                    </button>
                    <button @click="activeTab = 'history'"
                        :class="activeTab === 'history' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Decision History
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Tab: Submission Details -->
                <div x-show="activeTab === 'details'" x-cloak>
                    <div class="grid lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Title & Abstract -->
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $submission->title }}</h2>

                                <div class="mb-4">
                                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                        Abstract</h3>
                                    <div class="prose prose-sm max-w-none text-gray-600">
                                        {!! nl2br(e($submission->abstract)) !!}
                                    </div>
                                </div>

                                @if ($submission->keywords)
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                            Keywords</h3>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($submission->keywords_array as $keyword)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700">
                                                    {{ $keyword }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Authors -->
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Authors
                                </h3>
                                <div class="space-y-3">
                                    @foreach ($submission->authors as $author)
                                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                            <div
                                                class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 font-bold mr-3">
                                                {{ strtoupper(substr($author->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">
                                                    {{ $author->name }}
                                                    @if ($author->is_corresponding)
                                                        <span
                                                            class="ml-1 text-xs text-primary-600">(Corresponding)</span>
                                                    @endif
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $author->affiliation ?? 'No affiliation' }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Info -->
                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-3">Submission Info</h4>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Submitted</dt>
                                        <dd class="text-gray-900">
                                            {{ $submission->submitted_at?->format('M j, Y') ?? 'Draft' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Section</dt>
                                        <dd class="text-gray-900">{{ $submission->section->name ?? 'None' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Corresponding</dt>
                                        <dd class="text-gray-900">{{ $submission->author->email ?? 'N/A' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Review Summary -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-3">Review Summary</h4>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Total Assigned</dt>
                                        <dd class="font-medium text-gray-900">{{ $reviewSummary['total'] }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Completed</dt>
                                        <dd class="font-medium text-green-600">{{ $reviewSummary['completed'] }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Pending</dt>
                                        <dd class="font-medium text-yellow-600">
                                            {{ $reviewSummary['pending'] + $reviewSummary['accepted'] }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Review Results -->
                <div x-show="activeTab === 'reviews'" x-cloak>
                    @if ($submission->reviewAssignments->isEmpty())
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No reviewers assigned</h3>
                            <p class="text-gray-500 mb-4">Assign reviewers to begin the peer review process.</p>
                            <button @click="showAssignModal = true"
                                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                                Assign Reviewer
                            </button>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach ($submission->reviewAssignments->groupBy('round') as $round => $assignments)
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Round
                                        {{ $round }}</h3>

                                    <div class="space-y-4">
                                        @foreach ($assignments as $assignment)
                                            <div class="border border-gray-200 rounded-xl overflow-hidden">
                                                <!-- Reviewer Header -->
                                                <div class="bg-gray-50 px-6 py-4 flex items-center justify-between">
                                                    <div class="flex items-center space-x-3">
                                                        <div
                                                            class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 font-bold">
                                                            {{ strtoupper(substr($assignment->reviewer->name ?? 'R', 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <p class="font-medium text-gray-900">
                                                                {{ $assignment->reviewer->name ?? 'Unknown' }}</p>
                                                            <p class="text-sm text-gray-500">
                                                                {{ $assignment->reviewer->affiliation ?? 'No affiliation' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-3">
                                                        <!-- Status Badge -->
                                                        @switch($assignment->status)
                                                            @case('pending')
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                                            @break

                                                            @case('accepted')
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">In
                                                                    Progress</span>
                                                            @break

                                                            @case('completed')
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                                            @break

                                                            @case('declined')
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Declined</span>
                                                            @break
                                                        @endswitch

                                                        @if ($assignment->status !== 'completed')
                                                            <form
                                                                action="{{ route('editor.cancel-reviewer', $assignment) }}"
                                                                method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="text-red-600 hover:text-red-800 text-sm"
                                                                    onclick="return confirm('Cancel this reviewer assignment?')">
                                                                    Cancel
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Review Content (for completed reviews) -->
                                                @if ($assignment->status === 'completed')
                                                    <div class="p-6 space-y-4">
                                                        <!-- Recommendation -->
                                                        <div>
                                                            <span
                                                                class="text-sm font-medium text-gray-500">Recommendation:</span>
                                                            <span
                                                                class="ml-2 inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-{{ $assignment->recommendation_color }}-100 text-{{ $assignment->recommendation_color }}-800">
                                                                {{ $assignment->recommendation_label }}
                                                            </span>
                                                        </div>

                                                        <!-- Comments for Author -->
                                                        <div>
                                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Comments
                                                                for Author</h4>
                                                            <div
                                                                class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600 prose prose-sm max-w-none">
                                                                {!! $assignment->comments_for_author !!}
                                                            </div>
                                                        </div>

                                                        <!-- Confidential Comments (Editor Only) -->
                                                        @if ($assignment->comments_for_editor)
                                                            <div>
                                                                <h4
                                                                    class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                                                    <svg class="w-4 h-4 mr-1 text-yellow-500"
                                                                        fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                    Confidential Comments for Editor
                                                                </h4>
                                                                <div
                                                                    class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-gray-700">
                                                                    {!! $assignment->comments_for_editor !!}
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="text-xs text-gray-500">
                                                            Completed on
                                                            {{ $assignment->completed_at?->format('M j, Y \a\t g:ia') }}
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="p-6 text-sm text-gray-500">
                                                        <p>Due:
                                                            {{ $assignment->due_date?->format('M j, Y') ?? 'No deadline' }}
                                                        </p>
                                                        @if ($assignment->isOverdue())
                                                            <p class="text-red-600 mt-1">⚠ This review is overdue</p>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Tab: Files -->
                <div x-show="activeTab === 'files'" x-cloak>
                    @if ($submission->files->isEmpty())
                        <p class="text-gray-500 text-center py-8">No files uploaded.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($submission->files->groupBy('file_type') as $type => $files)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ ucfirst($type) }}</h4>
                                    @foreach ($files as $file)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-2">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-red-600" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M14,2H6A2,2,0,0,0,4,4V20a2,2,0,0,0,2,2H18a2,2,0,0,0,2-2V8Z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $file->file_name }}</p>
                                                    <p class="text-sm text-gray-500">Version {{ $file->version }} •
                                                        {{ $file->file_size_formatted }}</p>
                                                </div>
                                            </div>
                                            <a href="{{ route('files.download', $file) }}"
                                                class="text-primary-600 hover:text-primary-700 text-sm font-medium">Download</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Tab: Decision History -->
                <div x-show="activeTab === 'history'" x-cloak>
                    @php
                        $decisions = $submission->metadata['decisions'] ?? [];
                    @endphp

                    @if (empty($decisions))
                        <p class="text-gray-500 text-center py-8">No decisions recorded yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach (array_reverse($decisions) as $decision)
                                <div
                                    class="border-l-4 {{ $decision['decision'] === 'accept' ? 'border-green-500' : ($decision['decision'] === 'reject' ? 'border-red-500' : 'border-yellow-500') }} pl-4 py-2">
                                    <p class="font-medium text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $decision['decision'])) }}
                                    </p>
                                    @if ($decision['comments'] ?? null)
                                        <p class="text-sm text-gray-600 mt-1">{{ $decision['comments'] }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($decision['made_at'])->format('M j, Y g:ia') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Decision Modal -->
        <div x-show="showDecisionModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDecisionModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="showDecisionModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showDecisionModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <form action="{{ route('editor.decision', $submission) }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 py-5">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Record Editorial Decision</h3>

                            <!-- Decision Options -->
                            <div class="space-y-3 mb-4">
                                <label
                                    class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-colors"
                                    :class="decision === 'accept' ? 'border-green-500 bg-green-50' :
                                        'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" name="decision" value="accept" x-model="decision"
                                        class="sr-only">
                                    <div
                                        class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Accept Submission</p>
                                        <p class="text-sm text-gray-500">Ready for publication</p>
                                    </div>
                                </label>

                                <label
                                    class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-colors"
                                    :class="decision === 'revision' ? 'border-yellow-500 bg-yellow-50' :
                                        'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" name="decision" value="revision" x-model="decision"
                                        class="sr-only">
                                    <div
                                        class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Request Revisions</p>
                                        <p class="text-sm text-gray-500">Author needs to make changes</p>
                                    </div>
                                </label>

                                <label
                                    class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-colors"
                                    :class="decision === 'reject' ? 'border-red-500 bg-red-50' :
                                        'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" name="decision" value="reject" x-model="decision"
                                        class="sr-only">
                                    <div
                                        class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Decline Submission</p>
                                        <p class="text-sm text-gray-500">Not suitable for publication</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Comments -->
                            <div class="mb-4">
                                <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">
                                    Comments for Author
                                </label>
                                <textarea name="comments" id="comments" rows="4" x-model="comments"
                                    placeholder="Provide feedback for the author..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                            </div>

                            <!-- Notify Author -->
                            <label class="flex items-center">
                                <input type="checkbox" name="notify_author" value="1" checked
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm text-gray-600">Send email notification to author</span>
                            </label>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                            <button type="button" @click="showDecisionModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                                Cancel
                            </button>
                            <button type="submit" :disabled="!decision"
                                :class="decision ? 'bg-primary-600 hover:bg-primary-700' : 'bg-gray-300 cursor-not-allowed'"
                                class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors">
                                Confirm Decision
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assign Reviewer Modal -->
        <div x-show="showAssignModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showAssignModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="showAssignModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showAssignModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-4"
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <form action="{{ route('editor.assign-reviewer', $submission) }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 py-5">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assign Reviewer</h3>

                            @if ($reviewers->isEmpty())
                                <p class="text-gray-500">No available reviewers. All reviewers have been assigned.</p>
                            @else
                                <div class="mb-4">
                                    <label for="reviewer_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Select Reviewer
                                    </label>
                                    <select name="reviewer_id" id="reviewer_id" required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                        <option value="">Choose a reviewer...</option>
                                        @foreach ($reviewers as $reviewer)
                                            <option value="{{ $reviewer->id }}">
                                                {{ $reviewer->name }}
                                                ({{ $reviewer->affiliation ?? 'No affiliation' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Due Date
                                    </label>
                                    <input type="date" name="due_date" id="due_date"
                                        value="{{ now()->addDays(14)->format('Y-m-d') }}"
                                        min="{{ now()->addDay()->format('Y-m-d') }}"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                </div>

                                <div>
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                        Personal Message (Optional)
                                    </label>
                                    <textarea name="message" id="message" rows="3" placeholder="Add a personal message to the invitation..."
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                                </div>
                            @endif
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                            <button type="button" @click="showAssignModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                                Cancel
                            </button>
                            @if ($reviewers->isNotEmpty())
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                                    Send Invitation
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
