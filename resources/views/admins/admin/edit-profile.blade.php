@extends('layouts.master', ['title' => 'Edit Profil', 'main' => 'Profil'])
@section('content')
<!--begin::Content-->
<div class="content pt-6 d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl">
        <!--begin::Contacts App- Add New Contact-->
        <div class="row g-7">
            <!--begin::Content-->
            <div class="col-xl-12">
                <!--begin::Contacts-->
                <div class="card card-flush h-lg-100" id="kt_contacts_main">
                    <!--begin::Card header-->
                   <div class="card-header pt-6" id="kt_chat_contacts_header">
                       <!--begin::Card title-->
                       <h3 class="card-title align-items-start flex-column">
                           <span class="card-label fw-bold fs-3 mb-1">Edit Profil</span>
                       </h3>
                       <!--end::Card title-->
                   </div>
                       <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-5">
                        <!--begin::Form-->
                        <x-alert.alert-validation />
                        <form id="admin" action="{{ route('profile-admin.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold form-label" for="email">
                                    <span class="required">Email </span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Masukkan Email"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="email" class="form-control" id="email" placeholder="Contoh: admin@gmail.com" name="email" value="{{ @$admin->email ?? old('email') }}" required />
                                <!--end::Input-->
                            </div>

                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold form-label mt-3">
                                    <span class="required">Nama </span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Masukkan Nama"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" class="form-control" name="name" placeholder="Contoh: Admin" value="{{ @$admin->name ?? old('name') }}" required />
                                <!--end::Input-->
                            </div>
                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold form-label" for="password">
                                    <span class="required">Password</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Masukkan Password"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="password" placeholder="Kosongkan password jika tidak ingin mengubah" class="form-control" id="password" name="password" value="{{ old('password') }}" />
                                <!--end::Input-->
                            </div>
                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold form-label" for="password_confirmation">
                                    <span class="required">Konfirmasi Password</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Masukkan Konfirmasi Password"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="password" placeholder="Kosongkan password jika tidak ingin mengubah" class="form-control" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}" />
                                <!--end::Input-->
                            </div>
                            <div class="fv-row mb-6">
                                <x-form.image-upload label="Avatar" maxSize="2MB" name="avatar" :value="@$admin->avatar ?? null" nullable='1' />
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
                                    <button type="button" data-kt-contacts-type="cancel" class="btn btn-sm btn-secondary me-3">Batal</button>
                                </a>
                                <!--end::Button-->
                                <!--begin::Button-->
                                <button type="submit" data-kt-contacts-type="submit" class="btn btn-sm btn-primary">
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
