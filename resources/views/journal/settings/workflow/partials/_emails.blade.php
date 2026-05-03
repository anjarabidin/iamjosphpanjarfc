@php
    $journal = current_journal();
    $journalSlug = $journal->slug;
@endphp

<div x-data="{
    showEditModal: false,
    editingTemplate: null,
    searchQuery: '',

    editTemplate(template) {
        this.editingTemplate = template;
        this.showEditModal = true;
    },

    async updateStatus(templateId, event) {
        const newStatus = event.target.value;
        // Construct URL - using the named route would be cleaner if we could inject it into JS, 
        // but constructing it based on known pattern is fine for this context.
        // Route: journal.settings.workflow.email-templates.toggle -> /{journal}/settings/workflow/email-templates/{emailTemplate}/toggle
        const url = `/{{ $journalSlug }}/settings/workflow/email-templates/${templateId}/toggle`;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            });

            if (response.ok) {
                // Optional: show a toast or feedback
                // For now, relies on the user seeing the select change.
                // A reload isn't needed as the select state persists.
            } else {
                console.error('Failed to update status');
                // Revert the select if needed or show error
                alert('Failed to update status. Please try again.');
                event.target.value = newStatus == '1' ? '0' : '1';
            }
        } catch (e) {
            console.error(e);
            alert('An error occurred.');
        }
    }
}">
    <form action="{{ route('journal.settings.workflow.update', ['journal' => $journal->slug]) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="tab" value="emails">

        <div class="space-y-10">
            <!-- Section: Email Config -->
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-at text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Email Configuration</h3>
                        <p class="text-sm text-gray-500">Configure email sending settings.</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label for="email_signature" class="block text-sm font-medium text-gray-700 mb-2">Email
                            Signature</label>
                        <textarea name="email_signature" id="email_signature" rows="5"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="e.g. The Editor, {{ $journal->name }}">{{ $journal->email_signature }}</textarea>
                        <p class="mt-2 text-xs text-gray-500">This signature will be appended to the bottom of outgoing
                            emails.</p>
                    </div>

                    <div>
                        <label for="email_bounce_address" class="block text-sm font-medium text-gray-700 mb-2">Bounce
                            Address</label>
                        <input type="email" name="email_bounce_address" id="email_bounce_address"
                            value="{{ $journal->email_bounce_address }}"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-100 rounded text-xs text-blue-700">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            Undeliverable emails will be returned to this address. Ensure your server configuration
                            allows sending on behalf of this domain.
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200">

            <!-- Section: Templates -->
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-4">Email Templates</h4>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    {{-- Toolbar --}}
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                        <div class="relative w-full max-w-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-search text-gray-400"></i>
                            </div>
                            <input type="text" x-model="searchQuery"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Find email template...">
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Template Name</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Enabled</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($emailTemplates as $template)
                                    <tr
                                        x-show="searchQuery === '' || '{{ strtolower($template->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($template->key) }}'.includes(searchQuery.toLowerCase())">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-medium text-gray-900">{{ $template->name }}</span>
                                                <span
                                                    class="text-xs text-gray-500 font-mono">{{ $template->key }}</span>
                                                <span
                                                    class="text-xs text-gray-400 truncate max-w-md">{{ Str::limit($template->description, 60) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <select @change="updateStatus({{ $template->id }}, $event)"
                                                class="block w-32 pl-3 pr-10 py-1.5 text-xs border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                                                :class="$el.value == 1 ? 'text-green-700 bg-green-50 border-green-200' :
                                                    'text-red-700 bg-red-50 border-red-200'">
                                                <option value="1" {{ $template->is_enabled ? 'selected' : '' }}
                                                    class="text-gray-900 bg-white">Enabled</option>
                                                <option value="0" {{ !$template->is_enabled ? 'selected' : '' }}
                                                    class="text-gray-900 bg-white">Disabled</option>
                                            </select>
                                        </td>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-3">
                                                @if ($template->is_custom)
                                                    <button type="button"
                                                        onclick="submitForm('{{ route('journal.settings.workflow.email-templates.reset', ['journal' => $journalSlug, 'emailTemplate' => $template->id]) }}', 'POST', 'Reset this template to its default content?')"
                                                        class="text-xs text-orange-600 hover:text-orange-900 bg-orange-50 px-2 py-1 rounded">Reset</button>
                                                @endif

                                                <button type="button" @click="editTemplate({{ $template }})"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                    <i class="fa-solid fa-pen"></i> Edit
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-gray-200 flex justify-end">
            <button type="submit"
                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                <i class="fa-solid fa-check mr-2"></i>
                Save Setup
            </button>
        </div>
    </form>

    {{-- Edit Modal --}}
    <template x-teleport="body">
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[99] overflow-y-auto" role="dialog"
            aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4">

                {{-- Backdrop --}}
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true"
                    @click="showEditModal = false">
                </div>

                {{-- Modal Panel --}}
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-hidden transform transition-all z-[100]">

                    <template x-if="editingTemplate">
                        <form :action="'/{{ $journalSlug }}/settings/workflow/email-templates/' + editingTemplate?.id"
                            method="POST">
                            @csrf
                            @method('PUT')

                            <div class="bg-white px-6 py-6">
                                <div class="flex items-center justify-between mb-5">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">
                                            Edit Email Template
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <span class="font-medium text-gray-700"
                                                x-text="editingTemplate.name"></span> - <span
                                                x-text="editingTemplate.key" class="font-mono text-xs"></span>
                                        </p>
                                    </div>
                                    <button type="button" @click="showEditModal = false"
                                        class="text-gray-400 hover:text-gray-500">
                                        <i class="fa-solid fa-xmark text-xl"></i>
                                    </button>
                                </div>

                                <div
                                    class="bg-blue-50 border border-blue-100 rounded-lg p-3 mb-6 text-sm text-blue-800">
                                    <p x-text="editingTemplate.description"></p>
                                </div>

                                <div class="space-y-5">
                                    {{-- Subject --}}
                                    <div>
                                        <label for="subject"
                                            class="block text-sm font-semibold text-gray-700 mb-1">Subject Line</label>
                                        <input type="text" name="subject" id="subject"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            x-model="editingTemplate.subject" required>
                                    </div>

                                    {{-- Body --}}
                                    <div>
                                        <label for="body"
                                            class="block text-sm font-semibold text-gray-700 mb-1">Email Body</label>
                                        <textarea name="body" id="body" rows="12"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm leading-relaxed"
                                            x-model="editingTemplate.body" required></textarea>
                                    </div>

                                    {{-- Variables Hint --}}
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-3">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                            Available Variables</p>
                                        <div class="flex flex-wrap gap-2 text-xs font-mono text-gray-600">
                                            <span
                                                class="bg-white border border-gray-200 px-1.5 py-0.5 rounded">{$authorName}</span>
                                            <span
                                                class="bg-white border border-gray-200 px-1.5 py-0.5 rounded">{$recipientName}</span>
                                            <span
                                                class="bg-white border border-gray-200 px-1.5 py-0.5 rounded">{$submissionTitle}</span>
                                            <span
                                                class="bg-white border border-gray-200 px-1.5 py-0.5 rounded">{$journalName}</span>
                                            <span
                                                class="bg-white border border-gray-200 px-1.5 py-0.5 rounded">{$submissionUrl}</span>
                                            <span
                                                class="bg-white border border-gray-200 px-1.5 py-0.5 rounded">{$signature}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                                <button type="submit"
                                    class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                                    Save Changes
                                </button>
                                <button type="button" @click="showEditModal = false"
                                    class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
@push('scripts')
    <script src="{{ asset('assets/js/vendors/plugins/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            selector: '#email_signature',
            height: 350,
            menubar: false,
            plugins: 'lists link image table code autoresize',
            toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | table link image | code',
            branding: false,
            license_key: 'gpl',
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
