@extends('layouts.master', ['title' => request()->routeIs('permission.create') ? 'Tambah Permission' : 'Edit Permission', 'main' => 'Data Permission'])
@section('content')
    <!--begin::Content-->
    <div class="content pt-6 d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <!--begin::Container-->
        <div id="kt_content_container" class="app-container container-xxl">
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
                                    class="card-label fw-bold fs-3">{{ request()->routeIs('permission.create') ? 'Tambah Permission' : 'Edit Permission' }}</span>
                            </h3>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <!--begin::Form-->
                        <x-alert.alert-validation />
                        <form class="form"
                            action="{{ request()->routeIs('permission.create') ? route('permission.store') : route('permission.update', $permission->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />
                            <div class="card-body">
                                <div class="fv-row mb-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold form-label mt-3" for="label">
                                        <span class="required text-dark">Label Permission</span>
                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                            title="Label Permission yang diberikan"></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" class="form-control" name="label" id="label"
                                        value="{{ @$permission->label ?? old('label') }}" required />
                                    <!--end::Input-->
                                </div>
                                <!--begin::Input group-->
                                <div class="fv-row mb-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold form-label mt-3" for="name">
                                        <span class="required text-dark">Nama Permission</span>
                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                            title="Nama Permission yang diberikan"></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ @$permission->name ?? old('name') }}" required />
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Card body-->
                            <!--begin::Action buttons-->
                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <!--begin::Button-->
                                <a href="{{ route('permission.index') }}">
                                    <button type="button" class="btn btn-secondary me-3">Batal</button>
                                </a>
                                <!--end::Button-->
                                <!--begin::Button-->
                                <button type="submit" data-kt-contacts-type="submit" class="btn btn-primary btn-sm">
                                    <span class="indicator-label">Simpan</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <!--end::Button-->
                            </div>
                            <!--end::Action buttons-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Contacts-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Contacts App- Add New Contact-->
        </div>
        <!--end::Container-->
        <!--end::Post-->
    </div>
    <!--end::Content-->
    <!--end::Wrapper-->
@endsection
