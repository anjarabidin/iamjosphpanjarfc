@extends('layouts.master', ['title' => 'Data Log Activity'])
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex align-items-center gap-2 container-fluid">
        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Log Activity</h1>
    </div>
    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-fluid">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header d-flex align-items-center justify-content-end border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="">
                    </div>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Table-->
                    <table id="table-log-activity" class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th>#</th>
                                <th class="w-5px">User</th>
                                <th>Log</th>
                                <th>Event</th>
                                <th class="w-25px">Action At</th>
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
            <!--begin::Modals-->

        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</div>
<!--end::Wrapper-->

<!-- Modal-->
<div class="modal fade" id="modal-popout" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Previous Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-hover table-vcenter js-table-checkable-enabled" id="list-previous-data">
                </table>
            </div>
        </div>
    </div>
</div>


@endsection
@push('js')
<script>
    $(document).ready(() => {
        $('#table-log-activity').DataTable({
            'scrollX': true,
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "language": {
                "paginate": {
                    "next": "<i class='fa fa-angle-right'>",
                    "previous": "<i class='fa fa-angle-left'>"
                },
            },
            "ajax": {
                "url": "{{ route('log_activity.index') }}",
            },
            "columns": [{
                    "data": null,
                    "sortable": false,
                    "searchable": false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    "data": "name"
                },
                {
                    "data": "description"
                },
                {
                    "data": "event"
                },
                {
                    "data": "created_at"
                }
            ]
        });
    })

    function see_previous_data(id) {
        $.ajax({
            url: "{{ route('log_activity.show', ':id') }}".replace(':id', id),
            method: 'get',
            dataType: 'html',
            success: function(data) {
                $('#modal-popout').modal('show');
                $('#list-previous-data').html(data);
            }
        });
    }
</script>
@endpush
