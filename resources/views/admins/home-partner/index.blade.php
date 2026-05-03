@extends('layouts.master', ['title' => 'Data Partner', 'main' => 'Dashboard'])
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
                                <span class="card-label fw-bold fs-3 mb-1">Data Partner</span>
                            </h3>
                            <div class="card-toolbar">
                                <a href="{{ route('home-partner.create') }}" class="btn btn-primary btn-sm btn-create">
                                    <i class="fa fa-plus"></i>
                                    Partner
                                </a>
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table id="table-home-partner"
                                class="table table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th style="width: 5%">No</th>
                                        <th class="min-w-125px">Logo</th>
                                        <th class="min-w-125px">Urutan</th>
                                        <th class="min-w-125px">Status</th>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.3.1/css/rowReorder.dataTables.css" />
    <script src="https://cdn.datatables.net/rowreorder/1.3.1/js/dataTables.rowReorder.js"></script>
    <script>
        $(function() {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const table = $('#table-home-partner').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                responsive: true,
                searchDelay: 500,
                ajax: "{{ route('home-partner.index') }}",
                rowReorder: {
                    selector: 'tr',
                    dataSrc: 'order',
                    update: false // penting: biar DataTables tidak overwrite urutan lokal
                },
                language: {
                    paginate: {
                        next: "<i class='fa fa-angle-right'>",
                        previous: "<i class='fa fa-angle-left'>"
                    },
                    loadingRecords: "Loading...",
                    processing: "Processing..."
                },
                columns: [{
                        data: null,
                        sortable: false,
                        searchable: false,
                        render: (d, t, r, m) => m.row + m.settings._iDisplayStart + 1
                    },
                    {
                        data: 'image',
                        name: 'image',
                        render: (data, type, row) => {
                            if (!data) {
                                const initial = (row.name || '?').toString().charAt(0)
                                    .toUpperCase();
                                return `<span class="symbol-label fs-2x fw-bold text-primary bg-light-primary">${initial}</span>`;
                            }
                            return `<img src="${data}" alt="image" class="h-70px w-70px" />`;
                        }
                    },
                    {
                        data: 'order',
                        name: 'order',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                        orderable: true,
                        searchable: true,
                        render: v =>
                            `<span class="badge badge-light-${v ? 'success' : 'danger'}">${v ? 'Aktif' : 'Tidak Aktif'}</span>`
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'uuid',
                        name: 'uuid',
                        visible: false,
                        searchable: false
                    } // pastikan server kirim 'uuid'
                ]
            });

            table.on('row-reorder', function(e, diff, edit) {
                if (!diff.length) return;

                // Ambil baris yang dipicu drag (baris utama yg dipindah)
                const triggerNode = edit.triggerRow; // DOM TR
                const triggerData = table.row(triggerNode).data() || {};
                let id = triggerData.uuid || triggerData.id || table.row(triggerNode).id();
                id = String(id);

                // Hitung posisi global baru: start index halaman + newData (1-based)
                // (Ambil newData dari item diff yang node-nya sama dengan triggerNode)
                const pageInfo = table.page.info();
                const moved = diff.find(d => d.node === triggerNode) || diff[0];
                const newOrderGlobal = pageInfo.start + parseInt(moved.newData, 10); // udah 1-based

                fetch("{{ route('home-partner.reorder.single') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            id,
                            new_order: newOrderGlobal
                        })
                    })
                    .then(async res => {
                        if (!res.ok) throw new Error(await res.text());
                        return res.json();
                    })
                    .then(() => {
                        table.ajax.reload(null, false); // refresh alus
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Gagal update urutan. Coba ulangi.');
                        table.ajax.reload(null, false);
                    });
            });
        });
    </script>
@endpush
