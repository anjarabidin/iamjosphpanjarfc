@extends('layouts.app')

@php
    $journal = current_journal();
    $mastheadSettings = $journal->settings['masthead'] ?? [];
    $contactSettings = $journal->settings['contact'] ?? [];
@endphp

@section('title', 'Journal Settings - ' . ($journal->abbreviation ?? 'IAMJOS'))

@section('content')
    <div x-data="{ activeTab: 'masthead' }">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Journal Settings</h1>
                <p class="mt-1 text-sm text-gray-500">Configure your journal's identity, contacts, and structure.</p>
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
                    <button @click="activeTab = 'masthead'"
                        :class="activeTab === 'masthead' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-building-columns mr-2"></i>
                        Masthead
                    </button>
                    <button @click="activeTab = 'contact'"
                        :class="activeTab === 'contact' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-address-book mr-2"></i>
                        Contact
                    </button>
                    <button @click="activeTab = 'sections'"
                        :class="activeTab === 'sections' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-layer-group mr-2"></i>
                        Sections
                    </button>
                    <button @click="activeTab = 'categories'"
                        :class="activeTab === 'categories' ? 'border-primary-500 text-primary-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="flex-shrink-0 px-6 py-4 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-tags mr-2"></i>
                        Categories
                    </button>

                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6 lg:p-8">

                <!-- ============================================ -->
                <!-- TAB 1: MASTHEAD (General Info) -->
                <!-- ============================================ -->
                <div x-show="activeTab === 'masthead'" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <form action="{{ route('journal.settings.update', ['journal' => $journal->slug]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tab" value="masthead">

                        <div class="space-y-8">
                            <!-- Section: Basic Identity -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Journal Identity</h3>
                                <p class="text-sm text-gray-500 mb-6">Basic information about your journal.</p>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Journal Name -->
                                    <div class="lg:col-span-2">
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Journal
                                            Name *</label>
                                        <input type="text" name="name" id="name"
                                            value="{{ old('name', $journal->name) }}"
                                            placeholder="e.g., International Journal of Medicine" class="w-full" required>
                                    </div>

                                    <!-- Abbreviation -->
                                    <div>
                                        <label for="abbreviation"
                                            class="block text-sm font-medium text-gray-700 mb-1">Journal Initials /
                                            Abbreviation</label>
                                        <input type="text" name="abbreviation" id="abbreviation"
                                            value="{{ old('abbreviation', $journal->abbreviation) }}"
                                            placeholder="e.g., IJM" class="w-full">
                                        <p class="mt-1 text-xs text-gray-500">Short code for your journal (2-5 characters
                                            recommended)</p>
                                    </div>

                                    <!-- Publisher Name -->
                                    <div>
                                        <label for="publisher"
                                            class="block text-sm font-medium text-gray-700 mb-1">Publisher Name</label>
                                        <input type="text" name="publisher" id="publisher"
                                            value="{{ old('publisher', $journal->publisher ?? '') }}"
                                            placeholder="e.g., University Press" class="w-full">
                                    </div>

                                    <!-- ISSN Print -->
                                    <div>
                                        <label for="issn_print" class="block text-sm font-medium text-gray-700 mb-1">ISSN
                                            (Print)</label>
                                        <input type="text" name="issn_print" id="issn_print"
                                            value="{{ old('issn_print', $journal->issn_print ?? '') }}"
                                            placeholder="XXXX-XXXX" class="w-full font-mono">
                                    </div>

                                    <!-- URL ISSN Print -->
                                    <div>
                                        <label for="url_issn_print" class="block text-sm font-medium text-gray-700 mb-1">URL ISSN
                                            (Print)</label>
                                        <input type="url" name="url_issn_print" id="url_issn_print"
                                            value="{{ old('url_issn_print', $journal->url_issn_print ?? '') }}"
                                            placeholder="https://..." class="w-full">
                                    </div>

                                    <!-- ISSN Online -->
                                    <div>
                                        <label for="issn_online" class="block text-sm font-medium text-gray-700 mb-1">ISSN
                                            (Online)</label>
                                        <input type="text" name="issn_online" id="issn_online"
                                            value="{{ old('issn_online', $journal->issn_online ?? '') }}"
                                            placeholder="XXXX-XXXX" class="w-full font-mono">
                                    </div>

                                    <!-- URL ISSN Online -->
                                    <div>
                                        <label for="url_issn_online" class="block text-sm font-medium text-gray-700 mb-1">URL ISSN
                                            (Online)</label>
                                        <input type="url" name="url_issn_online" id="url_issn_online"
                                            value="{{ old('url_issn_online', $journal->url_issn_online ?? '') }}"
                                            placeholder="https://..." class="w-full">
                                    </div>
                                </div>
                            </div>

                            <hr class="border-gray-200">

                            <!-- Section: Descriptions -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Journal Descriptions</h3>
                                <p class="text-sm text-gray-500 mb-6">Describe your journal for readers and authors. Rich
                                    text formatting is supported.</p>

                                <div class="space-y-6">
                                    <!-- Journal Summary -->
                                    <div>
                                        <label for="summary" class="block text-sm font-medium text-gray-700 mb-1">Journal
                                            Summary</label>
                                        <textarea name="summary" id="summary" rows="4" class="tinymce-editor w-full">{{ old('summary', $journal->summary ?? '') }}</textarea>

                                        <div class="flex items-center gap-2 mt-3">
                                            <input type="hidden" name="show_summary" value="0">
                                            <input id="show_summary" name="show_summary" type="checkbox" value="1"
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                                {{ old('show_summary', $journal->show_summary) ? 'checked' : '' }}>
                                            <label for="show_summary" class="text-sm text-gray-700">
                                                Show the journal summary on the homepage
                                            </label>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">This appears on the journal homepage.</p>
                                    </div>

                                    <!-- About the Journal -->
                                    <div>
                                        <label for="about" class="block text-sm font-medium text-gray-700 mb-1">About
                                            the Journal</label>
                                        <textarea name="about" id="about" rows="8" class="tinymce-editor w-full">{{ old('about', $mastheadSettings['about'] ?? '') }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">Full description of the journal, its history,
                                            aims and scope, editorial policies.</p>
                                    </div>

                                    <!-- Editorial Team -->
                                    <div>
                                        <label for="editorial_team"
                                            class="block text-sm font-medium text-gray-700 mb-1">Editorial Team
                                            Description</label>
                                        <textarea name="editorial_team" id="editorial_team" rows="6" class="tinymce-editor w-full">{{ old('editorial_team', $mastheadSettings['editorial_team'] ?? '') }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">List your editorial board members and their
                                            roles.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                <i class="fa-solid fa-check mr-2"></i>
                                Save Masthead
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ============================================ -->
                <!-- TAB 2: CONTACT -->
                <!-- ============================================ -->
                <div x-show="activeTab === 'contact'" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <form action="{{ route('journal.settings.update', ['journal' => $journal->slug]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tab" value="contact">

                        <div class="space-y-8">
                            <!-- Section A: Mailing Address -->
                            <div class="bg-gray-50 rounded-xl p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-location-dot text-primary-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900">Mailing Address</h3>
                                        <p class="text-sm text-gray-500">Physical address for correspondence.</p>
                                    </div>
                                </div>

                                <div>
                                    <label for="mailing_address" class="block text-sm font-medium text-gray-700 mb-1">Full
                                        Address</label>
                                    <textarea name="mailing_address" id="mailing_address" rows="4"
                                        placeholder="Street Address&#10;City, State ZIP&#10;Country" class="w-full">{{ old('mailing_address', $contactSettings['mailing_address'] ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Section B: Principal Contact -->
                            <div class="bg-blue-50 rounded-xl p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-user-tie text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900">Principal Contact</h3>
                                        <p class="text-sm text-gray-500">Main editorial contact for author inquiries.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="contact_name"
                                            class="block text-sm font-medium text-gray-700 mb-1">Contact Name *</label>
                                        <input type="text" name="contact_name" id="contact_name"
                                            value="{{ old('contact_name', $contactSettings['principal']['name'] ?? '') }}"
                                            placeholder="Dr. John Smith" class="w-full" required>
                                    </div>
                                    <div>
                                        <label for="contact_email"
                                            class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                        <input type="email" name="contact_email" id="contact_email"
                                            value="{{ old('contact_email', $contactSettings['principal']['email'] ?? '') }}"
                                            placeholder="editor@journal.com" class="w-full" required>
                                    </div>
                                    <div>
                                        <label for="contact_phone"
                                            class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                        <input type="tel" name="contact_phone" id="contact_phone"
                                            value="{{ old('contact_phone', $contactSettings['principal']['phone'] ?? '') }}"
                                            placeholder="+62 xxx xxxx xxxx" class="w-full">
                                    </div>
                                    <div>
                                        <label for="contact_affiliation"
                                            class="block text-sm font-medium text-gray-700 mb-1">Affiliation</label>
                                        <input type="text" name="contact_affiliation" id="contact_affiliation"
                                            value="{{ old('contact_affiliation', $contactSettings['principal']['affiliation'] ?? '') }}"
                                            placeholder="University / Institution" class="w-full">
                                    </div>
                                </div>
                            </div>

                            <!-- Section C: Technical Support -->
                            <div class="bg-amber-50 rounded-xl p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-headset text-amber-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-900">Technical Support Contact</h3>
                                        <p class="text-sm text-gray-500">For technical issues and website support.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="support_name"
                                            class="block text-sm font-medium text-gray-700 mb-1">Support Name</label>
                                        <input type="text" name="support_name" id="support_name"
                                            value="{{ old('support_name', $contactSettings['support']['name'] ?? '') }}"
                                            placeholder="Tech Support Team" class="w-full">
                                    </div>
                                    <div>
                                        <label for="support_email"
                                            class="block text-sm font-medium text-gray-700 mb-1">Support Email</label>
                                        <input type="email" name="support_email" id="support_email"
                                            value="{{ old('support_email', $contactSettings['support']['email'] ?? '') }}"
                                            placeholder="support@journal.com" class="w-full">
                                    </div>
                                    <div>
                                        <label for="support_phone"
                                            class="block text-sm font-medium text-gray-700 mb-1">Support Phone</label>
                                        <input type="tel" name="support_phone" id="support_phone"
                                            value="{{ old('support_phone', $contactSettings['support']['phone'] ?? '') }}"
                                            placeholder="+62 xxx xxxx xxxx" class="w-full">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                <i class="fa-solid fa-check mr-2"></i>
                                Save Contacts
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ============================================ -->
                <!-- TAB 3: SECTIONS -->
                <!-- ============================================ -->
                <div x-show="activeTab === 'sections'" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-data="{
                        showSectionModal: false,
                        isEditMode: false,
                        editingSectionId: null,
                        sectionForm: {
                            name: '',
                            abbreviation: '',
                            policy: '',
                            meta_indexed: true,
                            meta_reviewed: true
                        },
                        deleteSectionId: null,
                        showDeleteConfirm: false,
                        openCreateModal() {
                            this.isEditMode = false;
                            this.editingSectionId = null;
                            this.sectionForm = { name: '', abbreviation: '', policy: '', meta_indexed: true, meta_reviewed: true };
                            this.showSectionModal = true;
                        },
                        openEditModal(section) {
                            this.isEditMode = true;
                            this.editingSectionId = section.id;
                            this.sectionForm = {
                                name: section.name || '',
                                abbreviation: section.abbreviation || '',
                                policy: section.policy || '',
                                meta_indexed: section.meta_indexed,
                                meta_reviewed: section.meta_reviewed
                            };
                            this.showSectionModal = true;
                        },
                        confirmDelete(id) {
                            this.deleteSectionId = id;
                            this.showDeleteConfirm = true;
                        }
                    }">

                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Journal Sections</h3>
                            <p class="text-sm text-gray-500">Organize articles into different sections or categories.</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <button type="button" @click="openCreateModal()"
                                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Create Section
                            </button>
                        </div>
                    </div>

                    <!-- Sections Table -->
                    @if ($sections->count() > 0)
                        <div class="overflow-hidden rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Section Name
                                        </th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                            Abbrev
                                        </th>
                                        <th scope="col"
                                            class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                            Options
                                        </th>
                                        <th scope="col"
                                            class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($sections as $section)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <p class="text-sm font-medium text-gray-900">{{ $section->name }}</p>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap hidden md:table-cell">
                                                @if ($section->abbreviation)
                                                    <span
                                                        class="px-2 py-1 text-xs font-mono bg-gray-100 text-gray-600 rounded">{{ $section->abbreviation }}</span>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap hidden sm:table-cell">
                                                <div class="flex items-center justify-center gap-3">
                                                    <div class="flex items-center gap-1.5"
                                                        title="{{ $section->meta_indexed ? 'Indexed' : 'Not Indexed' }}">
                                                        <span
                                                            class="w-4 h-4 rounded flex items-center justify-center {{ $section->meta_indexed ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                                                            <i
                                                                class="fa-solid text-[8px] {{ $section->meta_indexed ? 'fa-check' : 'fa-minus' }}"></i>
                                                        </span>
                                                        <span class="text-[10px] text-gray-500">IDX</span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5"
                                                        title="{{ $section->meta_reviewed ? 'Peer Reviewed' : 'Not Peer Reviewed' }}">
                                                        <span
                                                            class="w-4 h-4 rounded flex items-center justify-center {{ $section->meta_reviewed ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }}">
                                                            <i
                                                                class="fa-solid text-[8px] {{ $section->meta_reviewed ? 'fa-check' : 'fa-minus' }}"></i>
                                                        </span>
                                                        <span class="text-[10px] text-gray-500">PR</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button type="button"
                                                        @click="openEditModal({
                                                            id: '{{ $section->id }}',
                                                            name: '{{ addslashes($section->name) }}',
                                                            abbreviation: '{{ addslashes($section->abbreviation ?? '') }}',
                                                            policy: {{ json_encode($section->policy ?? '') }},
                                                            meta_indexed: {{ $section->meta_indexed ? 'true' : 'false' }},
                                                            meta_reviewed: {{ $section->meta_reviewed ? 'true' : 'false' }}
                                                        })"
                                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded transition-colors"
                                                        title="Edit">
                                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                                    </button>
                                                    <button type="button" @click="confirmDelete('{{ $section->id }}')"
                                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
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
                        <!-- Empty State -->
                        <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-layer-group text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-base font-medium text-gray-900 mb-1">No sections yet</h3>
                            <p class="text-sm text-gray-500 mb-4">Create your first journal section to organize articles.
                            </p>
                            <button type="button" @click="openCreateModal()"
                                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Create Section
                            </button>
                        </div>
                    @endif

                    <!-- Section Modal -->
                    <div x-show="showSectionModal" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto"
                        style="display: none;">
                        <div class="flex min-h-screen items-center justify-center p-4">
                            <!-- Backdrop -->
                            <div class="fixed inset-0 bg-black/50" @click="showSectionModal = false"></div>

                            <!-- Modal Content -->
                            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900"
                                        x-text="isEditMode ? 'Edit Section' : 'Create Section'"></h3>
                                    <button type="button" @click="showSectionModal = false"
                                        class="text-gray-400 hover:text-gray-600">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>

                                <form
                                    :action="isEditMode ?
                                        '{{ route('journal.settings.sections.update', ['journal' => $journal->slug, 'section' => ':id']) }}'
                                        .replace(':id', editingSectionId) :
                                        '{{ route('journal.settings.sections.store', ['journal' => $journal->slug]) }}'"
                                    method="POST">
                                    @csrf
                                    <template x-if="isEditMode">
                                        <input type="hidden" name="_method" value="PUT">
                                    </template>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Section Name
                                                *</label>
                                            <input type="text" name="name" x-model="sectionForm.name" required
                                                placeholder="e.g., Research Articles" class="w-full">
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Abbreviation</label>
                                            <input type="text" name="abbreviation" x-model="sectionForm.abbreviation"
                                                placeholder="e.g., RA" class="w-full">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Section
                                                Policy</label>
                                            <textarea name="policy" x-model="sectionForm.policy" rows="3"
                                                placeholder="Describe the submission policy for this section..." class="w-full"></textarea>
                                        </div>

                                        <div class="flex flex-col gap-3 pt-2">
                                            <label class="flex items-center gap-3">
                                                <input type="checkbox" name="meta_indexed" value="1"
                                                    x-model="sectionForm.meta_indexed"
                                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                                <span class="text-sm text-gray-700">Index this section in search
                                                    engines</span>
                                            </label>
                                            <label class="flex items-center gap-3">
                                                <input type="checkbox" name="meta_reviewed" value="1"
                                                    x-model="sectionForm.meta_reviewed"
                                                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                                <span class="text-sm text-gray-700">Items in this section are
                                                    peer-reviewed</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                                        <button type="button" @click="showSectionModal = false"
                                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg">
                                            <span x-text="isEditMode ? 'Update Section' : 'Create Section'"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div x-show="showDeleteConfirm" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex min-h-screen items-center justify-center p-4">
                            <div class="fixed inset-0 bg-black/50" @click="showDeleteConfirm = false"></div>
                            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm p-6 text-center">
                                <div
                                    class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-trash text-red-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Section?</h3>
                                <p class="text-sm text-gray-500 mb-6">This action cannot be undone. Sections with
                                    submissions cannot be deleted.</p>
                                <form
                                    :action="'{{ route('journal.settings.sections.destroy', ['journal' => $journal->slug, 'section' => ':id']) }}'
                                    .replace(':id', deleteSectionId)"
                                    method="POST" class="flex justify-center gap-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" @click="showDeleteConfirm = false"
                                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- TAB 4: CATEGORIES -->
                <!-- ============================================ -->
                <div x-show="activeTab === 'categories'" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-data="{
                        showCategoryModal: false,
                        isEditMode: false,
                        editingCategoryId: null,
                        categoryForm: {
                            name: '',
                            path: '',
                            description: ''
                        },
                        deleteCategoryId: null,
                        showDeleteConfirm: false,
                        openCreateModal() {
                            this.isEditMode = false;
                            this.editingCategoryId = null;
                            this.categoryForm = { name: '', path: '', description: '' };
                            this.showCategoryModal = true;
                        },
                        openEditModal(category) {
                            this.isEditMode = true;
                            this.editingCategoryId = category.id;
                            this.categoryForm = {
                                name: category.name || '',
                                path: category.path || '',
                                description: category.description || ''
                            };
                            this.showCategoryModal = true;
                        },
                        confirmDelete(id) {
                            this.deleteCategoryId = id;
                            this.showDeleteConfirm = true;
                        }
                    }">

                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Categories</h3>
                            <p class="text-sm text-gray-500">Organize journal content by topic or research area.</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <button type="button" @click="openCreateModal()"
                                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Add Category
                            </button>
                        </div>
                    </div>

                    @if ($categories->count() > 0)
                        <!-- Categories List -->
                        <div class="space-y-2">
                            @foreach ($categories as $category)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-tag text-primary-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $category->name }}</p>
                                            <p class="text-xs text-gray-500">/{{ $category->path }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                            @click="openEditModal({
                                                id: '{{ $category->id }}',
                                                name: '{{ addslashes($category->name) }}',
                                                path: '{{ addslashes($category->path) }}',
                                                description: {{ json_encode($category->description ?? '') }}
                                            })"
                                            class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-white rounded transition-colors"
                                            title="Edit">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </button>
                                        <button type="button" @click="confirmDelete('{{ $category->id }}')"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white rounded transition-colors"
                                            title="Delete">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-tags text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-base font-medium text-gray-900 mb-1">No categories defined</h3>
                            <p class="text-sm text-gray-500 mb-4 max-w-sm mx-auto">Categories help readers browse content
                                by topic. Add categories to organize your journal articles.</p>
                            <button type="button" @click="openCreateModal()"
                                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Create Your First Category
                            </button>
                        </div>
                    @endif

                    <!-- Category Modal -->
                    <div x-show="showCategoryModal" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex min-h-screen items-center justify-center p-4">
                            <div class="fixed inset-0 bg-black/50" @click="showCategoryModal = false"></div>
                            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900"
                                        x-text="isEditMode ? 'Edit Category' : 'Add Category'"></h3>
                                    <button type="button" @click="showCategoryModal = false"
                                        class="text-gray-400 hover:text-gray-600">
                                        <i class="fa-solid fa-xmark text-lg"></i>
                                    </button>
                                </div>

                                <form
                                    :action="isEditMode ?
                                        '{{ route('journal.settings.categories.update', ['journal' => $journal->slug, 'category' => ':id']) }}'
                                        .replace(':id', editingCategoryId) :
                                        '{{ route('journal.settings.categories.store', ['journal' => $journal->slug]) }}'"
                                    method="POST">
                                    @csrf
                                    <template x-if="isEditMode">
                                        <input type="hidden" name="_method" value="PUT">
                                    </template>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Category Name
                                                *</label>
                                            <input type="text" name="name" x-model="categoryForm.name" required
                                                placeholder="e.g., Computer Science" class="w-full">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Path / Slug</label>
                                            <div class="flex items-center">
                                                <span
                                                    class="px-3 py-2 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-sm text-gray-500">/</span>
                                                <input type="text" name="path" x-model="categoryForm.path"
                                                    placeholder="auto-generated-from-name" class="w-full rounded-l-none">
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from name
                                            </p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea name="description" x-model="categoryForm.description" rows="3"
                                                placeholder="Optional description of this category..." class="w-full"></textarea>
                                        </div>
                                    </div>

                                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                                        <button type="button" @click="showCategoryModal = false"
                                            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg">
                                            <span x-text="isEditMode ? 'Update Category' : 'Add Category'"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div x-show="showDeleteConfirm" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex min-h-screen items-center justify-center p-4">
                            <div class="fixed inset-0 bg-black/50" @click="showDeleteConfirm = false"></div>
                            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm p-6 text-center">
                                <div
                                    class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-trash text-red-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Category?</h3>
                                <p class="text-sm text-gray-500 mb-6">This action cannot be undone.</p>
                                <form
                                    :action="'{{ route('journal.settings.categories.destroy', ['journal' => $journal->slug, 'category' => ':id']) }}'
                                    .replace(':id', deleteCategoryId)"
                                    method="POST" class="flex justify-center gap-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" @click="showDeleteConfirm = false"
                                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        @endsection

        @push('scripts')
            <script>
                // Initialize TinyMCE for all rich text editors (OJS 3.3 Compatible)
                tinymce.init({
                    selector: '.tinymce-editor',
                    height: 500,
                    menubar: false,
                    plugins: 'lists link image table code autoresize',
                    toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | table link image | code',
                    branding: false,
                    license_key: 'gpl',
                    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
                    images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                        const xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', '{{ route('profile.upload.image') }}');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                        xhr.upload.onprogress = (e) => {
                            progress(e.loaded / e.total * 100);
                        };

                        xhr.onload = () => {
                            if (xhr.status === 403) {
                                reject({
                                    message: 'HTTP Error: ' + xhr.status,
                                    remove: true
                                });
                                return;
                            }

                            if (xhr.status < 200 || xhr.status >= 300) {
                                reject('HTTP Error: ' + xhr.status);
                                return;
                            }

                            const json = JSON.parse(xhr.responseText);

                            if (!json || typeof json.location != 'string') {
                                reject('Invalid JSON: ' + xhr.responseText);
                                return;
                            }

                            resolve(json.location);
                        };

                        xhr.onerror = () => {
                            reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                        };

                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());

                        xhr.send(formData);
                    })
                });
            </script>
        @endpush
