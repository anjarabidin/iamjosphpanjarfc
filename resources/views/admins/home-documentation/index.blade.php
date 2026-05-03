@extends('layouts.master', ['title' => 'Data Dokumentasi', 'main' => 'Dashboard'])
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
                                <span class="card-label fw-bold fs-3 mb-1">Data Dokumentasi</span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('home-documentation.create') }}"
                                    class="btn btn-primary btn-sm btn-create">
                                    <i class="fa fa-plus"></i>
                                    Dokumentasi
                                </a>
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table id="table-home-documentation"
                                class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th class="min-w-125px">Thumbnail</th>
                                        <th class="min-w-125px">Urutan</th>
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
            var table = $('#table-home-documentation').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                searchDelay: 500,
                ajax: "{{ route('home-documentation.index') }}",
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
                        data: 'media_url',
                        name: 'media_url',
                        render: function(data, type, row) {
                            if (row.media_type === 'image') {
                                return `<img src="${data}" class="img-fluid" style="max-width: 200px; max-height: 200px;">`;
                            } else if (row.media_type === 'video') {
                                return `<video width="200" height="200" controls>
                                            <source src="${data}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>`;
                            } else {
                                return `<span class="text-muted">Tidak ada media</span>`;
                            }
                        }
                    },
                    {
                        data: 'order',
                        name: 'order',
                        responsivePriority: -2
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
