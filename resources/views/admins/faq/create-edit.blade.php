@extends('layouts.master', ['main' => 'Data Faq', 'title' => request()->routeIs('faq.create') ? 'Tambah Faq' : 'Edit Faq'])
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
                        <h3 class="fw-bold m-0">{{ request()->routeIs('faq.create') ? 'Tambah Faq' : 'Edit Faq'
                            }}
                        </h3>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--begin::Card header-->
                <!--begin::Content-->
                <div id="kt_account_settings_profile_details" class="collapse show">
                    <!--begin::Form-->
                    <form class="form" method="POST" enctype="multipart/form-data" action="{{ request()->routeIs('faq.create') ? route('faq.store') : route('faq.update',
                        @$faq->id) }}">
                        @csrf
                        <x-form.put-method />
                        <x-alert.alert-validation />
                        <!--begin::Card body-->
                        <div class="card-body">

                            <!--begin::Input group-->
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Pertanyaan</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-12 fv-row">
                                            <input type="text" name="question" id="question"
                                                class="form-control form-control-lg mb-3 mb-lg-0"
                                                placeholder="Pertanyaan Anda"
                                                value="{{ @$faq->question ?? old('question') }}" required />
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Kategori</label>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row">
                                            <select name="category_id" class="form-select form-select-lg mb-3 mb-lg-0" required>
                                                <option value="">Pilih Kategori</option>
                                                @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ (isset($faq) && $faq->category_id == $category->id) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Pertanyaan
                                    (English)</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-10 fv-row">
                                            <input type="text" name="question_en" id="question_en"
                                                class="form-control form-control-lg mb-3 mb-lg-0"
                                                placeholder="Pertanyaan Anda"
                                                value="{{ @$faq->question_en ?? old('question_en') }}" required />
                                        </div>
                                        <div class="col-2">
                                            <button type="button" onclick="translateQuestionEnglish()" class="btn btn-translate">Translate English</button>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Pertanyaan
                                    (Chinese)</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-10 fv-row">
                                            <input type="text" name="question_cn" id="question_cn"
                                                class="form-control form-control-lg mb-3 mb-lg-0"
                                                placeholder="Pertanyaan Anda"
                                                value="{{ @$faq->question_cn ?? old('question_cn') }}" required />
                                        </div>
                                        <div class="col-lg-2">
                                            <button type="button" onclick="translateQuestionChinese()" class="btn btn-translate">Translate Chinese</button>
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
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Jawaban</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-12 fv-row">
                                            <textarea name="answer" id="answer" cols="30" rows="10"
                                                class="form-control form-control-lg mb-3 mb-lg-0"
                                                placeholder="Jawaban Anda"
                                                required>{{ @$faq->answer ?? old('answer') }}</textarea>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>

                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Jawaban
                                    (English)</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-10 fv-row">
                                            <textarea name="answer_en" id="answer_en" cols="30" rows="10"
                                                class="form-control form-control-lg mb-3 mb-lg-0"
                                                placeholder="Jawaban Anda"
                                                required>{{ @$faq->answer_en ?? old('answer_en') }}</textarea>
                                        </div>
                                        <div class="col-lg-2">
                                            <button type="button" onclick="translateAnswerEnglish()" class="btn btn-translate">Translate English</button>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Col-->
                            </div>

                            <div class="row mb-6">
                                <!--begin::Label-->
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Jawaban
                                    (Chinese)</label>
                                <!--end::Label-->
                                <!--begin::Col-->
                                <div class="col-lg-12">
                                    <!--begin::Row-->
                                    <div class="row">
                                        <!--begin::Col-->
                                        <div class="col-lg-10 fv-row">
                                            <textarea name="answer_cn" id="answer_cn" cols="30" rows="10"
                                                class="form-control form-control-lg mb-3 mb-lg-0"
                                                placeholder="Jawaban Anda"
                                                required>{{ @$faq->answer_cn ?? old('answer_cn') }}</textarea>
                                        </div>
                                        <div class="col-lg-2">
                                            <button type="button" onclick="translateAnswerChinese()" class="btn btn-translate">Translate Chinese</button>
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
                            <a href="{{ route('faq.index') }}"
                                class="btn btn-secondary btn-sm me-3">Batal</a>
                            <button type="submit" class="btn btn-primary btn-sm"
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
<script>
    // $('#question').on('change', () => translate('#question', '#question_en'));
    // $('#answer').on('change', () => translate('#answer', '#answer_en'));
    function translateQuestionEnglish() {
        translator('#question', '#question_en');
    }

    function translateQuestionChinese() {
        translateChinesePost('#question', '#question_cn');
    }

    function translateAnswerEnglish() {
        translator('#answer', '#answer_en');
    }

    function translateAnswerChinese() {
        translateChinesePost('#answer', '#answer_cn');
    }
</script>
@endpush
