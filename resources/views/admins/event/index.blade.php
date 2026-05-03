@extends('layouts.master', ['title' => 'Event Management', 'main' => 'Dashboard'])
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
                                <span class="card-label fw-bold fs-3 mb-1">Event Management</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Kelola event dari PHRI API</span>
                            </h3>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Button-->
                                <button type="button" class="btn btn-primary btn-sm" onclick="syncEvents()">
                                    <i class="fa fa-sync"></i>
                                    Sync Events
                                </button>
                                <!--end::Button-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table id="datatable" class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th class="w-100px">Image</th>
                                        <th>Nama Event</th>
                                        <th>Lokasi</th>
                                        <th>Penyelenggara</th>
                                        <th>Tanggal Event</th>
                                        <th>Approval</th>
                                        <th>Status</th>
                                        <th class="text-center min-w-100px">Aksi</th>
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
                    url: "{{ route('event.index') }}",
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
                        render: function(data, type, row) {
                            if (data == null) {
                                return `
                            <div class="symbol-label h-50px w-50px rounded-circle bg-light-primary d-flex justify-content-center align-items-center">
                                <span class="fs-2x fw-bold text-primary text-capitalize">
                                    ${row.name.charAt(0)}</span>
                            </div>`;
                            } else {
                                return `<img src="${data}" alt="image" class="w-75px h-50px img-thumnail object-fit-cover" />`;
                            }
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        responsivePriority: -2,
                    },
                    {
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'organized_by',
                        name: 'organized_by'
                    },
                    {
                        data: 'event_date',
                        name: 'event_date'
                    },
                    {
                        data: 'approval_status',
                        name: 'approval_status'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true,
                        responsivePriority: -1,
                    },
                ]
            });

            // Handle toggle status button click
            $(document).on('click', '.toggle-status-btn', function(e) {
                e.preventDefault();

                var button = $(this);
                var url = button.data('url');
                var eventId = button.data('id');

                // Show confirmation dialog
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mengubah status event ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable button during request
                        button.prop('disabled', true);

                        $.ajax({
                            url: url,
                            type: 'PATCH',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });

                                    // Reload datatable to refresh data
                                    table.ajax.reload(null, false);
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan saat mengubah status event.',
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan saat mengubah status event.',
                                    icon: 'error'
                                });
                            },
                            complete: function() {
                                // Re-enable button
                                button.prop('disabled', false);
                            }
                        });
                    }
                });
            });
        });

        // Function to sync events
        function syncEvents() {
            Swal.fire({
                title: 'Sync Events',
                text: 'Apakah Anda yakin ingin melakukan sinkronisasi event dari PHRI API?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Sync!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Syncing...',
                        text: 'Sedang melakukan sinkronisasi event, mohon tunggu...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Make AJAX request to sync events
                    $.ajax({
                        url: '{{ route('event.sync') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Reload datatable to show new data
                                $('#datatable').DataTable().ajax.reload();
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message ||
                                        'Terjadi kesalahan saat melakukan sinkronisasi.',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat melakukan sinkronisasi event.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
