@extends('layouts.master', ['title' => 'Data BPD', 'main' => 'Dashboard'])
@section('content')
    <div class="app-main pt-6 flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card card-flush shadow-sm">
                        <div class="card-header mt-6">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">📊 Data BPD</span>
                                <span class="text-muted fs-7">Daftar Badan Pengurus Daerah PHRI seluruh Indonesia</span>
                            </h3>
                            <div class="card-toolbar gap-3">
                                <div class="text-muted small me-3">
                                    Terakhir update:
                                    {{ $last ? \Carbon\Carbon::parse($last)->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H.i') . ' WIB' : '—' }}
                                </div>
                                <button id="btn-refresh-cache" class="btn btn-light-primary btn-sm">
                                    <i class="fa fa-rotate"></i> Refresh Cache
                                </button>
                            </div>
                        </div>

                        <div class="card-body pt-0">
                            <table id="datatable"
                                class="table table-striped table-hover align-middle table-row-dashed fs-6 gy-5 mb-0">
                                <thead>
                                    <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                        <th>No</th>
                                        <th class="min-w-250px">Pengurus</th>
                                        <th>Alamat</th>
                                        <th>Kontak</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const items = @json($items);

            $('#datatable').DataTable({
                ordering: true,
                serverSide: false,
                responsive: true,
                data: items,
                columns: [{
                        data: null,
                        render: (data, type, row, meta) => meta.row + 1,
                        className: 'fw-bold text-center'
                    },
                    {
                        data: null,
                        responsivePriority: 1,
                        render: (row) => {
                            const ketuaImg = row.img && row.img.trim() !== '' ?
                                row.img :
                                'https://via.placeholder.com/150';

                            const sekImg = row.img_sec && row.img_sec.trim() !== '' ?
                                row.img_sec :
                                'https://via.placeholder.com/150';

                            return `
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="symbol symbol-50px overflow-hidden rounded">
                                            <img src="${ketuaImg}" class="img-fluid" alt="Ketua"/>
                                        </div>
                                        <div class="symbol symbol-50px overflow-hidden rounded">
                                            <img src="${sekImg}" class="img-fluid" alt="Sekretaris"/>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">${row.nama}</div>
                                            <div class="small text-muted">${row.provinsi} - ${row.kota}</div>
                                            <div class="small">Ketua: <b>${row.nama_ketua}</b></div>
                                            <div class="small">Sekretaris: <b>${row.nama_sekretaris}</b></div>
                                        </div>
                                    </div>
                                `;
                        }
                    },
                    {
                        data: 'alamat'
                    },
                    {
                        data: null,
                        render: (row) => `
                        <div class="d-flex flex-column">
                            <a href="tel:${row.telp}" class="text-dark">
                                <i class="fa fa-phone text-success"></i> ${row.telp}
                            </a>
                            <a href="mailto:${row.email}" class="text-primary">
                                <i class="fa fa-envelope"></i> ${row.email}
                            </a>
                            ${row.web ? `<a href="${row.web}" target="_blank" class="text-info">
                                                                                                                                                    <i class="fa fa-globe"></i> Website
                                                                                                                                                </a>` : ''}
                        </div>
                    `
                    },
                    {
                        data: 'is_active',
                        render: (val) => val === '1' ?
                            '<span class="badge badge-light-success">Aktif</span>' :
                            '<span class="badge badge-light-danger">Nonaktif</span>'
                    }
                ],
                order: [
                    [1, 'asc']
                ]
            });

            $('#btn-refresh-cache').on('click', function(e) {
                e.preventDefault();
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Refreshing...');
                $.ajax({
                    url: "{{ route('bpd.refresh') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: res?.message || 'Cache diperbarui.',
                            icon: 'success',
                            timer: 1300,
                            showConfirmButton: false
                        });
                        window.location.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Gagal',
                            text: xhr.responseJSON?.message ||
                                'Tidak bisa menyegarkan cache.',
                            icon: 'error'
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(
                            '<i class="fa fa-rotate"></i> Refresh Cache');
                    }
                });
            });
        });
    </script>
@endpush
