<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" x-data="rolesTable()">
    {{-- Flash Message Container (JS controlled) --}}
    <div x-show="flashHash" x-transition class="fixed bottom-5 right-5 z-50">
        <div :class="flashType === 'success' ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : 'bg-red-50 border-red-500 text-red-700'"
            class="border-l-4 p-4 rounded shadow-lg flex items-center">
            <div class="flex-shrink-0">
                <i :class="flashType === 'success' ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-exclamation'"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm" x-text="flashMessage"></p>
            </div>
            <button @click="flashHash = null" class="ml-4 text-sm font-medium hover:text-gray-500 focus:outline-none">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    @if (session()->has('message'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4" role="alert">
        <div class="flex">
            <div class="flex-shrink-0"><i class="fa-solid fa-circle-check"></i></div>
            <div class="ml-3">
                <p class="text-sm">{{ session('message') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/4">Role Name</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Permission Level</th>

                    <th scope="col" class="px-4 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                        <div class="flex flex-col items-center gap-1" title="Submission Stage Access">
                            <i class="fa-solid fa-file-upload text-gray-400 text-sm"></i><span>Subm.</span>
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                        <div class="flex flex-col items-center gap-1" title="Review Stage Access">
                            <i class="fa-solid fa-glasses text-gray-400 text-sm"></i><span>Review</span>
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                        <div class="flex flex-col items-center gap-1" title="Copyediting Stage Access">
                            <i class="fa-solid fa-pen-nib text-gray-400 text-sm"></i><span>Copy.</span>
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                        <div class="flex flex-col items-center gap-1" title="Production Stage Access">
                            <i class="fa-solid fa-print text-gray-400 text-sm"></i><span>Prod.</span>
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24 border-l border-gray-200">
                        <div class="flex flex-col items-center gap-1" title="Allow Self-Registration">
                            <i class="fa-solid fa-user-plus text-gray-400 text-sm"></i><span>Reg.</span>
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                        <div class="flex flex-col items-center gap-1" title="Show in Contributor List">
                            <i class="fa-solid fa-users text-gray-400 text-sm"></i><span>List</span>
                        </div>
                    </th>
                    <th scope="col" class="px-4 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                        <div class="flex flex-col items-center gap-1" title="Allow Submission">
                            <i class="fa-solid fa-upload text-gray-400 text-sm"></i><span>Subm.</span>
                        </div>
                    </th>
                    <th scope="col" class="relative px-6 py-4 w-20"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($roles as $role)
                @php
                $levelMap = [
                    1 => ['label' => 'Journal Manager', 'color' => 'bg-red-100 text-red-800'],
                    2 => ['label' => 'Section Editor', 'color' => 'bg-blue-100 text-blue-800'],
                    3 => ['label' => 'Assistant', 'color' => 'bg-teal-100 text-teal-800'],
                    4 => ['label' => 'Reviewer', 'color' => 'bg-amber-100 text-amber-800'],
                    5 => ['label' => 'Author', 'color' => 'bg-green-100 text-green-800'],
                    6 => ['label' => 'Reader', 'color' => 'bg-gray-100 text-gray-600'],
                    0 => ['label' => 'Site Admin', 'color' => 'bg-purple-100 text-purple-800'], // Fallback/System
                ];
                $level = $role->permission_level ?? 6;
                $config = $levelMap[$level] ?? $levelMap[6];
                @endphp
                    <tr class="hover:bg-slate-50 transition duration-150 group">
                    {{-- Role Name --}}
                    <td class="px-6 py-4 font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                        {{ $role->name }}
                    </td>

                    {{-- Level Badge --}}
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $config['color'] }}">
                            {{ $config['label'] }}
                        </span>
                    </td>

                    {{-- CHECKBOXES (Axios Powered) --}}
                    @foreach(['permit_submission', 'permit_review', 'permit_copyediting', 'permit_production', 'allow_registration', 'show_contributor', 'allow_submission'] as $field)
                    <td class="px-4 py-4 text-center {{ $field === 'allow_registration' ? 'border-l border-gray-100' : '' }}">
                        <input type="checkbox"
                            @change="togglePermission('{{ $role->id }}', '{{ $field }}', $event.target.checked)"
                            class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 cursor-pointer transition-colors"
                            {{ $role->$field ? 'checked' : '' }}>
                    </td>
                    @endforeach

                    {{-- Actions --}}
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end items-center gap-4">
                            <a href="{{ route($routePrefix . '.roles.edit', ['journal' => current_journal()->slug, 'role' => $role->id]) }}"
                                class="text-blue-600 hover:text-blue-900 transition-colors" title="Edit Role">
                                <i class="fa-solid fa-pen-to-square text-base"></i>
                            </a>

                            {{-- Delete Form (Standard Blade) --}}
                            <form action="{{ route($routePrefix . '.roles.destroy', ['journal' => current_journal()->slug, 'role' => $role->id]) }}"
                                method="POST"
                                class="inline-block"
                                onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Delete Role">
                                    <i class="fa-solid fa-trash-can text-base"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    </tr>
                    @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    function rolesTable() {
        return {
            flashMessage: '',
            flashType: 'success',
            flashHash: null, // Used to trigger reactivity

            togglePermission(roleId, field, value) {
                // Determine the correct route URL dynamically
                // We construct it based on the current journal context if needed, or use a fairly standard pattern
                // Since this is inside a Blade file, we can use the route() helper for the base structure, 
                // but we need the role ID.

                // Let's assume a pattern: /admin/users/roles/{role}/toggle-permission 
                // However, we are in a tenant context usually: /{journal}/admin/users/roles/{role}/toggle-permission
                // Best way is to generate a template route in Blade.

                let url = "{{ route($routePrefix . '.roles.toggle-permission', ['journal' => current_journal()->slug, 'role' => ':roleId']) }}";
                url = url.replace(':roleId', roleId);

                axios.post(url, {
                        field: field,
                        value: value
                    })
                    .then(response => {
                        if (response.data.success) {
                            this.showFlash('success', 'Permission updated successfully.');
                        } else {
                            this.showFlash('error', response.data.message || 'Update failed.');
                            // Revert checkbox if needed (complex without x-model binding to specific rows, skipping for now)
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        this.showFlash('error', 'An error occurred while updating permission.');
                    });
            },

            showFlash(type, message) {
                this.flashType = type;
                this.flashMessage = message;
                this.flashHash = Date.now(); // Trigger change

                setTimeout(() => {
                    this.flashHash = null;
                }, 3000);
            }
        }
    }
</script>