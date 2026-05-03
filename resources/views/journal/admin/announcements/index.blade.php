@php
    $journal = current_journal();
    $journalSlug = $journal->slug;
@endphp

<x-app-layout :journal="$journal" :journalSlug="$journalSlug">
    <x-slot name="title">Announcements - {{ $journal->name }}</x-slot>

    <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8" x-data="announcementManager()">
        {{-- Page Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <nav class="text-sm text-gray-500 mb-2">
                    <a href="{{ route('journal.dashboard', $journalSlug) }}" class="hover:text-indigo-600">Dashboard</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700">Announcements</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">Announcements</h1>
                <p class="text-gray-500 mt-1">Manage news and announcements for your journal homepage</p>
            </div>
            <button @click="openCreateModal()"
                class="inline-flex items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i>
                Create New
            </button>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-green-800">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        {{-- Announcements Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if ($announcements->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-bullhorn text-2xl text-indigo-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No announcements yet</h3>
                    <p class="text-gray-500 mb-6">Create your first announcement to display on the journal homepage.</p>
                    <button @click="openCreateModal()"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Create Announcement
                    </button>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Title</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date Posted</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Expires</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($announcements as $announcement)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if ($announcement->is_urgent)
                                            <span
                                                class="flex-shrink-0 w-2 h-2 bg-red-500 rounded-full mr-3 animate-pulse"></span>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $announcement->title }}
                                            </div>
                                            @if ($announcement->excerpt)
                                                <div class="text-sm text-gray-500 truncate max-w-md">
                                                    {{ Str::limit($announcement->excerpt, 60) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $announcement->published_at?->format('M d, Y') ?? 'Not set' }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $announcement->published_at?->format('H:i') ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($announcement->expires_at)
                                        <div class="text-sm text-gray-900">
                                            {{ $announcement->expires_at->format('M d, Y') }}</div>
                                    @else
                                        <span class="text-sm text-gray-400">Never</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $status = $announcement->status;
                                        $statusClasses = match ($status) {
                                            'active' => 'bg-green-100 text-green-800',
                                            'expired' => 'bg-gray-100 text-gray-600',
                                            'inactive' => 'bg-yellow-100 text-yellow-800',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                    @if ($announcement->is_urgent)
                                        <span
                                            class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Urgent
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="flex items-center justify-end space-x-2">
                                        {{-- Toggle Active --}}
                                        <form
                                            action="{{ route('journal.announcements.toggle', ['journal' => $journalSlug, 'announcement' => $announcement->id]) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="p-2 rounded-lg {{ $announcement->is_active ? 'text-green-600 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-100' }} transition-colors"
                                                title="{{ $announcement->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i
                                                    class="fa-solid {{ $announcement->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Edit --}}
                                        <button @click="openEditModal('{{ $announcement->id }}')"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                            title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        {{-- Delete --}}
                                        <form
                                            action="{{ route('journal.announcements.destroy', ['journal' => $journalSlug, 'announcement' => $announcement->id]) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if ($announcements->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $announcements->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- Create/Edit Modal --}}
        <div x-show="showModal" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showModal = false">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showModal = false">
                </div>

                {{-- Modal Content --}}
                <div
                    class="relative inline-block w-full max-w-2xl px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-xl sm:my-8 sm:align-middle sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900"
                            x-text="isEditing ? 'Edit Announcement' : 'Create Announcement'"></h3>
                        <p class="text-sm text-gray-500 mt-1">Fill in the details below</p>
                    </div>

                    <form :action="formAction" method="POST" @submit="handleSubmit">
                        @csrf
                        <input type="hidden" name="_method" x-bind:value="isEditing ? 'PUT' : 'POST'">

                        <div class="space-y-5">
                            {{-- Title --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="title" x-model="form.title" required
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Enter announcement title">
                            </div>

                            {{-- Short Description / Excerpt --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                                <textarea name="excerpt" x-model="form.excerpt" rows="2"
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Brief summary for the card preview (max 500 characters)" maxlength="500"></textarea>
                                <p class="text-xs text-gray-400 mt-1">Displayed on the homepage announcement cards</p>
                            </div>

                            {{-- Full Content --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Content</label>
                                <textarea name="content" x-model="form.content" rows="5"
                                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Full announcement content (optional)"></textarea>
                            </div>

                            {{-- Date Fields --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Publish Date</label>
                                    <input type="datetime-local" name="published_at" x-model="form.published_at"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date
                                        <span class="text-gray-400">(Optional)</span></label>
                                    <input type="datetime-local" name="expires_at" x-model="form.expires_at"
                                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            {{-- Toggles --}}
                            <div class="grid grid-cols-2 gap-4">
                                <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="is_active" x-model="form.is_active" value="1"
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-900">Active</span>
                                        <p class="text-xs text-gray-500">Show on the homepage</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="is_urgent" x-model="form.is_urgent" value="1"
                                        class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-900">Urgent</span>
                                        <p class="text-xs text-gray-500">Highlight as important</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="showModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                                <span x-text="isEditing ? 'Save Changes' : 'Create Announcement'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function announcementManager() {
            return {
                showModal: false,
                isEditing: false,
                editingId: null,
                formAction: '{{ route('journal.announcements.store', ['journal' => $journalSlug]) }}',
                form: {
                    title: '',
                    excerpt: '',
                    content: '',
                    published_at: '',
                    expires_at: '',
                    is_active: true,
                    is_urgent: false,
                },

                openCreateModal() {
                    this.isEditing = false;
                    this.editingId = null;
                    this.formAction = '{{ route('journal.announcements.store', ['journal' => $journalSlug]) }}';
                    this.resetForm();
                    // Set default publish date to now
                    const now = new Date();
                    this.form.published_at = now.toISOString().slice(0, 16);
                    this.form.is_active = true;
                    this.showModal = true;
                },

                async openEditModal(id) {
                    this.isEditing = true;
                    this.editingId = id;
                    this.formAction =
                        `{{ url('/' . $journalSlug . '/announcements') }}/${id}`;

                    try {
                        const response = await fetch(
                            `{{ url('/' . $journalSlug . '/announcements') }}/${id}/edit`);
                        const data = await response.json();

                        this.form.title = data.title || '';
                        this.form.excerpt = data.excerpt || '';
                        this.form.content = data.content || '';
                        this.form.published_at = data.published_at || '';
                        this.form.expires_at = data.expires_at || '';
                        this.form.is_active = data.is_active;
                        this.form.is_urgent = data.is_urgent;

                        this.showModal = true;
                    } catch (error) {
                        console.error('Error fetching announcement:', error);
                        alert('Failed to load announcement data');
                    }
                },

                resetForm() {
                    this.form = {
                        title: '',
                        excerpt: '',
                        content: '',
                        published_at: '',
                        expires_at: '',
                        is_active: true,
                        is_urgent: false,
                    };
                },

                handleSubmit(event) {
                    // Let the form submit naturally
                    return true;
                }
            }
        }
    </script>
</x-app-layout>
