@extends('layouts.master', ['title' => 'Data Syarat Ketentuan', 'main' => 'Dashboard'])
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
                    <div class="card-header mt-6">
                        <!--begin::Card title-->
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Data Syarat Ketentuan</span>
                        </h3>
                        <!--end::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            {{-- <a type="button" class="btn btn-sm btn-primary"
                                href="{{ route('term-and-condition.create') }}">
                                <i class="fa fa-plus"></i>
                                Syarat Ketentuan</a> --}}
                            <!--end::Button-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-4">
                        <!--begin::Table-->
                        <table id="table-term-and-condition"
                            class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0" style="width:100%">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th style="width: 5%">No</th>
                                    <th>Tipe</th>
                                    <th>Konten</th>
                                    <th class="en-feature">Konten (EN)</th>
                                    <th style="width: 10%">Aksi</th>
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
        var table = $('#table-term-and-condition').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                ajax: {
                    url: "{{ route('term-and-condition.index') }}",
                },
                language: {
                    "paginate": {
                        "next": "<i class='fa fa-angle-right'>",
                        "previous": "<i class='fa fa-angle-left'>"
                    },
                    "loadingRecords": "Loading...",
                    "processing": "Processing...",
                },
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                columns: [
                    {
                    "data": null,
                    "sortable": false,
                    "searchable": false,
                    render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                    },
                    {
                        data: 'type',
                        name: 'type',
                    },
                    {
                        data: 'content',
                        name: 'content'
                    },
                    {
                        data: 'content_en',
                        name: 'content_en'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true,
                        responsivePriority: -2,
                    }]
                })
            })
</script>
@endpush
