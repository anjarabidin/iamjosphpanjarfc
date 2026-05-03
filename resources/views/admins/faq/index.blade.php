@extends('layouts.master', ['title' => 'Data FAQ', 'main' => 'Dashboard'])
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
                                <span class="card-label fw-bold fs-3 mb-1">Data FAQ</span>
                            </h3>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Button-->
                                <a type="button" class="btn btn-sm btn-primary btn-create"
                                    href="{{ route('faq.create') }}">
                                    <i class="fa fa-plus"></i>
                                    Faq</a>
                                <!--end::Button-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table id="table-faq" class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0"
                                style="width:100%">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th>Pertanyaan</th>
                                        <th class="en-feature">Pertanyaan (En)</th>
                                        <th>Jawaban</th>
                                        <th class="en-feature">Jawaban (En)</th>
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
            var table = $('#table-faq').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                ajax: {
                    url: "{{ route('faq.index') }}",
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
                        data: 'question',
                        name: 'question',
                        orderable: true,
                        searchable: true,
                        responsivePriority: -2,
                    },
                    {
                        data: 'question_en',
                        name: 'question_en',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'answer',
                        name: 'answer',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'answer_en',
                        name: 'answer_en',
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
