@extends('layouts.master', ['title' => 'Detail Event', 'main' => 'Event'])

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin="" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
@endpush

@section('content')
    <div class="content pt-6 d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="app-container container-xxl">
                @if ($event->start_date < date('Y-m-d') && $event->eventLeaderboards()->count() == 0)
                <div class="alert alert-danger alert-dismissible show fade">
                    <ul>
                        <li>Segera Menginputkan Data Leaderboard Karena Event Sudah Selesai</li>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body--> 
                    <div class="card-body">
                        <div class="d-flex gap-2 align-items-center mb-2">
                            <a href="{{ route('gym-place.index') }}" class="mt-1">
                                <span class="menu-icon back pt-1">
                                    <i class="ki-duotone ki-arrow-left">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </a>
                            <h1 class="text-capitalize mb-0">{{ $event->name }}</h1>
                            <a href="{{ route('event.edit', $event->id) }}">
                                <i class="ki-duotone ki-notepad-edit fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </a>
                        </div>
                        <div class="hover-scroll-x mt-5">
                            <div class="d-grid">
                                <ul class="nav nav-tabs flex-nowrap text-nowrap">
                                    <li class="nav-item">
                                        <a class="nav-link active btn btn-active-light btn-color-gray-600 btn-active-color-primary"
                                            id="nav_tab_information" data-bs-toggle="tab" href="#tab_information">
                                            Informasi Event
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary"
                                            id="nav_tab_participation" data-bs-toggle="tab" href="#tab_participation">
                                            Partisipasi Event
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn btn-active-light btn-color-gray-600 btn-active-color-primary"
                                            id="nav_tab_leaderboard" data-bs-toggle="tab" href="#tab_leaderboard">
                                            Event Leaderboard
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <div class="mt-6">
                    <div class="card-body v2">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade active show" id="tab_information" role="tabpanel">
                                <!--begin::Content-->
                                <div id="kt_app_content" class="app-content flex-column-fluid">
                                    <!--begin::Content container-->
                                    <div id="kt_app_content_container" class="container-xxl">
                                        <!--begin::Layout-->
                                        <div class="d-flex flex-column flex-xl-row">
                                            <!--begin::Sidebar-->
                                            <div class="flex-column flex-lg-row-auto w-100 w-xl-450px mb-10">
                                                <!--begin::Card-->
                                                <div class="card mb-5 mb-xl-8">
                                                    <!--begin::Card body-->
                                                    <div class="card-body pt-15">
                                                        <!--begin::Summary-->
                                                        <div class="d-flex flex-center flex-column mb-5">
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-200px mb-7">
                                                                <img src="{{ asset($event->image) }}" alt="image">
                                                                {{-- <img src="assets/media/avatars/300-1.jpg" alt="image" /> --}}
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Name-->
                                                            <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1">{{ $event->name }}</a>
                                                            <!--end::Name-->
                                                        </div>
                                                        <!--end::Summary-->
                                                        <!--begin::Location Map toggle-->
                                                        <div class="d-flex flex-stack fs-4 py-3">
                                                            <div class="fw-bold">Location Map</div>
                                                        </div>
                                                        <!--end::Details toggle-->
                                                        <div class="separator separator-dashed my-3"></div>
                                                        <!--begin::Details content-->
                                                        <div class="pb-5 fs-6">
                                                            <!--begin::Maps item-->
                                                            <div id="map" style="height: 300px"></div>
                                                            <!--begin::Maps item-->
                                                        </div>
                                                        <!--end::Details content-->
                                                    </div>
                                                    <!--end::Card body-->
                                                </div>
                                                <!--end::Card-->
                                            </div>
                                            <!--end::Sidebar-->
                                            <!--begin::Content-->
                                            <div class="flex-lg-row-fluid ms-lg-15">
                                                <!--end::Card-->
                                                <div class="card pt-4 mb-6 mb-xl-9">
                                                    <!--begin::Card header-->
                                                    <div class="card-header border-0">
                                                        <!--begin::Card title-->
                                                        <div class="card-title">
                                                            <h2>Tiket</h2>
                                                        </div>
                                                        <!--end::Card title-->
                                                    </div>
                                                    <!--end::Card header-->
                                                    <!--begin::Card body-->
                                                    <div class="card-body pt-0 pb-5">
                                                        <!--begin::Table-->
                                                        <div class="table-responsive">
                                                            <table class="table table-responsive align-middle table-row-dashed gy-5" id="kt_table_customers_payment">
                                                                <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                                                    <tr class="text-start text-muted text-uppercase gs-0">
                                                                        <th class="min-w-200px">Nama</th>
                                                                        <th class="min-w-80px">Kuota</th>
                                                                        <th class="min-w-80px">Price</th>
                                                                        <th class="min-w-100px">Tanggal Penjualan</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="fs-6 fw-semibold text-gray-600">
                                                                    @foreach ($event->eventTickets as $ticket)
                                                                    <tr>
                                                                        <td>{{ $ticket->name }}</td>
                                                                        <td>{{ $ticket->max_quota }}<br>sell: {{ $ticket->max_quota - $ticket->quota }}</td>
                                                                        <td>Rp.{{ number_format($ticket->price,0,',','.') }}</td>
                                                                        <td>{{ date('d F Y H:i', strtotime($ticket->start_date))  }} - <br>{{ date('d F Y H:i', strtotime($ticket->end_date)) }}</td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <!--end::Table-->
                                                    </div>
                                                    <!--end::Card body-->
                                                </div>
                                                <!--end::Card-->
                                                <div class="card pt-4 mb-6 mb-xl-9">
                                                    <!--begin::Card header-->
                                                    <div class="card-header border-0">
                                                        <!--begin::Card title-->
                                                        <div class="card-title">
                                                            <h2>Event Detail</h2>
                                                        </div>
                                                        <!--end::Card title-->
                                                    </div>
                                                    <!--end::Card header-->
                                                    <!--begin::Card body-->
                                                    <div class="card-body pt-0 pb-5">
                                                        <div class="row">
                                                            <div class="col-6 mb-2">
                                                                <label class="text-label text-muted">Nama Event</label>
                                                                <p class="text-label">{{ $event->name }}</p>
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <label class="text-label text-muted">Tanggal Event</label>
                                                                <p class="text-label">{{ date('d F Y H:i', strtotime($event->start_date . " " . $event->start_time)) }} - {{ date('H:i', strtotime($event->end_time)) }}</p>
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <label class="text-label text-muted">Tempat/Alamat Event</label>
                                                                <p class="text-label">{{ $event->place_name }}</p>
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <label class="text-label text-muted">Max. Penggunaan / User</label>
                                                                <p class="text-label">{{ $event->max_use }}</p>
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <label class="text-label text-muted">Status Publish</label>
                                                                <p class="text-label">
                                                                    @if ($event->is_publish)
                                                                    <span class='badge text-white bg-success'>Publish</span>
                                                                    @else
                                                                    <span class='badge text-white bg-danger'>Non Publish</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <label class="text-label text-muted">Status Event</label>
                                                                <p class="text-label">
                                                                    @if ($event->is_active)
                                                                    <span class='badge text-white bg-success'>Aktif</span>
                                                                    @else
                                                                    <span class='badge text-white bg-danger'>Non Aktif</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="text-label text-muted">Deskripsi</label>
                                                                <p class="text-label">{{ $event->description }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Content-->
                                        </div>
                                        <!--end::Layout-->
                                    </div>
                                    <!--end::Content container-->
                                </div>
                                <!--end::Content-->
                            </div>
                            <div class="tab-pane fade" id="tab_participation" role="tabpanel">
                                <!--begin::Content-->
                                <div id="kt_app_content" class="app-content flex-column-fluid">
                                    <!--begin::Content container-->
                                    <div id="kt_app_content_container" class="container-xxl">
                                        <div class="d-flex flex-column flex-xl-row">
                                            <div class="flex-lg-row-fluid">
                                                <!--end::Card-->
                                                <div class="card pt-4 mb-6 mb-xl-9">
                                                    <!--begin::Card header-->
                                                    <div class="card-header d-flex flex-wrap border-0">
                                                        <!--begin::Card title-->
                                                        <div class="card-title">
                                                            <h2>List User yang Berpatisipasi pada Event</h2>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 gap-lg-3">
                                                            <form action="{{ route('event-participant.export', $event->id) }}" method="GET"
                                                                enctype="multipart/form-data">
                                                                @method('GET')

                                                                <button class="btn btn-primary btn-sm text-nowrap" type="submit">
                                                                    <i class="ki-duotone ki-exit-up fs-3">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                        <span class="path3"></span>
                                                                    </i>
                                                                    Export Excel
                                                                </button>
                                                            </form>
                                                        </div>
                                                        <!--end::Card title-->
                                                    </div>
                                                    <!--end::Card header-->
                                                    <!--begin::Card body-->
                                                    <div class="card-body pt-0 pb-5">
                                                        <!--begin::Table-->
                                                        <div class="table-responsive">
                                                            <table id="datatable-event-participation" class="table table-hover align-start table-row-dashed fs-6 gy-5 mb-0">
                                                                <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                                        <th style="min-width: 20px">No</th>
                                                                        <th style="min-width: 150px">Nama User</th>
                                                                        <th style="min-width: 150px">Transaksi</th>
                                                                        <th style="min-width: 150px">Tanggal Beli</th>
                                                                        <th style="min-width: 200px">Tiket diBeli</th>
                                                                        <th style="min-width: 100px">Status</th>
                                                                        <th class="text-center" style="min-width: 100px">Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                        <!--end::Table-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Content container-->
                                </div>
                                <!--end::Content-->
                            </div>
                            <div class="tab-pane fade" id="tab_leaderboard" role="tabpanel">
                                <!--begin::Content-->
                                <div id="kt_app_content" class="app-content flex-column-fluid">
                                    <!--begin::Content container-->
                                    <div id="kt_app_content_container" class="container-xxl">
                                        <div class="d-flex flex-column flex-xl-row">
                                            <div class="flex-lg-row-fluid">
                                                <!--end::Card-->
                                                <div class="card d-flex pt-4 mb-6 mb-xl-9">
                                                    <!--begin::Card header-->
                                                    <div class="card-header border-0">
                                                        <!--begin::Card title-->
                                                        <div class="card-title">
                                                            <h2>Event Leaderboard</h2>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 gap-lg-3">
                                                            <a type="button" class="btn btn-primary btn-sm btn-create" href="{{ route('event-leaderboard.create', $event->id) }}">
                                                                <i class="fa fa-plus"></i>
                                                                List Leaderboard</a>
                                                        </div>
                                                        <!--end::Card title-->
                                                    </div>
                                                    <!--end::Card header-->
                                                    <!--begin::Card body-->
                                                    <div class="card-body pt-0 pb-5">
                                                        <!--begin::Table-->
                                                        <div class="table-responsive">
                                                            <table id="datatable-event-leaderboard" class="table table-hover align-start table-row-dashed fs-6 gy-5 mb-0">
                                                                <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                                        <th class="text-center" style="min-width: 70px">No Urutan</th>
                                                                        <th class="text-center" style="min-width: 50px">Avatar User</th>
                                                                        <th class="text-center" style="min-width: 150px">Nama User</th>
                                                                        <th class="text-center" style="min-width: 100px">Kode Ticket</th>
                                                                        <th class="text-center" style="min-width: 50px">Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                        <!--end::Table-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Content container-->
                                </div>
                                <!--end::Content-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Wrapper-->
    </div>

    <div class="modal fade" tabindex="-1" id="modal-event-ticket-order">
        <div class="modal-dialog mw-900px">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="user-name">Tiket ....</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <h4 class="mb-6" id="period"></h4>
                    <div class="row">
                        <table class="table table-sm table-striped table-bordered" id="table-event-ticket">
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endsection

    @push('js')
    <script>
        let latitude = {{ $event->latitude ?? '-6.175389999999936'}};
        let longitude = {{ $event->longitude ?? '106.82704000000007' }};

        var map, newMarker, markerLocation;
        $(function() {
            // Now map reference the global map declared in the first line
            map = L.map('map').setView([latitude, longitude], 8);

            L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
                maxZoom: 18
            }).addTo(map);
            newMarkerGroup = new L.LayerGroup();
            var marker = L.marker([latitude, longitude]).addTo(map);
        });
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#datatable-event-participation').DataTable({
                ordering: true,
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                columnDefs: [
                    {"targets": 4}
                ],
                ajax: {
                    url: '{{ route('event.show', ':id') }}'.replace(':id', '{{ $event->id }}'),
                    type: 'GET',
                    beforeSend: function() {
                        $('#datatable-event-participation tbody').empty();
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
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'transaction_id',
                        name: 'transaction_id',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                    },
                    {
                        data: 'ticket',
                        name: 'ticket',
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ]
            });

            var table_leaderboard = $('#datatable-event-leaderboard').DataTable({
                ordering: true,
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                columnDefs: [
                    {"targets": "_all", "className": "dt-center"}
                ],
                ajax: {
                    url: '{{ route('event-leaderboard.index', ':id') }}'.replace(':id', '{{ $event->id }}'),
                    type: 'GET',
                    beforeSend: function() {
                        $('#datatable-event-leaderboard tbody').empty();
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
                        data: 'order',
                        name: 'order',
                    },
                    {
                        data: 'user.avatar',
                        name: 'user.avatar',
                        sortable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (data == null) {
                                return `<div class="symbol-label h-50px w-50px rounded-circle bg-light-primary d-flex justify-content-center align-items-center">
                                            <span class="fs-2x fw-bold text-primary text-capitalize">
                                                ${row.user.name.charAt(0)}
                                            </span>
                                        </div>`;
                            } else {
                                return `<img src="${data}" alt="image" class="h-50px w-50px rounded-circle" />`;
                            }
                        }
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'user_ticket',
                        name: 'user_ticket'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        sortable: false,
                        searchable: false
                    }
                ],
                rowReorder: {
                    selector: 'td:not(:nth-child(4))', // Target all columns except the 4th
                    dataSrc: 'order', // Order data source
                    update: false  // Disable automatic update, we'll handle it with Ajax
                }
            });

            table_leaderboard.on('row-reorder', function(e, diff, edit) {
                var orderData = [];

                // Prepare an array of order changes
                diff.forEach(function(change) {
                    orderData.push({
                        id: table_leaderboard.row(change.node).data().id,
                        position: change.newData
                    });
                });

                // Send the reordered data to the reorder route
                $.ajax({
                    type: "POST",
                    url: '{{ route('event-leaderboard.reorder', $event->id) }}',
                    data: {
                        order: orderData,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // if (response.status === "success") {
                        //     console.log("Order updated successfully!");
                        // } else {
                        //     console.error("Error updating order:", response.message);
                        // }
                        table_leaderboard.ajax.reload();  // Reload table data
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Error:", textStatus, errorThrown);
                    }
                });
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

        function eventTicketOrder(id) {
            $.ajax({
                url: '{{ route('event.ticket-order', ':id') }}'.replace(':id', id),
                method: 'get',
                dataType: 'json',
                success: function(data) {
                    let table = document.getElementById("table-event-ticket");
                    let ticket = data.ticket_order;
                    let html =
                        "<tr>\
                            <th class='text-center'>No</th>\
                            <th class='text-center'>Tiket</th>\
                            <th class='text-center'>Kode Tiket</th>\
                            <th class='text-center'>Kuantitas</th>\
                            <th class='text-center'>Harga</th>\
                        </tr>";
                    let i = 1;
                    if (ticket.length > 0) {
                        ticket.forEach(function(item) {
                            console.log(item);
                            
                            html += "<tr>";
                            html += "<td class='text-center' style='width: 5%'>" + (i++) + "</td>";
                            html += "<td class='text-center' style='width: 25%'>" + item.name + "</td>";
                            html += "<td class='text-center'>" + item.code_number + "</td>";
                            html += "<td class='text-center'>" + item.quantity + "</td>";
                            html += "<td class='text-center'>" + item.price + "</td>";
                            html += "</tr>";
                        });
                        table.innerHTML = html;
                    }

                    $("#user-name").text('Tiket '+data.user)
                    
                    $('#modal-event-ticket-order').modal('show');
                }
            });
        }
    </script>
    @endpush
