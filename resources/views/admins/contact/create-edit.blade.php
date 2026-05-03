@extends('layouts.master', ['main' => 'Data Kontak', 'title' => request()->routeIs('contact.create') ? 'Tambah Kontak' : 'Edit Kontak'])
@section('content')
    <!--begin::Content wrapper-->
    <div class="d-flex pt-6 flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Basic info-->
                <div class="card mb-5 mb-xl-10">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Card title-->
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">
                                {{ request()->routeIs('contact.create')
                                    ? 'Tambah Kontak'
                                    : 'Edit
                                                                                                                                                                                                                            Kontak' }}
                            </h3>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--begin::Card header-->
                    <!--begin::Content-->
                    <div id="kt_account_settings_profile_details" class="collapse show">
                        <!--begin::Form-->
                        <form class="form" method="POST" enctype="multipart/form-data"
                            action="{{ request()->routeIs('contact.create') ? route('contact.store') : route('contact.update', @$contact->id) }}">
                            @csrf
                            <x-form.put-method />
                            <x-alert.alert-validation />
                            <!--begin::Card body-->
                            <div class="card-body">

                                <!--begin::Input group-->
                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nama</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-12">
                                        <!--begin::Row-->
                                        <div class="row">
                                            <!--begin::Col-->
                                            <div class="col-lg-12 fv-row">
                                                <input type="text" name="name" id="name"
                                                    class="form-control mb-3 mb-lg-0" placeholder="Nama Kontak Anda"
                                                    value="{{ @$contact->name ?? old('name') }}" required />
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Tipe</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-12">
                                        <!--begin::Row-->
                                        <div class="row">
                                            <!--begin::Col-->
                                            <div class="col-lg-12 fv-row">
                                                <select name="type" id="type" class="form-select mb-3 mb-lg-0"
                                                    required>
                                                    <option value="">Pilih Tipe Kontak</option>
                                                    <option value="email"
                                                        {{ @$contact->type == 'email' ? 'selected' : '' }}>
                                                        Email</option>
                                                    <option value="phone"
                                                        {{ @$contact->type == 'phone' ? 'selected' : '' }}>
                                                        Telepon</option>
                                                    <option value="whatsapp"
                                                        {{ @$contact->type == 'whatsapp' ? 'selected' : '' }}>
                                                        Whatsapp</option>
                                                </select>
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                    </div>
                                    <!--end::Col-->
                                </div>

                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Kontak</label>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-12">
                                        <!--begin::Row-->
                                        <div class="row">
                                            <!--begin::Col-->
                                            <div class="col-lg-12 fv-row">
                                                <input type="text" name="value" id="value"
                                                    class="form-control mb-3 mb-lg-0"
                                                    placeholder="Masukkan Kontak Yang Anda Inginkan"
                                                    value="{{ @$contact->value ?? old('value') }}" required />
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <div class="row mb-6">
                                    <!--begin::Col-->
                                    <div class="col-lg-12">
                                        <!--begin::Row-->
                                        <div class="row">
                                            <!--begin::Col-->
                                            <div class="col-lg-12 fv-row">
                                                <x-form.image-upload label="Icon Kontak" name="image" :value="@$contact->image ?? null"
                                                    nullable='1' />
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                    </div>
                                    <!--end::Col-->
                                </div>
                            </div>
                            <!--end::Card body-->
                            <!--begin::Actions-->
                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <a href="{{ route('contact.index') }}" class="btn btn-secondary btn-sm me-3">Batal</a>
                                <button type="submit" class="btn btn-sm btn-primary"
                                    id="kt_account_profile_details_submit">Simpan</button>
                            </div>
                            <!--end::Actions-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Basic info-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
@endsection
