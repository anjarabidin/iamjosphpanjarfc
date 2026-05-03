@extends('layouts.master', ['title' => 'Pesan Pengguna', 'main' => 'Dashboard'])
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
                                <span class="card-label fw-bold fs-3 mb-1">Pesan Pengguna</span>
                            </h3>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->

                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table id="table-contact-us"
                                class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0" style="width:100%">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th>Nama Pengguna</th>
                                        <th>Email</th>
                                        <th>No Hp</th>
                                        <th>Pesan</th>
                                        <th style="width: 15%" class="text-center">Aksi</th>
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
            var table = $('#table-contact-us').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                ajax: {
                    url: "{{ route('contact-us.index') }}",
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
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'email',
                        name: 'email',
                        render: function(data, type, row) {
                            if (data) {
                                return `<a href="mailto:${data}" style="text-decoration: none; color: #007bff;">
                                ${data} <i class="fa fa-envelope"></i>
                            </a>`;
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        render: function(data, type, row) {
                            if (data) {
                                // Format nomor telepon dengan mengganti 0 di awal menjadi 62 (kode negara Indonesia)
                                let phoneNumber = data.replace(/^0/, '62');

                                return `
                                <a href="https://wa.me/${phoneNumber}" target="_blank" 
                                style="text-decoration: none; color: #25D366;">
                                    <i class="fab fa-whatsapp"></i> ${data}
                                </a>
                            `;
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'message',
                        name: 'message',
                        render: function(data, type, row) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        responsivePriority: -1,
                    }
                ],
            });
        });
    </script>
@endpush
