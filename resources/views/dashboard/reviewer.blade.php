@extends('layouts.app')

@php
$currentJournal = current_journal();
@endphp

@section('title', 'Reviewer Dashboard - ' . ($currentJournal?->abbreviation ?? 'IAMJOS'))

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reviewer Workspace</h1>
            <p class="mt-1 text-sm text-gray-500">Welcome back! Viewing <span
                    class="font-semibold text-indigo-600">{{ $currentJournal?->abbreviation ?? $currentJournal?->name }}</span>
            </p>
        </div>
        {{-- No Submit Button for Reviewers --}}
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="text-sm font-medium text-gray-500">Pending Reviews</div>
            <div class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="text-sm font-medium text-gray-500">Completed Reviews</div>
            <div class="text-3xl font-bold text-emerald-600 mt-2">{{ $stats['completed'] }}</div>
        </div>
    </div>

    {{-- Assignments List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-700">Assigned Manuscripts</h3>
        </div>
        <div class="p-6">
            @if($assignments->isEmpty())
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-clipboard-check text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500">No active review assignments.</p>
            </div>
            @else
            <ul class="divide-y divide-gray-100">
                @foreach($assignments as $assignment)
                <li class="py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $assignment->submission->title }}</h4>
                        <div class="flex items-center gap-4 mt-1">
                            <span class="text-xs text-gray-500">
                                <i class="fa-regular fa-calendar-alt mr-1"></i>
                                Due: {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : 'No Date' }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                    {{ $assignment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $assignment->status_label }}
                            </span>
                        </div>
                    </div>
                    <div>
                        @if($assignment->status === 'pending')
                        <div class="flex gap-2">
                            {{-- Accept/Decline usually happens in a specific view, or we direct them to the show page --}}
                            <a href="{{ route('journal.reviewer.show', ['journal' => $currentJournal->slug, 'identifier' => $assignment->slug]) }}"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Review
                            </a>
                        </div>
                        @else
                        <a href="{{ route('journal.reviewer.show', ['journal' => $currentJournal->slug, 'identifier' => $assignment->slug]) }}"
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Continue Review
                        </a>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection