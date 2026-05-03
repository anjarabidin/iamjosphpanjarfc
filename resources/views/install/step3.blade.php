@extends('install.layout', ['step' => 3])

@section('content')

<div class="space-y-8" x-data="smtpSetup()">
    <div>
        <h2 class="text-xl font-bold text-gray-800">SMTP Configuration</h2>
        <p class="text-gray-500 text-sm mt-1">Set up your mail server for sending notifications. Test your configuration to ensure notifications can be delivered.</p>
    </div>

    <!-- Form -->
    <form id="smtpForm" method="GET" action="{{ route('install.step4') }}" class="space-y-5">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mail Host</label>
                <input type="text" x-model="formData.mail_host" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" placeholder="smtp.mailtrap.io" required>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mail Port</label>
                    <input type="text" x-model="formData.mail_port" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" placeholder="2525" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                    <select x-model="formData.mail_encryption" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm">
                        <option value="">None</option>
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mail Username</label>
                <input type="text" x-model="formData.mail_username" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" placeholder="" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mail Password</label>
                <input type="password" x-model="formData.mail_password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" placeholder="" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t border-gray-100">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Email Address</label>
                <input type="email" x-model="formData.mail_from_address" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" placeholder="noreply@example.com" required>
                <p class="text-xs text-gray-500 mb-1 pl-1">This email acts as the receiver during the test.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                <input type="text" x-model="formData.mail_from_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2.5 border text-sm" placeholder="IAMJOS System" required>
            </div>
        </div>

        <!-- Connection Test Result -->
        <div x-show="testResult !== null" x-cloak class="p-4 rounded-md text-sm font-medium border" :class="testSuccess ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'">
            <div class="flex items-start gap-2">
                <svg x-show="testSuccess" class="h-5 w-5 text-green-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <svg x-show="!testSuccess" class="h-5 w-5 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span x-text="testMessage"></span>
            </div>
        </div>

        <!-- Actions -->
        <div class="pt-6 border-t border-gray-200 flex justify-between items-center">
            
            <button type="button" @click="testConnection()" :disabled="isLoading" class="px-4 py-2 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700 transition text-sm font-medium flex items-center gap-2">
                <svg x-show="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="isLoading ? 'Sending...' : 'Send Test Email'"></span>
            </button>
            
            <div class="flex gap-3">
                <a href="{{ route('install.step2') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition text-sm font-medium">
                    Back
                </a>
                <button type="submit" :disabled="!testSuccess && !isLoading" class="px-5 py-2.5 rounded-md text-sm font-medium transition disabled:bg-indigo-300 disabled:cursor-not-allowed bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
                    Next Step &rarr;
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('smtpSetup', () => ({
            formData: {
                mail_host: '',
                mail_port: '',
                mail_encryption: 'tls',
                mail_username: '',
                mail_password: '',
                mail_from_address: '',
                mail_from_name: 'IAMJOS System',
                _token: '{{ csrf_token() }}'
            },
            isLoading: false,
            testResult: null,
            testSuccess: false,
            testMessage: '',

            async testConnection() {
                if(!this.formData.mail_host || !this.formData.mail_port || !this.formData.mail_username || !this.formData.mail_password || !this.formData.mail_from_address) {
                    this.testResult = 'error';
                    this.testSuccess = false;
                    this.testMessage = 'Please fill out all required fields first.';
                    return;
                }

                this.isLoading = true;
                this.testResult = null;

                try {
                    const response = await fetch('{{ route("install.test-smtp") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.formData._token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    const data = await response.json();
                    
                    this.testResult = data.success ? 'success' : 'error';
                    this.testSuccess = data.success;
                    this.testMessage = data.message;
                } catch (error) {
                    this.testResult = 'error';
                    this.testSuccess = false;
                    this.testMessage = 'Network error or invalid response formatting.';
                } finally {
                    this.isLoading = false;
                }
            }
        }));
    });
</script>

@endsection
