@extends('layouts.master', ['main' => 'Data Publisher', 'title' => 'Detail Publisher'])
@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class="app-container container-xxl pt-6">
        <div class="row g-7">
            <!--begin::Left Section-->
            <div class="col-xl-12">
                <div class="card mb-5">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3">Detail Publisher</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('publisher.edit', $publisher->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-2"></i>Edit
                            </a>
                            <a href="{{ route('publisher.index') }}" class="btn btn-secondary btn-sm ms-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                    <!--end::Card header-->

                    <!--begin::Card body-->
                    <div class="card-body pt-5">
                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs nav-line-tabs mb-5 border-bottom">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab_overview">
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-eye me-2"></i>Overview
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab_publisher_info">
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-building me-2"></i>Info Publisher
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab_dokumen">
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-file me-2"></i>Dokumen
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab_doi">
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-key me-2"></i>Konfigurasi DOI
                                    </span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="tab_content">
                            <!-- Tab: Overview -->
                            <div class="tab-pane fade show active" id="tab_overview">
                                <div class="row mb-5">
                                    <div class="col-md-3 text-center mb-4">
                                        @if ($publisher->admin->avatar)
                                            <img src="{{ $publisher->admin->avatar }}" alt="Avatar"
                                                class="img-fluid rounded object-fit-cover"
                                                style="max-height: 200px; max-width: 200px;" />
                                        @else
                                            <div class="symbol symbol-150px bg-light-primary">
                                                <span
                                                    class="symbol-label fs-1 fw-bold text-primary">{{ substr($publisher->admin->name, 0, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="fw-bold w-25">Nama Publisher</td>
                                                <td>: {{ $publisher->admin->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Kode</td>
                                                <td>: <span class="badge badge-light-primary">{{ $publisher->code }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Email</td>
                                                <td>: <a href="mailto:{{ $publisher->admin->email }}">{{ $publisher->admin->email }}</a></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Tipe</td>
                                                <td>: <span class="badge badge-light-info">{{ $publisher->type }}</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Status</td>
                                                <td>:
                                                    @if ($publisher->admin->is_active)
                                                        <span class="badge badge-light-success">Aktif</span>
                                                    @else
                                                        <span class="badge badge-light-danger">Nonaktif</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Publisher Info -->
                            <div class="tab-pane fade" id="tab_publisher_info">
                                <div class="row">
                                    <div class="col-md-6 mb-5">
                                        <h5 class="mb-3">Informasi Dasar</h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="fw-bold w-40">Alias</td>
                                                <td>: {{ $publisher->alias }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Tipe Publisher</td>
                                                <td>: {{ $publisher->type }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Website</td>
                                                <td>:
                                                    @if ($publisher->website)
                                                        <a href="{{ $publisher->website }}" target="_blank">{{ $publisher->website }}</a>
                                                    @else
                                                        <em class="text-muted">Tidak ada</em>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6 mb-5">
                                        <h5 class="mb-3">Informasi Kontak</h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="fw-bold w-40">Nama Kontak</td>
                                                <td>: {{ $publisher->contact_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Telepon/WA</td>
                                                <td>: <a href="tel:{{ $publisher->phone }}">{{ $publisher->phone }}</a></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <hr />

                                <h5 class="mb-3">Lokasi</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold text-muted">Alamat:</label>
                                        <p>{{ $publisher->address }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="fw-bold text-muted">Kota:</label>
                                        <p>{{ $publisher->city }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: Dokumen -->
                            <div class="tab-pane fade" id="tab_dokumen">
                                <div class="row">
                                    <div class="col-md-6 mb-5">
                                        <h5 class="mb-3">SK Kemenkumham</h5>
                                        @if ($publisher->sk_kemenkumham_link)
                                            <a href="{{ $publisher->sk_kemenkumham_link }}" target="_blank"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-external-link-alt me-2"></i>Buka Link
                                            </a>
                                        @else
                                            <p class="text-muted"><em>Tidak ada dokumen</em></p>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-5">
                                        <h5 class="mb-3">AKTA Notaris</h5>
                                        @if ($publisher->akta_notaris_link)
                                            <a href="{{ $publisher->akta_notaris_link }}" target="_blank"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-external-link-alt me-2"></i>Buka Link
                                            </a>
                                        @else
                                            <p class="text-muted"><em>Tidak ada dokumen</em></p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Tab: DOI Configuration -->
                            <div class="tab-pane fade" id="tab_doi">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Prefix DOI Utama</h5>
                                        <div class="bg-light p-3 rounded">
                                            <span class="badge badge-light-primary fs-6">{{ $publisher->prefix_doi }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if ($publisher->additional_prefixes && count($publisher->additional_prefixes) > 0)
                                    <hr class="my-5" />
                                    <h5 class="mb-3">Prefix DOI Tambahan</h5>
                                    <div class="row">
                                        @foreach ($publisher->additional_prefixes as $prefix)
                                            <div class="col-md-6 mb-3">
                                                <div class="bg-light p-3 rounded">
                                                    <span class="badge badge-light-secondary fs-6">{{ $prefix }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
            </div>
            <!--end::Left Section-->
        </div>
    </div>
    <!--end::Container-->
@endsection
