@extends('layouts.master', ['main' => 'Data Hotel', 'title' => request()->routeIs('hotel-booking.create') ? 'Tambah Hotel' : 'Edit Hotel'])

@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <div class="col-xl-12">
                <div class="card h-lg-100" id="kt_contacts_main">
                    <div class="card-header" id="kt_chat_contacts_header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">
                                {{ request()->routeIs('hotel-booking.create') ? 'Tambah Hotel' : 'Edit Hotel' }}
                            </span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <x-alert.alert-validation />
                        <form id="hotelBooking"
                            action="{{ request()->routeIs('hotel-booking.create') ? route('hotel-booking.store') : route('hotel-booking.update', @$hotelBooking->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />

                            {{-- Gambar --}}
                            <div class="fv-row mb-6">
                                <x-form.image-upload label="Foto" maxSize="2MB" name="image" :value="@$hotelBooking->image ?? null"
                                    id="image" nullable='1' />
                            </div>

                            <div class="fv-row mb-6">
                                <label for="name" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Nama</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', @$hotelBooking->name) }}" placeholder="Masukkan Nama" required />
                            </div>

                            <div class="fv-row mb-6">
                                <label for="price" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Harga</span>
                                </label>
                                <input type="text"class="form-control input-money" id="price" name="price"
                                    value="{{ old('price', @$hotelBooking->price) }}" placeholder="Masukkan Harga"
                                    required />
                            </div>

                            <div class="fv-row mb-6">
                                <label for="rating" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Rating</span>
                                </label>
                                <input type="text" class="form-control" id="rating" name="rating"
                                    value="{{ old('rating', @$hotelBooking->rating) }}" placeholder="Masukkan Rating"
                                    required />
                            </div>

                            <div class="fv-row mb-6">
                                <label for="url" class="fs-6 fw-bold form-label mt-3">
                                    <span class="text-dark">Url</span>
                                </label>
                                <input type="url" class="form-control" id="url" name="url"
                                    value="{{ old('url', @$hotelBooking->url) }}" placeholder="Masukkan Url" required />
                            </div>
                            {{-- Urutan --}}
                            @if (request()->routeIs('hotel-booking.edit'))
                                <div class="fv-row mb-6">
                                    <label for="is_active" class="fs-6 fw-bold form-label mt-3">
                                        <span class="text-dark">Status</span>
                                    </label>
                                    <select class="form-select" id="is_active" name="is_active" required>
                                        <option value="1" {{ @$hotelBooking->is_active == 1 ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="0" {{ @$hotelBooking->is_active == 0 ? 'selected' : '' }}>Tidak
                                            Aktif</option>
                                    </select>
                                </div>
                            @endif

                            {{-- Tombol Aksi --}}
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('hotel-booking.index') }}">
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
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('hotelBooking');
            const price = document.getElementById('price');

            const formatIDR = (num) => {
                if (isNaN(num) || num === '') return '';
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(num));
            };

            const getRaw = (val) => (val || '').toString().replace(/[^\d]/g, '');

            // Tampilkan format saat halaman diload (untuk nilai lama dari DB)
            if (price && price.value) {
                price.value = formatIDR(getRaw(price.value));
            }

            // Format saat user mengetik
            price.addEventListener('input', function() {
                const cursorAtEnd = this.selectionStart === this.value.length; // jaga UX
                const raw = getRaw(this.value);
                this.value = formatIDR(raw);
                if (cursorAtEnd) this.setSelectionRange(this.value.length, this.value.length);
            });

            // Kirim angka murni ke server
            form.addEventListener('submit', function() {
                price.value = getRaw(price.value); // jadi "1500000"
            });
        });
    </script>
@endpush
