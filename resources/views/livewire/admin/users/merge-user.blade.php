<div>
    {{-- Merge User Modal --}}
    @if($showModal)
    <div x-data="{ show: @entangle('showModal').live }" 
         x-show="show" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        
        {{-- Background Overlay --}}
        <div x-show="show" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="$wire.closeModal()">
        </div>

        {{-- Modal Panel --}}
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="show" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                {{-- Header --}}
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-code-merge text-white text-lg"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white" id="modal-title">
                                Merge Users
                            </h3>
                        </div>
                        <button type="button" 
                                wire:click="closeModal"
                                class="text-white/80 hover:text-white transition-colors">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="px-6 py-6 space-y-6">
                    
                    {{-- Warning Banner --}}
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800">
                                    ⚠️ Warning: This action is PERMANENT and IRREVERSIBLE
                                </h3>
                                @if($sourceUser)
                                <div class="mt-2 text-sm text-red-700 space-y-1">
                                    <p>
                                        <strong class="font-semibold">{{ $sourceUser->name }}</strong> 
                                        <span class="text-xs">({{ $sourceUser->email }})</span>
                                        will be <strong class="font-bold">DELETED</strong>.
                                    </p>
                                    <p class="mt-2">All their records will be transferred:</p>
                                    <ul class="list-disc list-inside ml-4 space-y-1 text-xs">
                                        <li>Submissions & Co-authorships</li>
                                        <li>Review Assignments</li>
                                        <li>Editorial Decisions & Assignments</li>
                                        <li>Discussion Messages</li>
                                        <li>File Uploads</li>
                                        <li>Journal Roles & Permissions</li>
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Source User Display --}}
                    @if($sourceUser)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Merge This User (Will Be Deleted)
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center text-red-700 font-bold">
                                {{ strtoupper(substr($sourceUser->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $sourceUser->name }}</p>
                                <p class="text-xs text-gray-500">{{ $sourceUser->email }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Target User Selection --}}
                    <div>
                        <label for="targetUserId" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-arrow-down mr-1 text-indigo-600"></i>
                            Merge Into This User (Target - Will Keep All Data)
                        </label>
                        <select id="targetUserId" 
                                wire:model="targetUserId"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="">-- Select Target User --</option>
                            @foreach($potentialTargets as $target)
                                <option value="{{ $target->id }}">
                                    {{ $target->name }} ({{ $target->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('targetUserId')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirmation Input --}}
                    <div>
                        <label for="confirmationText" class="block text-sm font-semibold text-gray-700 mb-2">
                            Type <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded font-mono text-xs">MERGE</span> to confirm:
                        </label>
                        <input type="text" 
                               id="confirmationText"
                               wire:model="confirmationText"
                               placeholder="Type MERGE here"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm font-mono uppercase"
                               autocomplete="off">
                        @error('confirmationText')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-200">
                    <button type="button" 
                            wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa-solid fa-times mr-1.5"></i>
                        Cancel
                    </button>
                    <button type="button" 
                            wire:click="executeMerge"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="px-5 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                        <i class="fa-solid fa-code-merge mr-1.5" wire:loading.remove></i>
                        <i class="fa-solid fa-spinner fa-spin mr-1.5" wire:loading></i>
                        <span wire:loading.remove>Merge Users</span>
                        <span wire:loading>Merging...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
