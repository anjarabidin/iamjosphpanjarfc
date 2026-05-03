{{-- History Tab Modal — with dedicated Action/File column, icons, stage badges, and sort --}}
<div x-data="{ currentTab: 'history', sortAsc: false }">

    {{-- Modal Header --}}
    <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50 rounded-t-lg">
        <h3 class="font-bold text-lg text-slate-800 truncate pr-4">{{ $submission->title }}</h3>
        <button type="button" onclick="closeLogModal()" class="text-slate-400 hover:text-red-500 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Tabs --}}
    <div class="px-6 pt-4 border-b border-slate-200 flex gap-6">
        <button @click="currentTab = 'history'"
                :class="currentTab === 'history' ? 'border-primary-600 text-primary-700' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="pb-3 text-sm font-bold border-b-2 transition">
            <i class="fa-solid fa-clock-rotate-left mr-1.5 text-xs"></i> Activity History
        </button>
        @if(auth()->user()->hasJournalPermission([1, 2], $submission->journal->id))
        <button @click="currentTab = 'notes'"
                :class="currentTab === 'notes' ? 'border-primary-600 text-primary-700' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="pb-3 text-sm font-bold border-b-2 transition">
            <i class="fa-solid fa-note-sticky mr-1.5 text-xs"></i> Internal Notes
        </button>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-6 bg-white max-h-[65vh] overflow-y-auto">
        <!-- History Tab Content (Existing) -->
        <div x-show="currentTab === 'history'">

            @php
                // Eager-load user + file to prevent N+1 queries
                $logs = $submission->activityLogs()->with(['user', 'files'])->get();
            @endphp

            @if($logs->isEmpty())
                <div class="text-center py-16 text-slate-400">
                    <i class="fa-solid fa-clock-rotate-left text-3xl mb-3 opacity-30"></i>
                    <p class="italic text-sm">No activity recorded yet.</p>
                </div>
            @else
                {{-- Sort toggle --}}
                <div class="flex justify-end mb-3">
                    <button @click="sortAsc = !sortAsc"
                            class="flex items-center gap-1.5 text-xs text-slate-500 hover:text-primary-600 transition font-medium select-none">
                        <i class="fa-solid fa-arrow-up-down text-[10px]"></i>
                        <span x-text="sortAsc ? 'Oldest First' : 'Newest First'"></span>
                    </button>
                </div>

                @php
                    $journalSlug = $submission->journal->slug;
                @endphp
                {{-- Table --}}
                <div x-data="{
                        rows: @js($logs->map(fn($l) => [
                            'id'          => $l->id,
                            'ts'          => $l->created_at->timestamp,
                            'date'        => $l->created_at->format('d M Y'),
                            'time'        => $l->created_at->format('H:i'),
                            'user'        => $l->user->name ?? 'System',
                            'icon'        => $l->icon,
                            'color'       => $l->color,
                            'title'       => $l->title,
                            'description' => $l->description ?? '',
                            'stage'       => $l->stage_label ?? '',
                            'metadata'    => $l->metadata ?? [],
                            'files'       => $l->files->map(fn($f) => [
                                'name' => $f->file_name,
                                'url'  => isset($f->metadata['copied_from_discussion']) 
                                            ? route('journal.discussion.file.download', ['journal' => $journalSlug, 'file' => $f->metadata['copied_from_discussion']])
                                            : route('files.download', ['file' => $f->id])
                            ])->values()
                        ])->values()),
                        get sorted() {
                            return [...this.rows].sort((a,b) => sortAsc ? a.ts - b.ts : b.ts - a.ts);
                        }
                    }">
                    <table class="w-full text-sm text-left border border-slate-200 rounded-lg overflow-hidden">
                        <thead class="text-[11px] text-slate-500 uppercase bg-slate-100 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-2.5 w-32">Date</th>
                                <th class="px-4 py-2.5 w-40">User</th>
                                <th class="px-4 py-2.5">Event</th>
                            </tr>
                        </thead>
                        <template x-for="row in sorted" :key="row.id">
                            <tbody x-data="{ open: false }" class="bg-white hover:bg-slate-50 transition border-b border-slate-100 last:border-b-0">
                                
                                {{-- Main Row --}}
                                <tr :class="{'cursor-pointer': row.files.length > 0}" @click="if(row.files.length > 0) open = !open">
                                    {{-- Date --}}
                                    <td class="px-4 py-3 text-slate-500 align-top relative">
                                        <div class="flex items-start gap-1.5">
                                            <template x-if="row.files.length > 0">
                                                <button type="button" class="mt-0.5 text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none">
                                                    <i class="fa-solid fa-chevron-right text-[10px] transition-transform duration-200"
                                                       :class="open ? 'rotate-90' : ''"></i>
                                                </button>
                                            </template>
                                            <template x-if="row.files.length === 0">
                                                <div class="w-3 shrink-0"></div> <!-- Alignment placeholder -->
                                            </template>
                                            <div>
                                                <div class="font-medium text-slate-700 text-xs whitespace-nowrap" x-text="row.date"></div>
                                                <div class="text-[10px] text-slate-400 whitespace-nowrap" x-text="row.time"></div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- User --}}
                                    <td class="px-4 py-3 align-top">
                                        <span class="font-medium text-slate-700 text-xs" x-text="row.user"></span>
                                    </td>

                                    {{-- Event --}}
                                    <td class="px-4 py-3 align-top pr-4">
                                        <div class="flex items-start gap-2.5">
                                            {{-- Color-coded icon --}}
                                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[10px] mt-0.5"
                                                  :class="{
                                                    'bg-indigo-100 text-indigo-600' : row.color === 'indigo',
                                                    'bg-purple-100 text-purple-600' : row.color === 'purple',
                                                    'bg-blue-100 text-blue-600'     : row.color === 'blue',
                                                    'bg-emerald-100 text-emerald-600': row.color === 'emerald',
                                                    'bg-amber-100 text-amber-600'   : row.color === 'amber',
                                                    'bg-sky-100 text-sky-600'       : row.color === 'sky',
                                                    'bg-teal-100 text-teal-600'     : row.color === 'teal',
                                                    'bg-orange-100 text-orange-600' : row.color === 'orange',
                                                    'bg-green-100 text-green-600'   : row.color === 'green',
                                                    'bg-gray-100 text-gray-500'     : row.color === 'gray' || !row.color,
                                                  }">
                                                <i class="fa-solid" :class="row.icon"></i>
                                            </span>
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <span class="font-semibold text-slate-800 text-xs leading-tight" x-text="row.title"></span>
                                                    {{-- Stage badge --}}
                                                    <template x-if="row.stage">
                                                        <span class="inline-block text-[9px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded bg-slate-200 text-slate-500"
                                                              x-text="row.stage"></span>
                                                    </template>
                                                </div>
                                                <p class="text-[11px] text-slate-500 mt-1" x-text="row.description"></p>
                                                
                                                {{-- Expand Hint --}}
                                                <template x-if="row.files.length > 0 && !open">
                                                    <p class="text-[10px] text-indigo-600 font-medium mt-1.5 flex items-center gap-1">
                                                        <i class="fa-solid fa-paperclip"></i>
                                                        <span x-text="row.files.length + ' Attached File' + (row.files.length > 1 ? 's' : '')"></span>
                                                    </p>
                                                </template>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Expanded Details Row --}}
                                <tr x-show="open" x-collapse>
                                    <td colspan="3" class="px-4 py-4 bg-slate-50/50 border-t border-slate-100/60 shadow-inner">
                                        <div class="pl-[4.5rem]"> {{-- Align with event description --}}
                                            
                                            {{-- Diff Viewer (if metadata has diff info) --}}
                                            <template x-if="row.metadata && row.metadata.diff">
                                                <div class="mb-4 text-xs bg-white border border-slate-200 rounded-lg p-3 shadow-sm">
                                                    <h4 class="font-bold text-slate-700 mb-2 border-b border-slate-100 pb-1">Changes Summary</h4>
                                                    <div class="space-y-1 text-slate-600" x-html="row.metadata.diff"></div>
                                                </div>
                                            </template>

                                            {{-- Files List --}}
                                            <template x-if="row.files.length > 0">
                                                <div class="space-y-2">
                                                    <template x-for="file in row.files">
                                                        <div class="flex items-center justify-between bg-white border border-slate-200 px-3 py-2 rounded-lg shadow-sm hover:border-indigo-300 transition-colors">
                                                            <div class="flex items-center gap-2 overflow-hidden">
                                                                <div class="w-8 h-8 rounded-md bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                                                    <i class="fa-regular fa-file-pdf text-indigo-500 text-sm"></i>
                                                                </div>
                                                                <span class="text-xs font-medium text-slate-700 truncate" x-text="file.name" :title="file.name"></span>
                                                            </div>
                                                            <a :href="file.url"
                                                               target="_blank"
                                                               class="flex-shrink-0 ml-4 inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white rounded-md text-[11px] font-bold transition-colors">
                                                                <i class="fa-solid fa-download"></i>
                                                                Download
                                                            </a>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>

                                        </div>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </template>
                    </table>
                </div>
            @endif

        </div>

        @if(auth()->user()->hasJournalPermission([1, 2], $submission->journal->id))
        <!-- Notes Tab Content -->
        <div x-show="currentTab === 'notes'" style="display: none;">
            <div class="space-y-4 mb-6">
                @forelse($submission->notes as $note)
                    <div class="border border-slate-200 rounded-lg p-4 relative group hover:border-slate-300 transition">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-bold text-sm text-slate-800">{{ $note->user->name ?? 'Unknown User' }}</h4>
                                <p class="text-[11px] text-slate-500">{{ $note->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            @if(auth()->id() === $note->user_id || auth()->user()->hasJournalPermission([1], $submission->journal->id))
                                <form action="{{ route('submission.notes.destroy', ['journal' => $submission->journal->slug, 'submission' => $submission, 'note' => $note]) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this note?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600 transition p-1 opacity-0 group-hover:opacity-100 focus:opacity-100" title="Delete Note">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="text-sm text-slate-700 whitespace-pre-wrap">{{ $note->note }}</div>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-400 bg-slate-50 border border-dashed border-slate-200 rounded-lg">
                        <i class="fa-regular fa-note-sticky text-3xl mb-3 opacity-40"></i>
                        <p class="text-sm">There are no internal notes for this submission yet.</p>
                    </div>
                @endforelse
            </div>

            <!-- Add Note Form -->
            <div class="mt-6 pt-6 border-t border-slate-200">
                <form action="{{ route('submission.notes.store', ['journal' => $submission->journal->slug, 'submission' => $submission]) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="note" class="block text-sm font-semibold text-slate-700 mb-2">Add Note</label>
                        <textarea name="note" id="note" rows="3" required
                                  class="w-full px-3 py-2 text-sm text-slate-700 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Type your internal editorial note here..."></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                            <i class="fa-solid fa-plus mr-1.5"></i> Add Note
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>