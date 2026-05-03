@extends('layouts.master', ['title' => 'Data Struktur Organisasi', 'main' => 'Dashboard'])

@section('content')
    <div class="app-main pt-6 flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">

                    <div class="card card-flush">
                        <div class="card-header mt-4">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold">Struktur Organisasi</span>
                            </h3>
                            <div class="card-toolbar gap-2">
                                <a href="{{ route('organization.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Tambah Jabatan
                                </a>
                                <a href="{{ route('member.create') }}" class="btn btn-light-primary btn-sm">
                                    <i class="fa fa-user-plus"></i> Tambah Anggota
                                </a>

                                <!-- Dropdown untuk Import/Export -->
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-light-success btn-sm dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-file-excel"></i> Excel
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <h6 class="dropdown-header">Jabatan</h6>
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('organization.export.positions') }}">
                                                <i class="fa fa-download me-2"></i>Export Jabatan
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#importPositionModal">
                                                <i class="fa fa-upload me-2"></i>Import Jabatan
                                            </a></li>
                                        <li><a class="dropdown-item" href="{{ route('organization.template.positions') }}">
                                                <i class="fa fa-file-download me-2"></i>Template Jabatan
                                            </a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <h6 class="dropdown-header">Anggota</h6>
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('member.export.excel') }}">
                                                <i class="fa fa-download me-2"></i>Export Anggota
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#importMemberModal">
                                                <i class="fa fa-upload me-2"></i>Import Anggota
                                            </a></li>
                                        <li><a class="dropdown-item" href="{{ route('member.template.excel') }}">
                                                <i class="fa fa-file-download me-2"></i>Template Anggota
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            {{-- Tabs --}}
                            <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-positions">Jabatan</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab-members">Anggota</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="orgTabs">

                                {{-- TAB: JABATAN --}}
                                <div class="tab-pane fade show active" id="tab-positions" role="tabpanel">
                                    <table id="table-positions"
                                        class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0 w-100">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th style="width:5%">No</th>
                                                <th>Nama</th>
                                                <th>Induk</th>
                                                <th>Anggota</th>
                                                <th>Urutan</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                                {{-- TAB: ANGGOTA --}}
                                <div class="tab-pane fade" id="tab-members" role="tabpanel">
                                    <table id="table-members"
                                        class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0 w-100">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th style="width:5%">No</th>
                                                <th>Foto</th>
                                                <th>Nama</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            const dtLang = {
                paginate: {
                    next: "<i class='fa fa-angle-right'></i>",
                    previous: "<i class='fa fa-angle-left'></i>"
                },
                loadingRecords: "Loading...",
                processing: "Processing..."
            };

            // DataTable Jabatan
            $('#table-positions').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                searchDelay: 500,
                ajax: "{{ route('organization.index') }}?type=position",
                language: dtLang,
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'parent_name',
                        name: 'parent_name',
                        render: d => d ?? '<span class="text-gray-500">— Root</span>'
                    },
                    {
                        data: 'member_name',
                        name: 'member_name',
                        render: (d, t, row) => {
                            if (!d) return '<span class="text-gray-500">— Kosong —</span>';
                            const p = row.member_photo;
                            return p ?
                                `<div class="d-flex align-items-center">
                                    <img src="${p}" class="rounded me-2" style="width:36px;height:48px;object-fit:cover">
                                    <span>${d}</span>
                                </div>` :
                                d;
                        }
                    },
                    {
                        data: 'order',
                        name: 'order'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ]
            });

            // DataTable Anggota
            $('#table-members').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                searchDelay: 500,
                ajax: "{{ route('organization.index') }}?type=member",
                language: dtLang,
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false,
                        render: (d, t, row) => d ?
                            `<img src="${d}" class="rounded" style="width:60px;height:80px;object-fit:cover">` :
                            `<span class="symbol-label fs-2x fw-bold text-primary bg-light-primary">${(row.name||'?').charAt(0)}</span>`
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ]
            });

            // Fix responsive saat ganti tab
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust().responsive.recalc();
            });
        });
    </script>

    <!-- Modal Import Position -->
    <div class="modal fade" id="importPositionModal" tabindex="-1" aria-labelledby="importPositionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('organization.import.positions') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importPositionModalLabel">Import Data Jabatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="positionFile" class="form-label">File Excel (.xlsx, .xls)</label>
                            <input type="file" class="form-control" id="positionFile" name="file"
                                accept=".xlsx,.xls" required>
                            <div class="form-text">
                                <small>
                                    <strong>Catatan:</strong>
                                    <ul class="mt-2">
                                        <li>Download template terlebih dahulu</li>
                                        <li>Pastikan format kolom sesuai template</li>
                                        <li>Jabatan Induk dan Nama Anggota harus sudah ada di sistem</li>
                                        <li>Maksimal file 2MB</li>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-upload"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import Member -->
    <div class="modal fade" id="importMemberModal" tabindex="-1" aria-labelledby="importMemberModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('member.import.excel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importMemberModalLabel">Import Data Anggota</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="memberFile" class="form-label">File Excel (.xlsx, .xls)</label>
                            <input type="file" class="form-control" id="memberFile" name="file"
                                accept=".xlsx,.xls" required>
                            <div class="form-text">
                                <small>
                                    <strong>Catatan:</strong>
                                    <ul class="mt-2">
                                        <li>Download template terlebih dahulu</li>
                                        <li>Pastikan format kolom sesuai template</li>
                                        <li>Kolom Nama wajib diisi</li>
                                        <li>Maksimal file 2MB</li>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-upload"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush
