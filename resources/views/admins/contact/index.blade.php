@extends('layouts.master', ['title' => 'Data Kontak', 'main' => 'Dashboard'])
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
                                {{-- <span class="card-label fw-bold fs-3 mb-1">Data Kontak</span> --}}
                            </h3>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Button-->
                                <a type="button" class="btn btn-primary btn-sm" href="{{ route('contact.create') }}">
                                    <i class="fa fa-plus"></i> Kontak</a>
                                <!--end::Button-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table id="table-contact" class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0"
                                style="width:100%">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th style="width: 20%">Icon</th>
                                        <th>Nama</th>
                                        <th>Tipe</th>
                                        <th>Kontak</th>
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
            var table = $('#table-contact').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                ajax: {
                    url: "{{ route('contact.index') }}",
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
                        data: 'image',
                        name: 'image',
                        orderable: false, // biasanya gambar tidak perlu diurutkan
                        searchable: false, // pencarian juga jarang lewat gambar
                        render: function(data, type, row) {
                            if (data) {
                                return `<img src="${data}" alt="Image" width="80" class="img-thumbnail" />`;
                            }
                            return `<img src="https://via.placeholder.com/80x80?text=No+Image" alt="No Image" width="80" class="img-thumbnail" />`;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: true,
                        searchable: true,
                        responsivePriority: -2,
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'value',
                        name: 'value',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true,
                        responsivePriority: -1,
                    }
                ]
            })
        })
    </script>
@endpush
