@extends('layouts.master', ['main' => 'Data Koordinator Wilayah', 'title' => request()->routeIs('regional-coordinator.create') ? 'Tambah Koordinator Wilayah' : 'Edit Koordinator Wilayah'])

@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <div class="col-xl-12">
                <div class="card h-lg-100" id="kt_contacts_main">
                    <div class="card-header" id="kt_chat_contacts_header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">
                                {{ request()->routeIs('regional-coordinator.create') ? 'Tambah Koordinator Wilayah' : 'Edit Koordinator Wilayah' }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <x-alert.alert-validation />
                        <form id="regionalCoordinator"
                            action="{{ request()->routeIs('regional-coordinator.create') ? route('regional-coordinator.store') : route('regional-coordinator.update', @$regionalCoordinator->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />

                            {{-- Gambar --}}
                            <div class="fv-row mb-6">
                                <x-form.image-upload label="Foto" maxSize="2MB" name="image" :value="@$regionalCoordinator->image ?? null"
                                    id="image" nullable='1' />
                            </div>

                            <div class="fv-row mb-6">
                                <label for="name" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Nama</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', @$regionalCoordinator->name) }}" placeholder="Masukkan Nama"
                                    required />
                            </div>

                            <div class="fv-row mb-6">
                                <label for="position" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Jabatan</span>
                                </label>
                                <input type="text" class="form-control" id="position" name="position"
                                    value="{{ old('position', @$regionalCoordinator->position) }}"
                                    onChange="translateContentEnglish()" placeholder="Masukkan Jabatan" required />
                            </div>

                            <div class="fv-row mb-6">
                                <label for="position_en" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Jabatan (EN)</span>
                                </label>
                                <input type="text" class="form-control" id="position_en" name="position_en"
                                    value="{{ old('position_en', @$regionalCoordinator->position_en) }}"
                                    placeholder="Masukkan Jabatan (EN)" required />
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
                                <a href="{{ route('regional-coordinator.index') }}">
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
            translate('#position', '#position_en');
        }
    </script>
@endpush
