@extends('layouts.master', ['title' => 'Data Author', 'main' => 'Dashboard'])
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
                                <span class="card-label fw-bold fs-3 mb-1">Data Author</span>
                                <span class="card-text fs-7 fw-semibold text-gray-500">Kelola data author sistem</span>
                            </h3>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar gap-2">
                                <div class="me-2">
                                    <select id="filter-status" class="form-select form-select-sm" aria-label="Filter Status">
                                        <option value="">Semua Status</option>
                                        <option value="1">Aktif</option>
                                        <option value="0">Nonaktif</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#importModal" aria-label="Import Data Author">
                                    <i class="fa fa-upload me-1"></i>Import
                                </button>
                                <a href="{{ route('author.export') }}" class="btn btn-success btn-sm" aria-label="Export Data Author">
                                    <i class="fa fa-download me-1"></i>Export
                                </a>
                                <a href="{{ route('author.template') }}" class="btn btn-secondary btn-sm" aria-label="Download Template">
                                    <i class="fa fa-file-excel me-1"></i>Template
                                </a>
                                <a href="{{ route('author.create') }}" class="btn btn-primary btn-sm btn-create" aria-label="Tambah Author Baru">
                                    <i class="fa fa-plus me-1"></i>Author
                                </a>
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table id="table-author" class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th class="min-w-100px">Avatar</th>
                                        <th class="min-w-120px">No. Registrasi</th>
                                        <th class="min-w-150px">Nama Author</th>
                                        <th class="min-w-150px">Email</th>
                                        <th class="min-w-150px">Institusi</th>
                                        <th class="min-w-100px">Status</th>
                                        <th class="text-center min-w-100px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-dark"></tbody>
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
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true" aria-labelledby="importModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel"><i class="fas fa-upload me-2"></i>Import Data Author</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form action="{{ route('author.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <strong>Info:</strong> Download template terlebih dahulu untuk memastikan format data sesuai. File harus berformat Excel (.xlsx, .xls) atau CSV.
                        </div>
                        <div class="fv-row mb-3">
                            <label class="form-label fw-bold" for="import_file">Pilih File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('import_file') is-invalid @enderror" id="import_file" name="import_file" accept=".xlsx,.xls,.csv" required />
                            <small class="form-text text-muted d-block mt-2">Format yang didukung: Excel (.xlsx, .xls), CSV</small>
                            @error('import_file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="alert alert-warning mb-0">
                            <strong>Catatan Penting:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Jika No. Registrasi sudah ada, data akan di-update</li>
                                <li>Email baru akan membuat akun author baru</li>
                                <li>Password default untuk akun baru: <code>password123</code></li>
                                <li>Periksa kembali data sebelum di-import</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-2"></i>Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(() => {
        var table = $('#table-author').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            responsive: true,
            searchDelay: 500,
            ajax: {
                url: "{{ route('author.index') }}",
                data: function (d) {
                    d.status = $('#filter-status').val();
                }
            },
            language: {
                "paginate": {
                    "next": "<i class='fa fa-angle-right'>",
                    "previous": "<i class='fa fa-angle-left'>"
                },
                "loadingRecords": "Memuat...",
                "processing": "Memproses...",
                "emptyTable": "Tidak ada data"
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
                    sortable: false,
                    searchable: false
                },
                {
                    data: 'registration_number',
                    name: 'registration_number',
                    responsivePriority: 1
                },
                {
                    data: 'name',
                    name: 'name',
                    responsivePriority: 2
                },
                {
                    data: 'email',
                    name: 'email',
                    responsivePriority: 3
                },
                {
                    data: 'institution',
                    name: 'institution'
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    sortable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    sortable: false,
                    searchable: false,
                    responsivePriority: -1
                }
            ]
        });

        $('#filter-status').on('change', function () {
            table.ajax.reload();
        });

        // Add placeholder for global search
        $(document).on('draw.dt', '#table-author', function() {
            $(this).closest('.dataTables_wrapper').find('.dataTables_filter input')
                .attr('placeholder', 'Cari: Nama, Email, Institusi, No. Reg');
        });
    });
</script>
@endpush
