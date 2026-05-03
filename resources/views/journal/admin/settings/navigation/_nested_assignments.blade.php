@php
    /**
     * Recursive partial for rendering nested menu assignments
     * @param \Illuminate\Support\Collection $assignments
     * @param int $level
     */
@endphp

@foreach($assignments as $assignment)
<li class="menu-item bg-white border border-slate-200 p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 cursor-move group {{ $level > 0 ? 'ml-6 border-l-4 border-l-indigo-200' : '' }}"
    :class="{ 'opacity-50 scale-95': draggedItem === '{{ $assignment->id }}', 'ring-2 ring-indigo-400 ring-opacity-50': draggedOverItem === '{{ $assignment->id }}' }"
    draggable="true"
    data-item-id="{{ $assignment->id }}"
    data-item-type="assigned"
    data-parent-id="{{ $assignment->parent_id }}"
    @dragstart="handleDragStart($event, '{{ $assignment->id }}', 'assigned')"
    @dragend="handleDragEnd($event)">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3 flex-1">
            <div class="drag-handle cursor-grab active:cursor-grabbing p-1 text-slate-400 group-hover:text-slate-600 transition-colors">
                <i class="fa-solid fa-grip-vertical"></i>
            </div>

            @if($level > 0)
                <div class="text-indigo-500">
                    <i class="fa-solid fa-level-up-alt fa-rotate-90 text-xs"></i>
                </div>
            @endif

            @if($assignment->item?->icon)
                <i class="{{ $assignment->item->icon }} text-slate-500 text-sm"></i>
            @endif

            <div class="flex-1">
                <span class="text-sm font-medium text-slate-700 block">{{ $assignment->item?->title ?? 'Unknown' }}</span>
                <span class="text-xs text-slate-400">
                    {{ ucfirst($assignment->item?->type ?? 'custom') }}
                    @if($level > 0)
                        <span class="text-indigo-600 font-medium">(submenu)</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($assignment->children->count() > 0)
                <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full font-medium">
                    {{ $assignment->children->count() }} sub
                </span>
            @endif

            <button @click="unassignItem('{{ $assignment->id }}')"
                class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors opacity-0 group-hover:opacity-100">
                <i class="fa-solid fa-times text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Render children recursively --}}
    @if($assignment->children->count() > 0)
        <ul class="mt-3 space-y-2">
            @include('journal.admin.settings.navigation._nested_assignments', ['assignments' => $assignment->children, 'level' => $level + 1])
        </ul>
    @endif
</li>
@endforeach