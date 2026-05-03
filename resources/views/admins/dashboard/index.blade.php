@extends('layouts.master', ['title' => 'Dashboard', 'main' => 'Dashboard'])
@section('content')
    <div class="app-main pt-6 flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    @if (!$isPasswordSafe)
                        <div class="container-fluid">
                            <div class="alert alert-danger fade show mt-4 mb-2" role="alert">
                                <strong>Perhatian!</strong> Password Anda tidak memenuhi kriteria keamanan. Segera ganti
                                password Anda
                                <a href="{{ route('profile-admin.edit') }}" class="text-primary">Ganti
                                    Password
                                    Sekarang</a>.
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    </div>
@endsection
