@extends('layouts.master', ['main' => 'Data Slider', 'title' => 'Detail Slider'])

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
                            <h3 class="fw-bold m-0">Detail Slider</h3>
                        </div>
                        <!--end::Card title-->
                        <!--begin::Actions-->
                        <div class="card-toolbar">
                            <a href="{{ route('home-slider.index') }}" class="btn btn-sm btn-secondary me-2">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <a href="{{ route('home-slider.edit', $homeSlider->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                        <!--end::Actions-->
                    </div>
                    <!--begin::Card header-->

                    <!--begin::Content-->
                    <div class="card-body">
                        <div class="row">
                            <!--begin::Media Section-->
                            <div class="col-lg-6 mb-6">
                                <h5 class="fw-semibold mb-4">Media Slider</h5>

                                @if ($homeSlider->media)
                                    <div class="card">
                                        <div class="card-body p-3">
                                            @if ($homeSlider->isImage())
                                                <img src="{{ $homeSlider->media_url }}" alt="Slider Image"
                                                    class="img-fluid rounded w-100"
                                                    style="max-height: 300px; object-fit: cover;" />
                                                <div class="mt-3">
                                                    <span class="badge badge-primary">Gambar</span>
                                                </div>
                                            @elseif($homeSlider->isVideo())
                                                <video controls class="w-100 rounded" style="max-height: 300px;">
                                                    <source src="{{ $homeSlider->media_url }}" type="video/mp4">
                                                    Browser Anda tidak mendukung video tag.
                                                </video>
                                                <div class="mt-3">
                                                    <span class="badge badge-success">Video</span>
                                                </div>
                                            @endif

                                            <!-- Processing Status -->
                                            <div class="mt-2">
                                                @if ($homeSlider->media_processing_status === 'processing')
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-spinner fa-spin"></i> Sedang Diproses
                                                    </span>
                                                @elseif($homeSlider->media_processing_status === 'completed')
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> Siap Ditampilkan
                                                    </span>
                                                @elseif($homeSlider->media_processing_status === 'failed')
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times"></i> Gagal Diproses
                                                    </span>
                                                @else
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-clock"></i> Menunggu Proses
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Tidak ada media yang diupload
                                    </div>
                                @endif
                            </div>
                            <!--end::Media Section-->

                            <!--begin::Info Section-->
                            <div class="col-lg-6">
                                <h5 class="fw-semibold mb-4">Informasi Slider</h5>

                                <!--begin::Details-->
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td class="fw-semibold text-gray-600 w-50">Judul (ID):</td>
                                                <td class="text-gray-900">{{ $homeSlider->title }}</td>
                                            </tr>
                                            @if ($homeSlider->title_en)
                                                <tr>
                                                    <td class="fw-semibold text-gray-600">Judul (EN):</td>
                                                    <td class="text-gray-900">{{ $homeSlider->title_en }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td class="fw-semibold text-gray-600">Deskripsi (ID):</td>
                                                <td class="text-gray-900">{{ $homeSlider->description }}</td>
                                            </tr>
                                            @if ($homeSlider->description_en)
                                                <tr>
                                                    <td class="fw-semibold text-gray-600">Deskripsi (EN):</td>
                                                    <td class="text-gray-900">{{ $homeSlider->description_en }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td class="fw-semibold text-gray-600">Teks Tombol (ID):</td>
                                                <td class="text-gray-900">{{ $homeSlider->button_text }}</td>
                                            </tr>
                                            @if ($homeSlider->button_text_en)
                                                <tr>
                                                    <td class="fw-semibold text-gray-600">Teks Tombol (EN):</td>
                                                    <td class="text-gray-900">{{ $homeSlider->button_text_en }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td class="fw-semibold text-gray-600">Link Tombol:</td>
                                                <td class="text-gray-900">
                                                    <a href="{{ $homeSlider->button_link }}" target="_blank"
                                                        class="text-primary">
                                                        {{ $homeSlider->button_link }}
                                                        <i class="fas fa-external-link-alt ms-1"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-gray-600">Status:</td>
                                                <td>
                                                    @if ($homeSlider->is_active)
                                                        <span class="badge badge-success">Aktif</span>
                                                    @else
                                                        <span class="badge badge-secondary">Tidak Aktif</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-gray-600">Urutan Tampil:</td>
                                                <td class="text-gray-900">{{ $homeSlider->sort_order }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-gray-600">Dibuat:</td>
                                                <td class="text-gray-900">
                                                    {{ $homeSlider->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold text-gray-600">Diperbarui:</td>
                                                <td class="text-gray-900">
                                                    {{ $homeSlider->updated_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!--end::Details-->
                            </div>
                            <!--end::Info Section-->
                        </div>
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
