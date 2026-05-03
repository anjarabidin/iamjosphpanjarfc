@extends('layouts.app')

@section('title', 'Workflow Settings - ' . ($journal->abbreviation ?? 'IAMJOS'))

@section('content')
    <div x-data="{
        activeTab: '{{ request('tab', 'submissions') }}',
        showChecklistModal: false,
        showReviewFormModal: false,
        newChecklist: { content: '', is_required: true },
        newReviewForm: { title: '', description: '' },
        isEditMode: false,
        editingItem: null
    }">

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-3"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <i class="fa-solid fa-check-circle text-emerald-600"></i>
                <span class="text-sm text-emerald-800">{{ session('success') }}</span>
                <button @click="show = false" class="ml-auto text-emerald-600 hover:text-emerald-800">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3" x-data="{ show: true }"
                x-show="show">
                <i class="fa-solid fa-exclamation-circle text-red-600"></i>
                <span class="text-sm text-red-800">{{ session('error') }}</span>
                <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        @endif

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Workflow Settings</h1>
                <p class="mt-1 text-sm text-gray-500">Configure submission, review, and publishing workflows.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="/{{ $journal->slug }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    View Journal Site
                </a>
            </div>
        </div>

        <!-- Main Settings Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="flex overflow-x-auto" aria-label="Tabs">
                    <button @click="activeTab = 'submissions'"
                        :class="activeTab === 'submissions' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-file-arrow-up mr-2"></i>
                        Submissions
                    </button>
                    <button @click="activeTab = 'review'"
                        :class="activeTab === 'review' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-clipboard-check mr-2"></i>
                        Review
                    </button>
                    <button @click="activeTab = 'library'"
                        :class="activeTab === 'library' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-folder-open mr-2"></i>
                        Publisher Library
                    </button>
                    <button @click="activeTab = 'emails'"
                        :class="activeTab === 'emails' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-envelope mr-2"></i>
                        Emails
                    </button>
                    <button @click="activeTab = 'notifications'"
                        :class="activeTab === 'notifications' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-brands fa-whatsapp mr-2"></i>
                        WhatsApp
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6 lg:p-8">

                <!-- ============================================ -->
                <!-- TAB 1: SUBMISSIONS -->
                <!-- ============================================ -->
                <div x-show="activeTab === 'submissions'" x-cloak>
                    <form action="{{ route('journal.settings.workflow.update', ['journal' => $journal->slug]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tab" value="submissions">

                        <div class="space-y-10">

                            <!-- Section: Author Guidelines -->
                            <div>
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-book-open text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900">Author Guidelines</h3>
                                        <p class="text-sm text-gray-500">Provide instructions for authors submitting
                                            manuscripts.</p>
                                    </div>
                                </div>
                                <!-- Tiptap Editor Container -->
                                <!-- Quill Editor Container -->
                                <div x-data="quillEditor({{ json_encode(old('author_guidelines', $journal->author_guidelines ?? '')) }})" class="mt-1">
                                    <!-- Hidden Input for Form Submission -->
                                    <input type="hidden" name="author_guidelines" :value="content">

                                    <!-- Editor Div -->
                                    <div x-ref="quillElement" class="bg-white rounded-md shadow-sm" style="height: 300px;">
                                    </div>
                                </div>

                                @push('styles')
                                    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                                    <style>
                                        .ql-toolbar.ql-snow {
                                            border-top-left-radius: 0.375rem;
                                            border-top-right-radius: 0.375rem;
                                            border-color: #d1d5db;
                                            background-color: #f9fafb;
                                        }

                                        .ql-container.ql-snow {
                                            border-bottom-left-radius: 0.375rem;
                                            border-bottom-right-radius: 0.375rem;
                                            border-color: #d1d5db;
                                            font-family: 'Inter', sans-serif;
                                            font-size: 0.875rem;
                                        }

                                        .ql-editor {
                                            min-height: 100%;
                                        }
                                    </style>
                                @endpush

                                @push('scripts')
                                    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
                                    <script>
                                        document.addEventListener('alpine:init', () => {
                                            Alpine.data('quillEditor', (initialContent) => ({
                                                editor: null,
                                                content: initialContent || '',
                                                init() {
                                                    this.$nextTick(() => {
                                                        this.editor = new Quill(this.$refs.quillElement, {
                                                            theme: 'snow',
                                                            modules: {
                                                                toolbar: [
                                                                    [{
                                                                        'font': []
                                                                    }, {
                                                                        'size': []
                                                                    }],
                                                                    ['bold', 'italic', 'underline', 'strike'],
                                                                    [{
                                                                        'color': []
                                                                    }, {
                                                                        'background': []
                                                                    }],
                                                                    [{
                                                                        'script': 'super'
                                                                    }, {
                                                                        'script': 'sub'
                                                                    }],
                                                                    [{
                                                                        'header': '1'
                                                                    }, {
                                                                        'header': '2'
                                                                    }, 'blockquote', 'code-block'],
                                                                    [{
                                                                        'list': 'ordered'
                                                                    }, {
                                                                        'list': 'bullet'
                                                                    }, {
                                                                        'indent': '-1'
                                                                    }, {
                                                                        'indent': '+1'
                                                                    }],
                                                                    ['direction', {
                                                                        'align': []
                                                                    }],
                                                                    ['link', 'image', 'video', 'clean']
                                                                ]
                                                            },
                                                            placeholder: 'Enter detailed guidelines for authors...'
                                                        });

                                                        // Set initial content securely
                                                        if (this.content) {
                                                            this.editor.clipboard.dangerouslyPasteHTML(this.content);
                                                        }

                                                        // Sync on change
                                                        this.editor.on('text-change', () => {
                                                            this.content = this.editor.root.innerHTML;
                                                        });
                                                    });
                                                }
                                            }));
                                        });
                                    </script>
                                @endpush
                            </div>

                            <hr class="border-gray-200">

                            <!-- Section: Submission Checklist -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-list-check text-amber-600"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-semibold text-gray-900">Submission Checklist</h3>
                                            <p class="text-sm text-gray-500">Authors must confirm each item before
                                                submitting.</p>
                                        </div>
                                    </div>
                                    <button type="button"
                                        @click="showChecklistModal = true; isEditMode = false; newChecklist = { content: '', is_required: true }"
                                        class="inline-flex items-center px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <i class="fa-solid fa-plus mr-2"></i>
                                        Add Item
                                    </button>
                                </div>

                                <div class="space-y-2">
                                    @forelse($checklists as $checklist)
                                        <div
                                            class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg group hover:bg-gray-100 transition-colors">
                                            <div
                                                class="flex-shrink-0 w-6 h-6 bg-white border-2 border-gray-300 rounded flex items-center justify-center mt-0.5">
                                                <i class="fa-solid fa-check text-xs text-primary-600"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-700">{{ $checklist->content }}</p>
                                            </div>
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                @if ($checklist->is_required)
                                                    <span
                                                        class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-medium rounded">Required</span>
                                                @else
                                                    <span
                                                        class="px-2 py-0.5 bg-gray-200 text-gray-600 text-xs font-medium rounded">Optional</span>
                                                @endif

                                                <!-- Helper JS used for Delete -->
                                                <button type="button"
                                                    onclick="submitForm('{{ route('journal.settings.workflow.checklists.destroy', ['journal' => $journal->slug, 'checklist' => $checklist->id]) }}', 'DELETE', 'Delete this checklist item?')"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded opacity-0 group-hover:opacity-100 transition-all">
                                                    <i class="fa-solid fa-trash text-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div
                                            class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                                            <i class="fa-solid fa-list-check text-3xl text-gray-300 mb-3"></i>
                                            <p class="text-sm text-gray-500">No checklist items defined.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <hr class="border-gray-200">

                            <!-- Section: Metadata -->
                            <div>
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-tags text-emerald-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900">Submission Metadata</h3>
                                        <p class="text-sm text-gray-500">Select which metadata fields are enabled for
                                            submissions.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @php
                                        $metaSettings = $journal->submission_metadata_settings ?? [];
                                        $metaFields = [
                                            'keywords' => 'Keywords',
                                            'references' => 'References',
                                            'languages' => 'Languages',
                                            'rights' => 'Rights',
                                            'coverage' => 'Coverage',
                                            'disciplines' => 'Disciplines',
                                        ];
                                    @endphp

                                    @foreach ($metaFields as $key => $label)
                                        <label
                                            class="flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg hover:border-primary-200 cursor-pointer transition-colors">
                                            <input type="hidden" name="metadata_{{ $key }}" value="0">
                                            <input type="checkbox" name="metadata_{{ $key }}" value="1"
                                                {{ $metaSettings[$key] ?? false ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                        </div>

                        <!-- Save Link -->
                        <div class="mt-10 pt-6 border-t border-gray-200 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                <i class="fa-solid fa-check mr-2"></i>
                                Save Submission Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ============================================ -->
                <!-- TAB 2: REVIEW -->
                <!-- ============================================ -->
                <div x-show="activeTab === 'review'" x-cloak>
                    <form action="{{ route('journal.settings.workflow.update', ['journal' => $journal->slug]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tab" value="review">

                        <div class="space-y-10">

                            <!-- Section: Review Mode -->
                            <div>
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-glasses text-purple-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900">Review Policy</h3>
                                        <p class="text-sm text-gray-500">Configure how the peer review process works.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                    <label
                                        class="relative flex flex-col p-4 bg-white border rounded-xl cursor-pointer hover:border-primary-500 transition-colors {{ $journal->review_mode === 'double_blind' ? 'border-primary-500 ring-1 ring-primary-500 bg-primary-50' : 'border-gray-200' }}">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-900">Double Blind</span>
                                            <input type="radio" name="review_mode" value="double_blind"
                                                class="text-primary-600 focus:ring-primary-500"
                                                {{ $journal->review_mode === 'double_blind' ? 'checked' : '' }}>
                                        </div>
                                        <p class="text-xs text-gray-500">Neither author nor reviewer knows each other's
                                            identity.</p>
                                    </label>

                                    <label
                                        class="relative flex flex-col p-4 bg-white border rounded-xl cursor-pointer hover:border-primary-500 transition-colors {{ $journal->review_mode === 'blind' ? 'border-primary-500 ring-1 ring-primary-500 bg-primary-50' : 'border-gray-200' }}">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-900">Blind</span>
                                            <input type="radio" name="review_mode" value="blind"
                                                class="text-primary-600 focus:ring-primary-500"
                                                {{ $journal->review_mode === 'blind' ? 'checked' : '' }}>
                                        </div>
                                        <p class="text-xs text-gray-500">Reviewer knows author, but author doesn't know
                                            reviewer.</p>
                                    </label>

                                    <label
                                        class="relative flex flex-col p-4 bg-white border rounded-xl cursor-pointer hover:border-primary-500 transition-colors {{ $journal->review_mode === 'open' ? 'border-primary-500 ring-1 ring-primary-500 bg-primary-50' : 'border-gray-200' }}">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-900">Open</span>
                                            <input type="radio" name="review_mode" value="open"
                                                class="text-primary-600 focus:ring-primary-500"
                                                {{ $journal->review_mode === 'open' ? 'checked' : '' }}>
                                        </div>
                                        <p class="text-xs text-gray-500">Identities are known to both parties.</p>
                                    </label>
                                </div>
                            </div>

                            <!-- Section: Timelines -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-4">Review Deadlines</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Response Time
                                            (Weeks)</label>
                                        <input type="number" name="review_response_weeks"
                                            value="{{ old('review_response_weeks', $journal->review_response_weeks) }}"
                                            min="1" max="12"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                        <p class="mt-1 text-xs text-gray-500">Time allowed for reviewer to accept/decline
                                            request.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Completion Time
                                            (Weeks)</label>
                                        <input type="number" name="review_completion_weeks"
                                            value="{{ old('review_completion_weeks', $journal->review_completion_weeks) }}"
                                            min="1" max="24"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                        <p class="mt-1 text-xs text-gray-500">Time allowed for reviewer to complete the
                                            review.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Review Forms -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-medium text-gray-900">Review Forms</h4>
                                    <button type="button"
                                        @click="showReviewFormModal = true; newReviewForm = { title: '', description: '' }"
                                        class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fa-solid fa-plus mr-2"></i>
                                        Create Form
                                    </button>
                                </div>

                                <div class="overflow-hidden rounded-xl border border-gray-200">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                                    Form Title</th>
                                                <th
                                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                                    Status</th>
                                                <th
                                                    class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">
                                                    Responses</th>
                                                <th
                                                    class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                                    Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($reviewForms as $form)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-4">
                                                        <p class="text-sm font-medium text-gray-900">{{ $form->title }}
                                                        </p>
                                                        @if ($form->description)
                                                            <p class="text-xs text-gray-500 mt-0.5">
                                                                {{ Str::limit($form->description, 50) }}
                                                            </p>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-4 text-center">
                                                        @if ($form->is_active)
                                                            <span
                                                                class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-full">Active</span>
                                                        @else
                                                            <span
                                                                class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-4 text-center hidden sm:table-cell">
                                                        <span
                                                            class="text-sm text-gray-500">{{ $form->response_count }}</span>
                                                    </td>
                                                    <td class="px-4 py-4 text-right">
                                                        <div class="flex items-center justify-end gap-2">
                                                            @if ($form->response_count == 0)
                                                                <button type="button"
                                                                    onclick="submitForm('{{ route('journal.settings.workflow.review-forms.destroy', ['journal' => $journal->slug, 'reviewForm' => $form->id]) }}', 'DELETE', 'Delete this review form?')"
                                                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded"
                                                                    title="Delete">
                                                                    <i class="fa-solid fa-trash text-sm"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 pt-6 border-t border-gray-200 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                <i class="fa-solid fa-check mr-2"></i>
                                Save Review Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ============================================ -->
                <!-- TAB 3: LIBRARY -->
                <!-- ============================================ -->
                <div x-show="activeTab === 'library'" x-cloak>
                    <!-- This tab has its own add/upload form, handled via Modal usually, or standalone -->
                    <!-- For simplicity, assuming "Add File" opens a modal. -->

                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Publisher Library</h3>
                            <p class="text-sm text-gray-500">Repository for documents, forms, and policies shared with
                                editors.</p>
                        </div>
                        <!-- Upload Form/Button could go here. For now, assuming modal trigger -->
                        <!-- NOTE: Implementation below is read-only list for now, as user didn't specify upload UI in Prompt,
                                                                                                                     But based on previous file, there was likely an upload modal.
                                                                                                                     I will keep the generic structure.
                                                                                                                -->
                    </div>

                    @if ($libraryFiles->count() > 0)
                        <div class="overflow-hidden rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">File
                                            Name</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">
                                            Type</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">
                                            Size</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($libraryFiles as $file)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-4">
                                                <div class="flex items-center gap-3">
                                                    <i class="fa-solid fa-file text-gray-400"></i>
                                                    <span
                                                        class="text-sm font-medium text-gray-900">{{ $file->original_name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 hidden sm:table-cell">
                                                <span
                                                    class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $file->file_type }}</span>
                                            </td>
                                            <td class="px-4 py-4 hidden md:table-cell">
                                                <span class="text-sm text-gray-500">{{ $file->formatted_size }}</span>
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('journal.settings.workflow.library.download', ['journal' => $journal->slug, 'libraryFile' => $file->id]) }}"
                                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded"
                                                        title="Download">
                                                        <i class="fa-solid fa-download text-sm"></i>
                                                    </a>
                                                    <button type="button"
                                                        onclick="submitForm('{{ route('journal.settings.workflow.library.destroy', ['journal' => $journal->slug, 'libraryFile' => $file->id]) }}', 'DELETE', 'Delete this file?')"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded"
                                                        title="Delete">
                                                        <i class="fa-solid fa-trash text-sm"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                            <i class="fa-solid fa-folder-open text-4xl text-gray-300 mb-4"></i>
                            <p class="text-base font-medium text-gray-700 mb-1">No files in library</p>
                            <p class="text-sm text-gray-500">Upload documents to share with the editorial team.</p>
                        </div>
                    @endif
                </div>

                <!-- ============================================ -->
                <!-- TAB 4: EMAILS -->
                <!-- ============================================ -->
                <!-- ============================================ -->
                <!-- TAB 4: EMAILS -->
                <!-- ============================================ -->
                <div x-show="activeTab === 'emails'" x-cloak>
                    @include('journal.settings.workflow.partials._emails', [
                        'emailTemplates' => $emailTemplates,
                    ])
                </div>

                <!-- ============================================ -->
                <!-- TAB 5: NOTIFICATIONS (WHATSAPP) -->
                <!-- ============================================ -->
                <div x-show="activeTab === 'notifications'" x-cloak>
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fa-brands fa-whatsapp text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">WhatsApp Notification Templates</h3>
                                <p class="text-sm text-gray-500">Manage automated WhatsApp messages sent by the system.</p>
                            </div>
                        </div>

                        <!-- WhatsApp Toggle Feature -->
                        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-6 shadow-sm flex items-center justify-between"
                            x-data="{
                                waNotificationsEnabled: {{ $journal->wa_notifications_enabled ? 'true' : 'false' }},
                                isSaving: false,
                                toggleWaNotifications() {
                                    if(this.isSaving) return;
                                    this.isSaving = true;
                                    
                                    fetch('{{ route('journal.settings.workflow.whatsapp.toggle', ['journal' => $journal->slug]) }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ enabled: this.waNotificationsEnabled ? 0 : 1 })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        this.isSaving = false;
                                        if (data.success) {
                                            this.waNotificationsEnabled = data.enabled;
                                        } else {
                                            alert(data.error || 'Failed to update settings');
                                            this.waNotificationsEnabled = !this.waNotificationsEnabled; // revert
                                        }
                                    })
                                    .catch(error => {
                                        this.isSaving = false;
                                        alert('An error occurred. Please try again.');
                                        this.waNotificationsEnabled = !this.waNotificationsEnabled; // revert
                                    });
                                }
                            }">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900">Enable WhatsApp Notifications</h4>
                                <p class="text-sm text-gray-500 mt-1">When turned off, the system will not send any automated WhatsApp messages even if templates are active.</p>
                            </div>
                            <div class="flex items-center">
                                <button type="button" 
                                    @click="toggleWaNotifications()"
                                    :class="waNotificationsEnabled ? 'bg-emerald-500' : 'bg-gray-200'" 
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" 
                                    role="switch" 
                                    :aria-checked="waNotificationsEnabled.toString()">
                                    <span class="sr-only">Use setting</span>
                                    <span 
                                        :class="waNotificationsEnabled ? 'translate-x-5' : 'translate-x-0'" 
                                        class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                                        <span :class="waNotificationsEnabled ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in'" class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity" aria-hidden="true">
                                            <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 12 12">
                                                <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <span :class="waNotificationsEnabled ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out'" class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity" aria-hidden="true">
                                            <svg class="h-3 w-3 text-emerald-600" fill="currentColor" viewBox="0 0 12 12">
                                                <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />
                                            </svg>
                                        </span>
                                    </span>
                                </button>
                                
                                <span x-show="isSaving" class="ml-3 text-xs text-gray-400 flex items-center gap-1" style="display: none;">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i> Saving...
                                </span>
                            </div>
                        </div>



                        @foreach ($notificationTemplates as $template)
                            <div class="bg-gray-50 rounded-xl border border-gray-200 p-4" x-data="{ expanded: false }">
                                <div class="flex items-center justify-between cursor-pointer"
                                    @click="expanded = !expanded">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-white rounded-lg border border-gray-200 text-gray-400">
                                            <i class="fa-regular fa-message"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-sm font-semibold text-gray-900">
                                                    {{ ucwords(str_replace('_', ' ', $template->event_key)) }}</h4>
                                                @if (isset($template->source))
                                                    @if ($template->source === 'journal')
                                                        <span
                                                            class="px-1.5 py-0.5 text-[10px] font-medium bg-blue-100 text-blue-700 rounded border border-blue-200">Custom</span>
                                                    @elseif($template->source === 'global')
                                                        <span
                                                            class="px-1.5 py-0.5 text-[10px] font-medium bg-purple-100 text-purple-700 rounded border border-purple-200">Global</span>
                                                    @else
                                                        <span
                                                            class="px-1.5 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-600 rounded border border-gray-200">Default</span>
                                                    @endif
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 mt-0.5">Event Key: <code
                                                    class="bg-gray-200 px-1 py-0.5 rounded">{{ $template->event_key }}</code>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if ($template->is_active)
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-full border border-emerald-200">Active</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-gray-200 text-gray-600 rounded-full border border-gray-300">Inactive</span>
                                        @endif
                                        <div
                                            class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-200 transition-colors">
                                            <i class="fa-solid fa-chevron-down text-gray-400 transition-transform duration-200"
                                                :class="{ 'rotate-180': expanded }"></i>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="expanded" x-collapse class="mt-4 pt-4 border-t border-gray-200">
                                    <form
                                        action="{{ route('journal.settings.workflow.notification-templates.update', ['journal' => $journal->slug, 'eventKey' => $template->event_key]) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                            <div class="lg:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Message
                                                    Template</label>
                                                <div class="relative">
                                                    <textarea name="body" rows="4"
                                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 font-mono text-sm leading-relaxed"
                                                        placeholder="Enter message here...">{{ $template->body }}</textarea>
                                                    <div
                                                        class="absolute bottom-2 right-2 text-xs text-gray-400 pointer-events-none">
                                                        WhatsApp Format
                                                    </div>
                                                </div>
                                                <div class="flex items-center mt-4 mb-2">
                                                    <input id="is_active_{{ $template->id ?? 'new' }}" type="checkbox"
                                                        name="is_active" value="1"
                                                        class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                        {{ isset($template) && $template->is_active ? 'checked' : '' }}>

                                                    <label for="is_active_{{ $template->id ?? 'new' }}"
                                                        class="ms-2 text-sm font-medium text-black">
                                                        Enable this notification
                                                    </label>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                                                    <h5
                                                        class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                                                        <i class="fa-solid fa-code"></i> Available Variables
                                                    </h5>
                                                    <div class="flex flex-wrap gap-2">
                                                        @if ($template->variables)
                                                            @foreach ($template->variables as $var)
                                                                <code
                                                                    class="px-2 py-1 bg-white border border-blue-200 rounded text-xs text-blue-700 font-mono shadow-sm select-all cursor-pointer hover:bg-blue-50"
                                                                    title="Click to copy">{<span>{{ $var }}</span>}</code>
                                                            @endforeach
                                                        @else
                                                            <span class="text-xs text-gray-400 italic">No variables
                                                                available</span>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs text-blue-600 mt-3 leading-relaxed">
                                                        Copy variables exactly as shown to insert dynamic data into your
                                                        message. Variables are case-sensitive.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex justify-end mt-4 pt-4 border-t border-gray-200/50">
                                            <button type="submit"
                                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                                <i class="fa-solid fa-save mr-2"></i>
                                                Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <!-- ============================================ -->
        <!-- MODALS -->
        <!-- ============================================ -->

        <!-- Checklist Modal -->
        <div x-show="showChecklistModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50" @click="showChecklistModal = false"></div>
                <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
                    <form
                        action="{{ route('journal.settings.workflow.checklists.store', ['journal' => $journal->slug]) }}"
                        method="POST">
                        @csrf
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Add Checklist Item</h3>
                            <button type="button" @click="showChecklistModal = false"
                                class="text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Content *</label>
                                <textarea name="content" x-model="newChecklist.content" required rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                            </div>
                            <label class="flex items-center gap-3">
                                <input type="checkbox" name="is_required" value="1"
                                    x-model="newChecklist.is_required"
                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-gray-700">Required item</span>
                            </label>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showChecklistModal = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 text-white bg-primary-600 rounded-lg hover:bg-primary-700">Add
                                Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Review Form Modal -->
        <div x-show="showReviewFormModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/50" @click="showReviewFormModal = false"></div>
                <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
                    <form
                        action="{{ route('journal.settings.workflow.review-forms.store', ['journal' => $journal->slug]) }}"
                        method="POST">
                        @csrf
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Create Review Form</h3>
                            <button type="button" @click="showReviewFormModal = false"
                                class="text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                <input type="text" name="title" x-model="newReviewForm.title" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" x-model="newReviewForm.description" rows="2"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showReviewFormModal = false"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 text-white bg-primary-600 rounded-lg hover:bg-primary-700">Create
                                Form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function submitForm(url, method = 'POST', message = null) {
            if (message && !confirm(message)) return;

            const form = document.createElement('form');
            form.action = url;
            form.method = 'POST';
            form.style.display = 'none';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                console.error('CSRF token not found');
                return;
            }

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            if (method !== 'POST') {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = method;
                form.appendChild(methodInput);
            }

            document.body.appendChild(form);
            form.submit();
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush
