@extends('layouts.app')

@section('title', 'OAI-PMH Import - ' . $journal->name)

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('journal.dashboard', ['journal' => $journal->slug]) }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                            </path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('journal.settings.tools.index', ['journal' => $journal->slug]) }}"
                            class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2">Tools</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">OAI-PMH Import</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">OAI-PMH Harvester</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Import articles, metadata, and files from another OJS 3.x journal using its OAI-PMH endpoint.
                    </p>

                    <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Target Journal:</strong><br>
                                    {{ $journal->name }} ({{ $journal->abbreviation }})
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-xs text-gray-500">
                        How it works:
                        <ul class="list-disc ml-4 mt-1 space-y-1">
                            <li>Validates the OAI Endpoint.</li>
                            <li>Parses Dublin Core (oai_dc) metadata.</li>
                            <li>Imports articles into the selected section.</li>
                            <li>Automatically downloads PDFs linked in the metadata.</li>
                            <li>Runs in background to handle large datasets.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <div class="shadow sm:rounded-md sm:overflow-hidden bg-white">
                    <div class="px-4 py-5 space-y-6 sm:p-6">

                        <!-- Form -->
                        <form id="oaiForm" onsubmit="event.preventDefault(); startHarvest();">
                            @csrf

                            <!-- Source URL -->
                            <div class="col-span-6 sm:col-span-4">
                                <label for="url" class="block text-sm font-medium text-gray-700">Source OAI URL</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="url" name="url" id="url" required
                                        placeholder="https://jurnal.ugm.ac.id/jurnal/oai"
                                        class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300 p-2 border">
                                    <button type="button" onclick="checkPreview()"
                                        class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                        <span>Check Connection</span>
                                    </button>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Enter the full OAI-PMH endpoint URL (usually ends in <code>/oai</code>).
                                </p>
                            </div>

                            <!-- Target Section -->
                            <div class="grid grid-cols-6 gap-6 mt-4">
                                <div class="col-span-6 sm:col-span-4">
                                    <label for="section_id" class="block text-sm font-medium text-gray-700">Target
                                        Section</label>
                                    <select id="section_id" name="section_id" required
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select Section to Import Into...</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">All imported articles will be assigned to this
                                        section.</p>
                                </div>
                            </div>

                            <!-- Status/Result Message -->
                            <div id="statusArea" class="mt-4 hidden">
                                <div class="rounded-md bg-blue-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" id="statusIcon"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3 flex-1 md:flex md:justify-between">
                                            <p class="text-sm text-blue-700 font-medium" id="statusMessage">Checking...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 mt-6 -mx-6 -mb-6 rounded-b-md">
                                <button type="submit" id="harvestBtn" disabled
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Start Background Harvest
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        // Using named routes generated by Laravel Blade
        const previewUrl = "{{ route('journal.settings.tools.import.oai.preview', ['journal' => $journal->slug]) }}";
        const harvestUrl = "{{ route('journal.settings.tools.import.oai.harvest', ['journal' => $journal->slug]) }}";

        async function checkPreview() {
            const urlInput = document.getElementById('url');
            const statusArea = document.getElementById('statusArea');
            const statusMessage = document.getElementById('statusMessage');
            const harvestBtn = document.getElementById('harvestBtn');

            if (!urlInput.value) {
                alert('Please enter a valid OAI URL first');
                return;
            }

            statusArea.classList.remove('hidden');
            statusMessage.textContent = 'Contacting OAI Endpoint...';
            statusMessage.className = 'text-sm text-blue-700 font-medium';
            harvestBtn.disabled = true;

            try {
                const response = await fetch(previewUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        url: urlInput.value
                    })
                });

                const data = await response.json();

                if (data.success) {
                    statusMessage.textContent = data.message; // "Found 150 records"
                    statusMessage.className = 'text-sm text-green-700 font-medium';
                    document.getElementById('statusIcon').className = 'h-5 w-5 text-green-400';
                    harvestBtn.disabled = false;
                } else {
                    statusMessage.textContent = 'Error: ' + data.message;
                    statusMessage.className = 'text-sm text-red-700 font-medium';
                    document.getElementById('statusIcon').className = 'h-5 w-5 text-red-400';
                    harvestBtn.disabled = true;
                }
            } catch (error) {
                statusMessage.textContent = 'Network or Server Error';
                statusMessage.className = 'text-sm text-red-700 font-medium';
                console.error(error);
            }
        }

        async function startHarvest() {
            const form = document.getElementById('oaiForm');
            const statusArea = document.getElementById('statusArea');
            const statusMessage = document.getElementById('statusMessage');
            const harvestBtn = document.getElementById('harvestBtn');

            if (!confirm('Are you sure you want to start the import process? This will run in the background.')) return;

            statusArea.classList.remove('hidden');
            statusMessage.textContent = 'Initiating Import Job...';
            harvestBtn.disabled = true;

            const formData = new FormData(form);
            const jsonData = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(harvestUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(jsonData)
                });

                const data = await response.json();

                if (data.success) {
                    statusMessage.textContent = data.message;
                    statusMessage.className = 'text-sm text-green-700 font-medium';
                    alert('Success: ' + data.message);
                    // Reset form or redirect? Maybe keep it there so they know it started.
                } else {
                    statusMessage.textContent = 'Error: ' + data.message;
                    statusMessage.className = 'text-sm text-red-700 font-medium';
                    harvestBtn.disabled = false;
                }
            } catch (error) {
                statusMessage.textContent = 'Network or Server Error';
                statusMessage.className = 'text-sm text-red-700 font-medium';
                console.error(error);
                harvestBtn.disabled = false;
            }
        }
    </script>
@endsection
