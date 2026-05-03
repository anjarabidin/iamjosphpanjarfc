@extends('layouts.master', ['main' => 'Data Jabatan', 'title' => request()->routeIs('organization.create') ? 'Tambah Jabatan' : 'Edit Jabatan'])

@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <div class="col-xl-12">
                <div class="card h-lg-100" id="kt_contacts_main">
                    <div class="card-header" id="kt_chat_contacts_header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">
                                {{ request()->routeIs('organization.create') ? 'Tambah Jabatan' : 'Edit Jabatan' }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <x-alert.alert-validation />

                        <form id="organization"
                            action="{{ request()->routeIs('organization.create')
                                ? route('organization.store')
                                : route('organization.update', @$organization->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />

                            {{-- Nama Jabatan (ID) --}}
                            <div class="fv-row mb-6">
                                <label for="name" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Nama Jabatan</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', @$organization->name) }}" placeholder="Masukkan Nama Jabatan"
                                    required />
                            </div>

                            {{-- Nama Jabatan (EN) + Button Translate --}}
                            <div class="fv-row mb-6">
                                <label for="name_en"
                                    class="fs-6 fw-bold form-label mt-3 d-flex align-items-center justify-content-between">
                                    <span class="text-dark">Nama Jabatan (EN)</span>
                                    <button type="button" id="btn-translate-name" class="btn btn-light-primary btn-sm">
                                        <i class="fa fa-language me-1"></i> Translate dari Indonesia
                                    </button>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        value="{{ old('name_en', @$organization->name_en) }}"
                                        placeholder="Masukkan Nama Jabatan (EN)" required />
                                    <span class="input-group-text bg-transparent border-start-0 ps-0">
                                        <span id="spin-translate-name"
                                            class="spinner-border spinner-border-sm d-none"></span>
                                    </span>
                                </div>
                                <small class="text-muted">Klik tombol “Translate dari Indonesia” untuk mengisi
                                    otomatis.</small>
                            </div>

                            {{-- Urutan --}}
                            <div class="fv-row mb-6">
                                <label for="order" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Urutan</span>
                                </label>
                                <input type="number" class="form-control" id="order" name="order"
                                    value="{{ old('order', @$organization->order) }}" placeholder="Masukkan Urutan"
                                    required />
                            </div>

                            {{-- Parent (Select2) --}}
                            <div class="fv-row mb-6">
                                <label for="parent_id" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Parent</span>
                                </label>
                                <select name="parent_id" id="parent_id" class="form-select">
                                    <option value="">Pilih Parent</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}"
                                            {{ old('parent_id', @$organization->parent_id) == $position->id ? 'selected' : '' }}>
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Member (Select2) --}}
                            <div class="fv-row mb-6">
                                <label for="member_id" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Member</span>
                                </label>
                                <select name="member_id" id="member_id" class="form-select">
                                    <option value="">Pilih Member</option>
                                    @foreach ($members as $member)
                                        <option value="{{ $member->id }}"
                                            {{ old('member_id', @$organization->member_id) == $member->id ? 'selected' : '' }}>
                                            {{ $member->name }}
                                        </option>
                                    @endforeach
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

@push('css')
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- Optional: Tema Select2 Bootstrap-5 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endpush

@push('js')
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

    <script>
        // Inisialisasi Select2
        $(function() {
            $('#parent_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Parent',
                allowClear: true,
                width: '100%'
            });

            $('#member_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Member',
                allowClear: true,
                width: '100%'
            });
        });

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
    </script>
@endpush
