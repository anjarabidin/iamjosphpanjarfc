@extends('layouts.master', ['main' => 'Data Iklan', 'title' => request()->routeIs('home-ads.create') ? 'Tambah Iklan' : 'Edit Iklan'])
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
                                class="card-label fw-bold fs-3">{{ request()->routeIs('home-ads.create') ? 'Tambah Iklan' : 'Edit Iklan' }}</span>
                        </h3>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-5">
                        <x-alert.alert-validation />

                        <form id="home-ads"
                            action="{{ request()->routeIs('home-ads.create') ? route('home-ads.store') : route('home-ads.update', @$homeAds->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />

                            <div class="row g-6">
                                {{-- KOLOM KIRI: INPUT TEKS --}}
                                <div class="col-12 col-xl-6">
                                    <div class="fv-row mb-6">
                                        <label for="start_date" class="fs-6 fw-bold form-label mt-3">
                                            <span class="required text-dark">Tanggal Mulai</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan Tanggal Mulai"></i>
                                        </label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            value="{{ old('start_date', @$homeAds->start_date ? \Carbon\Carbon::parse($homeAds->start_date)->format('Y-m-d') : null) }}"
                                            placeholder="Masukkan Tanggal Mulai" />
                                    </div>

                                    <div class="fv-row mb-6">
                                        <label for="end_date" class="fs-6 fw-bold form-label mt-3">
                                            <span class="required text-dark">Tanggal Selesai</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan Tanggal Selesai"></i>
                                        </label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            value="{{ old('end_date', @$homeAds->end_date ? \Carbon\Carbon::parse($homeAds->end_date)->format('Y-m-d') : null) }}"
                                            placeholder="Masukkan Tanggal Selesai" />
                                    </div>

                                    <div class="fv-row mb-6">
                                        <label for="link" class="fs-6 fw-bold form-label mt-3">
                                            <span class="required text-dark">Link</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan Link"></i>
                                        </label>
                                        <input type="url" class="form-control" id="link" name="link"
                                            value="{{ old('link', @$homeAds->link) }}" placeholder="Masukkan Link" />
                                    </div>

                                    <div class="fv-row mb-6">
                                        <label for="order" class="fs-6 fw-bold form-label mt-3">
                                            <span class="required text-dark">Urutan</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Masukkan Urutan"></i>
                                        </label>
                                        <input type="number" class="form-control" id="order" name="order"
                                            value="{{ old('order', @$homeAds->order) }}" placeholder="Masukkan Urutan" />
                                    </div>

                                    @if (request()->routeIs('home-ads.edit'))
                                        <div class="fv-row mb-6">
                                            <label for="is_active" class="fs-6 fw-bold form-label mt-3">
                                                <span class="required text-dark">Status</span>
                                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                    title="Aktifkan atau Nonaktifkan Iklan"></i>
                                            </label>
                                            <select class="form-select" id="is_active" name="is_active">
                                                <option value="1"
                                                    {{ old('is_active', @$homeAds->is_active) == 1 ? 'selected' : '' }}>
                                                    Aktif
                                                </option>
                                                <option value="0"
                                                    {{ old('is_active', @$homeAds->is_active) == 0 ? 'selected' : '' }}>
                                                    Nonaktif
                                                </option>
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                {{-- KOLOM KANAN: PREVIEW & UPLOAD MEDIA --}}
                                <div class="col-12 col-xl-6">
                                    <div class="fv-row mb-6">
                                        <label for="media" class="fs-6 fw-bold form-label mt-3">
                                            <span class="required text-dark">Media</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Upload Media"></i>
                                        </label>

                                        {{-- Preview saat edit --}}
                                        @if (request()->routeIs('home-ads.edit') && @$homeAds->media_url)
                                            <div class="mb-3">
                                                @if (@$homeAds->media_type === 'video')
                                                    <video class="img-fluid rounded border" controls>
                                                        <source src="{{ asset(@$homeAds->media_url) }}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @else
                                                    <img src="{{ asset(@$homeAds->media_url) }}"
                                                        class="img-fluid rounded border" alt="Media Iklan">
                                                @endif
                                            </div>
                                        @endif

                                        <input type="file" class="form-control" id="media" name="media"
                                            value="{{ old('media', @$homeAds->media) }}" placeholder="Upload Media" />
                                        <div class="form-text">Format: jpg, jpeg, png, gif, svg, mp4, mkv. Maks 10MB.</div>
                                    </div>
                                </div>
                            </div>

                            {{-- TOMBOL AKSI --}}
                            <div class="d-flex justify-content-end mt-2">
                                <a href="{{ route('home-ads.index') }}" class="btn btn-secondary me-3">Batal</a>

                                <button type="submit" data-kt-contacts-type="submit" class="btn btn-primary btn-sm">
                                    <span class="indicator-label">Simpan</span>
                                    <span class="indicator-progress">Mohon Tunggu...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </form>
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
