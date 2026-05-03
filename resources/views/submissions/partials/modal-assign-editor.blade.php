<div x-show="assignEditorModalOpen" x-cloak class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        {{-- Background overlay --}}
        <div x-show="assignEditorModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
            @click="assignEditorModalOpen = false"></div>

        {{-- Modal Panel --}}
        <div x-show="assignEditorModalOpen" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full max-h-[90vh] flex flex-col z-50">

            {{-- Header --}}
            <div
                class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-800 flex items-center" id="modal-title">
                    Assign Editor
                </h3>
                <button type="button" @click="assignEditorModalOpen = false"
                    class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-full hover:bg-gray-100">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>

            <div class="flex-1 overflow-hidden flex flex-col min-h-0">
                <form id="assignEditorForm"
                    action="{{ route('journal.workflow.assign-editor', ['journal' => $journal->slug, 'submission' => $submission->slug]) }}"
                    method="POST" class="flex flex-col h-full">
                    @csrf
                    <input type="hidden" name="user_id" :value="selectedEditor?.id">

                    {{-- Search & Filter --}}
                    <div class="p-4 border-b border-gray-100 flex-shrink-0 space-y-3">
                        {{-- Search Input --}}
                        <div class="relative">
                            <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" x-model="editorSearch" placeholder="Search by name or email..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                                autocomplete="off">
                        </div>

                        {{-- Role Filter --}}
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Filter Role:</span>
                            <select x-model="editorRoleFilter"
                                class="form-select flex-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">All Roles</option>
                                <option value="Editor">Editor</option>
                                <option value="Section Editor">Section Editor</option>
                                <option value="Journal Manager">Journal Manager</option>
                            </select>
                        </div>
                    </div>

                    {{-- Editor List --}}
                    <div class="flex-1 overflow-y-auto p-0 min-h-0">
                        @if ($potentialEditors->isEmpty())
                            <div class="p-8 text-center text-gray-500">
                                <i class="fa-solid fa-users-slash text-4xl mb-3 text-gray-300"></i>
                                <p>No eligible editors found.</p>
                            </div>
                        @else
                            {{-- Empty Search/Filter Results --}}
                            <div x-show="filteredEditors.length === 0" class="p-8 text-center text-gray-500"
                                style="display: none;">
                                <p>No editors match your search or filter.</p>
                            </div>

                            <template x-for="editor in filteredEditors" :key="editor.id">
                                <div @click="selectEditor(editor)"
                                    class="flex items-center px-6 py-4 cursor-pointer border-b border-gray-50 last:border-0 transition-colors"
                                    :class="selectedEditor?.id === editor.id ? 'bg-indigo-50/60' : 'hover:bg-gray-50'">

                                    {{-- Avatar --}}
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-sm flex-shrink-0 mr-4">
                                        <span x-text="editor.name.charAt(0).toUpperCase()"></span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 truncate" x-text="editor.name"></p>
                                        <p class="text-xs text-gray-500 truncate" x-text="editor.email"></p>
                                        <div class="mt-1 flex flex-wrap gap-1">
                                            {{-- Display Role Badges --}}
                                            <template x-if="editor.role_names">
                                                <template x-for="role in editor.role_names">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                                                        x-text="role"></span>
                                                </template>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Checkmark --}}
                                    <div class="w-6 h-6 rounded-full border flex items-center justify-center transition-all"
                                        :class="selectedEditor?.id === editor.id ? 'bg-indigo-600 border-indigo-600' :
                                            'border-gray-300 bg-white'">
                                        <i x-show="selectedEditor?.id === editor.id"
                                            class="fa-solid fa-check text-white text-xs"></i>
                                    </div>
                                </div>
                            </template>
                        @endif
                    </div>

                    {{-- Footer actions --}}
                    <div
                        class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3 rounded-b-xl flex-shrink-0">
                        <button type="button" @click="assignEditorModalOpen = false"
                            class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-200 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="!selectedEditor"
                            class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-sm hover:shadow">
                            Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
