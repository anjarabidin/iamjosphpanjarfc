@extends('layouts.master', ['title' => 'Detail Admin', 'main' => 'Admin'])
@push('css')
    <style>
        [data-bs-theme="light"] {
            --color-black2: #F1F1F2;
        }

        [data-bs-theme="dark"] {
            --color-black2: #262626;
        }

        .img_avatar {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
            object-fit: cover;
            border-radius: 100%;
        }

        .badge_role {
            font-size: 12px;
            font-weight: 600;
            color: var(--bs-info);
            padding: 4px 8px;
            background-color: var(--color-black2);
            border-radius: 12px;
            margin-top: 8px;
            line-height: 18px;
        }

        p.fs-details {
            font-size: 16px;
        }

        p {
            font-size: 14px;
        }
    </style>
@endpush
@section('content')
    <div class="app-container container-xxl">
        <a href="{{ route('admin.index') }}" class="mt-1 d-flex align-items-center gap-2">
            <span class="menu-icon back pt-1">
                <i class="ki-duotone ki-arrow-left">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
            </span>
            <p class="text-muted mb-0 fw-medium fs-details">Back</p>
        </a>
        <div class="row pt-6">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex flex-column align-items-center justify-content-center">
                            {{-- <img src="{{asset('assets/media/avatars/150-1.jpg')}}" class="img_avatar" alt=""> --}}
                            <img src="{{ asset($admin->avatar) }}" class="img_avatar" alt="">
                            <h1 class="text-capitalize mb-0">{{ $admin->name }}</h1>
                            <span class="badge badge_role">{{ $admin->is_active ? 'Aktif' : 'Non Aktif' }}</span>
                        </div>
                        <p class="mt-5 fs-details fw-medium">Detail</p>
                        <hr>
                        <p class="mb-0">Email</p>
                        <p class="text-muted">{{ $admin->email }}</p>
                        <p class="mb-0">Role</p>
                        <p class="text-muted">{{ $admin->role_name }}</p>
                        <p class="mb-1">Hak Akses</p>
                        @foreach ($admin->roles()->first()->permissions as $permission)
                            <span class="badge badge-primary m-1">{{ $permission->label }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                {{-- <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Log Activity</h3>
                    </div>
                    <div class="card-body pt-0">
                        @include('admins.admin.tab.log_activity')
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection
@push('js')
    @if (request()->tab)
        <script>
            $('#tab_{{ request()->tab }}').addClass('show active')
            $('#nav_tab_{{ request()->tab }}').addClass('active')
        </script>
    @else
        <script>
            $('#tab_information').addClass('show active')
            $('#nav_tab_information').addClass('active')
        </script>
    @endif
@endpush
