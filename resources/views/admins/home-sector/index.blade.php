@extends('layouts.master', ['title' => 'Data Badan Usaha', 'main' => 'Dashboard'])
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
                                <span class="card-label fw-bold fs-3 mb-1">Data Badan Usaha</span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('home-sector.create') }}" class="btn btn-primary btn-sm btn-create">
                                    <i class="fa fa-plus"></i>
                                    Badan Usaha
                                </a>
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table id="table-home-sector"
                                class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th class="min-w-125px">Ikon</th>
                                        <th class="min-w-125px">Nama</th>
                                        <th class="min-w-125px">Deskripsi</th>
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
        $(document).ready(() => {
            var table = $('#table-home-sector').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                searchDelay: 500,
                ajax: "{{ route('home-sector.index') }}",
                language: {
                    "paginate": {
                        "next": "<i class='fa fa-angle-right'>",
                        "previous": "<i class='fa fa-angle-left'>"
                    },
                    "loadingRecords": "Loading...",
                    "processing": "Processing...",
                },
                columns: [{
                        "data": null,
                        "sortable": false,
                        "searchable": false,
                        responsivePriority: -3,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'image',
                        name: 'image',
                        render: function(data, type, row) {
                            if (data == null) {
                                return `<span class="symbol-label fs-2x fw-bold text-primary bg-light-primary">${row.name.charAt(0)}</span>`;
                            } else {
                                return `<img src="${data}" alt="image" class="h-70px w-70px" />`;
                            }
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: true,
                        searchable: true,
                        responsivePriority: 1,
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: true,
                        searchable: true,
                        responsivePriority: 2,
                        render: function(data, type, row) {
                            return data ? data :
                                '<span class="text-muted">Tidak ada deskripsi</span>';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true,
                        responsivePriority: -1,
                    },
                ]
            });
        })
    </script>
@endpush
