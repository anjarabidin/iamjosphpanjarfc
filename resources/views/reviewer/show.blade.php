@php
    $journal = current_journal();
@endphp

<x-app-layout>
    <x-slot name="title">Review Submission</x-slot>
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('journal.reviewer.index', ['journal' => $journal->slug]) }}"
                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Review Submission</h1>
                <p class="mt-1 text-sm text-gray-500">Round {{ $assignment->round }} • Due
                    {{ $assignment->due_date?->format('M j, Y') ?? 'No deadline' }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6">

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Left Column: Manuscript Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Submission Info (Blind Review) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                            {{ $submission->section->name ?? 'Uncategorized' }}
                        </span>
                        <span class="text-sm text-gray-500">
                            Submitted {{ $submission->submitted_at?->format('M j, Y') }}
                        </span>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $submission->title }}</h2>

                    <!-- Additional Info Table -->
                    <div class="mb-6 bg-gray-50 rounded-lg p-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span
                                class="block text-gray-500 text-xs uppercase tracking-wider font-semibold">Language</span>
                            <span class="text-gray-900">{{ $submission->metadata['language'] ?? 'English' }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500 text-xs uppercase tracking-wider font-semibold">Submission
                                Type</span>
                            <span class="text-gray-900">{{ $submission->section->name ?? 'Article' }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500 text-xs uppercase tracking-wider font-semibold">Manuscript
                                ID</span>
                            <span class="text-gray-900">{{ $submission->submission_code ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span
                                class="block text-gray-500 text-xs uppercase tracking-wider font-semibold">Copyright</span>
                            <span class="text-gray-900">© {{ now()->year }} {{ $journal->name }}</span>
                        </div>
                    </div>

                    <!-- Abstract -->
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Abstract</h3>
                        <div class="prose prose-sm max-w-none text-gray-600">
                            {!! clean($submission->abstract) !!}
                        </div>
                    </div>

                    <!-- Keywords -->
                    @if ($submission->keywords)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Keywords</h3>
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

                <!-- Review Discussions -->
                <div class="mb-6">
                    <x-discussion-panel :submission="$submission" :stageId="2" stageName="Review" :discussions="$submission->discussions"
                        :participants="$participants" :journal="$journal" />
                </div>

                <!-- Review Files -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Review Files</h3>

                    @if ($manuscriptFiles->isEmpty())
                        <p class="text-gray-500">No review files available.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($manuscriptFiles as $file)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center min-w-0 flex-1 mr-4">
                                        <div
                                            class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-file-pdf text-red-500"></i>
                                        </div>
                                        <div class="ml-3 min-w-0 flex-1">
                                            <p class="font-medium text-gray-900 truncate"
                                                title="{{ $file->file_name }}">
                                                {{ $file->file_name }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                {{ $file->file_type_label }} • Version {{ $file->version }} •
                                                {{ $file->file_size_formatted }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 flex items-center space-x-2">
                                        @php
                                            $extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
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
                                        <a href="{{ route('files.download', $file) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                                            <i class="fa-solid fa-download mr-1.5"></i>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Review Form (only if not completed) -->
                @if ($assignment->status !== 'completed')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Submit Your Review</h3>

                        <form
                            action="{{ route('journal.reviewer.submit', ['journal' => $journal->slug, 'assignment' => $assignment]) }}"
                            method="POST" x-data="{ recommendation: '{{ old('recommendation') }}' }">
                            @csrf

                            <!-- Recommendation -->
                            <div class="mb-6">
                                <label for="recommendation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Recommendation <span class="text-red-500">*</span>
                                </label>
                                <select name="recommendation" id="recommendation" x-model="recommendation" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <option value="">Select your recommendation...</option>
                                    <option value="accept">Accept - Ready for publication</option>
                                    <option value="minor_revision">Minor Revision - Accept with minor changes</option>
                                    <option value="major_revision">Major Revision - Significant changes required
                                    </option>
                                    <option value="resubmit">Resubmit for Review - Needs substantial rework</option>
                                    <option value="reject">Reject - Not suitable for publication</option>
                                </select>
                                @error('recommendation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Comments for Author -->
                            <div class="mb-6">
                                <label for="comments_for_author" class="block text-sm font-medium text-gray-700 mb-2">
                                    Comments for Author <span class="text-red-500">*</span>
                                </label>
                                <textarea name="comments_for_author" id="comments_for_author" rows="8" placeholder="Provide detailed feedback..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('comments_for_author') }}</textarea>
                                @error('comments_for_author')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">These comments will be visible to the author.</p>
                            </div>

                            <!-- Comments for Editor (Confidential) -->
                            <div class="mb-6">
                                <label for="comments_for_editor" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confidential Comments for Editor
                                </label>
                                <textarea name="comments_for_editor" id="comments_for_editor" rows="4"
                                    placeholder="Optional: Share any confidential observations..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('comments_for_editor') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    These comments are confidential and will only be visible to the editor.
                                </p>
                            </div>

                            <!-- Reviewer Attachments -->
                            <div class="mb-6" x-data="reviewerAttachments()">
                                <h4 class="text-sm font-medium text-gray-900 mb-1">Reviewer Attachments</h4>
                                <p class="text-xs text-gray-500 mb-4">
                                    Upload files you would like the editor and/or author to consult, including revised versions of the original review file(s).
                                </p>
                                
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition-colors"
                                    @dragover.prevent="$el.classList.add('border-primary-500', 'bg-primary-50')"
                                    @dragleave.prevent="$el.classList.remove('border-primary-500', 'bg-primary-50')"
                                    @drop.prevent="handleDrop($event); $el.classList.remove('border-primary-500', 'bg-primary-50')">
                                    
                                    <input type="file" id="attachmentInput" class="hidden" @change="handleFileSelect($event)" accept=".doc,.docx,.pdf,.rtf">
                                    
                                    <label for="attachmentInput" class="cursor-pointer">
                                        <div class="text-gray-500 mb-2">
                                            <i class="fa-solid fa-cloud-arrow-up text-3xl mb-2 text-primary-500"></i>
                                            <p class="font-medium text-gray-900">Click to upload or drag and drop</p>
                                            <p class="text-xs">DOC, DOCX, PDF, RTF (Max 10MB)</p>
                                        </div>
                                    </label>

                                    <!-- Upload Progress -->
                                    <div x-show="isUploading" class="mt-4" style="display: none;">
                                        <div class="h-1 w-full bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-primary-600 transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1" x-text="`Uploading... ${uploadProgress}%`"></p>
                                    </div>
                                </div>

                                <!-- Uploaded Files List -->
                                <div class="mt-4 space-y-2" x-show="files.length > 0" style="display: none;">
                                    <h5 class="text-sm font-medium text-gray-700">Uploaded Files</h5>
                                    <template x-for="file in files" :key="file.id">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <i class="fa-solid fa-file text-gray-400"></i>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900" x-text="file.name"></p>
                                                    <p class="text-xs text-gray-500" x-text="file.size"></p>
                                                </div>
                                            </div>
                                            <button type="button" @click="deleteFile(file.id)" class="text-red-500 hover:text-red-700 p-1" title="Delete File">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                                <a href="{{ route('journal.reviewer.index', ['journal' => $journal->slug]) }}"
                                    class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                    Save as Draft
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Submit Review
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- Completed Review Summary -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Your Review</h3>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Completed {{ $assignment->completed_at?->format('M j, Y') }}
                            </span>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Recommendation</h4>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-{{ $assignment->recommendation_color }}-100 text-{{ $assignment->recommendation_color }}-800">
                                    {{ $assignment->recommendation_label }}
                                </span>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-1">Comments for Author</h4>
                                <div class="prose prose-sm max-w-none text-gray-700 bg-gray-50 rounded-lg p-4">
                                    {!! clean($assignment->comments_for_author) !!}
                                </div>
                            </div>

                            @if ($assignment->comments_for_editor)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-1">Confidential Comments for Editor
                                    </h4>
                                    <div
                                        class="prose prose-sm max-w-none text-gray-700 bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                        {!! clean($assignment->comments_for_editor) !!}
                                    </div>
                                </div>
                            @endif

                            @if ($reviewerAttachments->isNotEmpty())
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 mb-2">Reviewer Attachments</h4>
                                    <div class="space-y-2">
                                        @foreach($reviewerAttachments as $file)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <i class="fa-solid fa-file text-gray-400"></i>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $file->file_name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $file->file_size_formatted }}</p>
                                                    </div>
                                                </div>
                                                <a href="{{ route('files.download', ['file' => $file->id]) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium flex items-center">
                                                    <i class="fa-solid fa-download mr-1"></i> Download
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Review Guidelines & Tools -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fa-solid fa-scale-balanced mr-2 text-primary-600"></i>
                        Review Guidelines
                    </h3>

                    <div class="space-y-4 text-sm text-gray-600">
                        <div class="flex items-start space-x-3">
                            <div
                                class="w-6 h-6 bg-primary-50 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-primary-100">
                                <span class="text-primary-700 font-bold text-xs">1</span>
                            </div>
                            <p>Read the manuscript thoroughly and assess its scientific quality and contribution.</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div
                                class="w-6 h-6 bg-primary-50 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-primary-100">
                                <span class="text-primary-700 font-bold text-xs">2</span>
                            </div>
                            <p>Evaluate methodology, results, and conclusions for validity and clarity.</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div
                                class="w-6 h-6 bg-primary-50 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-primary-100">
                                <span class="text-primary-700 font-bold text-xs">3</span>
                            </div>
                            <p>Provide constructive feedback. Be respectful and helpful in your critique.</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-2 flex items-center">
                            <i class="fa-regular fa-circle-question mr-2 text-gray-400"></i>
                            Need Help?
                        </h4>
                        <p class="text-sm text-gray-500">Contact the editorial team if you have conflict of interest or
                            technical issues.</p>
                    </div>
                </div>
            </div>
        </div>



    </div>
    @push('scripts')
        <script src="{{ asset('assets/js/vendors/plugins/tinymce/tinymce.min.js') }}"></script>
        <script>
            tinymce.init({
                selector: '#comments_for_editor, #comments_for_author',
                height: 350,
                menubar: false,
                plugins: 'lists link image table code autoresize',
                toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | table link image | code',
                branding: false,
                license_key: 'gpl',
                images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', '{{ route('journal.profile.upload.image', $journal->slug) }}');
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
        
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('reviewerAttachments', () => ({
                    files: @json($reviewerAttachments->map(function($f) {
                        return [
                            'id' => $f->id,
                            'name' => $f->file_name,
                            'size' => $f->file_size_formatted
                        ];
                    })),
                    isUploading: false,
                    uploadProgress: 0,

                    handleDrop(e) {
                        if (e.dataTransfer.files.length > 0) {
                            this.uploadFile(e.dataTransfer.files[0]);
                        }
                    },

                    handleFileSelect(e) {
                        if (e.target.files.length > 0) {
                            this.uploadFile(e.target.files[0]);
                        }
                    },

                    uploadFile(file) {
                        // Validate file type
                        const allowedTypes = ['.doc', '.docx', '.pdf', '.rtf'];
                        const extension = '.' + file.name.split('.').pop().toLowerCase();
                        
                        if (!allowedTypes.includes(extension)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid File Type',
                                text: 'Only DOC, DOCX, PDF, and RTF files are allowed.',
                                confirmButtonColor: '#2563eb'
                            });
                            return;
                        }

                        // Validate file size (10MB)
                        if (file.size > 10 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File Too Large',
                                text: 'Maximum file size is 10MB.',
                                confirmButtonColor: '#2563eb'
                            });
                            return;
                        }

                        const formData = new FormData();
                        formData.append('file', file);
                        
                        this.isUploading = true;
                        this.uploadProgress = 0;

                        axios.post('{{ route("journal.reviewer.upload-attachment", ["journal" => $journal->slug, "assignment" => $assignment]) }}', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            },
                            onUploadProgress: (progressEvent) => {
                                this.uploadProgress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                            }
                        })
                        .then(response => {
                            this.files.unshift({
                                id: response.data.file.id,
                                name: response.data.file.file_name,
                                size: (response.data.file.file_size / 1024).toFixed(2) + ' KB'
                            });
                            
                            // Reset input
                            document.getElementById('attachmentInput').value = '';
                            
                            // Success toast
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: 'Attachment uploaded successfully'
                            });
                        })
                        .catch(error => {
                            const message = error.response?.data?.message || 'An error occurred during upload';
                            Swal.fire({
                                icon: 'error',
                                title: 'Upload Failed',
                                text: message,
                                confirmButtonColor: '#2563eb'
                            });
                        })
                        .finally(() => {
                            this.isUploading = false;
                            this.uploadProgress = 0;
                        });
                    },

                    deleteFile(id) {
                        Swal.fire({
                            title: 'Delete Attachment?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                axios.delete(`/${this.journalSlug}/reviewer/{{ $assignment->slug }}/attachment/${id}`)
                                    .then(response => {
                                        this.files = this.files.filter(f => f.id !== id);
                                        
                                        const Toast = Swal.mixin({
                                            toast: true,
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            timer: 3000,
                                            timerProgressBar: true
                                        });
                                        Toast.fire({
                                            icon: 'success',
                                            title: 'Attachment deleted successfully'
                                        });
                                    })
                                    .catch(error => {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Failed to delete attachment.',
                                            confirmButtonColor: '#2563eb'
                                        });
                                    });
                            }
                        });
                    },
                    
                    get journalSlug() {
                        return '{{ $journal->slug }}';
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>
