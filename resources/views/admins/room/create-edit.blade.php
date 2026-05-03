@extends('layouts.master', ['main' => 'Kelola Ruang Meeting', 'title' => isset($room) ? 'Edit Ruang Meeting' : 'Tambah Ruang Meeting'])

@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <div class="col-xl-12">
                <div class="card h-lg-100" id="kt_contacts_main">
                    <div class="card-header" id="kt_chat_contacts_header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">
                                {{ isset($room) ? 'Edit Ruang Meeting' : 'Tambah Ruang Meeting' }}
                            </span>
                            <span class="text-muted fs-7">Venue: {{ $venue->hotel }}</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('venue.rooms.index', $venue->id) }}" class="btn btn-sm btn-light">
                                <i class="fa fa-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-5">
                        <x-alert.alert-validation />
                        <form id="roomForm"
                            action="{{ isset($room) ? route('venue.rooms.update', [$venue->id, $room->id]) : route('venue.rooms.store', $venue->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($room))
                                @method('PUT')
                            @endif

                            {{-- Room Name --}}
                            <div class="fv-row mb-8">
                                <label for="name" class="fs-6 fw-bold form-label">
                                    <span class="text-dark">Nama Ruang</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name"
                                    value="{{ old('name', @$room->name) }}" placeholder="Contoh: Ballroom A, Ruang Rapat 1"
                                    required />
                                <div class="form-text">Nama ruang meeting yang akan ditampilkan</div>
                            </div>

                            {{-- Room Photo --}}
                            <div class="fv-row mb-8">
                                <label for="photo" class="fs-6 fw-bold form-label">
                                    <span class="text-dark">Foto Ruang</span>
                                </label>
                                <input type="file" class="form-control" id="photo" name="photo"
                                    accept="image/jpeg,image/png,image/jpg,image/webp" />
                                <div class="form-text">Format yang diizinkan: JPEG, PNG, JPG, WebP. Maksimal 5MB.</div>

                                @if (isset($room) && $room->photo)
                                    <div class="mt-4">
                                        <label class="form-label fw-semibold">Foto Saat Ini:</label>
                                        <div class="symbol symbol-100px">
                                            <img src="{{ asset('storage/' . $room->photo) }}" alt="Foto {{ $room->name }}"
                                                class="w-100 rounded border" style="height: 150px; object-fit: cover;" />
                                        </div>
                                    </div>
                                @endif

                                {{-- Photo Preview --}}
                                <div id="photo-preview" class="mt-4" style="display: none;">
                                    <label class="form-label fw-semibold">Preview Foto:</label>
                                    <div class="symbol symbol-100px">
                                        <img id="preview-image" src="" alt="Preview" class="w-100 rounded border"
                                            style="height: 150px; object-fit: cover;" />
                                    </div>
                                </div>
                            </div>

                            {{-- Room Layouts Section --}}
                            <div class="separator separator-dashed my-8"></div>

                            <div class="fv-row mb-8">
                                <label class="fs-6 fw-bold form-label mb-5">
                                    <span class="text-dark">Layout & Kapasitas Ruang</span>
                                </label>
                                <div class="form-text mb-3">Tambahkan berbagai layout yang tersedia untuk ruang ini beserta
                                    kapasitasnya</div>
                                @if ($venue->max_capacity > 0)
                                    <div class="alert alert-info d-flex align-items-center mb-3">
                                        <i class="fa fa-info-circle me-2"></i>
                                        <span>Kapasitas maksimal venue: <strong>{{ number_format($venue->max_capacity) }}
                                                orang</strong></span>
                                    </div>
                                    @if (isset($remainingCapacity))
                                        <div
                                            class="alert alert-{{ $remainingCapacity > 0 ? 'success' : 'warning' }} d-flex align-items-center mb-5">
                                            <i
                                                class="fa fa-{{ $remainingCapacity > 0 ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                                            <span>Sisa kapasitas yang tersedia:
                                                <strong>{{ number_format(max(0, $remainingCapacity)) }} orang</strong>
                                                @if ($remainingCapacity <= 0)
                                                    - Kapasitas venue sudah penuh!
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                @endif

                                <div id="layouts-container">
                                    @if (isset($room) && $room->meeting_room_layouts->count() > 0)
                                        @foreach ($room->meeting_room_layouts as $index => $layout)
                                            <div class="layout-item border rounded p-4 mb-4"
                                                data-index="{{ $index }}">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0 text-gray-800">Layout {{ $index + 1 }}</h6>
                                                    <button type="button"
                                                        class="btn btn-sm btn-light-danger remove-layout">
                                                        <i class="fa fa-trash"></i>
                                                        Hapus
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Jenis Layout</label>
                                                        <select class="form-control"
                                                            name="layouts[{{ $index }}][layout]" required>
                                                            <option value="">Pilih Layout</option>
                                                            <option value="Theater"
                                                                {{ old("layouts.$index.layout", $layout->layout) == 'Theater' ? 'selected' : '' }}>
                                                                Theater</option>
                                                            <option value="Classroom"
                                                                {{ old("layouts.$index.layout", $layout->layout) == 'Classroom' ? 'selected' : '' }}>
                                                                Classroom</option>
                                                            <option value="Boardroom"
                                                                {{ old("layouts.$index.layout", $layout->layout) == 'Boardroom' ? 'selected' : '' }}>
                                                                Boardroom</option>
                                                            <option value="U-Shape"
                                                                {{ old("layouts.$index.layout", $layout->layout) == 'U-Shape' ? 'selected' : '' }}>
                                                                U-Shape</option>
                                                            <option value="Banquet"
                                                                {{ old("layouts.$index.layout", $layout->layout) == 'Banquet' ? 'selected' : '' }}>
                                                                Banquet</option>
                                                            <option value="Cocktail"
                                                                {{ old("layouts.$index.layout", $layout->layout) == 'Cocktail' ? 'selected' : '' }}>
                                                                Cocktail</option>
                                                            <option value="Imperial"
                                                                {{ old("layouts.$index.layout", $layout->layout) == 'Imperial' ? 'selected' : '' }}>
                                                                Imperial</option>
                                                            <option value="Hollow Square"
                                                                {{ old("layouts.$index.layout", $layout->layout) == 'Hollow Square' ? 'selected' : '' }}>
                                                                Hollow Square</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Kapasitas (orang)</label>
                                                        <input type="number" class="form-control capacity-input"
                                                            name="layouts[{{ $index }}][capacity]"
                                                            value="{{ old("layouts.$index.capacity", $layout->capacity) }}"
                                                            placeholder="Contoh: 50" min="1"
                                                            @if ($venue->max_capacity > 0) max="{{ $venue->max_capacity }}" @endif />
                                                        @if ($venue->max_capacity > 0)
                                                            <div class="form-text">Maksimal:
                                                                {{ number_format($venue->max_capacity) }} orang</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="layout-item border rounded p-4 mb-4" data-index="0">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0 text-gray-800">Layout</h6>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Jenis Layout</label>
                                                    <select class="form-control" name="layouts[0][layout]" required>
                                                        <option value="">Pilih Layout</option>
                                                        <option value="Theater"
                                                            {{ old('layouts.0.layout') == 'Theater' ? 'selected' : '' }}>
                                                            Theater</option>
                                                        <option value="Classroom"
                                                            {{ old('layouts.0.layout') == 'Classroom' ? 'selected' : '' }}>
                                                            Classroom</option>
                                                        <option value="Boardroom"
                                                            {{ old('layouts.0.layout') == 'Boardroom' ? 'selected' : '' }}>
                                                            Boardroom</option>
                                                        <option value="U-Shape"
                                                            {{ old('layouts.0.layout') == 'U-Shape' ? 'selected' : '' }}>
                                                            U-Shape</option>
                                                        <option value="Banquet"
                                                            {{ old('layouts.0.layout') == 'Banquet' ? 'selected' : '' }}>
                                                            Banquet</option>
                                                        <option value="Cocktail"
                                                            {{ old('layouts.0.layout') == 'Cocktail' ? 'selected' : '' }}>
                                                            Cocktail</option>
                                                        <option value="Imperial"
                                                            {{ old('layouts.0.layout') == 'Imperial' ? 'selected' : '' }}>
                                                            Imperial</option>
                                                        <option value="Hollow Square"
                                                            {{ old('layouts.0.layout') == 'Hollow Square' ? 'selected' : '' }}>
                                                            Hollow Square</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Kapasitas (orang)</label>
                                                    <input type="number" class="form-control capacity-input"
                                                        name="layouts[0][capacity]"
                                                        value="{{ old('layouts.0.capacity') }}" placeholder="Contoh: 50"
                                                        min="1"
                                                        @if ($venue->max_capacity > 0) max="{{ $venue->max_capacity }}" @endif />
                                                    @if ($venue->max_capacity > 0)
                                                        <div class="form-text">Maksimal:
                                                            {{ number_format($venue->max_capacity) }} orang</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-center mt-4">
                                </div>
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="separator separator-dashed my-8"></div>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('venue.rooms.index', $venue->id) }}" class="btn btn-light">
                                    <i class="fa fa-times"></i>
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i>
                                    {{ isset($room) ? 'Perbarui Ruang' : 'Simpan Ruang' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Layout Template --}}
    <template id="layout-template">
        <div class="layout-item border rounded p-4 mb-4" data-index="__INDEX__">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-gray-800">Layout __NUMBER__</h6>
                <button type="button" class="btn btn-sm btn-light-danger remove-layout">
                    <i class="fa fa-trash"></i>
                    Hapus
                </button>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jenis Layout</label>
                    <select class="form-control" name="layouts[__INDEX__][layout]" required>
                        <option value="">Pilih Layout</option>
                        <option value="Theater">Theater</option>
                        <option value="Classroom">Classroom</option>
                        <option value="Boardroom">Boardroom</option>
                        <option value="U-Shape">U-Shape</option>
                        <option value="Banquet">Banquet</option>
                        <option value="Cocktail">Cocktail</option>
                        <option value="Imperial">Imperial</option>
                        <option value="Hollow Square">Hollow Square</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Kapasitas (orang)</label>
                    <input type="number" class="form-control capacity-input" name="layouts[__INDEX__][capacity]"
                        placeholder="Contoh: 50" min="1"
                        @if ($venue->max_capacity > 0) max="{{ $venue->max_capacity }}" @endif />
                    @if ($venue->max_capacity > 0)
                        <div class="form-text">Maksimal: {{ number_format($venue->max_capacity) }} orang</div>
                    @endif
                </div>
            </div>
        </div>
    </template>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let layoutIndex = {{ isset($room) ? $room->meeting_room_layouts->count() : 1 }};

            // Add layout functionality
            $('#add-layout').click(function() {
                const template = $('#layout-template').html();
                const newLayout = template
                    .replace(/__INDEX__/g, layoutIndex)
                    .replace(/__NUMBER__/g, layoutIndex + 1);

                $('#layouts-container').append(newLayout);
                layoutIndex++;
            });

            // Remove layout functionality
            $(document).on('click', '.remove-layout', function() {
                const container = $('#layouts-container');
                if (container.find('.layout-item').length > 1) {
                    $(this).closest('.layout-item').remove();
                    // Re-index remaining items
                    reindexLayouts();
                } else {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Minimal harus ada satu layout untuk ruang meeting.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            });

            // Reindex layouts after removal
            function reindexLayouts() {
                $('#layouts-container .layout-item').each(function(index) {
                    $(this).attr('data-index', index);
                    $(this).find('h6').text('Layout ' + (index + 1));

                    // Update input names
                    $(this).find('select').attr('name', 'layouts[' + index + '][layout]');
                    $(this).find('input[type="number"]').attr('name', 'layouts[' + index + '][capacity]');
                });

                layoutIndex = $('#layouts-container .layout-item').length;
            }

            // Form validation
            $('#roomForm').submit(function(e) {
                let hasValidLayout = false;

                $('.layout-item').each(function() {
                    const layoutSelect = $(this).find('select[name*="[layout]"]');
                    if (layoutSelect.val() !== '') {
                        hasValidLayout = true;
                        return false; // Break the loop
                    }
                });

                if (!hasValidLayout) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Minimal harus ada satu layout yang terisi untuk ruang meeting.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            });

            // Photo preview functionality
            $('#photo').change(function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#preview-image').attr('src', e.target.result);
                        $('#photo-preview').show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#photo-preview').hide();
                }
            });

            // Capacity validation
            const maxCapacity = {{ $venue->max_capacity ?? 0 }};
            const remainingCapacity = {{ $remainingCapacity ?? 0 }};

            // Real-time capacity validation
            $(document).on('input', '.capacity-input', function() {
                validateCapacities();
            });

            function validateCapacities() {
                if (maxCapacity <= 0) return;

                // Find highest capacity among all layouts
                let highestCapacity = 0;
                $('.capacity-input').each(function() {
                    const value = parseInt($(this).val()) || 0;
                    if (value > highestCapacity) {
                        highestCapacity = value;
                    }
                });

                // Check if highest capacity exceeds remaining capacity
                $('.capacity-input').each(function() {
                    const value = parseInt($(this).val()) || 0;

                    if (value > remainingCapacity) {
                        $(this).addClass('is-invalid');
                        // Show error message
                        if (!$(this).siblings('.invalid-feedback').length) {
                            $(this).after(
                                '<div class="invalid-feedback">Kapasitas melebihi sisa kapasitas venue (' +
                                remainingCapacity.toLocaleString() + ' orang)</div>');
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).siblings('.invalid-feedback').remove();
                    }
                });
            }

            // Enhanced form validation
            $('#roomForm').on('submit', function(e) {
                let hasError = false;
                let highestCapacity = 0;

                // Find highest capacity among all layouts
                $('.capacity-input').each(function() {
                    const value = parseInt($(this).val()) || 0;
                    if (value > highestCapacity) {
                        highestCapacity = value;
                    }
                });

                // Validate against remaining capacity
                if (maxCapacity > 0 && highestCapacity > remainingCapacity) {
                    hasError = true;
                    $('.capacity-input').each(function() {
                        const value = parseInt($(this).val()) || 0;
                        if (value > remainingCapacity) {
                            $(this).addClass('is-invalid');
                        }
                    });
                }

                if (hasError) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validasi Gagal!',
                        text: 'Kapasitas ruang (' + highestCapacity.toLocaleString() +
                            ' orang) melebihi sisa kapasitas venue (' + remainingCapacity
                            .toLocaleString() + ' orang)',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            });
        });
    </script>
@endsection
