@extends('layouts.master', ['title' => 'Detail Author', 'main' => 'Dashboard'])

@section('content')
<div class="app-main pt-6 flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <div class="card card-flush">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="fw-bold card-label">Detail Author</h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="{{ route('author.edit', $author->id) }}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                            <a href="{{ route('author.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row gy-4">
                            <div class="col-md-4 text-center">
                                @if($author->admin && $author->admin->avatar)
                                    <img src="{{ asset($author->admin->avatar) }}" class="rounded-circle" style="width:150px; height:150px; object-fit:cover;" alt="avatar">
                                @else
                                    <div class="symbol symbol-150px bg-light-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-2x">{{ strtoupper(Str::limit($author->admin->name ?? '', 2, '')) }}</div>
                                @endif
                                <div class="mt-3">
                                    <h4 class="m-0">{{ $author->admin->name }}</h4>
                                    <div class="text-muted">{{ $author->registration_number }}</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-label text-muted">Email</div>
                                        <div class="fw-bold">{{ $author->admin->email }}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-label text-muted">Status</div>
                                        <div class="fw-bold">{{ $author->admin->is_active ? 'Aktif' : 'Nonaktif' }}</div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-label text-muted">Institusi</div>
                                    <div class="fw-bold">{{ $author->institution }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-label text-muted">Telepon</div>
                                    <div class="fw-bold">{{ $author->phone }}</div>
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
