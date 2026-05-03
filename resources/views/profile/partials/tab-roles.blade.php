<form action="{{ route('journal.profile.roles.update', $journal->slug) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="border-b pb-4 mb-4">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Journal Roles</h3>
        <p class="mt-1 text-sm text-gray-500">Select the roles you wish to assume in this journal.</p>
    </div>

    @if ($availableRoles->isEmpty())
        <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        No self-registerable roles are available for this journal.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div x-data="{ selected: @js($userRolesIds) }" class="grid grid-cols-1 md:grid-cols-2 gap-2">

            @foreach ($availableRoles as $role)
                <label class="relative flex cursor-pointer rounded-xl border p-5 shadow-sm transition-all duration-200"
                    :class="selected.includes('{{ $role->id }}') ?
                        'border-blue-600 ring-1 ring-blue-600 bg-blue-50' :
                        'border-gray-200 bg-white hover:border-blue-300 hover:shadow-md'">

                    <input type="checkbox" name="selected_roles[]" value="{{ $role->id }}" class="sr-only"
                        x-model="selected">

                    <div class="flex flex-1 gap-4">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-full"
                                :class="selected.includes('{{ $role->id }}') ? 'bg-blue-100 text-blue-600' :
                                    'bg-gray-100 text-gray-500'">
                                @if ($role->name === 'Author')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg>
                                @elseif($role->name === 'Reviewer')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                @endif
                            </span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-base font-semibold"
                                :class="selected.includes('{{ $role->id }}') ? 'text-blue-900' : 'text-gray-900'">
                                {{ $role->name }}
                            </span>
                            <span class="mt-1 text-sm leading-snug"
                                :class="selected.includes('{{ $role->id }}') ? 'text-blue-700' : 'text-gray-500'">
                                @if ($role->name === 'Author')
                                    Submit manuscripts and track your work.
                                @elseif($role->name === 'Reviewer')
                                    Review submissions assigned to you.
                                @else
                                    Read content and receive notifications.
                                @endif
                            </span>
                        </div>
                    </div>

                    <div x-show="selected.includes('{{ $role->id }}')" x-cloak
                        class="absolute top-4 right-4 bg-blue-600 text-white rounded-full p-1 shadow-sm">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="flex justify-end pt-6 border-t mt-8">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm font-medium">
                Save Roles
            </button>
        </div>
    @endif
</form>

<div class="relative py-8">
    <div class="absolute inset-0 flex items-center" aria-hidden="true">
        <div class="w-full border-t border-gray-300"></div>
    </div>
    <div class="relative flex justify-center">
        <span class="bg-white px-3 text-base font-semibold text-gray-900">Other Journals</span>
    </div>
</div>

<div x-data="{
    isOpen: false,
    journalName: '',
    formAction: '',
    selectedRoles: [],
    availableRoles: []
}">
    <div class="grid grid-cols-1 gap-4">
        @foreach ($otherJournals as $otherJournal)
            @php
                // Check if user has ANY role in this other journal
                $isEnrolled = in_array($otherJournal->id, $enrolledJournalIds);
            @endphp
            <div
                class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm flex items-center justify-between hover:bg-gray-50 transition">
                <div class="flex items-center space-x-4">
                    <div
                        class="flex-shrink-0 h-12 w-12 bg-gray-200 rounded-md flex items-center justify-center text-gray-500 font-bold text-xl uppercase">
                        {{ substr($otherJournal->name, 0, 1) }}
                    </div>

                    <div>
                        <h4 class="text-base font-bold text-gray-900">{{ $otherJournal->name }}</h4>
                        <p class="text-sm text-gray-500 truncate max-w-md">
                            {{ $otherJournal->description ?? 'Open Access Journal' }}</p>
                    </div>
                </div>

                <div class="flex items-center">
                    @if ($isEnrolled)
                        <span
                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 mr-4">
                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Enrolled
                        </span>
                        <a href="{{ route('journal.profile.edit', $otherJournal->slug) }}"
                            class="text-sm font-medium text-blue-600 hover:text-blue-500 hover:underline">
                            Manage Roles
                        </a>
                    @else
                        <button type="button"
                            @click="
                            isOpen = true; 
                            journalName = '{{ addslashes($otherJournal->name) }}'; 
                            formAction = '{{ route('journal.enroll', $otherJournal->slug) }}';
                            selectedRoles = [];
                            availableRoles = {{ $otherJournal->roles->map(fn($r) => ['id' => $r->id, 'name' => $r->name])->values()->toJson() }};
                        "
                            class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-blue-600 shadow-sm ring-1 ring-inset ring-blue-300 hover:bg-blue-50">
                            Enroll
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Enrollment Modal -->
    <div x-show="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm" x-cloak
        style="display: none;">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 relative mx-4" @click.away="isOpen = false">

            <button type="button" @click="isOpen = false"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <h2 class="text-xl font-bold mb-2 text-gray-900">Join <span x-text="journalName"></span></h2>
            <p class="text-gray-500 mb-6">Select the roles you wish to assume in this journal.</p>

            <form :action="formAction" method="POST">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <template x-for="role in availableRoles" :key="role.id">
                        <label class="cursor-pointer border rounded-lg p-4 hover:bg-blue-50 transition-colors"
                            :class="selectedRoles.includes(role.name) ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' :
                                'border-gray-200'">
                            {{-- Note: Controller expects Role Names (strings) for 'roles' array --}}
                            <input type="checkbox" name="roles[]" :value="role.name" x-model="selectedRoles"
                                class="sr-only">
                            <div class="text-center">
                                <span class="block font-bold text-gray-900" x-text="role.name"></span>
                                <span class="text-xs text-gray-500"
                                    x-text="
                                    role.name === 'Author' ? 'Submit articles' :
                                    (role.name === 'Reviewer' ? 'Review submissions' : 
                                    (role.name === 'Reader' ? 'Get notifications' : 'Member'))
                                "></span>
                            </div>
                        </label>
                    </template>

                    {{-- Empty state if no roles available --}}
                    <div x-show="availableRoles.length === 0" class="col-span-full text-center py-4 text-gray-500">
                        No self-registerable roles available for this journal.
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="isOpen = false"
                        class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition-colors">Cancel</button>
                    <button type="submit" :disabled="selectedRoles.length === 0"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Confirm Join
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
