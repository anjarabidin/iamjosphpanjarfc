@extends('layouts.admin')

@section('title', 'Site Pages')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="pagesManager()">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Site Pages</h1>
                    <p class="text-sm text-gray-500">Manage custom static pages for your portal</p>
                </div>
                <a href="{{ route('admin.site-pages.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Create Page
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-green-800">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($pages->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Page
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Slug
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Updated
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="pages-list">
                        @foreach($pages as $page)
                            <tr class="hover:bg-gray-50" data-id="{{ $page->id }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-file-lines text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $page->title }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <code class="text-sm bg-gray-100 px-2 py-1 rounded text-gray-600">/page/{{ $page->slug }}</code>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="toggleStatus('{{ $page->id }}')"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $page->is_published ? 'bg-green-500' : 'bg-gray-300' }}"
                                            data-page-id="{{ $page->id }}"
                                            data-status="{{ $page->is_published ? 'true' : 'false' }}">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $page->is_published ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-500">
                                    {{ $page->updated_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($page->is_published)
                                            <a href="{{ route('site.page', $page->slug) }}" target="_blank"
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50"
                                               title="View Page">
                                                <i class="fa-solid fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.site-pages.edit', $page) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50"
                                           title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('admin.site-pages.destroy', $page) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this page?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50"
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
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-file-lines text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No pages yet</h3>
                <p class="text-gray-500 mb-6">Create your first custom page to get started.</p>
                <a href="{{ route('admin.site-pages.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Create Page
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function pagesManager() {
    return {
        async toggleStatus(pageId) {
            try {
                const response = await fetch(`/admin/site-pages/${pageId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    const button = document.querySelector(`button[data-page-id="${pageId}"]`);
                    const toggle = button.querySelector('span');
                    
                    if (data.is_published) {
                        button.classList.remove('bg-gray-300');
                        button.classList.add('bg-green-500');
                        toggle.classList.remove('translate-x-0');
                        toggle.classList.add('translate-x-5');
                    } else {
                        button.classList.remove('bg-green-500');
                        button.classList.add('bg-gray-300');
                        toggle.classList.remove('translate-x-5');
                        toggle.classList.add('translate-x-0');
                    }
                }
            } catch (error) {
                console.error('Error toggling status:', error);
            }
        }
    }
}
</script>
@endpush
@endsection
