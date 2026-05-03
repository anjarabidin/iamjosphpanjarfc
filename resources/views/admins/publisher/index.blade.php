@extends('layouts.master', ['title' => 'Data Publisher', 'main' => 'Dashboard'])
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
                        <div class="card-header mt-4">
                            <!--begin::Card title-->
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Data Publisher</span>
                                <span class="card-text fs-7 fw-semibold text-gray-500">Kelola data publisher</span>
                            </h3>
                            <div class="card-toolbar gap-2">
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="fa fa-upload me-1"></i>Import
                                </button>
                                <a href="{{ route('publisher.export') }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-download me-1"></i>Export
                                </a>
                                <a href="{{ route('publisher.template') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-file-excel me-1"></i>Download Template
                                </a>
                                 <a href="{{ route('publisher.create') }}" class="btn btn-primary btn-create btn-sm">
                                    <i class="ki-duotone ki-plus fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Publisher
                                </a>
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table id="table-publisher" class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th class="min-w-100px">Avatar</th>
                                        <th class="min-w-150px">Nama Publisher</th>
                                        <th class="min-w-100px">Kode</th>
                                        <th class="min-w-120px">Email</th>
                                        <th class="min-w-100px">Tipe</th>
                                        <th class="min-w-120px">Kota</th>
                                        <th class="min-w-100px">Status</th>
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


<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>Import Data Publisher
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('publisher.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Download template terlebih dahulu untuk memastikan format data sesuai. File harus berformat Excel (.xlsx, .xls) atau CSV.</small>
                    </div>
                    <div class="fv-row mb-3">
                        <label class="form-label fw-bold" for="import_file">
                            <span class="required">Pilih File</span>
                        </label>
                        <input type="file" class="form-control @error('import_file') is-invalid @enderror"
                            id="import_file" name="import_file" accept=".xlsx,.xls,.csv" required />
                        <small class="form-text text-muted mt-2">Format: Excel (.xlsx, .xls) atau CSV</small>
                        @error('import_file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-primary">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Catatan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Jika kode publisher sudah ada, data akan di-update</li>
                            <li>Email baru akan membuat akun publisher baru</li>
                            <li>Password default: password123 (untuk akun baru)</li>
                            <li>Periksa kembali data sebelum di-import</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('js')
    <script>
        $(document).ready(() => {
            var table = $('#table-publisher').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                searchDelay: 500,
                ajax: "{{ route('publisher.index') }}",
                language: {
                    "paginate": {
                        "next": "<i class='fa fa-angle-right'>",
                        "previous": "<i class='fa fa-angle-left'>"
                    },
                    "loadingRecords": "Loading...",
                    "processing": "Processing...",
                },
                columns: [
                    {
                        "data": null,
                        "sortable": false,
                        "searchable": false,
                        responsivePriority: -3,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'avatar',
                        name: 'avatar',
                        render: function(data, type, row) {
                            if (data == null) {
                                const initials = row.name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().slice(0, 2);
                                return `<span class="symbol-label fs-2x fw-bold text-primary bg-light-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">${initials}</span>`;
                            } else {
                                return `<img src="${data}" alt="image" class="h-50px w-50px rounded-circle object-fit-cover" />`;
                            }
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        responsivePriority: -2
                    },
                    {
                        data: 'code',
                        name: 'code',
                        render: function(data) {
                            return `<span class="badge badge-light-primary">${data}</span>`;
                        }
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'type_badge',
                        name: 'type'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        render: function(data, type, row) {
                            let badgeClass = data == true ? 'badge-light-success' : 'badge-light-danger';
                            let label = data == true ? 'Aktif' : 'Nonaktif';
                            return `<span class="badge ${badgeClass}">${label}</span>`;
                        },
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        responsivePriority: -1,
                    },
                ]
            });
        })
    </script>
@endpush
