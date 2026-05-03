@extends('layouts.master', ['main' => 'Data Admin', 'title' => request()->routeIs('admin.create') ? 'Tambah Admin' : 'Edit Admin'])
@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <!--begin::Contacts App- Add New Contact-->
        <div class="row g-7">
            <!--begin::Content-->
            <div class="col-xl-12">
                <!--begin::Contacts-->
                <div class="card h-lg-100" id="kt_contacts_main">
                    <!--begin::Card header-->
                    <div class="card-header" id="kt_chat_contacts_header">
                        <!--begin::Card title-->
                        <h3 class="card-title align-items-start flex-column">
                            <span
                                class="card-label fw-bold fs-3">{{ request()->routeIs('admin.create') ? 'Tambah Admin' : 'Edit Admin' }}</span>
                        </h3>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-5">
                        <!--begin::Form-->
                        <x-alert.alert-validation />
                        <form id="admin"
                            action="{{ request()->routeIs('admin.create') ? route('admin.store') : route('admin.update', @$admin->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />
                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold form-label" for="email">
                                    <span class="required text-dark">Email </span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Email"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="email" class="form-control" id="email"
                                    placeholder="Contoh: admin@gmail.com" name="email"
                                    value="{{ @$admin->email ?? old('email') }}" required />
                                <!--end::Input-->
                            </div>

                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label for="name" class="fs-6 fw-bold form-label mt-3">
                                    <span class="required text-dark">Nama </span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Nama"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" id="name" class="form-control" name="name"
                                    placeholder="Contoh: Admin" value="{{ @$admin->name ?? old('name') }}" required />
                                <!--end::Input-->
                            </div>
                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold form-label" for="password">
                                    <span class="text-dark">Password</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Password"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="password"
                                    placeholder="{{ @$admin ? 'Kosongkan password jika tidak ingin mengubah' : '' }}"
                                    class="form-control" id="password" name="password" value="{{ old('password') }}" />
                                <!--end::Input-->
                            </div>
                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold form-label" for="password_confirmation">
                                    <span class="text-dark">Konfirmasi Password</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Konfirmasi Password"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="password"
                                    placeholder="{{ @$admin ? 'Kosongkan password jika tidak ingin mengubah' : '' }}"
                                    class="form-control" id="password_confirmation" name="password_confirmation"
                                    value="{{ old('password_confirmation') }}" />
                                <!--end::Input-->
                            </div>
                            <div class="fv-row mb-6">
                                <label class="fs-6 fw-bold form-label" for="role_id">
                                    <span class="required text-dark">Role</span>
                                </label>
                                <select name="role_id" class="form-select" id="role_id">
                                    <option value="">--Pilih Role--</option>
                                    @foreach ($roles as $item)
                                        <option value="{{ $item->id }}"
                                            @if (old('role_id', @$admin->role_id) == $item->id) selected @endif>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="fv-row mb-6">
                                <x-form.image-upload label="Avatar" maxSize="2MB" name="avatar" :value="@$admin->avatar ?? null"
                                    nullable='1' />
                            </div>
                            <!--end::Input group-->
                            <!--begin::Separator-->
                            <div class="separator mb-6">
                                <input type="hidden" name="id" value="{{ @$admin->id }}">
                            </div>
                            <!--end::Separator-->
                            <!--begin::Action buttons-->
                            <div class="d-flex justify-content-end">
                                <!--begin::Button-->
                                <a href="{{ route('admin.index') }}">
                                    <button type="button" data-kt-contacts-type="cancel"
                                        class="btn btn-secondary me-3">Batal</button>
                                </a>
                                <!--end::Button-->
                                <!--begin::Button-->
                                <button type="submit" data-kt-contacts-type="submit" class="btn btn-primary btn-sm">
                                    <span class="indicator-label">Simpan</span>
                                    <span class="indicator-progress">Mohon Tunggu...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <!--end::Button-->
                            </div>
                            <!--end::Action buttons-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Contacts-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Contacts App- Add New Contact-->
    </div>
    <!--end::Container-->
    </div>
    <!--end::Content-->
    <!--end::Wrapper-->
@endsection
