@extends('layouts.master', ['title' => 'Data Slider', 'main' => 'Dashboard'])
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
                                <span class="card-label fw-bold fs-3 mb-1">Data Slider</span>
                            </h3>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Button-->
                                <a type="button" class="btn btn-primary btn-sm" href="{{ route('home-slider.create') }}">
                                    <i class="fa fa-plus"></i> Tambah Slider</a>
                                <!--end::Button-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <x-alert.alert-validation />
                            <!--begin::Table-->
                            <table id="table-home-slider"
                                class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0" style="width:100%">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th style="width: 15%">Media</th>
                                        <th>Judul</th>
                                        <th>Deskripsi</th>
                                        <th>Tombol</th>
                                        <th>Status</th>
                                        <th>Urutan</th>
                                        <th>Aksi</th>
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
            var table = $('#table-home-slider').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                ajax: {
                    url: "{{ route('home-slider.index') }}",
                },
                language: {
                    "paginate": {
                        "next": "<i class='fa fa-angle-right'>",
                        "previous": "<i class='fa fa-angle-left'>"
                    },
                    "loadingRecords": "Loading...",
                    "processing": "Processing...",
                },
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
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
                        data: 'media_preview',
                        name: 'media_preview',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'title',
                        name: 'title',
                        orderable: true,
                        searchable: true,
                        responsivePriority: -2,
                        render: function(data, type, row) {
                            if (data && data.length > 40) {
                                return data.substring(0, 40) + '...';
                            }
                            return data || '-';
                        }
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: true,
                        searchable: true,
                        responsivePriority: -1,
                        render: function(data, type, row) {
                            if (data && data.length > 50) {
                                return data.substring(0, 50) + '...';
                            }
                            return data || '-';
                        }
                    },
                    {
                        data: 'button_text',
                        name: 'button_text',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (data && row.button_link) {
                                return `<a href="${row.button_link}" target="_blank" class="text-decoration-none" title="${row.button_link}">
                                            ${data} <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>`;
                            }
                            return data || '-';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'sort_order',
                        name: 'sort_order',
                        orderable: true,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        responsivePriority: -1,
                    }
                ]
            })
        })
    </script>
@endpush
