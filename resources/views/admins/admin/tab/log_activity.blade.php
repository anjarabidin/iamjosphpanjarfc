<table id="kt_permissions_table" class="table align-middle table-row-dashed fs-6 gy-5 mb-0 w-100">
    <thead>
        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
            <th style="width: 3%;">#</th>
            <th>Log</th>
            <th>Event</th>
            <th>Action At</th>
        </tr>
    </thead>
    <tbody class="fw-semibold text-dark">
    </tbody>
</table>

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

@push('js')
<script>
    $(document).ready(() => {
        $('#kt_permissions_table').DataTable({
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
                url: "{{ route('log_activity.index') }}",
                data: {
                    admin_id: "{{$admin->id}}"
                }
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
