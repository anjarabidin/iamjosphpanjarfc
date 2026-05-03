@extends('layouts.master', ['main' => 'Data Arah dan Komitmen', 'title' => request()->routeIs('direction-commitment.create') ? 'Tambah Arah dan Komitmen' : 'Edit Arah dan Komitmen'])

@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <div class="col-xl-12">
                <div class="card h-lg-100" id="kt_contacts_main">
                    <div class="card-header" id="kt_chat_contacts_header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">
                                {{ request()->routeIs('direction-commitment.create') ? 'Tambah Arah dan Komitmen' : 'Edit Arah dan Komitmen' }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <x-alert.alert-validation />
                        <form id="directionCommitment"
                            action="{{ request()->routeIs('direction-commitment.create') ? route('direction-commitment.store') : route('direction-commitment.update', @$directionCommitment->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />

                            {{-- Gambar --}}
                            <div class="fv-row mb-6">
                                <x-form.image-upload label="Gambar 1" maxSize="2MB" name="image" :value="@$directionCommitment->image ?? null"
                                    id="image" nullable='1' />
                            </div>

                            {{-- SubJudul --}}
                            <div class="row">
                                <div class="col-md-10">
                                    <label for="content" class="fs-6 fw-bold form-label mt-3">
                                        <span class="text-dark">Konten (ID)</span>
                                    </label>
                                    <textarea class="form-control" id="content" name="content">{{ old('content', @$directionCommitment->content) }}</textarea>
                                </div>
                                <div class="col-md-2 mt-8">
                                    <button type="button" class="btn btn-info btn-sm w-100"
                                        onclick="translateContentEnglish()">
                                        Translate
                                    </button>
                                </div>
                            </div>
                            <div class="fv-row mb-6">
                                <label for="content_en" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Konten (EN)</span>
                                </label>
                                <textarea class="form-control" id="content_en" name="content_en">{{ old('content_en', @$directionCommitment->content_en) }}</textarea>
                            </div>

                            {{-- Urutan --}}
                            <div class="fv-row mb-6">
                                <label for="order" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Urutan</span>
                                </label>
                                <input type="number" class="form-control" id="order" name="order"
                                    value="{{ old('order', @$directionCommitment->order) }}" placeholder="Masukkan Urutan"
                                    required />
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('direction-commitment.index') }}">
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
        function translateContentEnglish() {
            translate('#content', '#content_en');
        }
    </script>
@endpush
