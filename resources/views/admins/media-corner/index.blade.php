@extends('layouts.master', ['title' => 'Data Media', 'main' => 'Dashboard'])
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
                        <div class="card-header mt-4">
                            <!--begin::Card title-->
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Data Media</span>
                            </h3>
                            {{-- <div class="card-toolbar">
                                <a href="{{ route('media-corner.create') }}" class="btn btn-primary btn-sm btn-create">
                                    <i class="fa fa-plus"></i>
                                    Partner
                                </a>
                            </div> --}}
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table id="table-media-corner"
                                class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th class="min-w-125px">Thumbnail</th>
                                        <th class="min-w-125px">Judul</th>
                                        <th class="min-w-125px">Channel</th>
                                        <th class="min-w-125px">Status</th>
                                        <th class="min-w-125px">Publish</th>
                                        <th class="min-w-125px">URL</th>
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
        $(document).ready(() => {
            var table = $('#table-media-corner').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                searchDelay: 500,
                ajax: "{{ route('media-corner.index') }}",
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
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'title',
                        name: 'title',
                        orderable: true,
                        searchable: true,
                        responsivePriority: 1,
                    },
                    {
                        data: 'channel',
                        name: 'channel',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return `<span class="badge badge-light-${data ? 'success' : 'danger'}">${data ? 'Aktif' : 'Tidak Aktif'}</span>`;
                        },
                        responsivePriority: 2,
                    },
                    {
                        data: 'published_at',
                        name: 'published_at',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'url',
                        name: 'url',
                        orderable: true,
                        searchable: true,
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
                var mediaId = button.data('id');

                // Show confirmation dialog
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mengubah status media ini?',
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
                                        text: 'Terjadi kesalahan saat mengubah status media.',
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan saat mengubah status media.',
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
        })
    </script>
@endpush
