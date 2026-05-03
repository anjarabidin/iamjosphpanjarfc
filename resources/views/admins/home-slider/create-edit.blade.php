@extends('layouts.master', ['main' => 'Data Slider', 'title' => request()->routeIs('home-slider.create') ? 'Tambah Slider' : 'Edit Slider'])

@push('css')
    <style>
        .dropzone-wrapper {
            position: relative;
        }

        .dropzone-area {
            border: 2px dashed #e1e3ea;
            border-radius: 0.75rem;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .dropzone-area:hover {
            border-color: #009ef7;
            background-color: #f1faff;
        }

        .dropzone-area.dragover {
            border-color: #009ef7;
            background-color: #e8f4fd;
            transform: scale(1.02);
        }

        .media-preview-item {
            position: relative;
            display: inline-block;
        }

        .media-preview-item .remove-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            z-index: 10;
        }

        .form-check-input:checked~.form-check-label .status-text::before {
            content: "Aktif";
        }

        .form-check-input:not(:checked)~.form-check-label .status-text::before {
            content: "Tidak Aktif";
        }
    </style>
@endpush
@section('content')
    <!--begin::Content wrapper-->
    <!--begin::Content container-->
    <div id="kt_app_content_container" class="app-container container-xxl">
        <!--begin::Form-->
        <form class="form" method="POST" enctype="multipart/form-data" id="slider-form"
            action="{{ request()->routeIs('home-slider.create') ? route('home-slider.store') : route('home-slider.update', @$homeSlider->id) }}">
            @csrf
            <x-form.put-method />
            <x-alert.alert-validation />

            <div class="row g-5 g-xl-10">
                <!--begin::Col-->
                <div class="col-xl-8">
                    <!--begin::Basic info-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                            data-bs-target="#kt_slider_basic_info" aria-expanded="true"
                            aria-controls="kt_slider_basic_info">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Informasi Dasar</h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->

                        <!--begin::Content-->
                        <div id="kt_slider_basic_info" class="collapse show">
                            <!--begin::Card body-->
                            <div class="card-body border-top p-9">
                                <!--begin::Input group-->
                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Judul
                                        Slider</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8">
                                        <input type="text" name="title" id="title"
                                            class="form-control form-control-lg form-control-solid"
                                            placeholder="Masukkan judul slider yang menarik"
                                            value="{{ @$homeSlider->title ?? old('title') }}" required />
                                        <div class="form-text">Judul akan ditampilkan sebagai heading utama slider
                                        </div>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Sub
                                        Judul</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8">
                                        <input type="text" name="description" id="description"
                                            class="form-control form-control-lg form-control-solid"
                                            placeholder="Masukkan deskripsi singkat slider"
                                            value="{{ @$homeSlider->description ?? old('description') }}" required />
                                        <div class="form-text">Deskripsi pendek yang menjelaskan konten slider</div>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Teks
                                        Tombol</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8">
                                        <input type="text" name="button_text" id="button_text"
                                            class="form-control form-control-lg form-control-solid"
                                            placeholder="Contoh: Pelajari Lebih Lanjut"
                                            value="{{ @$homeSlider->button_text ?? old('button_text') }}" required />
                                        <div class="form-text">Teks yang akan ditampilkan pada tombol call-to-action
                                        </div>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Input group-->

                                <!--begin::Input group-->
                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Link
                                        Tombol</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-8">
                                        <div class="input-group input-group-solid">
                                            <span class="input-group-text">
                                                <i class="ki-duotone ki-link fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                            <input type="url" name="button_link" id="button_link"
                                                class="form-control form-control-lg" placeholder="https://example.com"
                                                value="{{ @$homeSlider->button_link ?? old('button_link') }}" required />
                                        </div>
                                        <div class="form-text">URL tujuan ketika tombol diklik</div>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Basic info-->

                    <!--begin::Media Upload-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header border-0">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Media Slider</h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->

                        <!--begin::Card body-->
                        <div class="card-body border-top p-9">
                            <!--begin::Media Type Selection-->
                            <div class="row mb-6">
                                <div class="col-lg-12">
                                    <div class="d-flex flex-stack">
                                        <div class="d-flex">
                                            <div class="symbol symbol-40px me-4">
                                                <div class="symbol-label bg-light-primary">
                                                    <i class="ki-duotone ki-picture fs-2 text-primary">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <span class="fw-semibold fs-6 text-gray-800">Upload Media</span>
                                                <span class="fw-semibold fs-7 text-muted">Pilih gambar atau video
                                                    untuk slider</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Media Type Selection-->

                            <!--begin::Upload Area-->
                            <div class="row mb-6">
                                <div class="col-lg-12">
                                    <!--begin::Dropzone-->
                                    <div class="dropzone-wrapper">
                                        <div class="dropzone-area" id="media-dropzone">
                                            <div class="dropzone-content text-center p-8">
                                                <i class="ki-duotone ki-file-up fs-3x text-primary mb-4">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <div class="mb-4">
                                                    <h4 class="fs-5 fw-bold text-gray-900 mb-2">Drop files here
                                                        atau klik untuk upload</h4>
                                                    <span class="fw-semibold fs-7 text-gray-500">
                                                        Upload gambar atau video untuk slider
                                                    </span>
                                                </div>
                                                <input type="file" name="media" id="media"
                                                    class="form-control mb-3" accept="image/*,video/*" hidden />
                                                <button type="button" class="btn btn-primary" id="select-file-btn">
                                                    <i class="ki-duotone ki-folder-up fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Pilih File
                                                </button>

                                                <!-- Debug buttons -->
                                                {{-- <div class="mt-3 d-flex gap-2">
                                                            <button type="button" class="btn btn-sm btn-secondary"
                                                                id="test-file-btn">
                                                                Test File Input
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-info"
                                                                id="debug-elements-btn">
                                                                Debug Elements
                                                            </button>
                                                        </div> --}}

                                                <!-- Fallback: Simple file input -->
                                                {{-- <div class="mt-3">
                                                            <small class="text-muted">Atau gunakan input file
                                                                sederhana:</small><br>
                                                            <input type="file" name="media_fallback"
                                                                id="media-fallback"
                                                                class="form-control form-control-sm mt-2"
                                                                accept="image/*,video/*" style="max-width: 300px;" />
                                                        </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Dropzone-->

                                    <!--begin::File Info-->
                                    <div class="mt-4">
                                        <div
                                            class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                                            <i class="ki-duotone ki-information-5 fs-2tx text-info me-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">Format yang Didukung</h4>
                                                    <div class="fs-6 text-gray-700">
                                                        <strong>Gambar:</strong> JPEG, PNG, JPG, GIF, WebP (max
                                                        50MB)<br>
                                                        <strong>Video:</strong> MP4, AVI, MOV, WMV, FLV (max 50MB)
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::File Info-->
                                </div>
                            </div>
                            <!--end::Upload Area-->

                            <!--begin::Media Preview-->
                            <div id="media-preview" class="row mb-6" style="display: none;">
                                <div class="col-lg-12">
                                    <div class="card bg-light-success">
                                        <div class="card-header border-0 pt-6">
                                            <h3 class="card-title align-items-start flex-column">
                                                <span class="card-label fw-bold fs-3 mb-1">Preview Media
                                                    Baru</span>
                                                <span class="text-muted mt-1 fw-semibold fs-7" id="file-info"></span>
                                            </h3>
                                            <div class="card-toolbar">
                                                <button type="button" class="btn btn-sm btn-light-danger"
                                                    id="clear-media-btn">
                                                    <i class="ki-duotone ki-trash fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                        <span class="path5"></span>
                                                    </i>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div id="preview-container" class="text-center"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Media Preview-->

                            <!--begin::Current Media-->
                            @if (isset($homeSlider) && $homeSlider->media)
                                <div id="current-media" class="row mb-6">
                                    <div class="col-lg-12">
                                        <div class="card bg-light-primary">
                                            <div class="card-header border-0 pt-6">
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label fw-bold fs-3 mb-1">Media Saat
                                                        Ini</span>
                                                    <span class="text-muted mt-1 fw-semibold fs-7">
                                                        {{ $homeSlider->isImage() ? 'Gambar' : 'Video' }} yang
                                                        sedang digunakan
                                                    </span>
                                                </h3>

                                            </div>
                                            <div class="card-body pt-0">
                                                <div class="text-center">
                                                    @if ($homeSlider->isImage())
                                                        <img src="{{ $homeSlider->media_url }}" alt="Current Slider Media"
                                                            class="img-fluid rounded shadow-sm"
                                                            style="max-height: 300px;" />
                                                    @elseif($homeSlider->isVideo())
                                                        <video controls class="rounded shadow-sm"
                                                            style="max-height: 300px; max-width: 100%;">
                                                            <source src="{{ $homeSlider->media_url }}" type="video/mp4">
                                                            Browser Anda tidak mendukung video tag.
                                                        </video>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <!--end::Current Media-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Media Upload-->
                </div>
                <!--end::Col-->

                <!--begin::Col-->
                <div class="col-xl-4">
                    <!--begin::Settings-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header border-0">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Pengaturan Slider</h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->

                        <!--begin::Card body-->
                        <div class="card-body border-top p-9">
                            <!--begin::Status-->
                            <div class="row mb-8">
                                <div class="col-lg-12">
                                    <label class="fw-semibold fs-6 mb-2">Status Publikasi</label>
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                            value="1"
                                            {{ (isset($homeSlider) && $homeSlider->is_active) || old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-gray-400 ms-3" for="is_active">
                                            <span class="status-text">
                                                {{ (isset($homeSlider) && $homeSlider->is_active) || old('is_active', true) ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </label>
                                    </div>
                                    <div class="form-text">Slider hanya akan ditampilkan jika status aktif</div>
                                </div>
                            </div>
                            <!--end::Status-->

                            <!--begin::Sort Order-->
                            <div class="row mb-8">
                                <div class="col-lg-12">
                                    <label class="fw-semibold fs-6 mb-2">Urutan Tampil</label>
                                    <div class="input-group input-group-solid">
                                        <span class="input-group-text">
                                            <i class="ki-duotone ki-sort-up-down fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                        <input type="number" name="sort_order" id="sort_order" class="form-control"
                                            min="0" placeholder="0"
                                            value="{{ @$homeSlider->sort_order ?? old('sort_order', 0) }}" />
                                    </div>
                                    <div class="form-text">Angka yang lebih kecil akan ditampilkan lebih dulu</div>
                                </div>
                            </div>
                            <!--end::Sort Order-->

                            <!--begin::Preview-->
                            <div class="row mb-8">
                                <div class="col-lg-12">
                                    <div
                                        class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                                        <i class="ki-duotone ki-information-5 fs-2tx text-warning me-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <h4 class="text-gray-900 fw-bold">Tips</h4>
                                                <div class="fs-6 text-gray-700">
                                                    • Gunakan gambar dengan resolusi tinggi (min 1920x1080)<br>
                                                    • Video sebaiknya tidak lebih dari 30 detik<br>
                                                    • Pastikan konten sesuai dengan target audience
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Preview-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Settings-->
                </div>
                <!--end::Col-->
            </div>

            <!--begin::Actions-->
            <div class="d-flex justify-content-end">
                <a href="{{ route('home-slider.index') }}" class="btn btn-light me-5">
                    <i class="ki-duotone ki-arrow-left fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <span class="indicator-label">
                        <i class="ki-duotone ki-check fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Simpan Slider
                    </span>
                    <span class="indicator-progress">
                        Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
            <!--end::Actions-->
        </form>
        <!--end::Form-->
    </div>
    <!--end::Content container-->
    <!--end::Content wrapper-->
@endsection

@push('js')
    <script>
        // Debug functions (global scope)
        function testFileInput() {
            const mediaInput = document.getElementById('media');
            console.log('Testing file input:', mediaInput);
            if (mediaInput) {
                mediaInput.click();
                console.log('File input clicked');
            } else {
                console.error('Media input not found');
            }
        }

        function debugElements() {
            const elements = {
                mediaInput: document.getElementById('media'),
                selectFileBtn: document.getElementById('select-file-btn'),
                dropzone: document.getElementById('media-dropzone'),
                clearMediaBtn: document.getElementById('clear-media-btn'),
                mediaFallback: document.getElementById('media-fallback')
            };

            console.log('Debug Elements:', elements);
            alert('Check console for element debug info');
        }

        // File handling
        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                previewMedia(file);
            }
        }

        function previewMedia(file) {
            const previewContainer = document.getElementById('media-preview');
            const previewContent = document.getElementById('preview-container');
            const fileInfo = document.getElementById('file-info');
            const currentMedia = document.getElementById('current-media');

            // Validate file size (50MB)
            const maxSize = 50 * 1024 * 1024; // 50MB in bytes
            if (file.size > maxSize) {
                Swal.fire({
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file tidak boleh lebih dari 50MB',
                    icon: 'error'
                });
                clearMediaInput();
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
                'video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/flv'
            ];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    title: 'Format File Tidak Didukung',
                    text: 'Silakan pilih file gambar (JPEG, PNG, GIF, WebP) atau video (MP4, AVI, MOV, WMV, FLV)',
                    icon: 'error'
                });
                clearMediaInput();
                return;
            }

            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const fileName = file.name;
            const fileType = file.type;

            // Show file info
            fileInfo.innerHTML = `${fileName} • ${fileSize} MB • ${fileType}`;

            // Hide current media if exists
            if (currentMedia) {
                currentMedia.style.display = 'none';
            }

            // Clear previous preview
            previewContent.innerHTML = '';

            if (file.type.startsWith('image/')) {
                // Image preview
                const img = document.createElement('img');
                img.className = 'img-fluid rounded shadow-sm';
                img.style.maxHeight = '300px';
                img.onload = function() {
                    URL.revokeObjectURL(img.src);
                };
                img.src = URL.createObjectURL(file);
                previewContent.appendChild(img);

            } else if (file.type.startsWith('video/')) {
                // Video preview
                const video = document.createElement('video');
                video.controls = true;
                video.className = 'rounded shadow-sm';
                video.style.maxHeight = '300px';
                video.style.maxWidth = '100%';
                video.onload = function() {
                    URL.revokeObjectURL(video.src);
                };
                video.src = URL.createObjectURL(file);
                previewContent.appendChild(video);
            }

            previewContainer.style.display = 'block';
        }

        function clearMediaPreview() {
            const previewContainer = document.getElementById('media-preview');
            const currentMedia = document.getElementById('current-media');

            previewContainer.style.display = 'none';
            if (currentMedia) {
                currentMedia.style.display = 'block';
            }

            clearMediaInput();
        }

        function clearMediaInput() {
            const mediaInput = document.getElementById('media');
            mediaInput.value = '';
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const dropzone = document.getElementById('media-dropzone');
            const mediaInput = document.getElementById('media');
            const statusToggle = document.getElementById('is_active');
            const statusText = document.querySelector('.status-text');
            const selectFileBtn = document.getElementById('select-file-btn');
            const clearMediaBtn = document.getElementById('clear-media-btn');

            // Debug: Check if elements exist
            console.log('Elements found:', {
                dropzone: !!dropzone,
                mediaInput: !!mediaInput,
                selectFileBtn: !!selectFileBtn,
                clearMediaBtn: !!clearMediaBtn
            });

            // File input change event
            if (mediaInput) {
                mediaInput.addEventListener('change', function() {
                    console.log('File input changed:', this.files);
                    handleFileSelect(this);
                });
            }

            // Fallback file input
            const mediaFallback = document.getElementById('media-fallback');
            if (mediaFallback) {
                mediaFallback.addEventListener('change', function() {
                    console.log('Fallback file input changed:', this.files);
                    if (this.files && this.files[0] && mediaInput) {
                        // Copy the file to the main input
                        mediaInput.files = this.files;
                        handleFileSelect(this);
                    }
                });
            }

            // Select file button click
            if (selectFileBtn && mediaInput) {
                selectFileBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Select file button clicked');
                    mediaInput.click();
                });
            }

            // Dropzone click (fallback)
            if (dropzone && mediaInput) {
                dropzone.addEventListener('click', function(e) {
                    // Only trigger if not clicking the button
                    if (e.target !== selectFileBtn && !selectFileBtn.contains(e.target)) {
                        console.log('Dropzone clicked');
                        mediaInput.click();
                    }
                });
            }

            // Drag and drop events
            if (dropzone) {
                dropzone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    dropzone.classList.add('dragover');
                });

                dropzone.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    dropzone.classList.remove('dragover');
                });

                dropzone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    dropzone.classList.remove('dragover');

                    const files = e.dataTransfer.files;
                    if (files.length > 0 && mediaInput) {
                        mediaInput.files = files;
                        handleFileSelect(mediaInput);
                    }
                });
            }

            // Clear media button
            if (clearMediaBtn) {
                clearMediaBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    clearMediaPreview();
                });
            }

            // Status toggle
            if (statusToggle && statusText) {
                statusToggle.addEventListener('change', function() {
                    statusText.textContent = this.checked ? 'Aktif' : 'Tidak Aktif';
                });
            }

            // Form submission
            const form = document.getElementById('slider-form');
            const submitBtn = document.getElementById('submit-btn');

            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    submitBtn.setAttribute('data-kt-indicator', 'on');
                    submitBtn.disabled = true;
                });
            }

            // Reset form
            if (form) {
                form.addEventListener('reset', function() {
                    clearMediaPreview();
                });
            }

            // Debug buttons
            const testFileBtn = document.getElementById('test-file-btn');
            const debugElementsBtn = document.getElementById('debug-elements-btn');

            if (testFileBtn) {
                testFileBtn.addEventListener('click', function() {
                    testFileInput();
                });
            }

            if (debugElementsBtn) {
                debugElementsBtn.addEventListener('click', function() {
                    debugElements();
                });
            }
        });

        // Debug functions
        function testFileInput() {
            const mediaInput = document.getElementById('media');
            console.log('Testing file input:', mediaInput);
            if (mediaInput) {
                mediaInput.click();
                console.log('File input clicked');
            } else {
                console.error('Media input not found');
            }
        }

        function debugElements() {
            const elements = {
                mediaInput: document.getElementById('media'),
                selectFileBtn: document.getElementById('select-file-btn'),
                dropzone: document.getElementById('media-dropzone'),
                clearMediaBtn: document.getElementById('clear-media-btn'),
                mediaFallback: document.getElementById('media-fallback')
            };

            console.log('Debug Elements:', elements);
            alert('Check console for element debug info');
        }

        // Validation
        function validateForm() {
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const buttonText = document.getElementById('button_text').value.trim();
            const buttonLink = document.getElementById('button_link').value.trim();

            if (!title || !description || !buttonText || !buttonLink) {
                Swal.fire({
                    title: 'Form Tidak Lengkap',
                    text: 'Mohon lengkapi semua field yang wajib diisi',
                    icon: 'warning'
                });
                return false;
            }

            return true;
        }
    </script>
@endpush
