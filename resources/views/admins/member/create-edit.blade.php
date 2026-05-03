@extends('layouts.master', ['main' => 'Data Anggota', 'title' => request()->routeIs('member.create') ? 'Tambah Anggota' : 'Edit Anggota'])

@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <div class="col-xl-12">
                <div class="card h-lg-100" id="kt_contacts_main">
                    <div class="card-header" id="kt_chat_contacts_header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">
                                {{ request()->routeIs('member.create') ? 'Tambah Anggota' : 'Edit Anggota' }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <x-alert.alert-validation />

                        <form id="member"
                            action="{{ request()->routeIs('member.create') ? route('member.store') : route('member.update', @$member->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />

                            <div class="fv-row mb-6">
                                <x-form.image-upload label="Foto" maxSize="2MB" name="image" :value="@$member->image ?? null"
                                    nullable='1' />
                            </div>

                            {{-- Nama Anggota (ID) --}}
                            <div class="fv-row mb-6">
                                <label for="name" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Nama Anggota</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', @$member->name) }}" placeholder="Masukkan Nama Anggota"
                                    required />
                            </div>

                            {{-- Type Anggota --}}
                            <div class="fv-row mb-6">
                                <label for="type" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Jenis Anggota</span>
                                </label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">-- Pilih Jenis Anggota --</option>
                                    <option value="organization"
                                        {{ old('type', @$member->type ?? @$type) == 'organization' ? 'selected' : '' }}>
                                        Organisasi
                                    </option>
                                    <option value="bpp"
                                        {{ old('type', @$member->type ?? @$type) == 'bpp' ? 'selected' : '' }}>
                                        BPP
                                    </option>
                                </select>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('organization.index') }}">
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
