@extends('layouts.master', ['main' => 'Data Tipe Ruang Pertemuan', 'title' => request()->routeIs('meeting-room-type.create') ? 'Tambah Tipe Ruang Pertemuan' : 'Edit Tipe Ruang Pertemuan'])
@section('content')
    <!--begin::Content wrapper-->
    <div class="d-flex pt-6 flex-column flex-column-fluid">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Basic info-->
                <div class="card mb-5 mb-xl-10">
                    <!--begin::Card header-->
                    <div class="card-header">
                        <!--begin::Card title-->
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">
                                {{ request()->routeIs('meeting-room-type.create')
                                    ? 'Tambah Tipe Ruang Pertemuan'
                                    : 'Edit
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            Tipe Ruang Pertemuan' }}
                            </h3>
                        </div>
                        <!--end::Card title-->
                    </div>
                    <!--begin::Card header-->
                    <!--begin::Content-->
                    <div id="kt_account_settings_profile_details" class="collapse show">
                        <!--begin::Form-->
                        <form class="form" method="POST" enctype="multipart/form-data"
                            action="{{ request()->routeIs('meeting-room-type.create') ? route('meeting-room-type.store') : route('meeting-room-type.update', @$meetingRoomType->id) }}">
                            @csrf
                            <x-form.put-method />
                            <x-alert.alert-validation />
                            <!--begin::Card body-->
                            <div class="card-body">

                                <!--begin::Input group-->
                                <div class="row mb-6">
                                    <!--begin::Label-->
                                    <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nama</label>
                                    <!--end::Label-->
                                    <!--begin::Col-->
                                    <div class="col-lg-12">
                                        <!--begin::Row-->
                                        <div class="row">
                                            <!--begin::Col-->
                                            <div class="col-lg-12 fv-row">
                                                <input type="text" name="name" id="name"
                                                    class="form-control mb-3 mb-lg-0"
                                                    placeholder="Nama Tipe Ruang Pertemuan Anda"
                                                    value="{{ @$meetingRoomType->name ?? old('name') }}" required />
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <div class="row mb-6">
                                    <!--begin::Col-->
                                    <div class="col-lg-12">
                                        <!--begin::Row-->
                                        <div class="row">
                                            <!--begin::Col-->
                                            <div class="col-lg-12 fv-row">
                                                <x-form.image-upload label="Icon Tipe Ruang Pertemuan" name="image"
                                                    :value="@$meetingRoomType->image ?? null" nullable='1' />
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                    </div>
                                    <!--end::Col-->
                                </div>
                            </div>
                            <!--end::Card body-->
                            <!--begin::Actions-->
                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <a href="{{ route('meeting-room-type.index') }}"
                                    class="btn btn-secondary btn-sm me-3">Batal</a>
                                <button type="submit" class="btn btn-sm btn-primary"
                                    id="kt_account_profile_details_submit">Simpan</button>
                            </div>
                            <!--end::Actions-->
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Basic info-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->
@endsection
