@extends('layouts.master', ['title' => 'Data Anggota', 'main' => 'Dashboard'])

@section('content')
    <div class="app-main pt-6 flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">

                    <div class="card card-flush">
                        <div class="card-header mt-4">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold">Data Anggota</span>
                            </h3>
                            <div class="card-toolbar gap-2">
                                <a href="{{ route('member.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Tambah Anggota
                                </a>

                                <!-- Dropdown untuk Import/Export -->
                                <div class="btn-group">
                                    <button type="button" class="btn btn-light-success btn-sm dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-file-excel"></i> Excel
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('member.export.excel') }}">
                                                <i class="fa fa-download me-2"></i>Export Anggota
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#importMemberModal">
                                                <i class="fa fa-upload me-2"></i>Import Anggota
                                            </a></li>
                                        <li><a class="dropdown-item" href="{{ route('member.template.excel') }}">
                                                <i class="fa fa-file-download me-2"></i>Download Template
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <table id="table-members"
                                class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0 w-100">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width:5%">No</th>
                                        <th>Foto</th>
                                        <th>Nama</th>
                                        <th>Dibuat</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-dark">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
                            <input type="file" class="form-control" id="memberFile" name="file" accept=".xlsx,.xls"
                                required>
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
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            const dtLang = {
                paginate: {
                    next: "<i class='fa fa-angle-right'></i>",
                    previous: "<i class='fa fa-angle-left'></i>"
                },
                loadingRecords: "Loading...",
                processing: "Processing..."
            };

            $('#table-members').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                searchDelay: 500,
                ajax: "{{ route('member.index') }}",
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
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });
        });
    </script>
@endpush
