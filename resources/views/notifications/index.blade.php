@php
    $pageTitle = 'All Notifications';
@endphp

<x-app-layout>
    <x-slot name="title">{{ $pageTitle }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $pageTitle }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    You have <span class="font-semibold text-indigo-600">{{ $unreadCount }}</span> unread notifications
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center gap-3">
                @if ($unreadCount > 0)
                    <button onclick="markAllAsRead()"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fa-solid fa-check-double mr-2 text-gray-400"></i>
                        Mark All as Read
                    </button>
                @endif
                <button onclick="clearReadNotifications()"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                    <i class="fa-solid fa-trash-can mr-2"></i>
                    Clear Read
                </button>
            </div>
        </div>
    </x-slot>

    {{-- Notification List --}}
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @forelse($notifications as $notification)
                @php
                    $notifData = $notification->data;
                    $type = $notifData['type'] ?? 'general';
                    $message = $notifData['message'] ?? 'You have a new notification.';
                    $url = $notifData['url'] ?? '#';
                    $isUnread = !$notification->read_at;

                    // Icon mapping
                    $iconMap = [
                        'review' => 'fa-book-open',
                        'submission' => 'fa-file-alt',
                        'discussion' => 'fa-comments',
                        'decision' => 'fa-gavel',
                        'general' => 'fa-bell',
                    ];
                    $icon = $iconMap[$type] ?? 'fa-bell';

                    // Color mapping
                    $colorMap = [
                        'review' => 'text-blue-500 bg-blue-100',
                        'submission' => 'text-green-500 bg-green-100',
                        'discussion' => 'text-purple-500 bg-purple-100',
                        'decision' => 'text-amber-500 bg-amber-100',
                        'general' => 'text-gray-500 bg-gray-100',
                    ];
                    $color = $colorMap[$type] ?? 'text-gray-500 bg-gray-100';
                @endphp

                <div
                    class="flex items-start gap-4 p-4 border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors group {{ $isUnread ? 'bg-indigo-50/30' : '' }}">
                    {{-- Icon --}}
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $color }}">
                            <i class="fa-solid {{ $icon }}"></i>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <a href="{{ $url }}"
                            onclick="markAsReadAndGo(event, '{{ $notification->id }}', '{{ $url }}')"
                            class="block">
                            <p class="text-sm {{ $isUnread ? 'font-semibold text-gray-900' : 'text-gray-600' }}">
                                {{ $message }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fa-regular fa-clock mr-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                                @if ($notification->read_at)
                                    <span class="mx-1">•</span>
                                    <span class="text-green-500">
                                        <i class="fa-solid fa-check mr-1"></i>Read
                                    </span>
                                @endif
                            </p>
                        </a>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        @if ($isUnread)
                            <button onclick="markNotificationAsRead('{{ $notification->id }}')"
                                class="p-2 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                title="Mark as read">
                                <i class="fa-solid fa-check text-sm"></i>
                            </button>
                        @endif
                        <button onclick="deleteNotification('{{ $notification->id }}')"
                            class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                            title="Delete">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>

                    {{-- Unread Indicator --}}
                    @if ($isUnread)
                        <div class="flex-shrink-0 self-center">
                            <span class="w-2.5 h-2.5 bg-indigo-500 rounded-full block"></span>
                        </div>
                    @endif
                </div>
            @empty
                {{-- Empty State --}}
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-bell-slash text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications yet</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">
                        When you receive notifications about your submissions, reviews, or discussions, they'll appear
                        here.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function markNotificationAsRead(id) {
                fetch(`/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    }).then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }

            function markAsReadAndGo(event, id, url) {
                event.preventDefault();
                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                }).then(() => {
                    window.location.href = url;
                });
            }

            function markAllAsRead() {
                fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    }).then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }

            function deleteNotification(id) {
                if (!confirm('Delete this notification?')) return;

                fetch(`/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    }).then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }

            function clearReadNotifications() {
                if (!confirm('Clear all read notifications?')) return;

                fetch('/notifications/clear-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    }).then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }
        </script>
    @endpush
</x-app-layout>
