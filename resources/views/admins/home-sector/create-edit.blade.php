@extends('layouts.master', ['main' => 'Data Badan Usaha', 'title' => request()->routeIs('home-sector.create') ? 'Tambah Badan Usaha' : 'Edit Badan Usaha'])
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
                                class="card-label fw-bold fs-3">{{ request()->routeIs('home-sector.create') ? 'Tambah Badan Usaha' : 'Edit Badan Usaha' }}</span>
                        </h3>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-5">
                        <!--begin::Form-->
                        <x-alert.alert-validation />
                        <form id="home-sector"
                            action="{{ request()->routeIs('home-sector.create') ? route('home-sector.store') : route('home-sector.update', @$homeSector->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />
                            <div class="fv-row mb-6">
                                <x-form.image-upload label="Ikon" maxSize="2MB" name="image" :value="@$homeSector->image ?? null"
                                    nullable='1' />
                            </div>
                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label for="name" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Nama Badan Usaha</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Nama Badan Usaha"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', @$homeSector->name) }}" placeholder="Masukkan Nama Badan Usaha"
                                    required />
                                <!--end::Input-->
                            </div>
                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label for="description" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Deskripsi</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Deskripsi"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <textarea class="form-control" id="description" name="description" placeholder="Masukkan Deskripsi Badan Usaha"
                                    required>{{ old('description', @$homeSector->description) }}</textarea>
                                <!--end::Input-->
                            </div>
                            {{-- <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label for="link" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Tautan Link</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Tautan"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="url" class="form-control" id="link" name="link"
                                    value="{{ old('link', @$homeSector->link) }}" placeholder="Masukkan Tautan Link"
                                    required />
                                <!--end::Input-->
                            </div> --}}
                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label for="order" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Urutan</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Urutan"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="number" class="form-control" id="order" name="order"
                                    value="{{ old('order', @$homeSector->order) }}" placeholder="Masukkan Urutan" />
                                <!--end::Input-->
                            </div>
                            <!--end::Separator-->
                            <!--begin::Action buttons-->
                            <div class="d-flex justify-content-end">
                                <!--begin::Button-->
                                <a href="{{ route('home-sector.index') }}">
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
