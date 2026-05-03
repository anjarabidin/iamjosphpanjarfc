@extends('layouts.master', ['title' => 'Shop', 'main' => 'Dashboard'])

@push('style')
<style>
    .card .card-header {
        border-bottom: none !important;
    }

    .border-none {
        border: none !important;
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
                <div class="card">
                    <!--begin::Header-->
                    <div class="card-header border-none py-5">
                        <!--begin::Title-->
                        <h3 class="card-title fw-bold text-gray-800">Dashboard Shop</h3>
                        <!--end::Title-->
                        <!--begin::Toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Daterangepicker(defined in src/js/layout/app.js)-->
                            {{-- <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left"
                                class="btn btn-sm btn-light d-flex align-items-center px-4" id="daterangepicker">
                                <!--begin::Display range-->
                                <div class="text-gray-600 fw-bold">Loading date range...</div>
                                <!--end::Display range-->
                                <i class="ki-duotone ki-calendar-8 fs-1 ms-2 me-0">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                </i>
                            </div> --}}
                            <div style="width: max-content" class="ms-auto my-4">
                                <label for="dateRange"
                                    class="btn btn-sm btn-light text-dark fw-600 d-flex align-items-center px-4">
                                    <input placeholder="Pick date rage"
                                        class="bg-transparent text-dark fw-600 cursor-pointer" id="dateRange" />
                                    <i class="ki-duotone ki-calendar fs-1 ms-0 me-0">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                    </i>
                                </label>
                                <input type="text" id="start_date" hidden>
                                <input type="text" id="end_date" hidden>
                            </div>
                            <!--end::Daterangepicker-->
                        </div>
                        <!--end::Toolbar-->
                    </div>
                    <!--end::Header-->
                    <!--begin::Card body-->
                    <div class="card-body d-flex justify-content-between flex-column pb-0 px-0 pt-1">
                        <!--begin::Items-->
                        <div class="d-flex flex-wrap d-grid gap-5 px-9 mb-5">
                            <!--begin::Item-->
                            <div class="me-md-2">
                                <!--begin::Statistics-->
                                <div class="d-flex mb-2">
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2" id="total_transaction">{{ $totalTransaction ?? 0
                                        }}</span>
                                </div>
                                <!--begin::Description-->
                                <span class="fs-6 fw-semibold text-gray-400">Total Transaksi</span>
                                <!--end::Description-->
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="border-start-dashed border-start border-gray-300 px-5 ps-md-10 pe-md-7 me-md-5">
                                <!--begin::Statistics-->
                                <div class="d-flex mb-2">
                                    <span class="fs-4 fw-semibold text-gray-400 me-1">Rp</span>
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2" id="total_omset">{{
                                        number_format($totalTurnover
                                        ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <!--end::Statistics-->
                                <!--begin::Description-->
                                <span class="fs-6 fw-semibold text-gray-400">Omset</span>
                                <!--end::Description-->
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Items-->
                        <!--begin::Chart-->
                        <div class="min-h-auto mx-10 mt-2">
                            <div
                                class="mb-2 d-flex flex-wrap align-items-center justify-content-between gap-4 border-0 pt-6">
                                <h3 class="card-title fw-bold text-gray-800">Daftar Pesanan Shop</h3>
                                <div class="w-sm-200px">
                                    <select data-control="select2" data-hide-search="true" id="status"
                                        class="form-select cursor-pointer">
                                        <option value="">Semua Status</option>
                                        @foreach ($status as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="datatable-shop-order"
                                    class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th style="width: 10%">No</th>
                                            <th style="width: 40%">Kode Transaksi</th>
                                            <th style="width: 30%">Nomor Pesanan</th>
                                            <th style="width: 40%">Nama Pelanggan</th>
                                            <th style="width: 40%">Total Harga</th>
                                            <th style="width: 40%">Status</th>
                                            <th style="width: 25%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-dark fw-semibold"></tbody>
                                </table>
                            </div>
                        </div>
                        <!--end::Chart-->
                    </div>
                    <!--end::Card body-->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
    $(document).ready(() => {
        var table = $('#datatable-shop-order').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            responsive: true,
            destroy: true,
            ajax: {
                url: "{{ route('shop.dashboard') }}",
                data: function (d) {
                    d.status = $('#status').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
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
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            columns: [
                {
                    "data": null,
                    "sortable": false,
                    "searchable": false,
                    responsivePriority: -3,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'transaction.payment_code',
                    name: 'transaction.payment_code',
                    orderable: true,
                    searchable: true,
                },
                {
                    data: 'order_number',
                    name: 'order_number',
                    orderable: true,
                    searchable: true,
                    responsivePriority: -2,
                },
                {
                    data: 'user.name',
                    name: 'user.name',
                    orderable: true,
                    searchable: true,
                },
                {
                    data: 'transaction.pay_amount',
                    name: 'transaction.pay_amount',
                    render: function(data, type, row) {
                        return 'Rp' + data.toLocaleString('id-ID');
                    },
                },
                {
                    data: 'status',
                    name: 'status',
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
        $('#status').on('change', function() {
            table.ajax.reload();
            updateByDateStatus();
        });
    })
</script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(function() {
        var start = moment().startOf('year');
        var end = moment().endOf('year');
        function cb(start, end) {
            $('#dateRange span').html(start.format('D/MM/YYYY') + ' - ' + end.format('D/MM/YYYY'));
            var start = start.format('YYYY-MM-DD');
            var end = end.format('YYYY-MM-DD');
            $('#start_date').val(start);
            $('#end_date').val(end);
            $('#datatable-shop-order').DataTable().ajax.reload();

            updateByDateStatus();
        }

        $('#dateRange').daterangepicker({ 
            startDate: start,
            endDate: end,
            ranges: {
                'Semua Waktu': [moment().subtract(5, 'years'), moment()],
                'Hari Ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                'Tahun Ini': [moment().startOf('year'), moment().endOf('year')],
                'Tahun Kemarin': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1,
                    'year').endOf('year')],
            }
        }, cb);
        cb(start, end);
    });

    function updateByDateStatus(){
        // Mengambil data baru berdasarkan tanggal yang dipilih
        $.ajax({
            url: "{{ route('shop.dashboard', 'data=shop') }}",
            method: 'GET',
            data: {
                status: $('#status').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val()
            },
            success: function(data) {
                console.log(data)
                $('#total_transaction').text(data.totalTransaction);
                $('#total_omset').text(data.totalTurnover.toLocaleString('id-ID'));
            },
            error: function(err) {
                console.log(err);
            }
        });
    }
</script>
@endpush
