@extends('layouts.master', ['title' => isset($author) ? 'Edit Author' : 'Tambah Author', 'main' => 'Dashboard'])
@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="app-main pt-6 flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card card-flush">
                    <div class="card-header mt-4">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">{{ isset($author) ? 'Edit Author' : 'Tambah Author' }}</span>
                            <span class="card-text fs-7 fw-semibold text-gray-500">Kelola data author</span>
                        </h3>
                    </div>
                    <form action="{{ isset($author) ? route('author.update', $author->id) : route('author.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <x-form.put-method />
                        <div class="card-body">
                            <div class="row gy-4">
                                <div class="col-12 col-md-4">
                                    <div class="mb-3 text-center">
                                        <div class="fv-row mb-6">
                                            <x-form.image-upload label="Avatar" maxSize="2MB" name="avatar" :value="@$author->admin->avatar ?? null"
                                                nullable='1' />
                                            <div class="form-text text-muted mt-2">Gunakan avatar JPG/PNG maksimal 2MB. Rekomendasi rasio 1:1 untuk tampilan terbaik.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required" for="name">Nama</label>
                                            <input id="name" type="text" name="name" value="{{ old('name', $author->admin->name ?? '') }}" class="form-control form-control-solid @error('name') is-invalid @enderror" required placeholder="Nama lengkap (contoh: Budi Santoso)" autocomplete="name" autofocus>
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required" for="email">Email</label>
                                            <input id="email" type="email" name="email" value="{{ old('email', $author->admin->email ?? '') }}" class="form-control form-control-solid @error('email') is-invalid @enderror" required placeholder="email@domain.com" autocomplete="email">
                                            <div class="form-text text-muted">Gunakan email aktif untuk login.</div>
                                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="password">Password</label>
                                            <input id="password" type="password" name="password" class="form-control form-control-solid @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak ingin mengubah (min 8 karakter)" autocomplete="new-password">
                                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control form-control-solid" placeholder="Ulangi password" autocomplete="new-password">
                                        </div>
                                    </div>
                                        <div class="mb-3">
                                        <label class="form-label" for="institution">Institusi</label>
                                        <input id="institution" type="text" name="institution" value="{{ old('institution', $author->institution ?? '') }}" class="form-control form-control-solid @error('institution') is-invalid @enderror" placeholder="Nama institusi atau organisasi (opsional)" autocomplete="organization">
                                        @error('institution')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                        <div class="mb-3">
                                        <label class="form-label" for="phone">Telepon/WA</label>
                                        <input id="phone" type="text" inputmode="tel" name="phone" value="{{ old('phone', $author->phone ?? '') }}" class="form-control form-control-solid @error('phone') is-invalid @enderror" placeholder="Contoh: +6281234567890 atau 081234567890">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                        <div class="mb-3">
                                        <label class="form-label required" for="is_active">Status</label>
                                        <select id="is_active" name="is_active" class="form-select form-select-solid">
                                            <option value="" disabled>Pilih status</option>
                                            <option value="1" {{ old('is_active', $author->admin->is_active ?? 1) == 1 ? 'selected' : '' }}>Aktif</option>
                                            <option value="0" {{ old('is_active', $author->admin->is_active ?? 1) == 0 ? 'selected' : '' }}>Nonaktif</option>
                                        </select>
                                    </div>
                                    @if(isset($author))
                                    <div class="mb-3">
                                        <label class="form-label" for="registration_number">No. Registrasi</label>
                                        <input id="registration_number" type="text" value="{{ $author->registration_number }}" class="form-control form-control-solid" readonly>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('author.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button class="btn btn-primary">Simpan</button>
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
 // Optional: JS code to preview avatar when selected
 $('input[name="avatar"]').on('change', function(e) {
     const input = this;
     if (input.files && input.files[0]) {
         const reader = new FileReader();
         reader.onload = function(e) {
             $(input).closest('.mb-3').find('img').attr('src', e.target.result);
         }
         reader.readAsDataURL(input.files[0]);
     }
 })
</script>
@endpush
