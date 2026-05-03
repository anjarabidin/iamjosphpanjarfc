@extends('layouts.master', ['title' => 'Dashboard Pendapatan', 'main' => 'Dashboard Pendapatan'])
@push('css')
<style>
    .bg-1 {
        background-color: #655025;
    }

    .bg-2 {
        background-color: #8A6E33;
    }

    .bg-3 {
        background-color: #B18D41;
    }

    .bg-4 {
        background-color: #071437;
    }

    .bg-5 {
        background-color: #4B5675;
    }

    .bg-6 {
        background-color: #B5B5C3;
    }

    .bg-7 {
        background-color: #E4E6EF;
    }

    .bg-8 {
        background-color: #EFF2F5;
    }

    .title-box {
        font-size: 2.125rem;
    }

    .box-image {
        padding: 0.69rem;
        border-radius: 0.5rem;
    }

    .bg-primary-surface {
        background: #FAF6F0;
    }

    .bg-danger-surface {
        background: #FEECF0;
    }
</style>
@endpush

@section('content')
<div class="app-main pt-6 flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="d-flex align-items-center justify-content-end my-4 gap-3">
                    <div>
                        <select name="gym_place_id" id="gym_place_id" class="form-select w-170px" onchange="table()">
                            <option value="">Semua Gym Place</option>
                            @foreach ($gym_places as $gym_place)
                            <option value="{{$gym_place->id}}">{{$gym_place->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="width: max-content">
                        <label for="dateRange" class="btn btn-sm btn-light text-dark fw-600 d-flex align-items-center px-4">
                            <input placeholder="Pick date range" class="bg-transparent text-dark fw-600 cursor-pointer"
                                id="dateRange" />
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
                </div>
                <div class="row mb-5 g-5 g-xl-8">

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <p class="text-muted fw-medium">Total Pendapatan (Paid)</p>
                                    <div class="box-image bg-primary-surface">
                                        <img src="{{ asset('assets/media/icons/paid.svg') }}" alt="">
                                    </div>
                                </div>
                                <h1 class="fw-bold title-box text-dark">Rp{{ number_format($totalPaid, 0, ',', '.') }}</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <p class="text-muted fw-medium">Pending Pembayaran</p>
                                    <div class="box-image bg-danger-surface">
                                        <img src="{{ asset('assets/media/icons/pending.svg') }}" alt="">
                                    </div>
                                </div>
                                <h1 class="fw-bold title-box text-danger">Rp{{ number_format($totalPending, 0, ',', '.') }}</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header py-0">
                                <h3 class="card-title">Tipe Paket Pembelian</h3>
                            </div>
                            <div class="card-body">
                                <div id="cardLoadingOverlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; display: flex; align-items: center; justify-content: center;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div class="row d-flex align-items-center">
                                    <div class="col-sm-5 pe-0">
                                        <!--begin::Chart-->
                                        <div id="chart-type"></div>
                                        <!--end::Chart-->
                                    </div>
                                    <div class="col-sm-7 ps-0">
                                        <!--begin::Labels-->
                                        <div id="chartTypeLabels" class="d-flex flex-column gap-2">

                                        </div>
                                    </div>
                                    <!--end::Labels-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card h-100">
                            <div class="card-header py-0">
                                <h3 class="card-title">Jenis Pembelian</h3>
                            </div>
                            <div class="card-body">
                                <div id="cardLoadingOverlay2" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; display: flex; align-items: center; justify-content: center;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <div class="row d-flex align-items-center">
                                    <div class="col-sm-5 pe-0">
                                        <!--begin::Chart-->
                                        <div id="chart-purcahase"></div>
                                        <!--end::Chart-->
                                    </div>
                                    <div class="col-sm-7 ps-0">
                                        <!--begin::Labels-->
                                        <div id="paymentLabelsContainer" class="d-flex flex-column gap-2"></div>
                                        <!--begin::Label-->
                                        @foreach ($paymentData as $index => $data)
                                        <div class="d-flex gap-4 align-items-center justify-content-between">

                                        </div>
                                        @endforeach
                                        <!--end::Label-->
                                    </div>
                                </div>
                                <!--end::Labels-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    {{-- Statistik Pendapatan --}}
                    <div class="card mb-5">
                        <div class="card-header">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">Statistik Pendapatan</span>
                                <span class="text-muted mt-2 fw-semibold fs-7">Pendapatan Bulanan</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="kt_chart_pembelian" style="height: 25rem;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    {{-- Riwayat Pendapatan --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Riwayat Pendapatan</h3>
                            <div class="card-toolbar">
                                <a href="{{ route('transaction.index') }}" class="btn btn-sm btn-light">
                                    Lihat Semua
                                </a>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table id="kt_riwayat_pembelian"
                                    class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="min-w-125px">NAMA</th>
                                            <th class="min-w-125px">METODE PEMBAYARAN</th>
                                            <th>PROGRAM</th>
                                            <th>PEMBAYARAN</th>
                                            <th class="min-w-125px">TANGGAL PEMBAYARAN</th>
                                            <th>STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-dark">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('js')
<script src="https://cdn.anychart.com/releases/8.12.0/js/anychart-base.min.js" type="text/javascript"></script>
<script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
<script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<!--end::Vendors Javascript-->
<!-- Load jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Load Date Range Picker CSS dan JS -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<!-- Load jQuery Mask Plugin jika digunakan -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<!-- Load DataTables CSS dan JS -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js" type="text/javascript"></script>


{{-- Filter datepicker --}}
<script>
    var table;

    $(document).ready(function() {
        const statisticChart = async () => {
            const element = document.getElementById('kt_chart_pembelian');

            var height = parseInt(KTUtil.css(element, 'height'));

            var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
            var borderColor = KTUtil.getCssVariableValue('--bs-border-dashed-color');
            var baseprimaryColor = KTUtil.getCssVariableValue('--bs-primary');
            var lightprimaryColor = KTUtil.getCssVariableValue('--bs-primary');
            var basesuccessColor = KTUtil.getCssVariableValue('--bs-dark-text-emphasis');
            var lightsuccessColor = KTUtil.getCssVariableValue('--bs-dark-text-emphasis');

            if (!element) {
                console.error('Element grafik tidak ditemukan!');
                return;
            }

            const endpoint = '/dashboard-income';
            try {
                const response = await fetch(`${endpoint}?ajax=true`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                const incomeData = data.monthlyIncome || [];

                var options = {
                    series: [{
                        name: 'Pendapatan',
                        data: incomeData,
                    }],
                    chart: {
                        fontFamily: 'inherit',
                        type: 'area',
                        height: height,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {},
                    legend: {
                        show: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    fill: {
                        type: "gradient",
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.2,
                            stops: [15, 120, 100]
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        show: true,
                        width: 3,
                        colors: [baseprimaryColor, basesuccessColor]
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '12px'
                            }
                        },
                        crosshairs: {
                            position: 'front',
                            stroke: {
                                color: labelColor,
                                width: 1,
                                dashArray: 3
                            }
                        },
                        tooltip: {
                            enabled: true,
                            offsetY: 0,
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '12px'
                            },
                            formatter: function (val) {
                                return val.toLocaleString('id-ID'); // Format angka menjadi ribuan
                            }
                        }
                    },
                    states: {
                        normal: {
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        },
                        hover: {
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        },
                        active: {
                            allowMultipleDataPointsSelection: false,
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        }
                    },
                    tooltip: {
                        style: {
                            fontSize: '12px'
                        },
                        y: {
                            formatter: function (val) {
                                return 'Rp ' + val.toLocaleString('id-ID'); // Format angka dengan prefix "Rp"
                            }
                        }
                    },
                    colors: [lightprimaryColor, lightsuccessColor],
                    grid: {
                        borderColor: borderColor,
                        strokeDashArray: 4,
                        yaxis: {
                            lines: {
                                show: true
                            }
                        }
                    },
                    markers: {
                        colors: [baseprimaryColor, basesuccessColor],
                        strokeColor: [baseprimaryColor, basesuccessColor],
                        strokeWidth: 3
                    }
                };

                var chart = new ApexCharts(element, options);
                chart.render();

            } catch (error) {
                console.error('Gagal mengambil data:', error);
            }
        };

        statisticChart();

        $(document).ready(function() {
            var start = moment().startOf('year').format('YYYY-MM-DD');
            var end = moment().endOf('year').format('YYYY-MM-DD');

            table = $('#kt_riwayat_pembelian').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('transaction.index') }}",
                    type: 'GET',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = "PAID";
                        d.gym_place_id = $('#gym_place_id').val();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data:", error);
                        console.error("Response:", xhr.responseText);
                    }
                },
                language: {
                    "paginate": {
                        "next": "<i class='fa fa-angle-right'></i>",
                        "previous": "<i class='fa fa-angle-left'></i>"
                    },
                    "loadingRecords": "Loading...",
                    "processing": "Processing..."
                },
                columns: [{
                    data: null,
                    name: 'user.name',
                    render: function(data, type, row) {
                        if (row.user && row.user.name) {
                            return row.user.name;
                        } else if (row.guest && row.guest.name) {
                            return row.guest.name;
                        } else {
                            return 'Unknown';
                        }
                    },
                    responsivePriority: -2,
                    },
                    {
                        data: null,
                        sortable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let offline_payment = data.offline_payment_method ? " (" + data.offline_payment_method + ")" : "";
                            let data_payment_method = data.payment_method.type === "AUTO" ? data.payment_method.name : data.payment_method.name + offline_payment;
                            return data_payment_method;
                        }
                    },
                    {
                        data: null,
                        sortable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (row?.transaction_details && row.transaction_details.length > 0) {
                                let items = '';
                                row.transaction_details.forEach((item) => {
                                    if (item.parentable_type === "App\\Models\\Event") {
                                        items += `<p>${item.parent?.name} (Tiket Event)</p>`;
                                        if (row.event_ticket_order?.event_ticket_order_detail_groups) {
                                            items += '<ul>';
                                            row.event_ticket_order.event_ticket_order_detail_groups.forEach((eventTicketOrderDetail) => {
                                                items += `<li><span class="text-capitalize">${eventTicketOrderDetail.event_ticket?.name} (${eventTicketOrderDetail.total_quantity} Tiket)</span></li>`;
                                            });
                                            items += '</ul>';
                                        }
                                    } else {
                                        items += `<li><i>${item.parent?.name}</i></li>`;
                                    }
                                });
                                return `<ul>${items}</ul>`;
                            } else if (row?.user_timeoff_history) {
                                let month = Math.ceil(row.user_timeoff_history.duration / 30);
                                return `<i>Cuti Membership ${month} Bulan</i>`;
                            } else {
                                let name = row?.annual_payment_history?.name;
                                return `<i>${name}</i>`;
                            }
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return 'Rp ' + row.pay_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                    },
                    {
                        data: 'paid_at',
                        name: 'paid_at',
                        render: function(data) {
                            return moment(data).format('D MMM YYYY, h:mm A');
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            let badgeClass = data === 'PAID' ? 'badge-light-success' : data === 'PENDING' ? 'badge-light-warning' : 'badge-light-danger';
                            return `<span class="badge ${badgeClass}">${data === 'PAID' ? 'LUNAS' : data}</span>`;
                        },
                    },
                ]
            });

            // Handler filter
            $('#gym_place_id').change(function() {
                table.ajax.reload();
                loadDashboardData();
            });

            // Handler date range juga bisa reload table & chart
            $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
                table.ajax.reload();
                loadDashboardData();
            });

            function cb(start, end) {
                $('#dateRange span').html(moment(start).format('D/MM/YYYY') + ' - ' + moment(end).format('D/MM/YYYY'));
                $('#start_date').val(moment(start).format('YYYY-MM-DD'));
                $('#end_date').val(moment(end).format('YYYY-MM-DD'));

                table.ajax.reload();
                loadDashboardData();
            }

            $('#dateRange').daterangepicker({
                startDate: moment(start),
                endDate: moment(end),
                ranges: {
                    'Hari Ini': [moment(), moment()],
                    'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                    'Tahun Ini': [moment().startOf('year'), moment().endOf('year')],
                    'Tahun Kemarin': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                }
            }, cb);

            cb(start, end);

            function loadDashboardData() {
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const gymPlaceId = $('#gym_place_id').val();
                // Tampilkan overlay loading
                $('#cardLoadingOverlay').show();
                $('#cardLoadingOverlay2').show();

                $.ajax({
                    url: "{{ route('dashboard-income.index') }}",
                    type: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate,
                        gym_place_id: gymPlaceId
                    },
                    success: function(response) {
                        console.log("Response received:", response);

                        $('.title-box.text-dark').text(`Rp${new Intl.NumberFormat('id-ID').format(response.totalPaid)}`);
                        $('.title-box.text-danger').text(`Rp${new Intl.NumberFormat('id-ID').format(response.totalPending)}`);

                        renderChartType(response.chartData);
                        renderChartPurchase(response.paymentData);

                        updateChartTypeLabels(response.chartData, response.totalPaid);
                        updatePaymentLabels(response.paymentData);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data:", error);
                        console.error("Response:", xhr.responseText);
                        alert("Gagal memuat data. Silakan coba lagi.");
                    },
                    complete: function() {
                        // Sembunyikan overlay loading di dalam card
                        $('#cardLoadingOverlay').hide();
                        $('#cardLoadingOverlay2').hide();
                    }
                });
            }

            function renderChartType(chartData) {
                const data = chartData.map(item => ({
                    x: item.name,
                    value: item.total
                }));

                if (window.chartType) {
                    window.chartType.data(data);
                } else {
                    window.chartType = anychart.pie(data);
                    window.chartType.background(false);
                    window.chartType.width('100%');
                    window.chartType.innerRadius('70%');
                    window.chartType.height('100%');
                    window.chartType.legend(false);
                    window.chartType.labels().enabled(true);
                    window.chartType.tooltip().anchor("bottomLeft");
                    const colors = ["#655025", "#8A6E33", "#B18D41", "#071437", "#4B5675", "#B5B5C3", "#E4E6EF"];
                    window.chartType.palette(colors);
                    window.chartType.container('chart-type');
                    window.chartType.draw();
                }
            }

            function renderChartPurchase(paymentData) {
                const data = paymentData.map(item => ({
                    x: item.name,
                    value: item.total
                }));

                if (window.chartPurchase) {
                    window.chartPurchase.data(data);
                } else {
                    window.chartPurchase = anychart.pie(data);
                    window.chartPurchase.background(false);
                    window.chartPurchase.width('100%');
                    window.chartPurchase.height('100%');
                    window.chartPurchase.innerRadius('70%');
                    window.chartPurchase.legend(false);
                    window.chartPurchase.labels().enabled(true);
                    window.chartPurchase.tooltip().anchor("bottomLeft");
                    const colors = ["#B18D41", "#E4E6EF"];
                    window.chartPurchase.palette(colors);
                    window.chartPurchase.container('chart-purcahase');
                    window.chartPurchase.draw();
                }
            }

            function updateChartTypeLabels(chartData, totalPaid) {
                const labelsContainer = document.getElementById('chartTypeLabels');
                if (!labelsContainer) return;

                labelsContainer.innerHTML = ''; // Bersihkan konten sebelumnya

                chartData.forEach((item, index) => {
                    const percentage = totalPaid > 0 ? ((item.total / totalPaid) * 100).toFixed(2) : '0.00';
                    const bulletColor = `bg-${index + 1}`; // Ubah warna sesuai index atau definisikan warna CSS untuk tiap `bg-1`, `bg-2`, dll.

                    labelsContainer.innerHTML += `
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex gap-2 align-items-center">
                    <div class="bullet w-8px h-3px rounded-2 ${bulletColor} me-3"></div>
                    <div class="label-chart">${item.name} (${percentage}%)</div>
                </div>
                <div class="fw-bolder text-gray-700 text-xxl-end">Rp${new Intl.NumberFormat('id-ID').format(item.total)}</div>
            </div>
        `;
                });
            }

            function updatePaymentLabels(paymentData) {
                const labelsContainer = document.getElementById('paymentLabelsContainer');
                if (!labelsContainer) return;

                labelsContainer.innerHTML = '';

                const totalAmount = paymentData.reduce((sum, item) => sum + parseFloat(item.total), 0);

                console.log("Total Amount:", totalAmount);

                paymentData.forEach((data, index) => {
                    const totalValue = parseFloat(data.total);
                    const percentage = totalAmount > 0 ? ((totalValue / totalAmount) * 100).toFixed(2) : '0.00';

                    console.log(`Data ${index + 1}: ${data.name} Total: ${totalValue} Percentage: ${percentage}%`);

                    const bulletColor = index % 2 === 0 ? 'bg-primary' : 'bg-secondary';

                    labelsContainer.innerHTML += `
            <div class="d-flex gap-4 align-items-center justify-content-between">
                <div class="d-flex gap-2 align-items-center">
                    <div class="bullet w-8px h-3px rounded-2 ${bulletColor} me-3"></div>
                    <div class="label-chart">${data.name} (${percentage}%)</div>
                </div>
                <div class="fw-bolder text-gray-700 text-xxl-end">Rp${new Intl.NumberFormat('id-ID').format(totalValue)}</div>
            </div>
        `;
                });
            }

            // chart type
            const chartData = @json($chartData);

            const chartType = () => {
                const data = chartData.map(item => ({
                    x: item.name,
                    value: item.total
                }));

                const chart = anychart.pie(data);
                chart.background(false);
                chart.width('100%');
                chart.innerRadius('70%');
                chart.height('100%');
                chart.legend(false);
                chart.labels().enabled(true);
                chart.tooltip().anchor("bottomLeft");

                const colors = ["#655025", "#8A6E33", "#B18D41", "#071437", "#4B5675", "#B5B5C3", "#E4E6EF"];
                chart.palette(colors);

                chart.container('chart-type');
                chart.draw();
            }

            const chartPurchase = () => {
                const paymentData = @json($paymentData);

                const data = paymentData.map(item => ({
                    x: item.name,
                    value: item.total
                }));

                const chart = anychart.pie(data);
                chart.background(false);

                chart.width('100%');
                chart.height('100%');
                chart.innerRadius('70%');

                chart.legend(false);
                chart.labels().enabled(true);

                chart.tooltip().anchor("bottomLeft");

                const colors = ["#B18D41", "#E4E6EF"];
                chart.palette(colors);

                chart.container('chart-purcahase');
                chart.draw();
            }

            // chartPurchase();
            // chartType();
            // statisticChart();
            // statisticChartType();

        });
    });
</script>
@endpush
