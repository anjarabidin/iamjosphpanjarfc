@extends('layouts.master', ['main' => 'Data Ruang Pertemuan', 'title' => 'Ruang Pertemuan'])

@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <div class="col-xl-12">
                <div class="card h-lg-100" id="kt_contacts_main">
                    <div class="card-header" id="kt_chat_contacts_header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">
                                Informasi Ruang Pertemuan
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <x-alert.alert-validation />
                        <form id="meeting-room-info" action="{{ route('meeting-room-info.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />

                            {{-- Gambar --}}
                            <div>
                                <div class="fv-row mb-6">
                                    <x-form.image-upload label="Gambar" maxSize="2MB" name="image" :value="@$meetingRoomInfo->image ?? null"
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
                                        value="{{ old('title', @$meetingRoomInfo->title) }}" placeholder="Masukkan Judul"
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
                                    value="{{ old('title_en', @$meetingRoomInfo->title_en) }}"
                                    placeholder="Enter Title in English" />
                            </div>

                            {{-- SubJudul --}}
                            <div class="row">
                                <div class="col-md-10">
                                    <label for="subtitle" class="fs-6 fw-bold form-label mt-3">
                                        <span class="text-dark">SubJudul (ID)</span>
                                    </label>
                                    <textarea class="form-control" id="subtitle" name="subtitle" rows="4">{{ old('subtitle', @$meetingRoomInfo->subtitle) }}</textarea>
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
                                <textarea class="form-control" id="subtitle_en" name="subtitle_en" rows="4">{{ old('subtitle_en', @$meetingRoomInfo->subtitle_en) }}</textarea>
                            </div>

                            <input type="hidden" name="id" value="{{ old('id', @$meetingRoomInfo->id) }}" />
                            {{-- Tombol Aksi --}}
                            <div class="d-flex justify-content-end">
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
    </script>
@endpush
