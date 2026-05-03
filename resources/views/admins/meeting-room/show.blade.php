@extends('layouts.master', ['main' => 'Detail Meeting Venue', 'title' => $meetingRoom->hotel])

@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <div class="col-xl-12">
                <div class="card h-lg-100" id="kt_contacts_main">
                    <div class="card-header" id="kt_chat_contacts_header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">
                                Detail Meeting Venue: {{ $meetingRoom->hotel }}
                            </span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">
                                {{ $meetingRoom->city_name }}, {{ $meetingRoom->province_name }}
                            </span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('meeting-room.edit', $meetingRoom->id) }}"
                                class="btn btn-sm btn-warning me-2">
                                <i class="fa fa-edit"></i>
                                Edit
                            </a>
                            <a href="{{ route('meeting-room.index') }}" class="btn btn-sm btn-light">
                                <i class="fa fa-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-5">
                        {{-- Navigation Tabs --}}
                        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#overview_tab">
                                    <i class="ki-duotone ki-home fs-2 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Overview
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#gallery_tab">
                                    <i class="ki-duotone ki-picture fs-2 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Galeri
                                    @if ($meetingRoom->galleries && count($meetingRoom->galleries) > 0)
                                        <span
                                            class="badge badge-circle badge-primary ms-1">{{ count($meetingRoom->galleries) }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#rooms_tab">
                                    <i class="ki-duotone ki-entrance-left fs-2 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Ruang Meeting
                                    @if ($meetingRoom->meeting_rooms && count($meetingRoom->meeting_rooms) > 0)
                                        <span
                                            class="badge badge-circle badge-success ms-1">{{ count($meetingRoom->meeting_rooms) }}</span>
                                    @endif
                                </a>
                            </li>
                        </ul>

                        {{-- Tab Content --}}
                        <div class="tab-content" id="myTabContent">
                            {{-- Overview Tab --}}
                            <div class="tab-pane fade show active" id="overview_tab" role="tabpanel">
                                <div class="row g-6">
                                    {{-- Venue Information Card --}}
                                    <div class="col-lg-6">
                                        <div class="card card-flush h-100">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <i class="ki-duotone ki-information fs-1 text-primary me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                    <h3 class="fw-bold m-0">Informasi Venue</h3>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-5">
                                                    <label class="fw-semibold text-muted fs-7 mb-1">Nama Hotel/Venue</label>
                                                    <div class="fw-bold fs-6">{{ $meetingRoom->hotel }}</div>
                                                </div>
                                                <div class="mb-5">
                                                    <label class="fw-semibold text-muted fs-7 mb-1">Alamat Lengkap</label>
                                                    <div class="fw-bold fs-6">{{ $meetingRoom->address }}</div>
                                                </div>
                                                <div class="mb-5">
                                                    <label class="fw-semibold text-muted fs-7 mb-1">Lokasi</label>
                                                    <div class="fw-bold fs-6">
                                                        <i class="ki-duotone ki-geolocation text-primary me-1">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        {{ $meetingRoom->city_name }}, {{ $meetingRoom->province_name }}
                                                    </div>
                                                </div>
                                                @if ($meetingRoom->max_capacity)
                                                    <div class="mb-0">
                                                        <label class="fw-semibold text-muted fs-7 mb-1">Kapasitas
                                                            Maksimum</label>
                                                        <div class="fw-bold fs-6 text-success">
                                                            <i class="ki-duotone ki-user fs-4 text-success me-1">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            {{ number_format($meetingRoom->max_capacity) }} orang
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Contact Information Card --}}
                                    <div class="col-lg-6">
                                        <div class="card card-flush h-100">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <i class="ki-duotone ki-call fs-1 text-success me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                    </i>
                                                    <h3 class="fw-bold m-0">Kontak</h3>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @if ($meetingRoom->email || $meetingRoom->phone)
                                                    @if ($meetingRoom->email)
                                                        <div class="mb-5">
                                                            <label class="fw-semibold text-muted fs-7 mb-1">Email</label>
                                                            <div class="fw-bold fs-6">
                                                                <a href="mailto:{{ $meetingRoom->email }}"
                                                                    class="text-hover-primary">
                                                                    <i class="ki-duotone ki-sms text-primary me-1">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                    </i>
                                                                    {{ $meetingRoom->email }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($meetingRoom->phone)
                                                        <div class="mb-0">
                                                            <label class="fw-semibold text-muted fs-7 mb-1">Telepon</label>
                                                            <div class="fw-bold fs-6">
                                                                <a href="tel:{{ $meetingRoom->phone }}"
                                                                    class="text-hover-primary">
                                                                    <i class="ki-duotone ki-phone text-success me-1">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                    </i>
                                                                    {{ $meetingRoom->phone }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="d-flex flex-column text-center py-5">
                                                        <i class="ki-duotone ki-call fs-3x text-gray-400 mb-4">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                        </i>
                                                        <div class="text-gray-500">Belum ada informasi kontak</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- System Information Card --}}
                                    @if ($meetingRoom->external_id)
                                        <div class="col-12">
                                            <div class="card card-flush">
                                                <div class="card-header">
                                                    <div class="card-title">
                                                        <i class="ki-duotone ki-code fs-1 text-info me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                        </i>
                                                        <h3 class="fw-bold m-0">Informasi Sistem</h3>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="fw-semibold text-muted fs-7 mb-1">External
                                                                ID</label>
                                                            <div class="fw-bold fs-6">
                                                                <code
                                                                    class="bg-light-primary text-primary px-2 py-1 rounded">{{ $meetingRoom->external_id }}</code>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="fw-semibold text-muted fs-7 mb-1">Terakhir
                                                                Diperbarui</label>
                                                            <div class="fw-bold fs-6">
                                                                <i class="ki-duotone ki-calendar text-warning me-1">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i>
                                                                {{ $meetingRoom->updated_at->format('d M Y H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Gallery Tab --}}
                            <div class="tab-pane fade" id="gallery_tab" role="tabpanel">
                                {{-- Upload Gallery Form --}}
                                <div class="card card-flush mb-6">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <i class="ki-duotone ki-cloud-add fs-1 text-success me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <h3 class="fw-bold m-0">Upload Galeri</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <form id="galleryUploadForm" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row align-items-end">
                                                <div class="col-md-8">
                                                    <label class="form-label fw-bold" for="gallery_images">Pilih Gambar
                                                        (Multiple)</label>
                                                    <input type="file" class="form-control" id="gallery_images"
                                                        name="gallery_images[]"
                                                        accept="image/jpeg,image/png,image/jpg,image/gif" multiple
                                                        required>
                                                    <div class="form-text">
                                                        Format: JPG, PNG, GIF. Maksimal 5MB per gambar.
                                                        <br>Anda dapat memilih hingga 10 gambar sekaligus.
                                                        <br><span id="fileCount" class="text-muted"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <button type="submit" class="btn btn-primary w-100" id="uploadBtn"
                                                        disabled>
                                                        <i class="fa fa-upload me-2"></i>
                                                        Upload Gambar
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                {{-- Gallery Grid --}}
                                @if ($meetingRoom->galleries && count($meetingRoom->galleries) > 0)
                                    <div class="row g-5">
                                        @foreach ($meetingRoom->galleries as $index => $gallery)
                                            <div class="col-lg-4 col-md-6 gallery-item"
                                                data-gallery-id="{{ $gallery->id }}">
                                                <div class="card card-flush">
                                                    <div class="card-body p-0">
                                                        <div class="position-relative overflow-hidden rounded">
                                                            <img src="{{ asset($gallery->image) }}"
                                                                alt="{{ $meetingRoom->hotel }} - Foto {{ $index + 1 }}"
                                                                class="img-fluid gallery-image"
                                                                style="height: 250px; width: 100%; object-fit: cover; cursor: pointer; transition: transform 0.3s ease;"
                                                                data-bs-toggle="modal" data-bs-target="#galleryModal"
                                                                data-image="{{ asset($gallery->image) }}"
                                                                data-title="{{ $meetingRoom->hotel }} - Foto {{ $index + 1 }}"
                                                                onmouseover="this.style.transform='scale(1.05)'"
                                                                onmouseout="this.style.transform='scale(1)'">
                                                            <div class="position-absolute top-0 end-0 m-3">
                                                                <span
                                                                    class="badge badge-primary">{{ $index + 1 }}/{{ count($meetingRoom->galleries) }}</span>
                                                            </div>
                                                            <div class="position-absolute top-0 start-0 m-3">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger btn-icon delete-gallery-btn"
                                                                    data-gallery-id="{{ $gallery->id }}"
                                                                    title="Hapus Gambar">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                            <div
                                                                class="position-absolute bottom-0 start-0 end-0 bg-gradient-dark p-3">
                                                                <div class="text-white fw-bold">{{ $meetingRoom->hotel }}
                                                                </div>
                                                                <div class="text-gray-300 fs-7">Klik untuk memperbesar
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="d-flex flex-column text-center py-10" id="emptyGalleryState">
                                        <i class="ki-duotone ki-picture fs-3x text-gray-400 mb-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="text-gray-700 fs-2 fw-bold mb-2">Belum Ada Galeri</div>
                                        <div class="text-gray-500">Belum ada foto yang tersedia untuk venue ini</div>
                                    </div>
                                @endif
                            </div>

                            {{-- Rooms Tab --}}
                            <div class="tab-pane fade" id="rooms_tab" role="tabpanel">
                                @if ($meetingRoom->meeting_rooms && count($meetingRoom->meeting_rooms) > 0)
                                    <div class="row g-5">
                                        @foreach ($meetingRoom->meeting_rooms as $index => $room)
                                            <div class="col-lg-6 col-xl-4">
                                                <div class="card card-flush h-100">
                                                    <div class="card-body p-0">
                                                        {{-- Room Photo --}}
                                                        <div class="position-relative overflow-hidden rounded-top">
                                                            @if ($room->photo)
                                                                <img src="{{ Storage::url($room->photo) }}"
                                                                    alt="{{ $room->name }}"
                                                                    class="img-fluid room-image"
                                                                    style="height: 200px; width: 100%; object-fit: cover; cursor: pointer; transition: transform 0.3s ease;"
                                                                    data-bs-toggle="modal" data-bs-target="#roomModal"
                                                                    data-image="{{ Storage::url($room->photo) }}"
                                                                    data-title="{{ $room->name }}"
                                                                    onmouseover="this.style.transform='scale(1.05)'"
                                                                    onmouseout="this.style.transform='scale(1)'">
                                                            @else
                                                                <div class="bg-light-primary d-flex align-items-center justify-content-center"
                                                                    style="height: 200px;">
                                                                    <div class="text-center">
                                                                        <i
                                                                            class="ki-duotone ki-home fs-3x text-primary mb-3">
                                                                            <span class="path1"></span>
                                                                            <span class="path2"></span>
                                                                        </i>
                                                                        <div class="text-primary fw-bold">
                                                                            {{ $room->name }}</div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="position-absolute top-0 end-0 m-3">
                                                                <span class="badge badge-success">Ruang
                                                                    {{ $index + 1 }}</span>
                                                            </div>
                                                        </div>

                                                        {{-- Room Info --}}
                                                        <div class="p-5">
                                                            <div class="mb-4">
                                                                <h4 class="fw-bold text-gray-900 mb-2">{{ $room->name }}
                                                                </h4>
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-3">
                                                                    <span class="text-muted fs-7">Layout Tersedia</span>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-light-primary add-layout-btn"
                                                                        data-room-id="{{ $room->id }}"
                                                                        data-room-name="{{ $room->name }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#layoutModal">
                                                                        <i class="fa fa-plus me-1"></i>
                                                                        Tambah Layout
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            {{-- Room Layouts --}}
                                                            @if ($room->meeting_room_layouts->count() > 0)
                                                                <div class="layout-container"
                                                                    data-room-id="{{ $room->id }}">
                                                                    @foreach ($room->meeting_room_layouts as $layout)
                                                                        <div class="layout-item d-flex justify-content-between align-items-center p-3 mb-2 bg-light-info rounded"
                                                                            data-layout-id="{{ $layout->id }}">
                                                                            <div class="flex-grow-1">
                                                                                <div
                                                                                    class="d-flex align-items-center mb-1">
                                                                                    <i
                                                                                        class="ki-duotone ki-design-1 text-info me-2 fs-6">
                                                                                        <span class="path1"></span>
                                                                                        <span class="path2"></span>
                                                                                    </i>
                                                                                    <span
                                                                                        class="fw-semibold text-gray-800 fs-7">
                                                                                        {{ $layout->type->name ?? 'N/A' }}
                                                                                    </span>
                                                                                </div>
                                                                                <div class="d-flex align-items-center">
                                                                                    <i
                                                                                        class="ki-duotone ki-user text-success me-1 fs-8">
                                                                                        <span class="path1"></span>
                                                                                        <span class="path2"></span>
                                                                                    </i>
                                                                                    <span
                                                                                        class="text-success fw-bold fs-8">
                                                                                        {{ number_format($layout->capacity) }}
                                                                                        orang
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="d-flex">
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-light-warning me-1 edit-layout-btn"
                                                                                    data-layout-id="{{ $layout->id }}"
                                                                                    data-room-id="{{ $room->id }}"
                                                                                    data-room-name="{{ $room->name }}"
                                                                                    data-type-id="{{ $layout->meeting_room_type_id }}"
                                                                                    data-capacity="{{ $layout->capacity }}"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#layoutModal"
                                                                                    title="Edit Layout">
                                                                                    <i class="fa fa-edit fs-8"></i>
                                                                                </button>
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-light-danger delete-layout-btn"
                                                                                    data-layout-id="{{ $layout->id }}"
                                                                                    data-room-id="{{ $room->id }}"
                                                                                    data-type-name="{{ $layout->type->name ?? 'Layout' }}"
                                                                                    title="Hapus Layout">
                                                                                    <i class="fa fa-trash fs-8"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="text-center py-4 layout-empty-state"
                                                                    data-room-id="{{ $room->id }}">
                                                                    <i
                                                                        class="ki-duotone ki-design-1 fs-2x text-gray-400 mb-2">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                    </i>
                                                                    <div class="text-gray-500 fs-7">Belum ada layout</div>
                                                                </div>
                                                            @endif

                                                            {{-- Room Actions --}}
                                                            <div class="text-center mt-4 pt-3 border-top">
                                                                <a href="{{ route('venue.rooms.edit', [$meetingRoom->id, $room->id]) }}"
                                                                    class="btn btn-sm btn-light-primary me-2">
                                                                    <i class="fa fa-edit me-1"></i>
                                                                    Edit Ruang
                                                                </a>
                                                                @if ($room->photo)
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-light-info"
                                                                        data-bs-toggle="modal" data-bs-target="#roomModal"
                                                                        data-image="{{ Storage::url($room->photo) }}"
                                                                        data-title="{{ $room->name }}">
                                                                        <i class="fa fa-eye me-1"></i>
                                                                        Lihat Foto
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="d-flex flex-column text-center py-10">
                                        <i class="ki-duotone ki-entrance-left fs-3x text-gray-400 mb-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="text-gray-700 fs-2 fw-bold mb-2">Belum Ada Ruang Meeting</div>
                                        <div class="text-gray-500 mb-4">Belum ada ruang meeting yang tersedia untuk venue
                                            ini</div>
                                        <div>
                                            <a href="{{ route('venue.rooms.create', $meetingRoom->id) }}"
                                                class="btn btn-primary">
                                                <i class="fa fa-plus me-2"></i>
                                                Tambah Ruang Meeting
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Modal -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="galleryModalLabel">Galeri Venue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="" class="img-fluid rounded"
                        style="max-height: 70vh;">
                </div>
            </div>
        </div>
    </div>

    <!-- Room Modal -->
    <div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roomModalLabel">Foto Ruang Meeting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalRoomImage" src="" alt="" class="img-fluid rounded"
                        style="max-height: 70vh;">
                </div>
            </div>
        </div>
    </div>

    <!-- Layout Modal -->
    <div class="modal fade" id="layoutModal" tabindex="-1" aria-labelledby="layoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="layoutModalLabel">Tambah Layout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="layoutForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="layoutId" name="layout_id">
                        <input type="hidden" id="roomId" name="room_id">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Ruang Meeting</label>
                            <input type="text" class="form-control bg-light" id="roomName" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold" for="meeting_room_type_id">Tipe Layout</label>
                            <select class="form-select" id="meeting_room_type_id" name="meeting_room_type_id" required>
                                <option value="">Pilih Tipe Layout</option>
                            </select>
                            <div class="invalid-feedback" id="typeError"></div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold" for="capacity">Kapasitas</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="capacity" name="capacity"
                                    min="1" placeholder="Masukkan kapasitas" required>
                                <span class="input-group-text">orang</span>
                            </div>
                            <div class="invalid-feedback" id="capacityError"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="layoutSubmitBtn">
                            <i class="fa fa-save me-2"></i>
                            Simpan Layout
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Handle file input change to show file count
            $('#gallery_images').on('change', function() {
                var fileCount = this.files.length;
                var fileCountText = '';

                if (fileCount > 0) {
                    fileCountText = fileCount + ' file dipilih';
                    if (fileCount > 10) {
                        fileCountText += ' (maksimal 10 file akan diupload)';
                        $('#uploadBtn').prop('disabled', false);
                    } else {
                        $('#uploadBtn').prop('disabled', false);
                    }
                } else {
                    $('#uploadBtn').prop('disabled', true);
                }

                $('#fileCount').text(fileCountText);
            });

            // Handle gallery image clicks
            $('.gallery-image').click(function() {
                var imageSrc = $(this).data('image');
                var imageTitle = $(this).data('title');

                $('#modalImage').attr('src', imageSrc);
                $('#modalImage').attr('alt', imageTitle);
                $('#galleryModalLabel').text(imageTitle);
            });

            // Handle room image clicks
            $('.room-image, .btn[data-bs-target="#roomModal"]').click(function() {
                var imageSrc = $(this).data('image');
                var imageTitle = $(this).data('title');

                $('#modalRoomImage').attr('src', imageSrc);
                $('#modalRoomImage').attr('alt', imageTitle);
                $('#roomModalLabel').text(imageTitle);
            });

            // Handle gallery upload
            $('#galleryUploadForm').on('submit', function(e) {
                e.preventDefault();

                var files = $('#gallery_images')[0].files;
                if (files.length === 0) {
                    Swal.fire({
                        title: 'Perhatian!',
                        text: 'Silakan pilih minimal 1 gambar untuk diupload',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                if (files.length > 10) {
                    Swal.fire({
                        title: 'Perhatian!',
                        text: 'Maksimal 10 gambar yang dapat diupload sekaligus',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                var formData = new FormData(this);
                var uploadBtn = $('#uploadBtn');
                var originalText = uploadBtn.html();

                // Disable button and show loading with progress
                uploadBtn.prop('disabled', true);
                uploadBtn.html('<i class="fa fa-spinner fa-spin me-2"></i>Mengupload ' + files.length +
                    ' gambar...');

                $.ajax({
                    url: '{{ route('meeting-room.gallery.upload', $meetingRoom->id) }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            var message = response.message;
                            var icon = 'success';

                            // If there are errors, show mixed result
                            if (response.errors && response.errors.length > 0) {
                                icon = 'warning';
                                message += '\n\nError detail:\n' + response.errors.join('\n');
                            }

                            // Show success message
                            Swal.fire({
                                title: response.uploaded_count > 0 ? 'Berhasil!' :
                                    'Perhatian!',
                                text: message,
                                icon: icon,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                // Redirect to refresh the page if any file uploaded
                                if (response.uploaded_count > 0) {
                                    window.location.href = response.redirect;
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Terjadi kesalahan saat mengupload gambar';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            // Handle validation errors
                            if (xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                var errorList = [];

                                Object.keys(errors).forEach(function(key) {
                                    if (Array.isArray(errors[key])) {
                                        errorList = errorList.concat(errors[key]);
                                    } else {
                                        errorList.push(errors[key]);
                                    }
                                });

                                if (errorList.length > 0) {
                                    errorMessage += ':\n\n' + errorList.join('\n');
                                }
                            }
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    },
                    complete: function() {
                        // Re-enable button
                        uploadBtn.prop('disabled', false);
                        uploadBtn.html(originalText);

                        // Reset form and file count
                        $('#galleryUploadForm')[0].reset();
                        $('#fileCount').text('');
                        $('#uploadBtn').prop('disabled', true);
                    }
                });
            });

            // Handle gallery delete
            $(document).on('click', '.delete-gallery-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var galleryId = $(this).data('gallery-id');
                var galleryItem = $(this).closest('.gallery-item');

                Swal.fire({
                    title: 'Hapus Gambar?',
                    text: 'Apakah Anda yakin ingin menghapus gambar ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('meeting-room.gallery.delete', [$meetingRoom->id, '__GALLERY_ID__']) }}'
                                .replace('__GALLERY_ID__', galleryId),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Remove the gallery item from DOM
                                    galleryItem.fadeOut(300, function() {
                                        $(this).remove();

                                        // Check if no more gallery items
                                        if ($('.gallery-item').length === 0) {
                                            // Show empty state
                                            $('.row.g-5').replaceWith(`
                                                <div class="d-flex flex-column text-center py-10" id="emptyGalleryState">
                                                    <i class="ki-duotone ki-picture fs-3x text-gray-400 mb-4">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <div class="text-gray-700 fs-2 fw-bold mb-2">Belum Ada Galeri</div>
                                                    <div class="text-gray-500">Belum ada foto yang tersedia untuk venue ini</div>
                                                </div>
                                            `);

                                            // Update badge count
                                            $('.nav-link[href="#gallery_tab"] .badge')
                                                .remove();
                                        } else {
                                            // Update gallery indices and badge count
                                            var remainingCount = $(
                                                '.gallery-item').length;
                                            $('.nav-link[href="#gallery_tab"] .badge')
                                                .text(remainingCount);

                                            // Update image indices
                                            $('.gallery-item').each(function(
                                                index) {
                                                var badge = $(this)
                                                    .find(
                                                        '.badge-primary'
                                                    );
                                                badge.text((index + 1) +
                                                    '/' +
                                                    remainingCount);
                                            });
                                        }
                                    });

                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr) {
                                var errorMessage =
                                    'Terjadi kesalahan saat menghapus gambar';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    title: 'Error!',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });

            // Load meeting room types when modal is first opened
            let typesLoaded = false;
            $('#layoutModal').on('show.bs.modal', function() {
                if (!typesLoaded) {
                    loadMeetingRoomTypes();
                }
            });

            // Handle add layout button
            $(document).on('click', '.add-layout-btn', function() {
                const roomId = $(this).data('room-id');
                const roomName = $(this).data('room-name');

                // Reset form for adding
                $('#layoutForm')[0].reset();
                $('#layoutId').val('');
                $('#roomId').val(roomId);
                $('#roomName').val(roomName);
                $('#layoutModalLabel').text('Tambah Layout untuk ' + roomName);
                $('#layoutSubmitBtn').html('<i class="fa fa-save me-2"></i>Simpan Layout');

                // Clear previous errors
                clearFormErrors();
            });

            // Handle edit layout button
            $(document).on('click', '.edit-layout-btn', function() {
                const layoutId = $(this).data('layout-id');
                const roomId = $(this).data('room-id');
                const roomName = $(this).data('room-name');
                const typeId = $(this).data('type-id');
                const capacity = $(this).data('capacity');

                // Set form for editing
                $('#layoutId').val(layoutId);
                $('#roomId').val(roomId);
                $('#roomName').val(roomName);
                $('#meeting_room_type_id').val(typeId);
                $('#capacity').val(capacity);
                $('#layoutModalLabel').text('Edit Layout untuk ' + roomName);
                $('#layoutSubmitBtn').html('<i class="fa fa-save me-2"></i>Update Layout');

                // Clear previous errors
                clearFormErrors();
            });

            // Handle layout form submission
            $('#layoutForm').on('submit', function(e) {
                e.preventDefault();

                const layoutId = $('#layoutId').val();
                const roomId = $('#roomId').val();
                const isEdit = layoutId !== '';

                let url = '';
                let method = '';

                if (isEdit) {
                    url =
                        '{{ route('meeting-room.layout.update', [$meetingRoom->id, '__ROOM_ID__', '__LAYOUT_ID__']) }}'
                        .replace('__ROOM_ID__', roomId)
                        .replace('__LAYOUT_ID__', layoutId);
                    method = 'PUT';
                } else {
                    url = '{{ route('meeting-room.layout.store', [$meetingRoom->id, '__ROOM_ID__']) }}'
                        .replace('__ROOM_ID__', roomId);
                    method = 'POST';
                }

                const formData = {
                    _token: '{{ csrf_token() }}',
                    _method: method,
                    meeting_room_type_id: $('#meeting_room_type_id').val(),
                    capacity: $('#capacity').val()
                };

                const submitBtn = $('#layoutSubmitBtn');
                const originalText = submitBtn.html();

                // Disable button and show loading
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fa fa-spinner fa-spin me-2"></i>Menyimpan...');

                // Clear previous errors
                clearFormErrors();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#layoutModal').modal('hide');

                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Refresh the page to show updated layouts
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            if (errors.meeting_room_type_id) {
                                $('#meeting_room_type_id').addClass('is-invalid');
                                $('#typeError').text(errors.meeting_room_type_id[0]);
                            }
                            if (errors.capacity) {
                                $('#capacity').addClass('is-invalid');
                                $('#capacityError').text(errors.capacity[0]);
                            }
                        } else {
                            // Other errors
                            let errorMessage = 'Terjadi kesalahan saat menyimpan layout';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    complete: function() {
                        // Re-enable button
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalText);
                    }
                });
            });

            // Handle delete layout button
            $(document).on('click', '.delete-layout-btn', function() {
                const layoutId = $(this).data('layout-id');
                const roomId = $(this).data('room-id');
                const typeName = $(this).data('type-name');

                Swal.fire({
                    title: 'Hapus Layout?',
                    text: `Apakah Anda yakin ingin menghapus layout "${typeName}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url =
                            '{{ route('meeting-room.layout.delete', [$meetingRoom->id, '__ROOM_ID__', '__LAYOUT_ID__']) }}'
                            .replace('__ROOM_ID__', roomId)
                            .replace('__LAYOUT_ID__', layoutId);

                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Remove the layout item from DOM or refresh
                                        window.location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage =
                                    'Terjadi kesalahan saat menghapus layout';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    title: 'Error!',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });

            // Function to load meeting room types
            function loadMeetingRoomTypes() {
                $.ajax({
                    url: '{{ route('meeting-room.types') }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const select = $('#meeting_room_type_id');
                            select.empty();
                            select.append('<option value="">Pilih Tipe Layout</option>');

                            response.data.forEach(function(type) {
                                select.append(
                                    `<option value="${type.id}">${type.name}</option>`);
                            });

                            typesLoaded = true;
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading room types:', xhr);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal memuat data tipe layout',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }

            // Function to clear form errors
            function clearFormErrors() {
                $('#meeting_room_type_id, #capacity').removeClass('is-invalid');
                $('#typeError, #capacityError').text('');
            }
        });
    </script>
@endpush
