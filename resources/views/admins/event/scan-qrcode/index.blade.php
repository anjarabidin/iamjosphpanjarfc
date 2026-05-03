@extends('layouts.master', ['title' => 'Event Scan Qrcode', 'main' => 'Dashboard'])
@push('css')
<style>
    .modal-cs-lg {
        --bs-modal-width: 940px;
    }
</style>
@endpush
@section('content')
<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid pt-6">
    <!--begin::Content container-->
    <div id="kt_app_content_container" class="app-container container-xxl">
        <!--begin::Card-->
        <div class="card card-flush">
            <!--begin::Card header-->
            <div class="card-header mt-6">
                <!--begin::Card toolbar-->
                <div class="ms-auto">
                    <div class="row">
                        <div class="col-md-12 pt-2 text-center" style="border: 1px dashed;">
                            <div class="row d-flex justify-content-center">
                                <div class="col-auto">
                                    <div id="qr-reader" class="mx-auto text-center"></div>
                                </div>
                            </div>
                            <div class="row mb-3 mt-3 gap-2 gap-md-0 d-flex justify-content-center">
                                <div class="col-auto">
                                    <button class="btn btn-sm btn-primary"
                                        onclick="giftPermission()">Permission</button>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-sm btn-primary" onclick="stopCamera()">Stop
                                        Kamera</button>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-sm btn-primary" onclick="scanBackCamera()">Mulai
                                        Scan</button>
                                </div>
                            </div>
                            <div class="row mb-3 mt-3 gap-2 gap-md-0 d-flex mt-2">
                                <div class="col-auto">
                                    <input type="text" class="form-control" id="code_number" autofocus>
                                </div>

                                <div class="col-auto">
                                    <button class="btn btn-sm btn-success" onclick="checkData()">Proses</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Card toolbar-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="mb-2 d-flex align-items-center flex-wrap justify-content-between gap-3 border-0 pt-6">
                    <!--begin::Card title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">List Riwayat Scan Event</span>
                    </h3>
                    <!--end::Card title-->
                    <div class="d-flex flex-wrap gap-3">
                        <div class="me-4">
                            @if(Auth::user()->is_show_all_gymplace)
                            <select name="gym_place_id" id="gym_place_id" 
                                class="form-select w-170px"
                                data-control="select2" data-hide-search="true" data-dropdown-css-class="w-150px"
                                data-placeholder="Pilih Gym Place" data-kt-table-widget-4="filter_status" onchange="table()">
                                @foreach ($gym_places as $gym_place)
                                <option value="{{$gym_place->id}}">{{$gym_place->name}}</option>
                                @endforeach
                            </select>
                            @else
                            @php
                                $userGymPlace = Auth::user()->gym_place;
                            @endphp
                            <select name="gym_place_id" id="gym_place_id" class="form-select w-170px" disabled>
                                @if($userGymPlace)
                                    <option value="{{ $userGymPlace->id }}" selected>{{ $userGymPlace->name }}</option>
                                @else
                                    <option value="">Tidak ada Gym Place</option>
                                @endif
                            </select>
                            <input type="hidden" name="gym_place_id" value="{{ $userGymPlace->id ?? '' }}">
                            @endif
                        </div>
                        <div>
                            <x-form.date-range-filter />
                            <input type="text" id="start_date" hidden>
                            <input type="text" id="end_date" hidden>
                        </div>
                    </div>
                </div>
                <!--begin::Table-->
                <table id="datatable" class="table table-hover align-middle table-row-dashed">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                            <th style="width: 5%">No</th>
                            <th>Nama User</th>
                            <th>Nomor Tiket</th>
                            <th>Event</th>
                            <th>Waktu Validasi</th>
                            <th>Status</th>
                            <th class="text-center min-w-100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark fw-semibold"></tbody>
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

@include('admins.event.scan-qrcode.modal')

@endsection
@include('admins.event.scan-qrcode.script')
