@extends('layouts.master', ['title' => 'Trending Artikel', 'main' => 'Dashboard'])
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
                            <span class="card-label fw-bold fs-3 mb-1">Tips & Artikel Trending Terbaru</span>
                        </h3>
                        <!--end::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Button-->

                            <!--end::Button-->
                        </div>
                        <!--end::Card toolbar-->
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        <!--begin::Table-->
                        <table id="datatable" class="table  table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th style="width: 5%">No</th>
                                    <th class="w-125px">Thumbnail</th>
                                    <th>Kategori</th>
                                    <th>Title</th>
                                    {{-- <th>Title (English)</th> --}}
                                    <th>Dilihat</th>
                                    <th>Tags</th>
                                    <th>Dibuat Pada</th>
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
    $(document).ready(function() {
        var table = $('#datatable').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('article-trending.index') }}",
                type: 'GET',
            },
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
                },
                {
                    data: 'article_category.name',
                    name: 'article_category.name',
                    responsivePriority: -1,
                },
                {
                    data: 'title',
                    name: 'title',
                    responsivePriority: -2,
                },
               
                {
                    data: 'total_views',
                    name: 'total_views',
                    render: function(data, type, row, meta) {
                        return `<span class="badge badge-light-primary me-1 mb-1">${data}</span>`;
                    }
                },
                {
                    data: null,
                    sortable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        let badge = '';
                        for (tag of row.article_tags) {
                            badge += `<span class="badge badge-secondary me-1 mb-1"><i>${tag.name}</i></span>`;
                        }
                        return badge;
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
            ]
        });
    });
</script>
@endpush
