@extends('layouts.master', ['title' => 'Event', 'main' => 'Dashboard'])

@push('css')
<style>
    .w-170px {

        width: 170px;
    }
</style>
@endpush
@section('content')
<div class="app-main pt-6 flex-column flex-row-fluid" id="kt_app_main">
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <div class="d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="app-container container-xxl">
                <x-alert.alert-validation />
                <!--begin::Card-->
                <div class="card card-flush mt-6">
                    <div class="card-body">
                        <div class="mb-2 d-flex flex-wrap align-items-center justify-content-between gap-4 border-0 pt-6">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Daftar Event</span>
                            </h3>
                            <div class="d-flex align-items-center gap-2 gap-lg-3">
                                <a type="button" class="btn btn-primary btn-sm btn-create" href="{{ route('event.create') }}">
                                    <i class="fa fa-plus"></i>
                                    Event</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="datatable-event" class="table table-hover align-start table-row-dashed fs-6 gy-5 mb-0">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th>Image</th>
                                        <th>Event</th>
                                        <th>Tanggal Event</th>
                                        <th>Tempat Event</th>
                                        <th>Status</th>
                                        <th style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-dark fw-semibold"></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
    $(document).ready(function() {
        var table = $('#datatable-event').DataTable({
            ordering: true,
            processing: true,
            serverSide: true,
            responsive: true,
            destroy: true,
            columnDefs: [
                {"targets": 4}
            ],
            ajax: {
                url: '{{ route('event.index') }}',
                type: 'GET',
                beforeSend: function() {
                    $('#datatable-ticket tbody').empty();
                }
            },
            language: {
                "paginate": {
                    "next": "<i class='fa fa-angle-right'>",
                    "previous": "<i class='fa fa-angle-left'>"
                },
                "loadingRecords": "Loading...",
                "processing": "Processing...",
            },
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
                    data: 'image',
                    name: 'image',
                    render: function(data, type, row, meta) {
                        if (row.image) {
                            return `<img src="${row.image}" alt="image" class="w-75px h-50px object-fit-cover" />`;
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'event_date',
                    name: 'event_date',
                },
                {
                    data: 'place_name',
                    name: 'place_name',
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    render: function(data, type, row) {
                        if (data) {
                            return `<span class="badge badge-light-success">Aktif</span>`;
                        } else {
                            return `<span class="badge badge-light-warning">Non Aktif</span>`;
                        }
                    },
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
        
        // Menyembunyikan tabel selama proses loading
        table.on('preXhr.dt', function(e, settings, data) {
            $('#datatable-event tbody').empty();
        });
        
        // Menampilkan tabel setelah data selesai dimuat
        table.on('draw.dt', function() {
            $('#datatable-event').fadeIn();
        });
    });

   
</script>
@endpush
