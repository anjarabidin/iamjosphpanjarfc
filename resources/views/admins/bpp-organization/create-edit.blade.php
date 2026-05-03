@extends('layouts.master', ['main' => 'Data Struktur Organisasi BPP', 'title' => request()->routeIs('bpp-organization.create') ? 'Tambah Jabatan BPP' : 'Edit Jabatan BPP'])
@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
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
                            <span class="card-label fw-bold fs-3">
                                {{ request()->routeIs('bpp-organization.create') ? 'Tambah Jabatan BPP' : 'Edit Jabatan BPP' }}
                            </span>
                        </h3>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-5">
                        <!--begin::Form-->
                        <x-alert.alert-validation />
                        <form id="bpp-organization"
                            action="{{ request()->routeIs('bpp-organization.create') ? route('bpp-organization.store') : route('bpp-organization.update', @$organization->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />

                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label for="name" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Nama Jabatan</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Nama Jabatan"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', @$organization->name) }}" placeholder="Masukkan Nama Jabatan"
                                    required />
                                <!--end::Input-->
                            </div>

                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label for="name_en"
                                    class="fs-6 fw-bold form-label mt-3 d-flex align-items-center justify-content-between">
                                    <span class="text-dark">Nama Jabatan (EN)</span>
                                    <button type="button" id="btn-translate-name" class="btn btn-light-primary btn-sm">
                                        <i class="fa fa-language me-1"></i> Translate dari Indonesia
                                    </button>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <div class="input-group">
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        value="{{ old('name_en', @$organization->name_en) }}"
                                        placeholder="Enter Position Name in English" />
                                    <span class="input-group-text bg-transparent border-start-0 ps-0">
                                        <span id="spin-translate-name"
                                            class="spinner-border spinner-border-sm d-none"></span>
                                    </span>
                                </div>
                                <small class="text-muted">Klik tombol "Translate dari Indonesia" untuk mengisi
                                    otomatis.</small>
                                <!--end::Input-->
                            </div>

                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label for="parent_id" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Jabatan Induk</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Pilih Jabatan Induk (Opsional)"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Select-->
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">-- Pilih Jabatan Induk --</option>
                                    @foreach ($positions as $position)
                                        @if ($position->id !== @$organization->id)
                                            <option value="{{ $position->id }}"
                                                {{ old('parent_id', @$organization->parent_id) == $position->id ? 'selected' : '' }}>
                                                {{ $position->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <!--end::Select-->
                            </div>

                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label for="member_id" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Anggota BPP</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Pilih Anggota BPP (Opsional)"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Select-->
                                <select class="form-select" id="member_id" name="member_id">
                                    <option value="">-- Pilih Anggota BPP --</option>
                                    @foreach ($members as $member)
                                        <option value="{{ $member->id }}"
                                            {{ old('member_id', @$organization->member_id) == $member->id ? 'selected' : '' }}>
                                            {{ $member->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <!--end::Select-->
                                <div class="form-text">
                                    <small>Jika anggota belum ada, <a
                                            href="{{ route('member.create', ['type' => 'bpp']) }}" target="_blank">tambah
                                            anggota BPP baru</a></small>
                                </div>
                            </div>

                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label for="order" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Urutan</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Urutan Jabatan"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="number" class="form-control" id="order" name="order"
                                    value="{{ old('order', @$organization->order ?? 0) }}" placeholder="Masukkan Urutan"
                                    min="0" required />
                                <!--end::Input-->
                            </div>

                            <!--end::Separator-->
                            <!--begin::Action buttons-->
                            <div class="d-flex justify-content-end">
                                <!--begin::Button-->
                                <a href="{{ route('bpp-organization.index') }}">
                                    <button type="button" data-kt-contacts-type="cancel"
                                        class="btn btn-secondary me-3">Batal</button>
                                </a>
                                <!--end::Button-->
                                <!--begin::Button-->
                                <button type="submit" data-kt-contacts-type="submit" class="btn btn-primary btn-sm">
                                    <span class="indicator-label">Simpan</span>
                                    <span class="indicator-progress">Mohon Tunggu...
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
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Tombol Translate: name -> name_en
            $('#btn-translate-name').on('click', async function() {
                const btn = $(this);
                const spin = $('#spin-translate-name');
                const fromSel = '#name';
                const toSel = '#name_en';

                btn.prop('disabled', true);
                spin.removeClass('d-none');

                try {
                    // Jika ada fungsi global translate(selectorFrom, selectorTo), gunakan
                    if (typeof translate === 'function') {
                        const out = translate(fromSel, toSel);
                        if (out && typeof out.then === 'function') {
                            await out; // kalau asynchronous
                        }
                    } else {
                        // Fallback: copy teks ID -> EN (agar tidak kosong saat slicing)
                        const src = $(fromSel).val() || '';
                        if (src && !$(toSel).val()) {
                            $(toSel).val(src);
                        } else if (src) {
                            // Jika sudah ada, tetap overwrite agar konsisten
                            $(toSel).val(src);
                        }
                    }
                } catch (err) {
                    console.error(err);
                    // Fallback terakhir: copy
                    const src = $(fromSel).val() || '';
                    if (src) $(toSel).val(src);
                } finally {
                    btn.prop('disabled', false);
                    spin.addClass('d-none');
                }
            });
        });
    </script>
@endpush
