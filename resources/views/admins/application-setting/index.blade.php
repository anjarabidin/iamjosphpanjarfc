@extends('layouts.master', ['title' => 'Application Settings', 'main' => 'Dashboard'])
@section('content')
    <div class="app-main pt-6 flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::AD/ART Upload Card-->
                    <div class="card card-flush mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header mt-6">
                            <!--begin::Card title-->
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Dokumen AD/ART</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Upload Anggaran Dasar/Anggaran Rumah
                                    Tangga</span>
                            </h3>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Upload Form-->
                            <form id="ad-art-form" enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-6">
                                    <div class="col-lg-12">
                                        <label class="form-label fw-semibold">Upload File AD/ART</label>
                                        <input type="file" name="ad_art" id="ad_art" class="form-control"
                                            accept=".pdf,.doc,.docx" />
                                        <div class="form-text">
                                            Format yang didukung: PDF, DOC, DOCX (Max 10MB)
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-6">
                                    <div class="col-lg-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ki-duotone ki-cloud-upload fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Upload AD/ART
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <!--end::Upload Form-->

                            <!--begin::Current File-->
                            @if (isset($settings) && $settings && $settings->hasAdArt())
                                <div id="current-ad-art" class="alert alert-primary d-flex align-items-center p-5">
                                    <i class="ki-duotone ki-file-text fs-2hx text-primary me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <h4 class="mb-1 text-dark">File AD/ART Saat Ini</h4>
                                        <span class="fw-semibold">{{ $settings->ad_art_file_name }}</span>
                                        <span class="text-muted">{{ $settings->ad_art_file_size }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('application-setting.download-ad-art') }}"
                                            class="btn btn-sm btn-light-primary">
                                            <i class="ki-duotone ki-download fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Download
                                        </a>
                                        <button type="button" class="btn btn-sm btn-light-danger" onclick="deleteAdArt()">
                                            <i class="ki-duotone ki-trash fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div id="no-ad-art" class="alert alert-info d-flex align-items-center p-5">
                                    <i class="ki-duotone ki-information-5 fs-2hx text-info me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-dark">Belum Ada File AD/ART</h4>
                                        <span>Silakan upload file Anggaran Dasar/Anggaran Rumah Tangga</span>
                                    </div>
                                </div>
                            @endif
                            <!--end::Current File-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::AD/ART Upload Card-->

                    <!--begin::Database Backup Card-->
                    <div class="card card-flush mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header mt-6">
                            <!--begin::Card title-->
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Database Backup</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Create and download database backup</span>
                            </h3>
                            <!--end::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Button-->
                                <button type="button" class="btn btn-primary btn-sm" onclick="createBackup()">
                                    <i class="fa fa-download"></i>
                                    Create Backup
                                </button>
                                <!--end::Button-->
                            </div>
                            <!--end::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Alert-->
                            <div class="alert alert-primary d-flex align-items-center p-5 mb-10">
                                <!--begin::Icon-->
                                <i class="ki-duotone ki-shield-tick fs-2hx text-primary me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <!--end::Icon-->
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-dark">Database Backup Information</h4>
                                    <span>This feature creates a complete backup of your MySQL database including all
                                        tables, data, and structure. The backup file will be downloaded
                                        automatically.</span>
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Alert-->

                            <!--begin::Recent Backups-->
                            <div class="mb-10">
                                <h4 class="fw-bold text-dark mb-5">Recent Backups</h4>
                                <div id="recent-backups-loading" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                                <div id="recent-backups-content" style="display: none;">
                                    <div class="table-responsive">
                                        <table class="table table-row-dashed table-row-gray-300 gy-4">
                                            <thead>
                                                <tr class="fw-bold fs-6 text-gray-800">
                                                    <th>Backup File</th>
                                                    <th>Size</th>
                                                    <th>Created At</th>
                                                </tr>
                                            </thead>
                                            <tbody id="recent-backups-list">
                                                <!-- Backup list will be populated here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="no-backups" style="display: none;">
                                    <div class="text-center text-muted">
                                        <i class="ki-duotone ki-folder fs-3x mb-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <p>No backup files found</p>
                                    </div>
                                </div>
                            </div>
                            <!--end::Recent Backups-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                    <!--begin::Row-->
                    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                            <!--begin::Card-->
                            <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">
                                        <h3 class="fw-bold text-dark">System Information</h3>
                                        <span class="text-muted fw-semibold fs-7">Server and application details</span>
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <!--end::Header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-5">
                                    <div id="system-info-loading" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                    <div id="system-info-content" style="display: none;">
                                        <div class="table-responsive">
                                            <table class="table table-row-dashed table-row-gray-300 gy-4">
                                                <tbody>
                                                    <tr>
                                                        <td class="fw-semibold text-muted">PHP Version</td>
                                                        <td class="text-end" id="php-version">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-muted">Laravel Version</td>
                                                        <td class="text-end" id="laravel-version">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-muted">Server Software</td>
                                                        <td class="text-end" id="server-software">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-muted">Memory Limit</td>
                                                        <td class="text-end" id="memory-limit">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-muted">Max Execution Time</td>
                                                        <td class="text-end" id="max-execution-time">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-muted">Upload Max Size</td>
                                                        <td class="text-end" id="upload-max-filesize">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-muted">Free Disk Space</td>
                                                        <td class="text-end" id="disk-free-space">-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-6">
                            <!--begin::Card-->
                            <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">
                                        <h3 class="fw-bold text-dark">Database Information</h3>
                                        <span class="text-muted fw-semibold fs-7">Database details and backup
                                            history</span>
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <!--end::Header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-5">
                                    <div id="database-info-loading" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                    <div id="database-info-content" style="display: none;">
                                        <div class="table-responsive">
                                            <table class="table table-row-dashed table-row-gray-300 gy-4">
                                                <tbody>
                                                    <tr>
                                                        <td class="fw-semibold text-muted">Database Name</td>
                                                        <td class="text-end" id="database-name">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-muted">Database Size</td>
                                                        <td class="text-end" id="database-size">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold text-muted">Table Count</td>
                                                        <td class="text-end" id="table-count">-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
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
            loadSystemInfo();
            loadDatabaseInfo();
        });

        function loadSystemInfo() {
            $.ajax({
                url: '{{ route('application-setting.system-info') }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#php-version').text(data.php_version);
                        $('#laravel-version').text(data.laravel_version);
                        $('#server-software').text(data.server_software);
                        $('#memory-limit').text(data.memory_limit);
                        $('#max-execution-time').text(data.max_execution_time + 's');
                        $('#upload-max-filesize').text(data.upload_max_filesize);
                        $('#disk-free-space').text(data.disk_free_space);

                        $('#system-info-loading').hide();
                        $('#system-info-content').show();
                    }
                },
                error: function() {
                    $('#system-info-loading').html(
                        '<div class="text-danger">Failed to load system information</div>');
                }
            });
        }

        function loadDatabaseInfo() {
            $.ajax({
                url: '{{ route('application-setting.database-info') }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#database-name').text(data.database_name);
                        $('#database-size').text(data.database_size);
                        $('#table-count').text(data.table_count);

                        // Load recent backups
                        const backupsList = $('#recent-backups-list');
                        backupsList.empty();

                        if (data.recent_backups && data.recent_backups.length > 0) {
                            data.recent_backups.forEach(function(backup) {
                                backupsList.append(`
                                    <tr>
                                        <td class="fw-semibold">${backup.name}</td>
                                        <td>${backup.size}</td>
                                        <td>${backup.created_at}</td>
                                    </tr>
                                `);
                            });
                            $('#recent-backups-content').show();
                        } else {
                            $('#no-backups').show();
                        }

                        $('#database-info-loading').hide();
                        $('#database-info-content').show();
                        $('#recent-backups-loading').hide();
                    }
                },
                error: function() {
                    $('#database-info-loading').html(
                        '<div class="text-danger">Failed to load database information</div>');
                    $('#recent-backups-loading').html(
                        '<div class="text-danger">Failed to load backup history</div>');
                }
            });
        }

        function createBackup() {
            Swal.fire({
                title: 'Create Database Backup',
                text: 'This will create a complete backup of your database. Do you want to continue?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Create Backup!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Creating Backup...',
                        text: 'Please wait while we create your database backup...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Create a temporary form to trigger file download
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('application-setting.backup') }}';
                    form.style.display = 'none';

                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    document.body.appendChild(form);

                    // Submit form to trigger download
                    form.submit();

                    // Clean up
                    document.body.removeChild(form);

                    // Close loading after a short delay
                    setTimeout(() => {
                        Swal.close();
                        Swal.fire({
                            title: 'Backup Created!',
                            text: 'Your database backup has been created and should start downloading shortly.',
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false
                        });

                        // Reload database info to show new backup
                        setTimeout(() => {
                            loadDatabaseInfo();
                        }, 2000);
                    }, 2000);
                }
            });
        }

        // AD/ART Upload Form Handler
        document.getElementById('ad-art-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const fileInput = document.getElementById('ad_art');

            if (!fileInput.files[0]) {
                Swal.fire({
                    title: 'File Tidak Dipilih',
                    text: 'Silakan pilih file AD/ART terlebih dahulu',
                    icon: 'warning'
                });
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Uploading...',
                text: 'Sedang mengupload file AD/ART...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('application-setting.upload-ad-art') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Reload page to show updated file
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengupload file',
                        icon: 'error'
                    });
                });
        });

        // Delete AD/ART function
        function deleteAdArt() {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus file AD/ART?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('application-setting.delete-ad-art') }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Reload page to show updated state
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message,
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghapus file',
                                icon: 'error'
                            });
                        });
                }
            });
        }
    </script>
@endpush
