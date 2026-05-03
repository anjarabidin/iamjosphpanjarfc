@extends('layouts.master', ['title' => 'Kelola Ruang Meeting', 'main' => 'Meeting Room'])

@section('content')
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <div class="col-xl-12">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">
                                Ruang Meeting - {{ $venue->hotel }}
                            </span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">
                                {{ $venue->city_name }}, {{ $venue->province_name }}
                            </span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('venue.rooms.create', $venue->id) }}" class="btn btn-sm btn-primary me-2">
                                <i class="fa fa-plus"></i>
                                Tambah Ruang
                            </a>
                            <a href="{{ route('meeting-room.index', $venue->id) }}" class="btn btn-sm btn-light">
                                <i class="fa fa-arrow-left"></i>
                                Kembali ke Venue
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-5">
                        @if ($rooms->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Foto</th>
                                            <th>Nama Ruang</th>
                                            <th>Layout & Kapasitas</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rooms as $room)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if ($room->photo)
                                                        <div class="symbol symbol-50px">
                                                            <img src="{{ asset('storage/' . $room->photo) }}"
                                                                alt="Foto {{ $room->name }}" class="w-100 rounded"
                                                                style="height: 50px; object-fit: cover;" />
                                                        </div>
                                                    @else
                                                        <div class="symbol symbol-50px">
                                                            <div class="symbol-label bg-light-secondary text-secondary">
                                                                <i class="fa fa-image fs-4"></i>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $room->name }}</strong>
                                                </td>
                                                <td>
                                                    @if ($room->meeting_room_layouts->count() > 0)
                                                        @foreach ($room->meeting_room_layouts as $layout)
                                                            <span class="badge badge-light-primary me-1 mb-1">
                                                                {{ ucfirst(str_replace('_', ' ', $layout->layout)) }}
                                                                @if ($layout->capacity > 0)
                                                                    ({{ $layout->capacity }} orang)
                                                                @endif
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">Belum ada layout</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center">
                                                        <a href="{{ route('venue.rooms.edit', [$venue->id, $room->id]) }}"
                                                            class="btn btn-icon btn-edit btn-active-light-primary w-30px h-30px me-3">
                                                            <i class="ki-duotone ki-notepad-edit fs-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </a>
                                                        <a data-id="formDelete{{ $room->id }}" type="button"
                                                            id="btnDelete{{ $room->id }}"
                                                            class="btn-delete btn btn-icon btn-active-light-primary w-30px h-30px me-3">
                                                            <i class="ki-duotone btn-delete ki-basket fs-2"
                                                                data-id="formDelete{{ $room->id }}">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"
                                                                    data-id="formDelete{{ $room->id }}"></span>
                                                            </i>
                                                        </a>
                                                        <form id="formDelete{{ $room->id }}"
                                                            action="{{ route('venue.rooms.destroy', [$venue->id, $room->id]) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('delete')
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fa fa-door-open text-muted fs-2x mb-3"></i>
                                <h5 class="text-muted">Belum Ada Ruang Meeting</h5>
                                <p class="text-muted mb-4">Venue ini belum memiliki ruang meeting.</p>
                                <a href="{{ route('venue.rooms.create', $venue->id) }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i>
                                    Tambah Ruang Pertama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Handle delete button clicks
            $(document).on('click', '.btn-delete', function() {
                var formId = $(this).data('id');
                var form = $('#' + formId);

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Yakin ingin menghapus ruang meeting ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
