@extends('layouts.app')

@section('title', 'New Submission - ' . $journal->name)

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* CKEditor Custom Styling */
        .ck-editor__editable {
            min-height: 250px !important;
        }

        .ck-editor__editable:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
        }
    </style>

    <!-- CKEditor 5 CDN (Free, No API Key Required) -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

    <div class="max-w-5xl mx-auto py-8">

        <div x-data="submissionWizard()" x-cloak class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

            <!-- Header / Progress -->
            <div class="bg-gray-50 border-b border-gray-200 px-8 py-4">
                <h1 class="text-xl font-bold text-gray-900 mb-4">Submit an Article</h1>

                <!-- Progress Bar -->
                <div class="relative">
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                        <div :style="'width: ' + ((step - 1) / 3 * 100) + '%'"
                            class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-600 transition-all duration-500">
                        </div>
                    </div>
                    <div class="flex justify-between text-xs font-semibold text-gray-500">
                        <span :class="{ 'text-indigo-700': step >= 1 }">1. Start</span>
                        <span :class="{ 'text-indigo-700': step >= 2 }">2. Upload</span>
                        <span :class="{ 'text-indigo-700': step >= 3 }">3. Metadata</span>
                        <span :class="{ 'text-indigo-700': step >= 4 }">4. Confirmation</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('journal.submissions.store', ['journal' => $journal->slug]) }}" method="POST"
                enctype="multipart/form-data" id="submissionForm" x-ref="form">
                @csrf

                <!-- Hidden File Input (ALWAYS in DOM for form submission) -->
                <input type="file" name="manuscript" x-ref="fileInput" class="hidden" accept=".doc,.docx,.pdf"
                    @change="handleFileChange($event.target.files[0])">

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mx-8 mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                            <h4 class="text-sm font-bold text-red-800">Please correct the following errors:</h4>
                        </div>
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Validation Errors (Frontend) -->
                <div x-show="validationErrors.length > 0" x-cloak
                    class="mx-8 mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                        <h4 class="text-sm font-bold text-red-800">Please correct the following errors:</h4>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        <template x-for="error in validationErrors" :key="error">
                            <li x-text="error"></li>
                        </template>
                    </ul>
                </div>

                <!-- STEP 1: START -->
                <div x-show="step === 1" x-transition class="p-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Submission Requirements</h2>
                    <p class="text-sm text-gray-500 mb-6">Create a new submission to the <span
                            class="font-bold">{{ $journal->name }}</span>. Please check the following requirements before
                        proceeding.</p>

                    <!-- Section Selection -->
                    <div class="mb-8 max-w-md">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Section <span
                                class="text-red-500">*</span></label>
                        <select name="section_id"
                            class="block w-full rounded-md border border-gray-300 bg-white text-black shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3"
                            style="appearance: auto; -webkit-appearance: listbox; color: black !important;" required>
                            <option value="" class="text-gray-500">Select a section...</option>
                            @if ($sections->count() > 0)
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" class="text-black"
                                        {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }} {{ $section->is_active ? '' : '(Inactive)' }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No active sections found.</option>
                            @endif
                        </select>
                    </div>

                    <!-- Checklist -->
                    <div class="space-y-4 mb-8">
                        <label class="block text-sm font-medium text-gray-700">Submission Checklist</label>
                        @if ($submissionChecklists->isNotEmpty())
                            @foreach ($submissionChecklists as $item)
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="requirements[]" value="{{ $item->id }}"
                                            x-model="requirements"
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label class="font-medium text-gray-700">{{ $item->content }}</label>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500 italic bg-gray-50 p-3 rounded">No specific requirements checked.
                            </p>
                        @endif
                    </div>

                    <!-- Copyright Notice -->
                    @if ($journal->license_terms)
                        <div class="bg-blue-50 p-4 rounded-lg mb-6 border border-blue-100">
                            <h4 class="text-sm font-bold text-blue-800 mb-2">Copyright Notice</h4>
                            <p class="text-xs text-blue-700 whitespace-pre-line">{{ $journal->license_terms }}</p>
                            <label class="flex items-center mt-3">
                                <input type="checkbox" required
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-xs text-blue-700 font-medium">I agree to the copyright terms.</span>
                            </label>
                        </div>
                    @endif

                    <!-- Comments for the Editor -->
                    <div class="border-t border-gray-200 pt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comments for the Editor
                            (Optional)</label>
                        <div id="commentsEditor" class="rounded-lg border border-gray-300">{{ old('comments_for_editor') }}
                        </div>
                        <textarea name="comments_for_editor" id="commentsHidden" class="hidden">{{ old('comments_for_editor') }}</textarea>
                        <p class="text-xs text-gray-500 mt-2">These comments will be visible only to the editorial team and
                            will be added as a discussion.</p>
                    </div>
                </div>

                <!-- STEP 2: UPLOAD SUBMISSION -->
                <div x-show="step === 2" x-transition class="p-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Upload Submission</h2>
                    <p class="text-sm text-gray-500 mb-6">Upload your manuscript file. Allowed formats: DOC, DOCX, PDF.</p>

                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-10 flex flex-col items-center justify-center transition-colors bg-gray-50 hover:bg-gray-100 hover:border-indigo-400 cursor-pointer"
                        @click="$refs.fileInput.click()"
                        @dragover.prevent="$el.classList.add('border-indigo-500', 'bg-indigo-50')"
                        @dragleave.prevent="$el.classList.remove('border-indigo-500', 'bg-indigo-50')"
                        @drop.prevent="$el.classList.remove('border-indigo-500', 'bg-indigo-50'); $refs.fileInput.files = $event.dataTransfer.files; handleFileChange($event.dataTransfer.files[0])">

                        <div x-show="!fileName" class="text-center pointer-events-none">
                            <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm font-medium text-gray-900">Drag and drop your file here</p>
                            <p class="text-xs text-gray-500 mt-1">or click to browse</p>
                        </div>

                        <div x-show="fileName" class="text-center w-full pointer-events-none">
                            <div class="flex items-center justify-center gap-3 mb-2">
                                <i class="fa-regular fa-file-word text-3xl text-indigo-600"></i>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-gray-900" x-text="fileName"></p>
                                    <p class="text-xs text-gray-500" x-text="fileSize"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="fileName" class="mt-3 text-center">
                        <button type="button" @click.stop="clearFile"
                            class="text-xs text-red-600 hover:text-red-800 font-medium">
                            <i class="fa-solid fa-times mr-1"></i> Remove File
                        </button>
                    </div>
                </div>

                <!-- STEP 3: ENTER METADATA -->
                <div x-show="step === 3" x-transition class="p-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Enter Metadata</h2>

                    <!-- Title & Abstract -->
                    <div class="space-y-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title <span
                                    class="text-red-500">*</span></label>
                            <textarea name="title" x-model="title" rows="2"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Article Title" required></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                            <input type="text" name="subtitle" x-model="subtitle"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Optional subtitle">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Abstract <span
                                    class="text-red-500">*</span></label>
                            <div id="abstractEditor" class="rounded-lg border border-gray-300">{{ old('abstract') }}
                            </div>
                            <textarea name="abstract" id="abstractHidden" class="hidden">{{ old('abstract') }}</textarea>
                        </div>
                        <div x-data="keywordInput()">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keywords</label>
                            <input type="text" x-ref="keywordInput"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Type keyword and press Enter">
                            <p class="text-xs text-gray-500 mt-1">Press Enter or comma to add keywords. Start typing to see
                                suggestions.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">References</label>
                            <textarea name="references" x-model="references" rows="5"
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Paste your references here..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">Provide a list of references for your work.</p>
                        </div>
                    </div>

                    <!-- Contributors -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <label class="block text-sm font-medium text-gray-900">List of Contributors</label>
                            <button type="button" @click="addAuthor"
                                class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-md font-medium transition">
                                <i class="fa-solid fa-plus mr-1"></i> Add Contributor
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(author, index) in authors" :key="index">
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 relative group">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">First Name</label>
                                            <input type="text" :name="'authors[' + index + '][first_name]'"
                                                x-model="author.first_name"
                                                class="w-full text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                required>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Last Name</label>
                                            <input type="text" :name="'authors[' + index + '][last_name]'"
                                                x-model="author.last_name"
                                                class="w-full text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs text-gray-500 mb-1">Email</label>
                                            <input type="email" :name="'authors[' + index + '][email]'"
                                                x-model="author.email"
                                                class="w-full text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                required>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Affiliation</label>
                                            <input type="text" :name="'authors[' + index + '][affiliation]'"
                                                x-model="author.affiliation"
                                                class="w-full text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">Country</label>
                                            <input type="text" :name="'authors[' + index + '][country]'"
                                                x-model="author.country"
                                                class="w-full text-sm rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                maxlength="100" placeholder="Indonesia">
                                        </div>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between">
                                        <label class="flex items-center">
                                            <input type="radio" name="primary_contact" :value="index"
                                                x-model="primaryContactIndex"
                                                class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-xs font-semibold text-gray-600">Primary Contact</span>
                                        </label>
                                        <button type="button" @click="removeAuthor(index)" x-show="authors.length > 1"
                                            class="text-xs text-red-500 hover:text-red-700">Delete</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: CONFIRMATION -->
                <div x-show="step === 4" x-transition class="p-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Confirm Submission</h2>
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-5 mb-6">
                        <p class="text-sm text-indigo-800">Please review your data before finishing. Once submitted, you
                            may not be able to edit specific details immediately.</p>
                    </div>

                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Title</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold" x-text="title"></dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Abstract</dt>
                            <dd class="mt-1 text-sm text-gray-900 italic prose prose-sm max-w-none" x-html="abstractHtml">
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">File</dt>
                            <dd class="mt-1 text-sm text-gray-900 flex items-center gap-2">
                                <i class="fa-regular fa-file-lines"></i>
                                <span x-text="fileName || 'No file selected'"></span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contributors</dt>
                            <dd class="mt-1 text-sm text-gray-900" x-text="authors.length + ' author(s)'"></dd>
                        </div>
                        <div x-show="references">
                            <dt class="text-sm font-medium text-gray-500">References</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line truncate max-w-xs"
                                x-text="references"></dd>
                        </div>
                    </dl>


                </div>

                <!-- Footer Buttons -->
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 flex justify-between items-center rounded-b-xl">
                    <button type="button" x-show="step > 1" @click="step--"
                        class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Back
                    </button>
                    <div x-show="step === 1"></div> <!-- Spacer -->

                    <button type="button" x-show="step < 4" @click="nextStep()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-medium shadow-sm transition">
                        Next <i class="fa-solid fa-arrow-right ml-1"></i>
                    </button>

                    <button type="button" x-show="step === 4" @click="submitForm()" :disabled="isSubmitting"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg text-sm font-medium shadow-sm transition flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Finish Submission <i class="fa-solid fa-check ml-1"></i></span>
                        <span x-show="isSubmitting"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Processing...</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
        // CKEditor Instances
        let editorInstance = null;
        let commentsEditorInstance = null;

        // Custom Upload Adapter for CKEditor
        class CustomUploadAdapter {
            constructor(loader) {
                this.loader = loader;
            }

            upload() {
                return this.loader.file.then(file => new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('file', file);
                    fetch('{{ route('journal.upload.image', ['journal' => $journal->slug]) }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            }
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.location) {
                                resolve({
                                    default: result.location
                                });
                            } else {
                                reject(result.error || 'Upload failed');
                            }
                        })
                        .catch(error => reject(error));
                }));
            }

            abort() {}
        }

        function CustomUploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                return new CustomUploadAdapter(loader);
            };
        }

        // Initialize CKEditor
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Abstract Editor
            ClassicEditor
                .create(document.querySelector('#abstractEditor'), {
                    extraPlugins: [CustomUploadAdapterPlugin],
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', '|',
                            'bulletedList', 'numberedList', '|',
                            'outdent', 'indent', '|',
                            'link', 'imageUpload', 'blockQuote', 'insertTable', '|',
                            'undo', 'redo'
                        ]
                    },
                    image: {
                        toolbar: ['imageTextAlternative', 'imageStyle:inline', 'imageStyle:block',
                            'imageStyle:side'
                        ]
                    },
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                    },
                    placeholder: 'Enter your abstract here...'
                })
                .then(editor => {
                    editorInstance = editor;

                    // Sync content to hidden textarea on change
                    editor.model.document.on('change:data', () => {
                        document.querySelector('#abstractHidden').value = editor.getData();
                    });
                })
                .catch(error => {
                    console.error('CKEditor initialization failed:', error);
                });

            // Initialize Comments Editor
            ClassicEditor
                .create(document.querySelector('#commentsEditor'), {
                    extraPlugins: [CustomUploadAdapterPlugin],
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', '|',
                            'bulletedList', 'numberedList', '|',
                            'link', 'blockQuote', '|',
                            'undo', 'redo'
                        ]
                    },
                    placeholder: 'Enter your comments for the editor here...'
                })
                .then(editor => {
                    commentsEditorInstance = editor;
                    editor.model.document.on('change:data', () => {
                        document.querySelector('#commentsHidden').value = editor.getData();
                    });
                })
                .catch(error => {
                    console.error('Comments CKEditor initialization failed:', error);
                });
        });

        function submissionWizard() {
            return {
                step: {{ $errors->any() ? 1 : 1 }},
                requirements: [],
                totalRequirements: {{ $submissionChecklists->count() }},
                validationErrors: [],
                title: '{{ old('title', '') }}',
                subtitle: '{{ old('subtitle', '') }}',
                abstract: '',
                abstractHtml: '',
                fileName: '',
                fileSize: '',
                references: '{{ old('references', '') }}',
                primaryContactIndex: 0,
                authors: [
                    @php
                        $parts = explode(' ', auth()->user()->name, 2);
                        $first = old('authors.0.first_name', $parts[0]);
                        $last = old('authors.0.last_name', $parts[1] ?? '');
                    @endphp {
                        first_name: '{{ $first }}',
                        last_name: '{{ $last }}',
                        email: '{{ old('authors.0.email', auth()->user()->email) }}',
                        affiliation: '{{ old('authors.0.affiliation', auth()->user()->affiliation) }}',
                        country: '{{ old('authors.0.country', auth()->user()->country) }}'
                    }
                ],

                canProceed() {
                    if (this.step === 1) {
                        if (this.totalRequirements === 0) return true;
                        return this.requirements.length >= this.totalRequirements;
                    }
                    if (this.step === 2) {
                        return this.fileName !== '';
                    }
                    if (this.step === 3) {
                        // Get abstract from CKEditor
                        if (editorInstance) {
                            this.abstractHtml = editorInstance.getData();
                            // Strip HTML tags for plain text validation
                            const div = document.createElement('div');
                            div.innerHTML = this.abstractHtml;
                            this.abstract = div.textContent || div.innerText || '';
                        }
                        return this.title && this.abstract.trim() && this.authors.every(a => a.first_name && a.last_name &&
                            a.email);
                    }
                    return true;
                },

                nextStep() {
                    // Sync CKEditors
                    if (this.step === 1 && commentsEditorInstance) {
                        document.querySelector('#commentsHidden').value = commentsEditorInstance.getData();
                    }
                    if (this.step === 3 && editorInstance) {
                        document.querySelector('#abstractHidden').value = editorInstance.getData();
                        this.abstractHtml = editorInstance.getData();
                    }

                    // Validate current step
                    this.validationErrors = [];

                    if (this.step === 1) {
                        if (this.totalRequirements > 0 && this.requirements.length < this.totalRequirements) {
                            this.validationErrors.push('Please check all required submission checklist items.');
                        }
                    } else if (this.step === 2) {
                        if (!this.fileName) {
                            this.validationErrors.push('Please upload a manuscript file.');
                        }
                    } else if (this.step === 3) {
                        if (!this.title || this.title.trim() === '') {
                            this.validationErrors.push('Title is required.');
                        }

                        if (editorInstance) {
                            this.abstractHtml = editorInstance.getData();
                            const div = document.createElement('div');
                            div.innerHTML = this.abstractHtml;
                            this.abstract = div.textContent || div.innerText || '';
                        }
                        if (!this.abstract || this.abstract.trim() === '') {
                            this.validationErrors.push('Abstract is required.');
                        }

                        // Validate authors
                        this.authors.forEach((author, index) => {
                            if (!author.first_name || author.first_name.trim() === '') {
                                this.validationErrors.push(`Contributor ${index + 1}: First name is required.`);
                            }
                            if (!author.last_name || author.last_name.trim() === '') {
                                this.validationErrors.push(`Contributor ${index + 1}: Last name is required.`);
                            }
                            if (!author.email || author.email.trim() === '') {
                                this.validationErrors.push(`Contributor ${index + 1}: Email is required.`);
                            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(author.email)) {
                                this.validationErrors.push(`Contributor ${index + 1}: Valid email is required.`);
                            }
                        });
                    }

                    // Show errors or proceed
                    if (this.validationErrors.length > 0) {
                        // Scroll to top to show errors
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    } else {
                        this.step++;
                    }
                },

                handleFileChange(file) {
                    if (!file) return;
                    this.fileName = file.name;
                    this.fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                },

                clearFile() {
                    this.fileName = '';
                    this.fileSize = '';
                    this.$refs.fileInput.value = '';
                },

                addAuthor() {
                    this.authors.push({
                        first_name: '',
                        last_name: '',
                        email: '',
                        affiliation: '',
                        country: ''
                    });
                },

                removeAuthor(index) {
                    if (this.authors.length <= 1) return;
                    this.authors.splice(index, 1);
                    if (this.primaryContactIndex >= index && this.primaryContactIndex > 0) {
                        this.primaryContactIndex--;
                    }
                },

                isSubmitting: false,

                submitForm() {
                    if (this.isSubmitting) return;
                    this.isSubmitting = true;
                    this.$refs.form.submit();
                }
            }
        }

        // ========== KEYWORD INPUT (Tagify) ==========
        function keywordInput() {
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
                        script.onload = () => this.initializeTagify();
                        document.head.appendChild(script);
                    } else {
                        this.initializeTagify();
                    }
                },

                initializeTagify() {
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
@endsection
