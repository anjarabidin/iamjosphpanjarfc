@props([
    'journal',
    'submission',
    'pubStatus'
])

{{--
    This component uses the Alpine.js data scope from its parent container.
    Parent must define:
    - galleyModalOpen
    - editingGalley
    - galleyLabel, galleyLocale, galleyUrlPath, isRemote, remoteUrl, selectedFile
    - errors
    - submitGalley()
    - isSubmitting
--}}
<div x-show="galleyModalOpen" x-cloak class="fixed inset-0 z-[60] overflow-y-auto"
    role="dialog" aria-modal="true" aria-labelledby="galley-modal-title" @keydown.escape.window="galleyModalOpen = false">

    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        {{-- Backdrop --}}
        <div x-show="galleyModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm"
            @click="galleyModalOpen = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal Panel --}}
        <div x-show="galleyModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full max-w-3xl overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle ring-1 ring-black ring-opacity-5">

            {{-- Header (Explicit gradient styling) --}}
            <div class="relative px-6 py-5 bg-indigo-600 border-b border-indigo-500/30 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        {{-- Icon Container --}}
                        <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm shadow-inner border border-white/10">
                            <i class="text-xl text-white fa-solid fa-file-circle-plus"></i>
                        </div>
                        
                        {{-- Title & Subtitle --}}
                        <div>
                            <h3 id="galley-modal-title" class="text-xl font-bold text-white tracking-tight"
                                x-text="editingGalley ? 'Edit Galley' : 'Add Publication Galley'">
                                Add Publication Galley
                            </h3>
                            <p class="text-sm font-medium text-indigo-100/90">
                                Upload a file or link to an external source
                            </p>
                        </div>
                    </div>

                    {{-- Close Button --}}
                    <button @click="galleyModalOpen = false"
                        class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <i class="text-xl fa-solid fa-xmark"></i>
                        <span class="sr-only">Close</span>
                    </button>
                </div>

                {{-- Decorative pattern overlay (Opacity dikurangi sedikit supaya teks tetap terbaca) --}}
                <div class="absolute inset-0 opacity-10 pointer-events-none bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPgo8cmVjdCB3aWR0aD0iNCIgaGVpZ2h0PSI0IiBmaWxsPSIjZmZmIi8+CjxyZWN0IHdpZHRoPSIxIiBoZWlnaHQ9IjEiIGZpbGw9IiMwMDAiLz4KPC9zdmc+')]"></div>
            </div>

            {{-- Form Body --}}
            <div class="px-6 py-6 space-y-6 bg-white">

                {{-- Row 1: Label & Language --}}
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    {{-- Galley Label --}}
                    <div>
                        <label for="galley-label" class="block mb-2 text-sm font-semibold text-gray-800">
                            Galley Label <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="galley-label" x-model="galleyLabel" required
                            placeholder="e.g., PDF, HTML, EPUB"
                            class="block w-full text-base border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            :class="{ 'border-red-500': errors.label }">
                        <template x-if="errors.label">
                            <p class="mt-1 text-xs text-red-600" x-text="errors.label[0]">
                            </p>
                        </template>
                        <p class="mt-1.5 text-xs text-gray-500">Will be displayed as the download button label</p>
                    </div>

                    {{-- Language --}}
                    <div>
                        <label for="galley-locale" class="block mb-2 text-sm font-semibold text-gray-800">
                            Language
                        </label>
                        <select id="galley-locale" x-model="galleyLocale"
                            class="block w-full text-base border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="en">English</option>
                            <option value="id">Indonesian</option>
                            <option value="ar">Arabic</option>
                            <option value="fr">French</option>
                            <option value="de">German</option>
                            <option value="es">Spanish</option>
                            <option value="pt">Portuguese</option>
                            <option value="zh">Chinese</option>
                            <option value="ja">Japanese</option>
                            <option value="ko">Korean</option>
                            <option value="ru">Russian</option>
                        </select>
                    </div>
                </div>

                {{-- Row 2: URL Path --}}
                <div>
                    <label for="galley-url-path" class="block mb-2 text-sm font-semibold text-gray-800">
                        URL Path <span class="font-normal text-gray-400">(optional)</span>
                    </label>
                    <div class="flex items-center shadow-sm rounded-lg overflow-hidden group focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-1">
                        <span
                            class="inline-flex items-center h-[42px] px-3 text-sm text-gray-500 bg-gray-50 border border-r-0 border-gray-300 rounded-l-lg group-focus-within:border-indigo-500 group-focus-within:text-indigo-600 transition-colors">
                            /article/{{ $submission->slug }}/
                        </span>
                        <input type="text" id="galley-url-path" x-model="galleyUrlPath" placeholder="pdf"
                            class="flex-1 text-base border-gray-300 rounded-r-lg focus:ring-0 focus:border-indigo-500 group-focus-within:border-indigo-500"
                            :class="{ 'border-red-500': errors.url_path }">
                    </div>
                    <template x-if="errors.url_path">
                        <p class="mt-1 text-xs text-red-600" x-text="errors.url_path[0]">
                        </p>
                    </template>
                    <p class="mt-1.5 text-xs text-gray-500">Custom slug for SEO-friendly URLs. Only letters, numbers, dashes, and underscores.</p>
                </div>

                {{-- Divider --}}
                <div class="relative pt-2">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-start">
                        <span class="pr-3 text-sm font-semibold text-gray-800 bg-white">File Source</span>
                    </div>
                </div>

                {{-- Remote Toggle --}}
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" x-model="isRemote" id="is-remote-checkbox"
                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded cursor-pointer focus:ring-indigo-500 transition-all duration-150 ease-in-out">
                    </div>
                    <div class="ml-3">
                        <label for="is-remote-checkbox"
                            class="text-sm font-medium text-gray-900 cursor-pointer select-none hover:text-indigo-600 transition-colors">
                            This galley will be available at a separate website
                        </label>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Check this if the file is hosted externally (e.g., publisher's website, cloud storage)
                        </p>
                    </div>
                </div>

                {{-- Dynamic Content Area --}}
                <div class="min-h-[160px] transition-all duration-300">

                    {{-- Remote URL Input (if isRemote) --}}
                    <div x-show="isRemote"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        <label for="galley-remote-url" class="block mb-2 text-sm font-semibold text-gray-800">
                            Remote URL <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="text-gray-400 fa-solid fa-link"></i>
                            </div>
                            <input type="url" id="galley-remote-url" x-model="remoteUrl"
                                placeholder="https://example.com/article.pdf"
                                class="block w-full pl-10 text-base border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                :class="{ 'border-red-500': errors.url_remote }"
                                :required="isRemote">
                        </div>
                        <template x-if="errors.url_remote">
                            <p class="mt-1 text-xs text-red-600"
                                x-text="errors.url_remote[0]"></p>
                        </template>
                        <div class="flex items-start mt-2 space-x-2 text-xs text-blue-600 bg-blue-50 p-2.5 rounded-lg border border-blue-100">
                            <i class="mt-0.5 fa-solid fa-circle-info shrink-0"></i>
                            <p>Users will be redirected to this URL when they click the download button.</p>
                        </div>
                    </div>

                    {{-- File Upload (if NOT isRemote) --}}
                    <div x-show="!isRemote"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0">
                        <label class="block mb-2 text-sm font-semibold text-gray-800">
                            Upload File <span class="text-red-500"
                                x-show="!editingGalley">*</span>
                            <span class="font-normal text-gray-400"
                                x-show="editingGalley">(leave empty to keep current)</span>
                        </label>

                        {{-- Drop Zone --}}
                        <div class="relative group">
                            <label for="galley-file-input"
                                class="flex flex-col items-center justify-center w-full h-40 transition-all duration-200 border-2 border-dashed rounded-xl cursor-pointer"
                                :class="selectedFile ? 'border-emerald-400 bg-emerald-50/50 shadow-sm' :
                                    'border-gray-300 bg-gray-50 hover:border-indigo-400 hover:bg-indigo-50/50 hover:shadow-sm'">

                                <template x-if="!selectedFile">
                                    <div class="flex flex-col items-center justify-center py-6 text-center">
                                        <div class="flex items-center justify-center w-12 h-12 mb-3 transition-transform duration-300 bg-indigo-100 rounded-full group-hover:scale-110 group-hover:bg-indigo-200">
                                            <i class="text-xl text-indigo-600 fa-solid fa-cloud-arrow-up"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700">
                                            <span class="text-indigo-600 underline decoration-indigo-300 decoration-2 underline-offset-2 group-hover:decoration-indigo-500">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">PDF, HTML, EPUB, XML, DOC (Max 50MB)</p>
                                    </div>
                                </template>

                                <template x-if="selectedFile">
                                    <div class="flex items-center justify-center w-full h-full p-4">
                                        <div class="flex items-center w-full max-w-sm p-4 bg-white border border-emerald-200 rounded-lg shadow-sm">
                                            <div class="flex items-center justify-center w-10 h-10 mr-4 bg-emerald-100 rounded-full shrink-0">
                                                <i class="text-lg text-emerald-600 fa-solid fa-file-check"></i>
                                            </div>
                                            <div class="flex-1 min-w-0 text-left">
                                                <p class="text-sm font-medium text-gray-900 truncate"
                                                    x-text="selectedFileName"></p>
                                                <p class="text-xs text-emerald-600 font-medium cursor-pointer hover:underline group-hover:text-emerald-700">Click to change file</p>
                                            </div>
                                            <div class="ml-2 shrink-0">
                                                <i class="text-gray-300 fa-solid fa-pen-to-square"></i>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <input id="galley-file-input" type="file"
                                    class="hidden"
                                    accept=".pdf,.html,.htm,.epub,.xml,.doc,.docx"
                                    @change="handleGalleyFileSelect($event)">
                            </label>
                        </div>
                        <template x-if="errors.file">
                            <p class="mt-1 text-xs text-red-600" x-text="errors.file[0]">
                            </p>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-t border-gray-200">
                <p class="hidden text-xs text-gray-500 sm:block">
                    <i class="mr-1 fa-solid fa-info-circle"></i>
                    Galleys are the final published formats
                </p>
                <div class="flex items-center w-full gap-3 sm:w-auto">
                    <button type="button" @click="galleyModalOpen = false"
                        class="w-full px-5 py-2.5 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg sm:w-auto hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                    <button type="button" @click="submitGalley()"
                        :disabled="isSubmitting || (!isRemote && !selectedFile && !editingGalley)"
                        class="w-full inline-flex justify-center items-center px-5 py-2.5 text-sm font-medium text-white transition-all bg-indigo-600 border border-transparent rounded-lg sm:w-auto hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none hover:shadow-md">
                        <template x-if="isSubmitting">
                            <span class="flex items-center">
                                <i class="mr-2 fa-solid fa-circle-notch fa-spin"></i> Saving...
                            </span>
                        </template>
                        <template x-if="!isSubmitting">
                            <span class="flex items-center">
                                <i class="mr-2 fa-solid fa-check"></i>
                                <span x-text="editingGalley ? 'Update Galley' : 'Save Galley'"></span>
                            </span>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
