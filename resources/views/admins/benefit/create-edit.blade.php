@extends('layouts.master', ['main' => 'Data Benefit', 'title' => request()->routeIs('benefit.create') ? 'Tambah Benefit' : 'Edit Benefit'])

@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <div class="col-xl-12">
                <div class="card h-lg-100" id="kt_contacts_main">
                    <div class="card-header" id="kt_chat_contacts_header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">
                                {{ request()->routeIs('benefit.create') ? 'Tambah Benefit' : 'Edit Benefit' }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <x-alert.alert-validation />
                        <form id="benefit"
                            action="{{ request()->routeIs('benefit.create') ? route('benefit.store') : route('benefit.update', @$benefit->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />

                            {{-- Gambar --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <x-form.image-upload label="Gambar 1" maxSize="2MB" name="image" :value="@$benefit->image ?? null"
                                        id="image" nullable='1' />
                                </div>
                                <div class="col-md-4">
                                    <x-form.image-upload label="Gambar 2" maxSize="2MB" name="image_2" :value="@$benefit->image_2 ?? null"
                                        id="image_2" nullable='1' />
                                </div>
                                <div class="col-md-4">
                                    <x-form.image-upload label="Gambar 3" maxSize="2MB" name="image_3" :value="@$benefit->image_3 ?? null"
                                        id="image_3" nullable='1' />
                                </div>
                            </div>

                            {{-- Judul --}}
                            <div class="row">
                                <div class="col-md-10">
                                    <label for="title" class="fs-6 fw-bold form-label mt-3">
                                        <span class="text-dark">Judul (ID)</span>
                                    </label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ old('title', @$benefit->title) }}" placeholder="Masukkan Judul"
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
                                    value="{{ old('title_en', @$benefit->title_en) }}"
                                    placeholder="Enter Title in English" />
                            </div>

                            {{-- SubJudul --}}
                            <div class="row">
                                <div class="col-md-10">
                                    <label for="subtitle" class="fs-6 fw-bold form-label mt-3">
                                        <span class="text-dark">SubJudul (ID)</span>
                                    </label>
                                    <textarea class="form-control" id="subtitle" name="subtitle">{{ old('subtitle', @$benefit->subtitle) }}</textarea>
                                </div>
                                <div class="col-md-2 mt-8">
                                    <button type="button" class="btn btn-info btn-sm w-100"
                                        onclick="translateSubTitleEnglish()">
                                        Translate
                                    </button>
                                </div>
                            </div>
                            <div class="fv-row mb-6">
                                <label for="subtitle_en" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">SubJudul (EN)</span>
                                </label>
                                <textarea class="form-control" id="subtitle_en" name="subtitle_en">{{ old('subtitle_en', @$benefit->subtitle_en) }}</textarea>
                            </div>

                            {{-- Teks Tombol --}}
                            <div class="row">
                                <div class="col-md-10">
                                    <label for="button_text" class="fs-6 fw-bold form-label mt-3">
                                        <span class="text-dark">Teks Tombol (ID)</span>
                                    </label>
                                    <input type="text" class="form-control" id="button_text" name="button_text"
                                        value="{{ old('button_text', @$benefit->button_text) }}"
                                        placeholder="Masukkan Teks Tombol" required />
                                </div>
                                <div class="col-md-2 mt-8">
                                    <button type="button" class="btn btn-info btn-sm w-100"
                                        onclick="translateButtonTextEnglish()">
                                        Translate
                                    </button>
                                </div>
                            </div>
                            <div class="fv-row mb-6">
                                <label for="button_text_en" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Teks Tombol (EN)</span>
                                </label>
                                <input type="text" class="form-control" id="button_text_en" name="button_text_en"
                                    value="{{ old('button_text_en', @$benefit->button_text_en) }}"
                                    placeholder="Enter Button Text in English" />
                            </div>

                            {{-- Urutan --}}
                            <div class="fv-row mb-6">
                                <label for="order" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Urutan</span>
                                </label>
                                <input type="number" class="form-control" id="order" name="order"
                                    value="{{ old('order', @$benefit->order) }}" placeholder="Masukkan Urutan" required />
                            </div>

                            {{-- Link --}}
                            <div class="fv-row mb-6">
                                <label for="link" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Tautan Link (URL)</span>
                                </label>
                                <input type="url" class="form-control" id="link" name="url"
                                    value="{{ old('url', @$benefit->url) }}" placeholder="Masukkan Tautan Link"
                                    required />
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('benefit.index') }}">
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
    <script>
        function translateTitleEnglish() {
            translate('#title', '#title_en');
        }

        function translateSubTitleEnglish() {
            translate('#subtitle', '#subtitle_en');
        }

        function translateButtonTextEnglish() {
            translate('#button_text', '#button_text_en');
        }
    </script>
@endpush
