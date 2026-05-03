@extends('layouts.master', ['main' => 'Data Kategori FAQ', 'title' => request()->routeIs('category-faq.create') ? 'Tambah Kategori FAQ' : 'Edit Kategori FAQ'])
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
                    <div class="card-title m-0">
                        <h3 class="fw-bold m-0">{{ request()->routeIs('category-faq.create') ? 'Tambah Kategori FAQ' : 'Edit Kategori FAQ' }}</h3>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Content-->
                <div id="kt_account_settings_profile_details" class="collapse show">
                    <!--begin::Form-->
                    <form class="form" method="POST" action="{{ request()->routeIs('category-faq.create') ? route('category-faq.store') : route('category-faq.update', @$categoryFaq->id) }}">
                        @csrf
                        @if(!request()->routeIs('category-faq.create'))
                        @method('PUT')
                        @endif
                        <x-alert.alert-validation />
                        <!--begin::Card body-->
                        <div class="card-body">

                            <!-- Nama Kategori dalam Bahasa Indonesia -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nama Kategori</label>
                                <div class="col-lg-12">
                                    <input type="text" name="name" id="name" class="form-control form-control-lg mb-3" placeholder="Nama Kategori" value="{{ @$categoryFaq->name ?? old('name') }}" required />
                                </div>
                            </div>

                            <!-- Nama Kategori dalam Bahasa Inggris -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Nama Kategori (English)</label>
                                <div class="col-lg-10">
                                    <input type="text" name="name_en" id="name_en" class="form-control form-control-lg mb-3" placeholder="Category Name in English" value="{{ @$categoryFaq->name_en ?? old('name_en') }}" />
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" onclick="translateToEnglish()" class="btn btn-translate">Translate to English</button>
                                </div>
                            </div>

                            <!-- Nama Kategori dalam Bahasa Mandarin -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Nama Kategori (Chinese)</label>
                                <div class="col-lg-10">
                                    <input type="text" name="name_cn" id="name_cn" class="form-control form-control-lg mb-3" placeholder="Category Name in Chinese" value="{{ @$categoryFaq->name_cn ?? old('name_cn') }}" />
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" onclick="translateChineseName('#name', '#name_cn')" class="btn btn-translate">Translate to Chinese</button>
                                </div>
                            </div>

                            <!-- Checkbox Show Chat -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Tampilkan Chat CS</label>
                                <div class="col-lg-12">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="show_chat" id="show_chat" value="1" {{ old('show_chat', @$categoryFaq->show_chat) ? 'checked' : '' }} />
                                        <label class="form-check-label" for="show_chat">Ya</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Checkbox Show Complain -->
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Tampilkan Komplain</label>
                                <div class="col-lg-12">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="show_complain" id="show_complain" value="1" {{ old('show_complain', @$categoryFaq->show_complain) ? 'checked' : '' }} />
                                        <label class="form-check-label" for="show_complain">Ya</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--end::Card body-->

                        <!--begin::Actions-->
                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <a href="{{ route('category-faq.index') }}" class="btn btn-secondary btn-sm me-3">Batal</a>
                            <button type="submit" class="btn btn-primary btn-sm" id="kt_account_profile_details_submit">Simpan</button>
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
@push('js')
<script>
    // Fungsi untuk translate ke Bahasa Inggris
    function translateToEnglish() {
        translator('#name', '#name_en');
    }

    // Fungsi untuk translate ke Bahasa Mandarin
    function translateChineseName() {
        translator('#name', '#name_cn');
    }
</script>
@endpush
