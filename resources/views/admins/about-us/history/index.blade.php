@extends('layouts.master', ['main' => 'Data Tentang Kami', 'title' => 'Sejarah'])

@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <div class="col-xl-12">
                <div class="card h-lg-100" id="kt_contacts_main">
                    <div class="card-header" id="kt_chat_contacts_header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">
                                Sejarah Tentang Kami
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <x-alert.alert-validation />
                        <form id="aboutus-history" action="{{ route('aboutus-history.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />

                            {{-- Gambar --}}
                            <div>
                                <div class="fv-row mb-6">
                                    <x-form.image-upload label="Gambar" maxSize="2MB" name="image" :value="@$aboutUsHistory->image ?? null"
                                        id="image" nullable='1' />
                                </div>
                            </div>

                            {{-- Judul --}}
                            <div class="row">
                                <div class="col-md-10">
                                    <label for="title" class="fs-6 fw-bold form-label mt-3">
                                        <span class="text-dark">Judul (ID)</span>
                                    </label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ old('title', @$aboutUsHistory->title) }}" placeholder="Masukkan Judul"
                                        required />
                                </div>
                                <div class="col-md-2 mt-8">
                                    <button type="button" class="btn btn-info btn-sm w-100"
                                        onclick="translateTitleEnglish()">
                                        Translate
                                    </button>
                                </div>
                            </div>
                            <div class="fv-row mb-6">
                                <label for="title_en" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Judul (EN)</span>
                                </label>
                                <input type="text" class="form-control" id="title_en" name="title_en"
                                    value="{{ old('title_en', @$aboutUsHistory->title_en) }}"
                                    placeholder="Enter Title in English" />
                            </div>

                            {{-- SubJudul --}}
                            <div class="row">
                                <div class="col-md-10">
                                    <label for="content" class="fs-6 fw-bold form-label mt-3">
                                        <span class="text-dark">Konten (ID)</span>
                                    </label>
                                    <textarea class="form-control" id="content" name="content">{{ old('content', @$aboutUsHistory->content) }}</textarea>
                                </div>
                                <div class="col-md-2 mt-8">
                                    <button type="button" class="btn btn-info btn-sm w-100"
                                        onclick="translateSubTitleEnglish()">
                                        Translate
                                    </button>
                                </div>
                            </div>
                            <div class="fv-row mb-6">
                                <label for="content_en" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Konten (EN)</span>
                                </label>
                                <textarea class="form-control" id="content_en" name="content_en">{{ old('content_en', @$aboutUsHistory->content_en) }}</textarea>
                            </div>
                            <input type="hidden" name="id" value="{{ old('id', @$aboutUsHistory->id) }}" />
                            {{-- Tombol Aksi --}}
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('aboutus-history.index') }}">
                                    <button type="button" class="btn btn-secondary me-3">Batal</button>
                                </a>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <span class="indicator-label">Simpan</span>
                                    <span class="indicator-progress">Mohon Tunggu...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('assets/plugins/custom/tinymce/tinymce.bundle.js') }}"></script>
    <script>
        function translateTitleEnglish() {
            translate('#title', '#title_en');
        }

        function translateSubTitleEnglish() {
            translate('#subtitle', '#subtitle_en');
        }
    </script>
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
@endpush
