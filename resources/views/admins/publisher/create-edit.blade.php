@extends('layouts.master', ['main' => 'Data Publisher', 'title' => request()->routeIs('publisher.create') ? 'Tambah Publisher' : 'Edit Publisher'])
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
                                {{ request()->routeIs('publisher.create') ? 'Tambah Publisher' : 'Edit Publisher' }}
                            </span>
                        </h3>
                        <!--end::Card title-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-5">
                        <!--begin::Form-->
                        <x-alert.alert-validation />
                        <form id="publisher-form"
                            action="{{ request()->routeIs('publisher.create') ? route('publisher.store') : route('publisher.update', @$publisher->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />

                            <!-- Tabs Navigation -->
                            <ul class="nav nav-tabs nav-line-tabs mb-6 border-bottom">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_data_admin">
                                        <span class="d-flex align-items-center">
                                            <i class="fas fa-user me-2"></i>Data Admin
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_publisher_info">
                                        <span class="d-flex align-items-center">
                                            <i class="fas fa-building me-2"></i>Informasi Publisher
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_dokumen">
                                        <span class="d-flex align-items-center">
                                            <i class="fas fa-file me-2"></i>Dokumen & Legal
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_doi_config">
                                        <span class="d-flex align-items-center">
                                            <i class="fas fa-key me-2"></i>Konfigurasi DOI
                                        </span>
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="kt_tab_content">
                                <!-- Tab 1: Data Admin -->
                                <div class="tab-pane fade show active" id="kt_tab_data_admin" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="fv-row mb-6">
                                                <label class="fs-6 fw-bold form-label" for="email">
                                                    <span class="required text-dark">Email</span>
                                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                        title="Masukkan Email Publisher"></i>
                                                </label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                    id="email" placeholder="publisher@example.com" name="email"
                                                    value="{{ @$publisher->admin->email ?? old('email') }}" required />
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="fv-row mb-6">
                                                <label class="fs-6 fw-bold form-label" for="name">
                                                    <span class="required text-dark">Nama Publisher</span>
                                                </label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                    id="name" placeholder="Contoh: PT Jaya Publisher" name="name"
                                                    value="{{ @$publisher->admin->name ?? old('name') }}" required />
                                                @error('name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="fv-row mb-6">
                                                <label class="fs-6 fw-bold form-label" for="password">
                                                    <span class="text-dark">Password</span>
                                                </label>
                                                <input type="password"
                                                    placeholder="{{ @$publisher ? 'Kosongkan jika tidak ingin mengubah' : 'Minimal 8 karakter' }}"
                                                    class="form-control @error('password') is-invalid @enderror" id="password"
                                                    name="password" />
                                                @error('password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="fv-row mb-6">
                                                <label class="fs-6 fw-bold form-label" for="password_confirmation">
                                                    <span class="text-dark">Konfirmasi Password</span>
                                                </label>
                                                <input type="password"
                                                    placeholder="{{ @$publisher ? 'Kosongkan jika tidak ingin mengubah' : '' }}"
                                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                                    id="password_confirmation" name="password_confirmation" />
                                                @error('password_confirmation')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="fv-row mb-6">
                                        <label class="fs-6 fw-bold form-label" for="avatar">
                                            <span class="text-dark">Avatar/Logo</span>
                                        </label>
                                        <div class="input-group mb-3">
                                            <input type="file"
                                                class="form-control @error('avatar') is-invalid @enderror"
                                                id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(this)" />
                                            @error('avatar')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mt-3">
                                            @if (@$publisher?->admin?->avatar)
                                                <img src="{{ @$publisher->admin->avatar }}" alt="Avatar"
                                                    class="rounded object-fit-cover" style="max-height: 150px; max-width: 150px;" />
                                            @else
                                                <img id="avatarPreview" style="display: none; max-height: 150px; max-width: 150px;"
                                                    class="rounded object-fit-cover" />
                                            @endif
                                        </div>
                                    </div> --}}
                                     <div class="fv-row mb-6">
                                        <x-form.image-upload label="Logo / Avatar" maxSize="2MB" name="avatar" :value="@$publisher->avatar ?? null" nullable='1' />
                                    </div>
                                </div>

                                <!-- Tab 2: Informasi Publisher -->
                                <div class="tab-pane fade" id="kt_tab_publisher_info" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="fv-row mb-6">
                                                <label class="fs-6 fw-bold form-label" for="code">
                                                    <span class="required text-dark">Kode Publisher</span>
                                                </label>
                                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                    id="code" placeholder="Contoh: PUB001" name="code"
                                                    value="{{ @$publisher->code ?? old('code') }}" required />
                                                @error('code')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="fv-row mb-6">
                                                <label class="fs-6 fw-bold form-label" for="alias">
                                                    <span class="required text-dark">Alias Publisher</span>
                                                </label>
                                                <input type="text" class="form-control @error('alias') is-invalid @enderror"
                                                    id="alias" placeholder="Contoh: Jaya Pub" name="alias"
                                                    value="{{ @$publisher->alias ?? old('alias') }}" required />
                                                @error('alias')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="fv-row mb-6">
                                                <label class="fs-6 fw-bold form-label" for="type">
                                                    <span class="required text-dark">Tipe Publisher</span>
                                                </label>
                                                <select class="form-select @error('type') is-invalid @enderror" id="type"
                                                    name="type" required>
                                                    <option value="">-- Pilih Tipe --</option>
                                                    <option value="Institusi"
                                                        @if (old('type', @$publisher->type) == 'Institusi') selected @endif>
                                                        Institusi
                                                    </option>
                                                    <option value="Asosiasi"
                                                        @if (old('type', @$publisher->type) == 'Asosiasi') selected @endif>
                                                        Asosiasi
                                                    </option>
                                                    <option value="Yayasan"
                                                        @if (old('type', @$publisher->type) == 'Yayasan') selected @endif>
                                                        Yayasan
                                                    </option>
                                                    <option value="CV" @if (old('type', @$publisher->type) == 'CV') selected @endif>
                                                        CV
                                                    </option>
                                                    <option value="PT" @if (old('type', @$publisher->type) == 'PT') selected @endif>
                                                        PT
                                                    </option>
                                                </select>
                                                @error('type')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="fv-row mb-6">
                                                <label class="fs-6 fw-bold form-label" for="website">
                                                    <span class="text-dark">Website</span>
                                                </label>
                                                <input type="url" class="form-control @error('website') is-invalid @enderror"
                                                    id="website" placeholder="https://example.com" name="website"
                                                    value="{{ @$publisher->website ?? old('website') }}" />
                                                @error('website')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="fv-row mb-6">
                                                <label class="fs-6 fw-bold form-label" for="address">
                                                    <span class="required text-dark">Alamat</span>
                                                </label>
                                                <textarea class="form-control @error('address') is-invalid @enderror"
                                                    id="address" name="address" rows="3"
                                                    placeholder="Masukkan alamat lengkap" required>{{ @$publisher->address ?? old('address') }}</textarea>
                                                @error('address')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="fv-row mb-6">
                                                <label class="fs-6 fw-bold form-label" for="city">
                                                    <span class="required text-dark">Kota</span>
                                                </label>
                                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                                    id="city" placeholder="Contoh: Jakarta" name="city"
                                                    value="{{ @$publisher->city ?? old('city') }}" required />
                                                @error('city')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="fv-row mb-6">
                                                <label class="fs-6 fw-bold form-label" for="contact_name">
                                                    <span class="required text-dark">Nama Kontak</span>
                                                </label>
                                                <input type="text"
                                                    class="form-control @error('contact_name') is-invalid @enderror"
                                                    id="contact_name" placeholder="Nama Contact Person" name="contact_name"
                                                    value="{{ @$publisher->contact_name ?? old('contact_name') }}" required />
                                                @error('contact_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="fv-row mb-6">
                                                <label class="fs-6 fw-bold form-label" for="phone">
                                                    <span class="required text-dark">Telepon/WhatsApp</span>
                                                </label>
                                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                    id="phone" placeholder="Contoh: +62812345678 atau 0812345678"
                                                    name="phone" value="{{ @$publisher->phone ?? old('phone') }}" required />
                                                @error('phone')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 3: Dokumen & Legal -->
                                <div class="tab-pane fade" id="kt_tab_dokumen" role="tabpanel">
                                    <div class="alert alert-info mb-5">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Pastikan link adalah link akses publik dari Google Drive
                                    </div>

                                    <div class="fv-row mb-6">
                                        <label class="fs-6 fw-bold form-label" for="sk_kemenkumham_link">
                                            <span class="text-dark">SK Kemenkumham (Link Google Drive)</span>
                                        </label>
                                        <input type="url"
                                            class="form-control @error('sk_kemenkumham_link') is-invalid @enderror"
                                            id="sk_kemenkumham_link"
                                            placeholder="https://drive.google.com/file/d/..."
                                            name="sk_kemenkumham_link"
                                            value="{{ @$publisher->sk_kemenkumham_link ?? old('sk_kemenkumham_link') }}" />
                                        @error('sk_kemenkumham_link')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="fv-row mb-6">
                                        <label class="fs-6 fw-bold form-label" for="akta_notaris_link">
                                            <span class="text-dark">AKTA Notaris (Link Google Drive)</span>
                                        </label>
                                        <input type="url"
                                            class="form-control @error('akta_notaris_link') is-invalid @enderror"
                                            id="akta_notaris_link"
                                            placeholder="https://drive.google.com/file/d/..."
                                            name="akta_notaris_link"
                                            value="{{ @$publisher->akta_notaris_link ?? old('akta_notaris_link') }}" />
                                        @error('akta_notaris_link')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tab 4: Konfigurasi DOI -->
                                <div class="tab-pane fade" id="kt_tab_doi_config" role="tabpanel">
                                    <div class="fv-row mb-6">
                                        <label class="fs-6 fw-bold form-label" for="prefix_doi">
                                            <span class="required text-dark">Prefix DOI</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Contoh: 10.5555"></i>
                                        </label>
                                        <input type="text" class="form-control @error('prefix_doi') is-invalid @enderror"
                                            id="prefix_doi" placeholder="Contoh: 10.5555" name="prefix_doi"
                                            value="{{ @$publisher->prefix_doi ?? old('prefix_doi') }}" required />
                                        @error('prefix_doi')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="fv-row">
                                        <label class="fs-6 fw-bold form-label">
                                            <span class="text-dark">Prefix DOI Tambahan</span>
                                            <span class="text-muted">(Opsional)</span>
                                        </label>
                                        <div id="additional-prefixes-container">
                                            @php
                                                $additionalPrefixes = @$publisher->additional_prefixes ?? old('additional_prefixes') ?? [];
                                                $count = count($additionalPrefixes);
                                            @endphp
                                            @if ($count > 0)
                                                @foreach ($additionalPrefixes as $index => $prefix)
                                                    <div class="input-group mb-3 prefix-item">
                                                        <input type="text" class="form-control"
                                                            name="additional_prefixes[]" placeholder="Contoh: 10.6666"
                                                            value="{{ $prefix }}" />
                                                        <button class="btn btn-outline-danger" type="button"
                                                            onclick="removePrefix(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-secondary btn-sm mt-3"
                                            onclick="addPrefix()">
                                            <i class="fas fa-plus me-2"></i>Tambah Prefix
                                        </button>
                                        @error('additional_prefixes')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between mt-8">
                                <a href="{{ route('publisher.index') }}" class="btn btn-light">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-light me-3">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        {{ request()->routeIs('publisher.create') ? 'Tambah Publisher' : 'Update Publisher' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Contacts-->
            </div>
            <!--end::Content-->
        </div>
    </div>
    <!--end::Container-->
@endsection

@push('js')
    <script>
        function previewAvatar(input) {
            const preview = document.getElementById('avatarPreview');
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        function addPrefix() {
            const container = document.getElementById('additional-prefixes-container');
            const newItem = document.createElement('div');
            newItem.className = 'input-group mb-3 prefix-item';
            newItem.innerHTML = `
                <input type="text" class="form-control" name="additional_prefixes[]" placeholder="Contoh: 10.6666" />
                <button class="btn btn-outline-danger" type="button" onclick="removePrefix(this)">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            container.appendChild(newItem);
        }

        function removePrefix(button) {
            button.closest('.prefix-item').remove();
        }

        // Initialize tooltips
        $(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
