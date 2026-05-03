@extends('layouts.master', ['main' => 'Data Meeting Room', 'title' => request()->routeIs('meeting-room.create') ? 'Tambah Meeting Venue' : 'Edit Meeting Venue'])

@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <div class="col-xl-12">
                <div class="card h-lg-100" id="kt_contacts_main">
                    <div class="card-header" id="kt_chat_contacts_header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">
                                {{ request()->routeIs('meeting-room.create') ? 'Tambah Meeting Venue' : 'Edit Meeting Venue' }}
                            </span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('meeting-room.index') }}" class="btn btn-sm btn-light">
                                <i class="fa fa-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-5">
                        <x-alert.alert-validation />
                        <form id="meetingRoom"
                            action="{{ request()->routeIs('meeting-room.create') ? route('meeting-room.store') : route('meeting-room.update', @$meetingRoom->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />

                            {{-- Hotel Name --}}
                            <div class="fv-row mb-6">
                                <label for="name" class="fs-6 fw-bold form-label">
                                    <span class="text-dark">Nama Hotel/Resort</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', @$meetingRoom->name) }}" placeholder="Masukkan nama hotel/resort"
                                    required />
                            </div>

                            <div class="fv-row mb-6">
                                <label for="type" class="fs-6 fw-bold form-label">
                                    <span class="text-dark">Tipe</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="">Pilih Tipe</option>
                                    @foreach (['HOTEL', 'RESORT'] as $type)
                                        <option value="{{ $type }}"
                                            {{ old('type', @$meetingRoom->type) == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Address --}}
                            <div class="fv-row mb-6">
                                <label for="address" class="fs-6 fw-bold form-label">
                                    <span class="text-dark">Alamat</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Masukkan alamat lengkap"
                                    required>{{ old('address', @$meetingRoom->address) }}</textarea>
                            </div>

                            {{-- Location --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="fv-row mb-6">
                                        <label for="province_id" class="fs-6 fw-bold form-label">
                                            <span class="text-dark">Provinsi</span>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="province_id" name="province_id" required>
                                            <option value="">Pilih Provinsi</option>
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->id }}"
                                                    {{ old('province_id', @$meetingRoom->province_id) == $province->id ? 'selected' : '' }}>
                                                    {{ $province->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="fv-row mb-6">
                                        <label for="regency_id" class="fs-6 fw-bold form-label">
                                            <span class="text-dark">Kota/Kabupaten</span>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="regency_id" name="regency_id" required>
                                            <option value="">Pilih Kota/Kabupaten</option>
                                            @if (isset($regencies))
                                                @foreach ($regencies as $regency)
                                                    <option value="{{ $regency->id }}"
                                                        {{ old('regency_id', @$meetingRoom->regency_id) == $regency->id ? 'selected' : '' }}>
                                                        {{ $regency->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Contact Information --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="fv-row mb-6">
                                        <label for="email" class="fs-6 fw-bold form-label">
                                            <span class="text-dark">Email</span>
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', @$meetingRoom->email) }}"
                                            placeholder="contoh@email.com" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="fv-row mb-6">
                                        <label for="phone" class="fs-6 fw-bold form-label">
                                            <span class="text-dark">Telepon</span>
                                        </label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="{{ old('phone', @$meetingRoom->phone) }}" placeholder="021-xxxxx" />
                                    </div>
                                </div>
                            </div>

                            {{-- Max Capacity --}}
                            <div class="fv-row mb-6">
                                <label for="max_capacity" class="fs-6 fw-bold form-label">
                                    <span class="text-dark">Kapasitas Maksimum</span>
                                </label>
                                <input type="number" class="form-control" id="max_capacity" name="max_capacity"
                                    value="{{ old('max_capacity', @$meetingRoom->max_capacity) }}" placeholder="100"
                                    min="1" />
                                <div class="form-text">Kapasitas maksimum orang dalam venue ini</div>
                            </div>

                            {{-- Address --}}
                            <div class="fv-row mb-6">
                                <x-form.image-upload label="Foto Logo" maxSize="2MB" name="thumbnail" :value="@$meetingRoom->thumbnail ?? null"
                                    nullable='1' />
                            </div>

                            {{-- Submit Button --}}
                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-light me-3">Reset</button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">
                                        {{ request()->routeIs('meeting-room.create') ? 'Simpan' : 'Perbarui' }}
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
        $(document).ready(function() {
            // Handle province change
            $('#province_id').change(function() {
                const provinceId = $(this).val();
                const regencySelect = $('#regency_id');

                console.log('Province changed to:', provinceId);

                if (provinceId) {
                    console.log('Making AJAX request to:', `/meeting-room/regencies/${provinceId}`);
                    $.ajax({
                        url: `/meeting-room/regencies/${provinceId}`,
                        type: 'GET',
                        success: function(data) {
                            console.log('AJAX success, received data:', data);
                            regencySelect.empty();
                            regencySelect.append(
                                '<option value="">Pilih Kota/Kabupaten</option>');

                            $.each(data, function(index, regency) {
                                regencySelect.append(
                                    `<option value="${regency.id}">${regency.name}</option>`
                                );
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', error);
                            console.error('Response:', xhr.responseText);
                            alert('Gagal memuat data kota/kabupaten: ' + error);
                        }
                    });
                } else {
                    regencySelect.empty();
                    regencySelect.append('<option value="">Pilih Kota/Kabupaten</option>');
                }
            });
        });
    </script>
@endpush
