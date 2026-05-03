@php
    $stageNames = [
        1 => 'Pre-Review',
        2 => 'Review',
        3 => 'Copyediting',
        4 => 'Production',
    ];
@endphp

<div class="mt-8 pt-6 border-t border-gray-200">
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-sm font-medium text-gray-700">
            <i class="fa-regular fa-comments mr-2"></i>
            {{ $stageNames[$stageId] ?? 'Stage' }} Discussions
        </h4>
        <button type="button" class="text-sm text-indigo-600 hover:text-indigo-800">
            <i class="fa-solid fa-plus mr-1"></i> Add Discussion
        </button>
    </div>

    @if ($stageDiscussions->count() > 0)
        <div class="space-y-3">
            @foreach ($stageDiscussions as $discussion)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span
                                    class="text-indigo-600 font-medium text-sm">{{ substr($discussion->user?->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $discussion->subject }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $discussion->user?->name ?? 'Unknown' }}
                                    • {{ $discussion->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $discussion->is_open ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $discussion->is_open ? 'Open' : 'Closed' }}
                        </span>
                    </div>

                    @if ($discussion->messages->count() > 0)
                        <div class="mt-3 pl-11 space-y-2">
                            @foreach ($discussion->messages->take(2) as $message)
                                <div class="text-sm text-gray-600 bg-white rounded p-2 border border-gray-100">
                                    <span
                                        class="font-medium text-gray-900 text-xs">{{ $message->user?->name ?? 'Unknown' }}:</span>
                                    {{ Str::limit(strip_tags($message->body), 150) }}
                                </div>
                            @endforeach
                            @if ($discussion->messages->count() > 2)
                                <button type="button" class="text-xs text-indigo-600 hover:text-indigo-800">
                                    View all {{ $discussion->messages->count() }} messages
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-gray-50 rounded-lg p-6 text-center">
            <i class="fa-regular fa-comments text-3xl text-gray-300 mb-2"></i>
            <p class="text-sm text-gray-500">No discussions for this stage yet.</p>
        </div>
    @endif
</div>
