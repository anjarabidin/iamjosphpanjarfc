@extends('layouts.master', ['title' => 'Data Slider'])
@section('content')
<!--begin::Content wrapper-->
<div class="d-flex flex-column flex-column-fluid">
    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!--begin::Toolbar container-->
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <!--begin::Page title-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <!--begin::Title-->
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Data Slider</h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('slider.index') }}" class="text-muted text-hover-primary">Menu Slider</a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="breadcrumb-item text-muted">{{ request()->routeIs('slider.create') ? 'Tambah Slider' :
                        'Edit
                        Slider' }}</li>
                    <!--end::Item-->
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Page title-->
        </div>
        <!--end::Toolbar container-->
    </div>
    <!--end::Toolbar-->
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Basic info-->
            <div class="card mb-5 mb-xl-10">
                <!--begin::Card header-->
                <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                    data-bs-target="#kt_account_profile_details" aria-expanded="true"
                    aria-controls="kt_account_profile_details">
                    <!--begin::Card title-->
                    <div class="card-title m-0">
                        <h3 class="fw-bold m-0">{{ request()->routeIs('slider.create') ? 'Tambah Slider' : 'Edit Slider'
                            }}
                        </h3>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--begin::Card header-->
                <!--begin::Content-->
                <div id="kt_account_settings_profile_details" class="collapse show">
                    <!--begin::Form-->
                    <form class="form" method="POST" enctype="multipart/form-data" action="{{
                    request()->routeIs('slider.create') ? route('slider.store') : route('slider.update',
                        @$slider->id) }}">
                        @csrf
                        <x-form.put-method />
                        <x-alert.alert-validation />
                        <!--begin::Card body-->
                        <div class="card-body border-top p-9">

                            <!--begin::Input group-->
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Judul</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-12 fv-row">
                                            <input type="text" name="title" id="title"
                                                class="form-control form-control-solid mb-3 mb-lg-0"
                                                placeholder="Judul Slider Anda"
                                                value="{{ @$slider->title ?? old('title') }}" required />
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>

                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Judul (English)</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-10 fv-row">
                                            <input type="text" name="title_en" id="title_en"
                                                class="form-control form-control-solid mb-3 mb-lg-0"
                                                placeholder="Judul Slider Anda dalam bahasa Inggris"
                                                value="{{ @$slider->title_en ?? old('title_en') }}" />
                                        </div>
                                        <div class="col-lg-2 fv-row">
                                            <button type="button" onclick="translateTitleEnglish()" class="btn btn-translate">Translate English</button>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Judul (Chinese)</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-10 fv-row">
                                            <input type="text" name="title_cn" id="title_cn"
                                                class="form-control form-control-solid mb-3 mb-lg-0"
                                                placeholder="Judul Slider Anda dalam bahasa China"
                                                value="{{ @$slider->title_cn ?? old('title_cn') }}" />
                                        </div>
                                        <div class="col-lg-2 fv-row">
                                            <button type="button" onclick="translateTitleChinese()" class="btn btn-translate">Translate Chinese</button>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>

                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Sub Judul
                                </label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-12 fv-row">
                                            <input type="text" name="subtitle" id="subtitle"
                                                class="form-control form-control-solid mb-3 mb-lg-0"
                                                placeholder="Sub Judul Slider Anda"
                                                value="{{ @$slider->subtitle ?? old('subtitle') }}" />
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Sub Judul
                                    (English)</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-10 fv-row">
                                            <input type="text" name="subtitle_en" id="subtitle_en"
                                                class="form-control form-control-solid mb-3 mb-lg-0"
                                                placeholder="Sub Judul Slider Anda dalam bahasa Inggris"
                                                value="{{ @$slider->subtitle_en ?? old('subtitle_en') }}" />
                                        </div>
                                        <div class="col-lg-2 fv-row">
                                            <button type="button" onclick="translateSubTitleEnglish()" class="btn btn-translate">Translate English</button>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Sub Judul
                                    (Chinese)</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-10 fv-row">
                                            <input type="text" name="subtitle_cn" id="subtitle_cn"
                                                class="form-control form-control-solid mb-3 mb-lg-0"
                                                placeholder="Sub Judul Slider Anda dalam bahasa China"
                                                value="{{ @$slider->subtitle_cn ?? old('subtitle_cn') }}" />
                                        </div>
                                        <div class="col-lg-2 fv-row">
                                            <button type="button" onclick="translateSubTitleChinese()" class="btn btn-translate">Translate Chinese</button>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>

                            <!--begin::Input group-->
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Deskripsi</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-12 fv-row">
                                            <textarea name="description" id="description" cols="30" rows="10"
                                                class="form-control form-control-solid mb-3 mb-lg-0"
                                                placeholder="Deskripsi Slider Anda">{{ @$slider->description
                                                ?? old('description') }}</textarea>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>

                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Deskripsi
                                    (English)</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-10 fv-row">
                                            <textarea name="description_en" id="description_en" cols="30" rows="10"
                                                class="form-control form-control-solid mb-3 mb-lg-0"
                                                placeholder="Deskripsi Slider Anda dalam bahasa Inggris">
                                            {{ @$slider->description_en
                                                ?? old('description_en') }}</textarea>
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
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Deskripsi
                                    (Chinese)</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-10 fv-row">
                                            <textarea name="description_cn" id="description_cn" cols="30" rows="10"
                                                class="form-control form-control-solid mb-3 mb-lg-0"
                                                placeholder="Deskripsi Slider Anda dalam bahasa China">
                                            {{ @$slider->description_cn
                                                ?? old('description_cn') }}</textarea>
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

                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Status</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-12 fv-row">
                                            <select name="is_active" id="is_active"
                                                class="form-select form-select-solid" required>
                                                <option value="">Pilih Status</option>
                                                <option value="1" {{ @$slider->is_active == 1 ? 'selected' : '' }}>
                                                    Aktif</option>
                                                <option value="0" {{ @$slider->is_active == 0 ? 'selected' : '' }}>
                                                    Tidak Aktif</option>
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
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Tipe</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-12 fv-row">
                                            <select name="type" id="type" class="form-select form-select-solid">
                                                <option value="" @if (@$slider->type == '') selected @endif>
                                                    Tidak Ada
                                                </option>
                                                <option value="Membership" @if (@$slider->type == 'Membership')
                                                    selected @endif>
                                                    Membership
                                                </option>
                                                <option value="PersonalTrainer" @if (@$slider->type ==
                                                    'PersonalTrainer') selected
                                                    @endif>
                                                    Personal Trainer
                                                </option>
                                                <option value="GymClassBundling" @if (@$slider->type ==
                                                    'GymClassBundling') selected
                                                    @endif>
                                                    Personal Trainer Plus
                                                </option>
                                                <option value="Article" @if (@$slider->type == 'Article') selected
                                                    @endif>
                                                    Artikel
                                                </option>
                                            </select>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>

                            <div id="reference_input"
                                class="row mb-6 reference_input {{ @$slider->type == 'All' || @$slider->type == 'PersonalTrainer' ? 'd-none' : '' }}">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Slider Untuk</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-12 fv-row">
                                            <select name="reference_id" id="reference_id"
                                                class="form-select form-select-solid" data-control="select2"
                                                data-allow-clear="true">
                                                <option value="">Pilih Slider Untuk</option>

                                            </select>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>
                            @if (request()->routeIs('slider.edit'))
                            <input type="text" name="reference_value_id" id="reference_value_id"
                                value="{{ @$slider->reference_id }}" hidden>
                            @endif

                            <div class="row mb-6">
                                <div class="col-lg-12 fv-row">
                                    <x-form.image-upload label="Foto Slider (Wajib Diisi)" name="image"
                                        :value="@$slider->image ?? null" nullable='1' />
                                </div>
                                <!--end::Col-->
                            </div>
                        </div>
                        <!--end::Card body-->
                        <!--begin::Actions-->
                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <a href="{{ route('slider.index') }}"
                                class="btn btn-light btn-active-light-primary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary"
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
    $('#title').on('change', () => translate('#title', '#title_en'));
    $('#subtitle').on('change', () => translate('#subtitle', '#subtitle_en'));
