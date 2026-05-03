@extends('layouts.master', ['title' => 'Meeting Room', 'main' => 'Dashboard'])
@section('content')
    <div class="app-main pt-6 flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Card-->
                    <div class="card card-flush">
                        <!--begin::Card header-->
                        <div class="card-header mt-6">
                            <!--begin::Card title-->
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Meeting Room</span>
                                <span class="text-gray-500 mt-1 fw-semibold fs-6">Kelola data meeting room dan venue</span>
                            </h3>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Sync Button-->
                                <form action="{{ route('meeting-room.sync') }}" method="POST" class="d-inline me-2">
                                    @csrf
                                    <button type="submit" class="btn btn-info btn-sm">
                                        <i class="fa fa-sync"></i>
                                        Sinkronisasi Data PHRI
                                    </button>
                                </form>
                                <!--end::Sync Button-->
                                <!--begin::Add Button-->
                                {{-- <a type="button" class="btn btn-primary btn-sm disabled"
                                    href="{{ route('meeting-room.create') }}">
                                    <i class="fa fa-plus"></i>
                                    Tambah Venue
                                </a> --}}
                                <!--end::Add Button-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Search-->
                            <div class="card card-flush mb-5">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="fw-bold m-0">Filter & Pencarian Venue</h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <button type="button" class="btn btn-sm btn-light" id="reset-filters">
                                            <i class="fa fa-refresh"></i> Reset Filter
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Pencarian Global</label>
                                            <div class="position-relative">
                                                <input type="text" id="search-venue" class="form-control"
                                                    placeholder="Cari hotel, alamat, provinsi, atau kota...">
                                                <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                                                    <i class="fa fa-search text-gray-400"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Filter Provinsi</label>
                                            <select id="filter-province" class="form-select">
                                                <option value="">Semua Provinsi</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Filter Kota</label>
                                            <select id="filter-city" class="form-select">
                                                <option value="">Semua Kota</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Kapasitas Min</label>
                                            <input type="number" id="filter-capacity" class="form-control"
                                                placeholder="Min kapasitas" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Search-->
                            <!--begin::Table-->
                            <table id="datatable" class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th>Logo</th>
                                        <th>Hotel/Venue</th>
                                        <th>Provinsi</th>
                                        <th>Kota</th>
                                        <th>Jumlah Ruang</th>
                                        <th>Kapasitas Max</th>
                                        <th>Tipe</th>
                                        <th>Email</th>
                                        <th>Telepon</th>
                                        <th class="text-center min-w-100px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-dark">
                                </tbody>
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('meeting-room.index') }}",
                    data: function(d) {
                        d.search_venue = $('#search-venue').val();
                        d.filter_province = $('#filter-province').val();
                        d.filter_city = $('#filter-city').val();
                        d.filter_capacity = $('#filter-capacity').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'thumbnail',
                        name: 'thumbnail',
                        render: function(data, type, row) {
                            if (data == null) {
                                return `<span class="symbol-label fs-2x fw-bold text-primary bg-light-primary">${row.name.charAt(0)}</span>`;
                            } else {
                                return `<img src="${data}" alt="image" class="h-50px w-50px rounded-circle" />`;
                            }
                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'province_name',
                        name: 'province_name'
                    },
                    {
                        data: 'city_name',
                        name: 'city_name'
                    },
                    {
                        data: 'rooms_count',
                        name: 'rooms_count',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'max_capacity',
                        name: 'max_capacity'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                responsive: true,
                searching: false, // Disable default search
                language: {
                    processing: "Sedang memproses...",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            // Search venue with debounce
            let searchTimeout;
            $('#search-venue').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    table.draw();
                }, 500);
            });

            // Filter handlers
            $('#filter-province, #filter-city, #filter-capacity').on('change input', function() {
                table.draw();
            });

            // Province change handler for cities
            $('#filter-province').on('change', function() {
                const provinceValue = $(this).val();
                const citySelect = $('#filter-city');

                citySelect.empty().append('<option value="">Semua Kota</option>');

                if (provinceValue) {
                    // Load cities for selected province
                    $.get(`/meeting-room-cities/${encodeURIComponent(provinceValue)}`)
                        .done(function(data) {
                            $.each(data, function(index, city) {
                                citySelect.append(
                                    `<option value="${city.name}">${city.name}</option>`);
                            });
                        });
                }

                table.draw();
            });

            // Reset filters
            $('#reset-filters').on('click', function() {
                $('#search-venue').val('');
                $('#filter-province').val('');
                $('#filter-city').val('').empty().append('<option value="">Semua Kota</option>');
                $('#filter-capacity').val('');
                table.draw();
            });

            // Load provinces
            loadProvinces();

            function loadProvinces() {
                $.get("{{ route('meeting-room.filter-data') }}")
                    .done(function(data) {
                        const provinceSelect = $('#filter-province');

                        data.provinces.forEach(function(province) {
                            if (province) {
                                provinceSelect.append(
                                    `<option value="${province}">${province}</option>`);
                            }
                        });
                    })
                    .fail(function() {
                        console.error('Failed to load filter data');
                    });
            }
        });
    </script>
@endpush
