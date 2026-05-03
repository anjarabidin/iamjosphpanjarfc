@props(['submission', 'stageId', 'stageName', 'discussions' => collect(), 'participants' => collect(), 'journal'])

@php
    $currentUser = auth()->user();
    $isEditor = $currentUser->hasJournalPermission([
        \App\Models\Role::LEVEL_EDITOR,
        \App\Models\Role::LEVEL_SECTION_EDITOR,
        \App\Models\Role::LEVEL_MANAGER,
        \App\Models\Role::LEVEL_ADMIN
    ], $journal->id);
    $stageDiscussions = $discussions->where('stage_id', $stageId);

    // Stage names for display
    $stageLabels = [
        1 => 'Pre-Review',
        2 => 'Review',
        3 => 'Copyediting',
        4 => 'Production',
    ];
    $stageLabel = $stageLabels[$stageId] ?? 'Stage ' . $stageId;
@endphp

<div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden" x-data="discussionPanel({
    stageId: {{ $stageId }},
    submissionId: '{{ $submission->id }}',
    journalSlug: '{{ $journal->slug }}',
    csrfToken: '{{ csrf_token() }}',
    currentUserId: '{{ $currentUser->id }}',
    uploadImageUrl: '{{ route('journal.discussion.upload-image', ['journal' => $journal->slug]) }}',
    uploadFileUrl: '{{ route('journal.discussion.upload-file', $journal->slug) }}',
    createUrl: '{{ route('journal.discussion.create', ['journal' => $journal->slug, 'submission' => $submission]) }}',
})">

    {{-- Panel Header --}}
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
        <h3 class="text-base font-bold text-gray-900">{{ $stageLabel }} Discussions</h3>
        <button @click="openAddModal()" type="button"
            class="inline-flex items-center text-sm text-indigo-600 font-medium hover:text-indigo-800 transition-colors">
            <i class="fa-solid fa-plus mr-1.5"></i>
            Add Discussion
        </button>
    </div>

    {{-- Discussion List --}}
    <div class="divide-y divide-gray-100">
        @forelse($stageDiscussions as $discussion)
            @php
                $isOpen = $discussion->is_open;
                $messageCount = $discussion->messages->count();
                $replyCount = $messageCount - 1;
                $discussionParticipants = $discussion->participants ?? collect();
                $unreadCount = $discussion->unreadMessagesCountForUser($currentUser->id);
                
                // Get last read timestamp for highlighting
                $userParticipantRecord = $discussion->participantRecords->where('user_id', $currentUser->id)->first();
                $lastReadAt = $userParticipantRecord ? $userParticipantRecord->last_read_at : null;
            @endphp

            <details class="group" x-data="discussionThread({
                discussionId: '{{ $discussion->id }}',
                isOpen: {{ $isOpen ? 'true' : 'false' }},
                replyUrl: '{{ route('journal.discussion.reply', ['journal' => $journal->slug, 'submission' => $submission, 'discussion' => $discussion->id]) }}',
                closeUrl: '{{ route('journal.discussion.close', ['journal' => $journal->slug, 'submission' => $submission, 'discussion' => $discussion->id]) }}',
                reopenUrl: '{{ route('journal.discussion.reopen', ['journal' => $journal->slug, 'submission' => $submission, 'discussion' => $discussion->id]) }}',
                uploadFileUrl: '{{ route('journal.discussion.upload-file', $journal->slug) }}',
                uploadImageUrl: '{{ route('journal.discussion.upload-image', ['journal' => $journal->slug]) }}',
                markAsReadUrl: '{{ route('journal.discussion.read', ['journal' => $journal->slug, 'submission' => $submission, 'discussion' => $discussion->id]) }}',
                csrfToken: '{{ csrf_token() }}',
            })" @toggle="if($el.open) markAsRead()">

                {{-- Summary Row --}}
                <summary
                    class="flex items-center justify-between px-6 py-4 cursor-pointer transition-colors {{ $unreadCount > 0 ? 'bg-indigo-50 font-semibold border-l-4 border-indigo-600' : 'bg-white font-normal hover:bg-gray-50' }}">
                    <div class="flex items-center gap-4">
                        <i
                            class="fa-regular fa-comments text-gray-400 group-open:text-indigo-500 transition-colors"></i>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-900 group-open:text-indigo-600">
                                    {{ $discussion->subject }}
                                </p>
                                @if (!$isOpen)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        <i class="fa-solid fa-lock text-[10px] mr-1"></i>
                                        Closed
                                    </span>
                                @endif
                                @if ($unreadCount > 0)
                                    <span class="unread-badge inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 ml-2">
                                        {{ $unreadCount }} New
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">
                                From {{ $discussion->user->name }} •
                                {{ $discussion->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        {{-- Participant Avatars --}}
                        <div class="flex -space-x-2">
                            @foreach ($discussionParticipants->take(3) as $participant)
                                <div class="w-6 h-6 rounded-full bg-indigo-100 border-2 border-white flex items-center justify-center text-indigo-600 text-[10px] font-bold"
                                    title="{{ $participant->name }}">
                                    {{ strtoupper(substr($participant->name, 0, 1)) }}
                                </div>
                            @endforeach
                            @if ($discussionParticipants->count() > 3)
                                <div
                                    class="w-6 h-6 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-gray-600 text-[10px] font-bold">
                                    +{{ $discussionParticipants->count() - 3 }}
                                </div>
                            @endif
                        </div>

                        {{-- Reply Count Badge --}}
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $replyCount > 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $replyCount }} {{ $replyCount === 1 ? 'reply' : 'replies' }}
                        </span>

                        <i
                            class="fa-solid fa-chevron-down text-gray-400 transform group-open:rotate-180 transition-transform"></i>
                    </div>
                </summary>

                {{-- Expanded Content --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{-- Participants Header --}}
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <span
                                class="text-xs font-medium text-gray-500 uppercase tracking-wider">Participants:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($discussionParticipants as $participant)
                                    @php
                                        $role = $participant->id === $submission->user_id ? 'Author' : 'Editor';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $role === 'Author' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $participant->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Close/Reopen Button for Editors --}}
                        @if ($isEditor)
                            <div>
                                @if ($isOpen)
                                    <form
                                        action="{{ route('journal.discussion.close', ['journal' => $journal->slug, 'submission' => $submission, 'discussion' => $discussion->id]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center text-xs text-gray-500 hover:text-red-600 font-medium transition-colors">
                                            <i class="fa-solid fa-lock mr-1"></i>
                                            Close Discussion
                                        </button>
                                    </form>
                                @else
                                    <form
                                        action="{{ route('journal.discussion.reopen', ['journal' => $journal->slug, 'submission' => $submission, 'discussion' => $discussion->id]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center text-xs text-gray-500 hover:text-green-600 font-medium transition-colors">
                                            <i class="fa-solid fa-lock-open mr-1"></i>
                                            Reopen
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Messages Thread --}}
                    <div class="space-y-4">
                        @foreach ($discussion->messages as $message)
                            @php
                                $isOwner = $message->user_id === $currentUser->id;
                                $canEdit = $isEditor || $isOwner;
                                $messageRole = $message->user_id === $submission->user_id ? 'Author' : 'Editor';
                                
                                // Highlight if not owner and (never read OR newer than last read)
                                $isNew = !$isOwner && (is_null($lastReadAt) || $message->created_at->gt($lastReadAt));
                            @endphp
                            <div class="flex gap-3" x-data="{ editing: false, editBody: '' }">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-sm font-bold">
                                        {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="{{ $isNew ? 'bg-indigo-50 border-indigo-200' : 'bg-white border-gray-200' }} p-4 rounded-lg shadow-sm border flex-1">
                                    {{-- Message Header --}}
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-sm font-semibold text-gray-900">{{ $message->user->name }}</span>
                                            <span
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $messageRole === 'Author' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ $messageRole }}
                                            </span>
                                            <span
                                                class="text-xs text-gray-400">{{ $message->created_at->format('M d, Y \a\t H:i') }}</span>
                                        </div>
                                        @if ($canEdit)
                                            <button @click="editing = true; editBody = `{!! addslashes(str_replace(["\r", "\n"], '', $message->body)) !!}`"
                                                x-show="!editing"
                                                class="text-gray-400 hover:text-indigo-600 transition-colors"
                                                title="Edit">
                                                <i class="fa-solid fa-pencil text-xs"></i>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Message Body (View Mode) --}}
                                    <div x-show="!editing" class="prose prose-sm text-gray-700 max-w-none">
                                        {!! $message->body !!}
                                    </div>

                                    {{-- Message Body (Edit Mode) --}}
                                    <div x-show="editing" x-cloak class="space-y-3">
                                        <form
                                            action="{{ route('journal.discussion.message.update', ['journal' => $journal->slug, 'submission' => $submission, 'discussion' => $discussion->id, 'message' => $message->id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <textarea name="body" x-model="editBody" rows="4"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                                            <div class="flex justify-end gap-2 mt-2">
                                                <button type="button" @click="editing = false"
                                                    class="px-3 py-1.5 text-xs text-gray-600 hover:text-gray-800">
                                                    Cancel
                                                </button>
                                                <button type="submit"
                                                    class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded hover:bg-indigo-700">
                                                    Save Changes
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    {{-- Attachments --}}
                                    @if ($message->files && $message->files->count() > 0)
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <p class="text-xs font-medium text-gray-500 mb-2">
                                                <i class="fa-solid fa-paperclip mr-1"></i>
                                                Attachments
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($message->files as $file)
                                                    <a href="{{ route('journal.discussion.file.download', ['journal' => $journal->slug, 'file' => $file->id]) }}"
                                                        class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-xs text-gray-700 transition-colors">
                                                        <i class="fa-regular fa-file mr-1.5"></i>
                                                        {{ Str::limit($file->original_name, 20) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Reply Form --}}
                    @if ($isOpen)
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div x-show="!replyExpanded">
                                <button @click="replyExpanded = true; $nextTick(() => initReplyEditor())" type="button"
                                    class="w-full text-left px-4 py-3 bg-white border border-gray-200 rounded-lg text-sm text-gray-500 hover:bg-gray-50 hover:border-gray-300 transition-all">
                                    <i class="fa-regular fa-comment-dots mr-2"></i>
                                    Write a reply...
                                </button>
                            </div>

                            <div x-show="replyExpanded" x-cloak
                                class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <form
                                    action="{{ route('journal.discussion.reply', ['journal' => $journal->slug, 'submission' => $submission, 'discussion' => $discussion->id]) }}"
                                    method="POST" class="space-y-4" @submit="submitting = true">
                                    @csrf

                                    {{-- Hidden fields for files --}}
                                    <template x-for="(file, index) in replyFiles" :key="file.id">
                                        <div>
                                            <input type="hidden" :name="'attached_files[' + index + '][id]'"
                                                :value="file.id">
                                            <input type="hidden" :name="'attached_files[' + index + '][name]'"
                                                :value="file.name">
                                        </div>
                                    </template>

                                    {{-- Rich Text Editor --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Reply</label>
                                        <textarea name="body" :id="'reply-editor-' + discussionId" class="hidden"></textarea>
                                    </div>

                                    {{-- File Attachments --}}
                                    <div class="border-t border-gray-100 pt-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-medium text-gray-700">Attachments</span>
                                            <label
                                                class="text-xs text-indigo-600 font-medium hover:underline cursor-pointer">
                                                <i class="fa-solid fa-paperclip mr-1"></i>
                                                Add File
                                                <input type="file" class="sr-only"
                                                    @change="uploadReplyFile($event)">
                                            </label>
                                        </div>
                                        <ul class="space-y-1">
                                            <template x-for="file in replyFiles" :key="file.id">
                                                <li
                                                    class="flex items-center justify-between py-1.5 px-2 bg-gray-50 rounded text-xs border border-gray-100">
                                                    <div class="flex items-center gap-2">
                                                        <i class="fa-regular fa-file text-gray-400"></i>
                                                        <span x-text="file.name"
                                                            class="text-gray-700 truncate max-w-[200px]"></span>
                                                    </div>
                                                    <button type="button"
                                                        @click="replyFiles = replyFiles.filter(f => f.id !== file.id)"
                                                        class="text-red-500 hover:text-red-700">
                                                        <i class="fa-solid fa-times"></i>
                                                    </button>
                                                </li>
                                            </template>
                                            <template x-if="replyFiles.length === 0">
                                                <li class="text-xs text-gray-400 italic py-1">No files attached.</li>
                                            </template>
                                        </ul>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" @click="replyExpanded = false; resetReplyForm()"
                                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                                            Cancel
                                        </button>
                                        <button type="submit" :disabled="submitting"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                                            <i class="fa-solid fa-paper-plane mr-2"></i>
                                            <span x-text="submitting ? 'Sending...' : 'Send Reply'"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        {{-- Closed Notice --}}
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="bg-gray-100 border border-gray-200 rounded-lg p-4 text-center">
                                <p class="text-sm text-gray-500">
                                    <i class="fa-solid fa-lock mr-2"></i>
                                    This discussion is closed.
                                    @if ($discussion->closed_at)
                                        Closed {{ $discussion->closed_at->diffForHumans() }}.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </details>
        @empty
            <div class="px-6 py-10 text-center">
                <i class="fa-regular fa-comments text-gray-300 text-4xl mb-3"></i>
                <p class="text-sm text-gray-500">No discussions in this stage yet.</p>
                <button @click="openAddModal()" type="button"
                    class="mt-3 inline-flex items-center text-sm text-indigo-600 font-medium hover:text-indigo-800">
                    <i class="fa-solid fa-plus mr-1.5"></i>
                    Start a discussion
                </button>
            </div>
        @endforelse
    </div>

    {{-- ==================== ADD DISCUSSION MODAL ==================== --}}
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="add-discussion-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 transition-opacity"
                @click="showAddModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showAddModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900" id="add-discussion-title">
                            <i class="fa-regular fa-comments text-indigo-500 mr-2"></i>
                            Add {{ $stageLabel }} Discussion
                        </h3>
                        <button @click="showAddModal = false" type="button"
                            class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <form :action="createUrl" method="POST" class="p-6 space-y-5" @submit="submittingNew = true">
                    @csrf
                    <input type="hidden" name="stage_id" value="{{ $stageId }}">

                    {{-- Hidden inputs for attached files --}}
                    <template x-for="(file, index) in newDiscussionFiles" :key="file.id">
                        <div>
                            <input type="hidden" :name="'attached_files[' + index + '][id]'" :value="file.id">
                            <input type="hidden" :name="'attached_files[' + index + '][name]'"
                                :value="file.name">
                        </div>
                    </template>

                    {{-- Subject --}}
                    <div>
                        <label for="subject-{{ $stageId }}"
                            class="block text-sm font-medium text-gray-700 mb-1">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="subject" id="subject-{{ $stageId }}" required
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Brief description of the discussion topic">
                    </div>

                    {{-- Participants Selection --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Participants <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500 ml-1">(Select who should be part of this
                                discussion)</span>
                        </label>
                        <div
                            class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                            {{-- Current User (Always included, grayed out) --}}
                            <label
                                class="flex items-center gap-3 p-2 rounded-lg bg-indigo-50 border border-indigo-200">
                                <input type="checkbox" name="participants[]" value="{{ $currentUser->id }}" checked
                                    disabled
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded cursor-not-allowed">
                                <input type="hidden" name="participants[]" value="{{ $currentUser->id }}">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <div
                                        class="w-8 h-8 rounded-full bg-indigo-200 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span
                                            class="text-sm font-medium text-gray-900 block truncate">{{ $currentUser->name }}
                                            (You)</span>
                                        <span
                                            class="text-xs text-gray-500 block truncate">{{ $currentUser->email }}</span>
                                    </div>
                                </div>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                    Creator
                                </span>
                            </label>

                            {{-- Other Participants --}}
                            @foreach ($participants->reject(fn($p) => $p->id === $currentUser->id) as $participant)
                                @php
                                    $role = $participant->id === $submission->user_id ? 'Author' : 'Editor';
                                    $isOtherParty =
                                        ($currentUser->id === $submission->user_id && $role === 'Editor') ||
                                        ($currentUser->id !== $submission->user_id && $role === 'Author');
                                @endphp
                                <label
                                    class="flex items-center gap-3 p-2 rounded-lg hover:bg-white cursor-pointer transition-colors">
                                    <input type="checkbox" name="participants[]" value="{{ $participant->id }}"
                                        {{ $isOtherParty ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                        <div
                                            class="w-8 h-8 rounded-full {{ $role === 'Author' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($participant->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <span
                                                class="text-sm font-medium text-gray-900 block truncate">{{ $participant->name }}</span>
                                            <span
                                                class="text-xs text-gray-500 block truncate">{{ $participant->email }}</span>
                                        </div>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $role === 'Author' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $role }}
                                    </span>
                                </label>
                            @endforeach

                            @if ($participants->reject(fn($p) => $p->id === $currentUser->id)->isEmpty())
                                <p class="text-sm text-gray-500 italic text-center py-2">No other participants
                                    available.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Message --}}
                    <div>
                        <label for="new-discussion-editor-{{ $stageId }}"
                            class="block text-sm font-medium text-gray-700 mb-1">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <textarea name="body" id="new-discussion-editor-{{ $stageId }}"></textarea>
                        </div>
                    </div>

                    {{-- File Attachments --}}
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-gray-900">Attachments</h4>
                            <label
                                class="inline-flex items-center text-sm text-indigo-600 font-medium hover:text-indigo-800 cursor-pointer">
                                <i class="fa-solid fa-paperclip mr-1.5"></i>
                                Attach File
                                <input type="file" class="sr-only" @change="uploadNewDiscussionFile($event)">
                            </label>
                        </div>
                        <ul class="space-y-2">
                            <template x-for="file in newDiscussionFiles" :key="file.id">
                                <li
                                    class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-regular fa-file text-gray-400"></i>
                                        <span class="text-sm text-gray-700" x-text="file.name"></span>
                                        <span class="text-xs text-gray-500"
                                            x-text="(file.size / 1024).toFixed(0) + ' KB'"></span>
                                    </div>
                                    <button type="button" class="text-red-500 hover:text-red-700 text-sm"
                                        @click="newDiscussionFiles = newDiscussionFiles.filter(f => f.id !== file.id)">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </li>
                            </template>
                            <template x-if="newDiscussionFiles.length === 0">
                                <li class="text-sm text-gray-500 italic py-2">No files attached.</li>
                            </template>
                        </ul>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" @click="showAddModal = false"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" :disabled="submittingNew"
                            class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                            <i class="fa-solid fa-paper-plane mr-2"></i>
                            <span x-text="submittingNew ? 'Creating...' : 'Create Discussion'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Alpine.js Component Logic --}}
<script>
    document.addEventListener('alpine:init', () => {
        // Main Discussion Panel
        Alpine.data('discussionPanel', (config) => ({
            showAddModal: false,
            newDiscussionFiles: [],
            newEditorInstance: null,
            submittingNew: false,

            ...config,

            openAddModal() {
                this.showAddModal = true;
                this.newDiscussionFiles = [];
                this.$nextTick(() => this.initNewDiscussionEditor());
            },

            initNewDiscussionEditor() {
                const editorEl = document.querySelector(`#new-discussion-editor-${this.stageId}`);
                if (!editorEl || this.newEditorInstance) return;

                ClassicEditor
                    .create(editorEl, {
                        simpleUpload: {
                            uploadUrl: this.uploadImageUrl,
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken
                            }
                        }
                    })
                    .then(editor => {
                        this.newEditorInstance = editor;
                    })
                    .catch(err => console.error(err));
            },

            async uploadNewDiscussionFile(event) {
                const file = event.target.files[0];
                if (!file) return;

                let formData = new FormData();
                formData.append('file', file);

                try {
                    const res = await fetch(this.uploadFileUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: formData
                    });
                    const data = await res.json();
                    this.newDiscussionFiles.push(data);
                } catch (err) {
                    alert('Upload failed');
                }
                event.target.value = '';
            }
        }));

        // Discussion Thread (Reply Area)
        Alpine.data('discussionThread', (config) => ({
            replyExpanded: false,
            replyFiles: [],
            replyEditorInstance: null,
            submitting: false,

            ...config,

            initReplyEditor() {
                const editorEl = document.querySelector(`#reply-editor-${this.discussionId}`);
                if (!editorEl || this.replyEditorInstance) return;

                ClassicEditor
                    .create(editorEl, {
                        simpleUpload: {
                            uploadUrl: this.uploadImageUrl,
                            headers: {
                                'X-CSRF-TOKEN': this.csrfToken
                            }
                        }
                    })
                    .then(editor => {
                        this.replyEditorInstance = editor;
                    })
                    .catch(err => console.error(err));
            },

            resetReplyForm() {
                this.replyFiles = [];
                if (this.replyEditorInstance) {
                    this.replyEditorInstance.setData('');
                }
            },

            async uploadReplyFile(event) {
                const file = event.target.files[0];
                if (!file) return;

                let formData = new FormData();
                formData.append('file', file);

                try {
                    const res = await fetch(this.uploadFileUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: formData
                    });
                    const data = await res.json();
                    this.replyFiles.push(data);
                } catch (err) {
                    alert('Upload failed');
                }
                event.target.value = '';
            },

            markAsRead() {
                // Optimistic UI update can happen here if needed, 
                // but usually we rely on the expansion to trigger the call.
                // We don't necessarily need to reload the page or change UI state immediately 
                // unless we want to remove the 'Unread' badge live.
                
                fetch(this.markAsReadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        // Optional: trigger an event or update local state to remove badge
                        const summaryEl = this.$el.querySelector('summary');
                        if(summaryEl) {
                           summaryEl.classList.remove('bg-indigo-50', 'font-semibold', 'border-l-4', 'border-indigo-600');
                           summaryEl.classList.add('bg-white', 'font-normal');
                           const badge = summaryEl.querySelector('.unread-badge');
                           if(badge) badge.remove();
                        }
                    }
                });
            }
        }));
    });
</script>
