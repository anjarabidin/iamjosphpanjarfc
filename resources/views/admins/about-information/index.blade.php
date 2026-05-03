@extends('layouts.master', ['title' => 'Tentang Aplikasi', 'main' => 'Dashboard'])
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
                        <h3 class="fw-bold m-0">{{ request()->routeIs('about-information.create') ? 'Tambah Tentang
                            Aplikasi' :
                            'Edit Tentang Aplikasi'
                            }}
                        </h3>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--begin::Card header-->
                <!--begin::Content-->
                <div id="kt_account_settings_profile_details" class="collapse show">
                    <!--begin::Form-->
                    <form class="form" method="POST" enctype="multipart/form-data"
                        action="{{ route('about-information.store') }}">
                        @csrf
                        <x-form.put-method />
                        <x-alert.alert-validation />
                        <!--begin::Card body-->
                        <div class="card-body">

                            <!--begin::Input group-->
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Tentang
                                    Aplikasi</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-12 fv-row">
                                            <textarea name="content" id="content" cols="30" rows="10"
                                                class="form-control form-control-lg mb-3 mb-lg-0"
                                                placeholder="Tentang Aplikasi Nest GYM">{{ @$aboutInformation->content ??
                                                 old('content') }}</textarea>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>

                            <div class="row mb-6 en-feature">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Tentang Aplikasi
                                    (English)</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-10 fv-row">
                                            <textarea name="content_en" id="content_en" cols="30" rows="10"
                                                class="form-control form-control-lg mb-3 mb-lg-0"
                                                placeholder="Tentang Aplikasi Nest GYM (English)">{{
                                                @$aboutInformation->content_en ??
                                                 old('content_en') }}</textarea>
                                        </div>
                                        <div class="col-lg-2 fv-row">
                                            <button type="button" onclick="translateDescriptionEnglish()" class="btn btn-translate">Translate English</button>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>

                            <div class="row mb-6 en-feature">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Tentang Aplikasi
                                    (Chinese)</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-10 fv-row">
                                            <textarea name="content_cn" id="content_cn" cols="30" rows="10"
                                                class="form-control form-control-lg mb-3 mb-lg-0"
                                                placeholder="Tentang Aplikasi Nest GYM (Chinese)">{{
                                                @$aboutInformation->content_cn ??
                                                 old('content_cn') }}</textarea>
                                        </div>
                                        <div class="col-lg-2 fv-row">
                                            <button type="button" onclick="translateDescriptionChinese()" class="btn btn-translate">Translate Chinese</button>
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
                            <a href="{{ route('about-information.index') }}"
                                class="btn btn-secondary btn-sm me-3">Batal</a>
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
@push('js')
<script src="{{ asset('assets/plugins/custom/tinymce/tinymce.bundle.js') }}"></script>
<script>
    function delay(callback, ms) {
    var timer = 0;
    return function() {
    var context = this,
    args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function() {
    callback.apply(context, args);
    }, ms || 0);
    };
    }
    
    tinymce.init({
        selector: '#content, #content_en, #content_cn',
        height: 500,
        menubar: false,
        branding: false,
        toolbar: ["styleselect fontselect fontsizeselect",
        "undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify",
        "bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | code"
        ],
        plugins: "advlist autolink link image lists charmap print preview code"
    });

    function translateDescriptionEnglish() {
        let content = tinymce.get('content').getContent();
        
        $.ajax({
            type: 'POST',
            url: "{{ route('translate_post') }}",
            data: {
                text: content
            },
            cache: false,
            success: function(msg) {
                tinymce.get('content_en').setContent(msg);
            },
            error: function(data) {
                console.log('error:', data)
            },
        });
    }

    function translateDescriptionChinese() {
        let content = tinymce.get('content').getContent();
        $.ajax({
            type: 'POST',
            url: "{{ route('translate_post.chinese') }}",
            data: {
                text: content
            },
            cache: false,
            success: function(msg) {
                tinymce.get('content_cn').setContent(msg);
            },
            error: function(data) {
                console.log('error:', data)
            },
        });
    }
</script>
@endpush
