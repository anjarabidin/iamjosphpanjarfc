@php
    $journal = current_journal();
    $hasEditor = $submission->hasEditor();
    $currentStage = $submission->stage_id ?? 1;

    $stages = [
        1 => ['name' => 'Submission', 'icon' => 'fa-paper-plane'],
        2 => ['name' => 'Review', 'icon' => 'fa-eye'],
        3 => ['name' => 'Copyediting', 'icon' => 'fa-pen-to-square'],
        4 => ['name' => 'Production', 'icon' => 'fa-book'],
    ];
@endphp

<x-app-layout>
    <x-slot name="title">{{ $submission->title }} - Workflow</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
            <div class="flex-1 min-w-0">
                <nav class="text-sm text-gray-500 mb-2">
                    <a href="{{ route('journal.editorial.queue', ['journal' => $journal->slug]) }}"
                        class="hover:text-indigo-600">Queue</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700">Workflow</span>
                </nav>
                <h1 class="text-xl font-bold text-gray-900 break-words">{{ $submission->title }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $submission->author?->name ?? 'Unknown Author' }}
                    @if ($submission->section)
                        • {{ $submission->section->title }}
                    @endif
                </p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-4 flex-shrink-0 flex items-center gap-2">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @switch($submission->status)
                        @case('submitted') bg-blue-100 text-blue-800 @break
                        @case('in_review') bg-yellow-100 text-yellow-800 @break
                        @case('revision_required') bg-orange-100 text-orange-800 @break
                        @case('accepted') bg-green-100 text-green-800 @break
                        @case('rejected') bg-red-100 text-red-800 @break
                        @case('published') bg-emerald-100 text-emerald-800 @break
                        @default bg-gray-100 text-gray-800
                    @endswitch
                ">
                    {{ $submission->status_label }}
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fa-solid fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- UNASSIGNED ALERT -->
    @unless ($hasEditor)
        <div class="mb-6 bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-amber-400 text-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-bold text-amber-800">No Editor Assigned</h3>
                    <p class="mt-1 text-sm text-amber-700">
                        This submission is unassigned. You must assign an editor before proceeding with the workflow.
                    </p>
                    <div class="mt-3">
                        <button type="button" onclick="document.getElementById('assignEditorModal').showModal()"
                            class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                            <i class="fa-solid fa-user-plus mr-2"></i>
                            Assign Editor
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endunless

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content (3 cols) -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Stage Tabs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ activeTab: {{ $currentStage }} }">
                <div class="border-b border-gray-200">
                    <nav class="flex" aria-label="Tabs">
                        @foreach ($stages as $stageId => $stage)
                            <button type="button" @click="activeTab = {{ $stageId }}"
                                :class="activeTab === {{ $stageId }} ? 'border-indigo-500 text-indigo-600 bg-indigo-50' :
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="flex-1 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors relative {{ $stageId <= $currentStage ? '' : 'opacity-50' }}">
                                <i class="fa-solid {{ $stage['icon'] }} mr-2"></i>
                                {{ $stage['name'] }}
                                @if ($stageId === $currentStage)
                                    <span class="absolute top-1 right-1 w-2 h-2 bg-green-500 rounded-full"></span>
                                @endif
                            </button>
                        @endforeach
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Stage 1: Submission -->
                    <div x-show="activeTab === 1" x-transition>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Submission Details</h3>

                        <!-- Abstract -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Abstract</h4>
                            <div class="prose prose-sm max-w-none text-gray-600 bg-gray-50 p-4 rounded-lg">
                                {!! clean($submission->abstract) !!}
                            </div>
                        </div>

                        <!-- Keywords -->
                        @if ($submission->keywords)
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Keywords</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($submission->keywords_array as $keyword)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                            {{ $keyword }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Contributors -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Contributors</h4>
                            <div class="bg-gray-50 rounded-lg divide-y divide-gray-200">
                                @forelse($submission->authors as $author)
                                    <div class="p-3 flex items-center justify-between">
                                        <div>
                                            <span class="font-medium text-gray-900">
                                                {{ $author->first_name ?? '' }}
                                                {{ $author->last_name ?? $author->name }}
                                            </span>
                                            <span class="text-sm text-gray-500 ml-2">{{ $author->email }}</span>
                                            @if ($author->is_primary_contact)
                                                <span
                                                    class="ml-2 text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">Primary</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="p-3 text-gray-500 text-sm">No contributors.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Submission Files -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Submission Files</h4>
                            <div class="bg-gray-50 rounded-lg divide-y divide-gray-200">
                                @forelse($submission->files as $file)
                                    <div class="p-3 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <i class="fa-regular fa-file text-gray-400"></i>
                                            <div>
                                                <span class="font-medium text-gray-900">{{ $file->file_name }}</span>
                                                <span
                                                    class="text-sm text-gray-500 ml-2">{{ number_format($file->file_size / 1024, 2) }}
                                                    KB</span>
                                            </div>
                                        </div>
                                        <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                            <i class="fa-solid fa-download mr-1"></i> Download
                                        </a>
                                    </div>
                                @empty
                                    <p class="p-3 text-gray-500 text-sm">No files uploaded.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Pre-Review Discussions -->
                        @include('submissions.partials.discussions', [
                            'stageId' => 1,
                            'stageDiscussions' => $discussions->get(1, collect()),
                        ])
                    </div>

                    <!-- Stage 2: Review -->
                    <div x-show="activeTab === 2" x-transition>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Review</h3>

                        <!-- Review Rounds -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-medium text-gray-700">Reviewers</h4>
                                @if ($hasEditor)
                                    <button type="button" class="text-sm text-indigo-600 hover:text-indigo-800">
                                        <i class="fa-solid fa-plus mr-1"></i> Add Reviewer
                                    </button>
                                @endif
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                @forelse($submission->reviewAssignments as $review)
                                    <div class="flex items-center justify-between py-2">
                                        <div>
                                            <span
                                                class="font-medium text-gray-900">{{ $review->reviewer?->name ?? 'Unknown' }}</span>
                                            <span
                                                class="text-sm text-gray-500 ml-2">{{ ucfirst($review->status ?? 'pending') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm text-center py-4">No reviewers assigned yet.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Review Discussions -->
                        @include('submissions.partials.discussions', [
                            'stageId' => 2,
                            'stageDiscussions' => $discussions->get(2, collect()),
                        ])
                    </div>

                    <!-- Stage 3: Copyediting -->
                    <div x-show="activeTab === 3" x-transition>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Copyediting</h3>

                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <i class="fa-solid fa-pen-to-square text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Copyediting files and tasks will appear here.</p>
                        </div>

                        <!-- Copyediting Discussions -->
                        @include('submissions.partials.discussions', [
                            'stageId' => 3,
                            'stageDiscussions' => $discussions->get(3, collect()),
                        ])
                    </div>

                    <!-- Stage 4: Production -->
                    <div x-show="activeTab === 4" x-transition>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Production</h3>

                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <i class="fa-solid fa-book text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Production files (Galleys/PDFs) will appear here.</p>
                        </div>

                        <!-- Production Discussions -->
                        @include('submissions.partials.discussions', [
                            'stageId' => 4,
                            'stageDiscussions' => $discussions->get(4, collect()),
                        ])
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar (1 col) -->
        <div class="space-y-6">
            <!-- Assigned Editors -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Assigned Editors</h3>
                    <button type="button" onclick="document.getElementById('assignEditorModal').showModal()"
                        class="text-indigo-600 hover:text-indigo-800">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($submission->editorialAssignments->where('is_active', true) as $assignment)
                        <div class="p-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span
                                        class="text-indigo-600 font-medium text-sm">{{ substr($assignment->user->name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $assignment->user->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ ucfirst(str_replace('_', ' ', $assignment->role)) }}</p>
                                </div>
                            </div>
                            <form
                                action="{{ route('journal.workflow.remove-editor', ['journal' => $journal->slug, 'submission' => $submission->id, 'assignment' => $assignment->id]) }}"
                                method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500"
                                    onclick="return confirm('Remove this editor?')">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500 text-sm">
                            No editors assigned
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Workflow Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Workflow Actions</h3>
                </div>
                <div class="p-4 space-y-3">
                    @if (!$hasEditor)
                        <p class="text-sm text-gray-500 text-center">Assign an editor to enable workflow actions.</p>
                    @else
                        @switch($currentStage)
                            @case(1)
                                <!-- Stage 1: Send to Review -->
                                <form
                                    action="{{ route('journal.workflow.change-stage', ['journal' => $journal->slug, 'submission' => $submission->id]) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="stage_id" value="2">
                                    <input type="hidden" name="action" value="send_to_review">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                        <i class="fa-solid fa-arrow-right mr-2"></i> Send to Review
                                    </button>
                                </form>

                                <form
                                    action="{{ route('journal.workflow.change-stage', ['journal' => $journal->slug, 'submission' => $submission->id]) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="stage_id" value="1">
                                    <input type="hidden" name="action" value="decline">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 text-red-700 hover:bg-red-50 text-sm font-medium rounded-lg transition-colors"
                                        onclick="return confirm('Decline this submission?')">
                                        <i class="fa-solid fa-times mr-2"></i> Decline Submission
                                    </button>
                                </form>
                            @break

                            @case(2)
                                <!-- Stage 2: Review Actions -->
                                <form
                                    action="{{ route('journal.workflow.change-stage', ['journal' => $journal->slug, 'submission' => $submission->id]) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="stage_id" value="3">
                                    <input type="hidden" name="action" value="accept">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                        <i class="fa-solid fa-check mr-2"></i> Accept Submission
                                    </button>
                                </form>

                                <form
                                    action="{{ route('journal.workflow.change-stage', ['journal' => $journal->slug, 'submission' => $submission->id]) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="stage_id" value="2">
                                    <input type="hidden" name="action" value="request_revisions">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                        <i class="fa-solid fa-rotate-left mr-2"></i> Request Revisions
                                    </button>
                                </form>

                                <form
                                    action="{{ route('journal.workflow.change-stage', ['journal' => $journal->slug, 'submission' => $submission->id]) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="stage_id" value="2">
                                    <input type="hidden" name="action" value="decline">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 text-red-700 hover:bg-red-50 text-sm font-medium rounded-lg transition-colors"
                                        onclick="return confirm('Decline this submission?')">
                                        <i class="fa-solid fa-times mr-2"></i> Decline Submission
                                    </button>
                                </form>
                            @break

                            @case(3)
                                <!-- Stage 3: Send to Production -->
                                <form
                                    action="{{ route('journal.workflow.change-stage', ['journal' => $journal->slug, 'submission' => $submission->id]) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="stage_id" value="4">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                        <i class="fa-solid fa-arrow-right mr-2"></i> Send to Production
                                    </button>
                                </form>
                            @break

                            @case(4)
                                <!-- Stage 4: Schedule Publication -->
                                <button type="button" onclick="document.getElementById('scheduleModal').showModal()"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                    <i class="fa-solid fa-calendar mr-2"></i> Schedule for Publication
                                </button>
                            @break
                        @endswitch
                    @endif
                </div>
            </div>

            <!-- Submission Metadata -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Metadata</h3>
                </div>
                <div class="p-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Section</span>
                        <span class="text-gray-900 font-medium">{{ $submission->section?->title ?? 'None' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Stage</span>
                        <span
                            class="text-gray-900 font-medium">{{ $stages[$currentStage]['name'] ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Submitted</span>
                        <span
                            class="text-gray-900 font-medium">{{ $submission->submitted_at?->format('M d, Y') ?? 'N/A' }}</span>
                    </div>
                    @if ($submission->issue)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Issue</span>
                            <span class="text-gray-900 font-medium">Vol. {{ $submission->issue->volume }} No.
                                {{ $submission->issue->number }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Editor Modal -->
    <dialog id="assignEditorModal" class="rounded-xl shadow-xl p-0 max-w-md w-full backdrop:bg-black/50">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Assign Editor</h3>
                <button type="button" onclick="document.getElementById('assignEditorModal').close()"
                    class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form
                action="{{ route('journal.workflow.assign-editor', ['journal' => $journal->slug, 'submission' => $submission->id]) }}"
                method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                        <select name="user_id" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Choose an editor...</option>
                            @foreach ($availableEditors as $editor)
                                <option value="{{ $editor->id }}">{{ $editor->name }} ({{ $editor->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="editor">Editor</option>
                            <option value="section_editor">Section Editor</option>
                            <option value="manager">Journal Manager</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('assignEditorModal').close()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        Assign
                    </button>
                </div>
            </form>
        </div>
    </dialog>
</x-app-layout>
