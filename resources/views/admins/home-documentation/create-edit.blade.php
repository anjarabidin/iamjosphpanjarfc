@extends('layouts.master', ['main' => 'Data Dokumentasi', 'title' => request()->routeIs('home-documentation.create') ? 'Tambah Dokumentasi' : 'Edit Dokumentasi'])
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
                                class="card-label fw-bold fs-3">{{ request()->routeIs('home-documentation.create') ? 'Tambah Dokumentasi' : 'Edit Dokumentasi' }}</span>
                        </h3>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-5">
                        <!--begin::Form-->
                        <x-alert.alert-validation />
                        <form id="home-documentation"
                            action="{{ request()->routeIs('home-documentation.create') ? route('home-documentation.store') : route('home-documentation.update', @$homeDocumentation->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />
                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label for="media" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Media</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Upload Media"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                @if (request()->routeIs('home-documentation.edit') && @$homeDocumentation->media_url)
                                    <div class="mb-3">
                                        @if (@$homeDocumentation->media_type === 'video')
                                            <video width="320" height="240" controls>
                                                <source src="{{ asset(@$homeDocumentation->media_url) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                            <img src="{{ asset(@$homeDocumentation->media_url) }}" class="img-fluid"
                                                alt="Media Dokumentasi">
                                        @endif
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="media" name="media"
                                    value="{{ old('media', @$homeDocumentation->media) }}" placeholder="Upload Media" />

                                <!--end::Input-->
                            </div>
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
                                    value="{{ old('order', @$homeDocumentation->order) }}" placeholder="Masukkan Urutan" />
                                <!--end::Input-->
                            </div>
                            <div class="fv-row mb-6">
                                <x-form.image-upload label="Thumbnail" maxSize="2MB" name="thumbnail" :value="@$homeDocumentation->thumbnail ?? null"
                                    nullable='1' />
                            </div>
                            <!--end::Separator-->
                            <!--begin::Action buttons-->
                            <div class="d-flex justify-content-end">
                                <!--begin::Button-->
                                <a href="{{ route('home-documentation.index') }}">
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