</script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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
        selector: '#description, #description_en, #description_cn',
        height: 500,
        menubar: false,
        toolbar: ["styleselect fontselect fontsizeselect",
            "undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify",
            "bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code"
        ],
        plugins: "advlist autolink link image lists charmap print preview code"
    });

    function translateDescriptionEnglish() {
        let content = tinymce.get('description').getContent();
        
        $.ajax({
            type: 'POST',
            url: "{{ route('translate_post') }}",
            data: {
                text: content
            },
            cache: false,
            success: function(msg) {
                tinymce.get('description_en').setContent(msg);
            },
            error: function(data) {
                console.log('error:', data)
            },
        });
    }

    function translateDescriptionChinese() {
        let content = tinymce.get('description').getContent();
        $.ajax({
            type: 'POST',
            url: "{{ route('translate_post.chinese') }}",
            data: {
                text: content
            },
            cache: false,
            success: function(msg) {
                tinymce.get('description_cn').setContent(msg);
            },
            error: function(data) {
                console.log('error:', data)
            },
        });
    }
</script>
<script>
    $(document).ready(() => {
        let url = "{{ route('slider.get-reference', ':type') }}";

        // onload page check if type is All or PersonalTrainer
        checkType();

        $('#type').on('change', function () {
            checkType();
        });

        function checkType() {
            let type = $('#type').val();
            let reference_id = $('#reference_value_id').val();

            if (type === '' || type === 'PersonalTrainer') {
                $('#reference_id').html(''); // Clear reference_id options
                $('#reference_input').addClass('d-none'); // Hide reference_id input
            } else {
                $('#reference_id').html(''); // Clear reference_id options
                // show reference_id clear class d-none
                $('#reference_input').removeClass('d-none');

                url = "{{ route('slider.get-reference', ':type') }}";
                url = url.replace(':type', type);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#reference_id').append('<option value="">--Pilih Slider Untuk--</option>');
                        $.each(data.data, function (key, value) {
                            $('#reference_id').append('<option value="' + value.id + '" ' + (reference_id == value.id ? 'selected' : '') + '>' + value.name + '</option>');
                        });
                    }
                });
            }
        }
    });
</script>
<script>
    function translateTitleEnglish() {
        translate('#title', '#title_en');
    }
    
    function translateTitleChinese() {
        translateChinese('#title', '#title_cn');
    }

    function translateSubTitleEnglish() {
        translate('#subtitle', '#subtitle_en');
    }
    
    function translateSubTitleChinese() {
        translateChinese('#subtitle', '#subtitle_cn');
    }
    
</script>
@endpush
