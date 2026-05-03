@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<!-- Header -->
<div class="flex justify-between items-start mb-8">
    <div>
        <nav class="flex mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <span class="text-gray-400 text-sm font-medium">Users</span>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-gray-300 mx-2 text-xs"></i>
                        <span class="text-sm font-medium text-indigo-600">Roles & Permissions</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">Roles</h1>
        <p class="text-sm text-gray-500 mt-1">Configure user roles and their workflow access levels.</p>
    </div>
    <div>
        <a href="{{ route($routePrefix . '.roles.create', ['journal' => $journal->slug]) }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors shadow-sm shadow-indigo-200">
            <i class="fa-solid fa-plus"></i>
            Create New Role
        </a>
    </div>
</div>

<!-- Roles Table (Alpine + Axios) -->
@include('livewire.admin.users.roles-table', [
'roles' => $roles,
'journal' => $journal,
'routePrefix' => $routePrefix
])

@endsection