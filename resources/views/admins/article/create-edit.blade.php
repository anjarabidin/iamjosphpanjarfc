@extends('layouts.master', [
    'main' => 'Tips & Artikel',
    'title' => request()->routeIs('article.create')
        ? 'Tambah Tips &
Artikel'
        : 'Edit Tips & Artikel',
])
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
    <!--begin::Content-->
    <div class="conten pt-6 t d-flex flex-column flex-column-fluid" id="kt_content">
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
                                    class="card-label fw-bold fs-3">{{ request()->routeIs('article.create')
                                        ? 'Tambah Tips & Artikel'
                                        : 'Edit Tips &
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                Artikel' }}
                                </span>
                            </h3>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body">
                            <!--begin::Form-->
                            <x-alert.alert-validation />
                            <form class="form"
                                action="{{ request()->routeIs('article.create') ? route('article.store') : route('article.update', $article->id) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <x-form.put-method />
                                <div class="form-group">
                                    <!--begin::Label-->
                                    <x-form.image-upload label="Thumbnail" name="thumbnail" :value="@$article->thumbnail ?? null" />
                                </div>
                                <!--begin::Input group-->
                                <div class="fv-row mb-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold form-label mt-3" for="name">
                                        <span class="required text-dark">Kategori</span>
                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                            title="Pilih Kategori"></i>
                                    </label>
                                    <!--end::Label-->
                                    <select name="article_category_id" class="form-control" required>
                                        <option value="">--Pilih Kategori--</option>
                                        @foreach ($articleCategories as $articleCategory)
                                            <option
                                                {{ $articleCategory->id == @$article?->article_category_id ? 'selected' : '' }}
                                                value="{{ $articleCategory->id }}">{{ $articleCategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold form-label mt-3" for="name">
                                        <span class="required text-dark">Title</span>
                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                            title="Input Title"></i>
                                    </label>
                                    <!--end::Label-->
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <!--begin::Input-->
                                            <input type="text" id="title" placeholder="Title Indonesia"
                                                class="form-control" name="title"
                                                value="{{ old('title', @$article->title) }}" required />
                                            <!--end::Input-->
                                        </div>
                                        <div class="col-sm-6 en-feature">
                                            <!--begin::Input-->
                                            <input type="text" id="title_en" placeholder="Title Inggris"
                                                class="form-control" name="title_en"
                                                value="{{ old('title', @$article->title_en) }}" />
                                            <!--end::Input-->
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold form-label mt-3" for="name">
                                        <span class="required text-dark">Konten</span>
                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                            title="Input Konten"></i>
                                    </label>
                                    <!--end::Label-->
                                    <div class="row">
                                        <div class="lg-12">
                                            <!--begin::Input-->
                                            <textarea placeholder="Konten Indonesia" class="form-control" id="content" name="content">{{ old('content', @$article->content) }}</textarea>
                                            <!--end::Input-->
                                        </div>
                                        <div class="lg-12 mb-4 en-feature">
                                            <!--begin::Input-->
                                            <textarea placeholder="Konten Inggris" class="form-control" id="content_en" name="content_en">{{ old('content_end', @$article->content_en) }}</textarea>
                                            <!--end::Input-->
                                        </div>
                                    </div>
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold form-label mt-3" for="tag">
                                        <span class="required text-dark">Tags</span>
                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                            title="Pilih Tag"></i>
                                    </label>
                                    <!--end::Label-->
                                    <select name="tag[]" id="tag" multiple class="form-control select2" required>
                                        @foreach ($tags as $tag)
                                            <option
                                                {{ in_array($tag->name, @$article?->article_tags?->pluck('name')?->toArray() ?? []) ? 'selected' : '' }}
                                                value="{{ $tag->name }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="fv-row mb-6 en-feature">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold form-label mt-3" for="en_tag">
                                        <span class="required text-dark">Tags (en)</span>
                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                            title="Pilih Tag"></i>
                                    </label>
                                    <!--end::Label-->
                                    <select name="en_tag[]" id="en_tag" multiple class="form-control select2">
                                        @foreach ($enTags as $tag)
                                            <option
                                                {{ in_array($tag->name, @$article?->en_article_tags?->pluck('name')?->toArray() ?? []) ? 'selected' : '' }}
                                                value="{{ $tag->name }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!--end::Input group-->
                                @if (route('article.store'))
                                    <input type="checkbox" name="is_send_notification" value="1">
                                    Kirimkan Notifikasi ke semua user ?
                                @endif
                                <!--begin::Separator-->
                                <div class="separator mb-6 mt-2"></div>
                                <!--end::Separator-->
                                <!--begin::Action buttons-->
                                <div class="d-flex justify-content-end">
                                    <!--begin::Button-->
                                    <a href="{{ route('article.index') }}">
                                        <button type="button" class="btn btn-sm btn-secondary me-3">Batal</button>
                                    </a>
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <button type="submit" data-kt-contacts-type="submit" class="btn btn-sm btn-primary">
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
            setup: function(ed) {
                ed.on('change', delay(function(e) {
                    let content = ed.getContent();
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('translator.post') }}",
                        data: {
                            translate: content
                        },
                        cache: false,
                        success: function(msg) {
                            tinymce.get('content_en').setContent(msg);
                        },
                        error: function(data) {
                            console.log('error:', data)
                        },
                    })
                }, 2000));
            },
            selector: '#content, #content_en',
            height: 600,
            branding: false,
            menubar: false,
            toolbar: ["styleselect fontselect fontsizeselect",
                "undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify",
                "bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code"
            ],
            plugins: "advlist autolink link image lists charmap print preview code"
        });
    </script>
    <script>
        $('#title').on('change', () => translate('#title', '#title_en'));
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('#tag').select2({
            placeholder: 'Pilih atau buat Tag',
            tags: true
        });
        $('#en_tag').select2({
            placeholder: 'Pilih atau buat Tag',
            tags: true
        });
    </script>
@endpush
