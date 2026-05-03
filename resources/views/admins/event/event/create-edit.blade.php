@extends('layouts.master', ['main' => 'Data Event', 'title' => request()->routeIs('event.create') ? 'Tambah Event' : 'Edit Event'])
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>

<!-- Load Esri Leaflet from CDN -->
<script src="https://unpkg.com/esri-leaflet@3.0.0/dist/esri-leaflet.js"></script>
<script src="https://unpkg.com/esri-leaflet-vector@3.0.0/dist/esri-leaflet-vector.js"></script>


<!-- Load Esri Leaflet Geocoder from CDN -->
<link rel="stylesheet" href="https://unpkg.com/esri-leaflet-geocoder@3.0.0/dist/esri-leaflet-geocoder.css">
<script src="https://unpkg.com/esri-leaflet-geocoder@3.0.0/dist/esri-leaflet-geocoder.js"></script>


<style type="text/css">
    .pointer {
        position: absolute;
        top: 90px;
        right: 60px;
        z-index: 99999;
        display: none;
    }
</style>
@endpush
@section('content')
<!--begin::Container-->
<div id="kt_content_container" class="app-container container-xxl pt-6">
    <!--begin::Contacts App- Add New Contact-->
    <div class="row g-7">
        <!--begin::Content-->
        <div class="col-xl-12">
            <!--begin::Contacts-->
            <div class="card h-lg-100" id="kt_contacts_main">
                <!--begin::Card header-->
                <div class="card-header" id="kt_chat_contacts_header">
                    <!--begin::Card title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3">{{ request()->routeIs('event.create') ? 'Tambah Event' : 'Edit Event' }}</span>
                    </h3>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-5">
                    <!--begin::Form-->
                    <x-alert.alert-validation />
                    <form id="shop-product"
                        action="{{ request()->routeIs('event.create') ? route('event.store') : route('event.update', @$event->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <x-form.put-method />

                        <div class="form-group">
                            <x-form.image-upload label="Image Event" name="image" :value="@$event->image" />
                        </div>

                        <div class="fv-row mb-6">
                            <!--begin::Label-->
                            <label class="fs-6 fw-bold form-label" for="name">
                                <span class="required text-dark">Nama Event</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Masukkan Nama Event"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control" id="name" placeholder="Contoh: Nest Gym Marathon Day"
                                name="name" value="{{ @$event->name ?? old('name') }}" required />
                            <!--end::Input-->
                        </div>

                        <div class="fv-row mb-6">
                            <!--begin::Label-->
                            <label class="fs-6 fw-bold form-label" for="name_en">
                                <span class="required text-dark">Nama Event (English)</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Masukkan Nama Event (English)"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div class="row">
                                <div class="col-10">
                                    <input type="text" class="form-control" id="name_en" placeholder=""
                                        name="name_en" value="{{ @$event->name_en ?? old('name_en') }}" />
                                </div>
                                <div class="col-2">
                                    <button type="button" onclick="translateNameEnglish()" class="btn btn-translate">Translate English</button>
                                </div>
                            </div>
                            <!--end::Input-->
                        </div>

                        <div class="fv-row mb-6">
                            <!--begin::Label-->
                            <label class="fs-6 fw-bold form-label" for="name_cn">
                                <span class="required text-dark">Nama Event (Chinese)</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Masukkan Nama Event (Chinese)"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div class="row">
                                <div class="col-10">
                                    <input type="text" class="form-control" id="name_cn" placeholder=""
                                    name="name_cn" value="{{ @$event->name_cn ?? old('name_cn') }}" />
                                </div>
                                <div class="col-2">
                                    <button type="button" onclick="translateNameChinese()" class="btn btn-translate">Translate Chinese</button>
                                </div>
                            </div>
                            <!--end::Input-->
                        </div>

                        <div class="fv-row row mb-6 mt-3">
                            <div class="col-4">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold form-label" for="start_event_date">
                                    <span class="text-dark required">Tanggal Event</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Tanggal Tanggal Event"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="date" class="form-control" id="start_event_date"
                                    name="start_event_date"
                                    value="{{ @$event->start_date ?? old('start_event_date') }}" />
                                <!--end::Input-->
                            </div>
                            <div class="col-4">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold form-label" for="start_time">
                                    <span class="text-dark required">Waktu Event Mulai</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Tanggal Waktu Event Mulai"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="time" class="form-control" id="start_time"
                                    name="start_time"
                                    value="{{ @$event->start_time ?? old('start_time') }}" />
                                <!--end::Input-->
                            </div>
                            <div class="col-4">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold form-label" for="end_time">
                                    <span class="text-dark required">Waktu Event Selesai</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Tanggal Waktu Event Selesai"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="time" class="form-control" id="end_time"
                                    name="end_time"
                                    value="{{ @$event->end_time ?? old('end_time') }}" />
                                <!--end::Input-->
                            </div>
                        </div>

                        <div class="fv-row mb-6">
                            <!--begin::Label-->
                            <label class="required fw-semibold fs-6">Nama Tempat Event/ Alamat</label>
                            <!--end::Label-->
                            <input type="text" class="form-control" name="place_name" value="{{ old('place_name', @$event->place_name) }}" required>
                            <input type="text" id="latitude" value="{{ @$event->latitude }}" name="latitude" hidden>
                            <input type="text" id="longitude" value="{{ @$event->longitude }}" name="longitude" hidden>
                        </div>
                        <div id="map" class="mb-6" style="height: 400px"></div>

                        <div class="fv-row mb-6">
                            <!--begin::Label-->
                            <label class="fs-6 fw-bold form-label" for="description">
                                <span class="required text-dark">Deskripsi Event</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Masukkan Deskripsi Event"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea class="form-control" id="description" placeholder=""
                                rows="6"
                                name="description">{{ @$event->description ?? old('description') }}</textarea>
                            <!--end::Input-->
                        </div>

                        <div class="fv-row mb-6">
                            <!--begin::Label-->
                            <label class="fs-6 fw-bold form-label" for="description_en">
                                <span class="required text-dark">Deskripsi Event (English)</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Masukkan Deskripsi Event dalam Bahasa Inggris"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div class="row">
                                 <div class="col-10">
                                    <textarea class="form-control" id="description_en"
                                        placeholder="" rows="6"
                                        name="description_en">{{ @$event->description_en ?? old('description_en') }}</textarea>
                                </div>
                                <div class="col-2">
                                    <button type="button" onclick="translateDescriptionEnglish()" class="btn btn-translate">Translate English</button>
                                </div>
                            </div>
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-6">
                            <!--begin::Label-->
                            <label class="fs-6 fw-bold form-label" for="description_en">
                                <span class="required text-dark">Deskripsi Event (Chinese)</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Masukkan Deskripsi Event dalam Bahasa China"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div class="row">
                                <div class="col-10">
                                    <textarea class="form-control" id="description_cn"
                                        placeholder="" rows="6"
                                        name="description_cn">{{ @$event->description_cn ?? old('description_cn') }}</textarea>
                                </div>
                                <div class="col-2">
                                    <button type="button" onclick="translateDescriptionChinese()" class="btn btn-translate">Translate Chinese</button>
                                </div>
                            </div>
                            <!--end::Input-->
                        </div>

                        <div class="fv-row mb-6">
                            <label class="fs-6 fw-bold form-label" for="max_use">
                                <span class="text-dark required">Max. Penggunaan / User</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Masukkan Max. Penggunaan / User"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control" id="max_use"
                                name="max_use"
                                value="{{ @$event->max_use ?? old('max_use', 1) }}" required/>
                        </div>

                        <div class="fv-row mb-6">
                            <!--begin::Label-->
                            <label class="fs-6 fw-bold form-label mt-3" for="is_active">
                                <span class="required">Status Aktif</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Pilih status Aktif"></i>
                            </label>
                            <!--end::Label-->
                            <select name="is_active" id="is_active" class="form-control" required>
                                <option value="">--Pilih Status--</option>
                                @if (request()->routeIs('event.create'))
                                    <option value="1" selected>Aktif</option>
                                    <option value="0">Non Aktif</option>
                                @else
                                    <option {{@$event->is_active == 1 ? 'selected' : ''}} value="1">Aktif</option>
                                    <option {{@$event->is_active == 0 ? 'selected' : ''}} value="0">Non Aktif</option>
                                @endif
                            </select>
                        </div>
                        <div class="fv-row mb-6">
                            <!--begin::Label-->
                            <label class="fs-6 fw-bold form-label mt-3" for="is_publish">
                                <span class="required">Status Publish</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Pilih status Publish"></i>
                            </label>
                            <!--end::Label-->
                            <select name="is_publish" id="is_publish" class="form-control" required>
                                <option value="">--Pilih Status--</option>
                                @if (request()->routeIs('event.create'))
                                    <option value="1" selected>Publish</option>
                                    <option value="0">Non Publish</option>
                                @else
                                    <option {{@$event->is_publish == 1 ? 'selected' : ''}} value="1">Publish</option>
                                    <option {{@$event->is_publish == 0 ? 'selected' : ''}} value="0">Non Publish</option>
                                @endif
                            </select>
                        </div>

                        <div class="fv-row mb-6">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="button" class="btn btn-primary btn-sm" id="add_tiket">
                                        <i class="fa fa-plus text-white"></i>Tiket
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if (request()->routeIs('event.create'))
                        <div class="">
                            <div class="card-header" id="kt_chat_contacts_header">
                                <!--begin::Card title-->
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3">Kategori Tiket</span>
                                </h3>
                                <!--end::Card title-->
                            </div>
                            <div class="card-body">
                                <div class="row event-ticket">
                                    <div class="col-lg-12">
                                        <div class="fv-row mb-6">
                                            <label class="fs-6 fw-bold form-label" for="ticket_name">
                                                <span class="text-dark">Tiket</span>
                                            </label>
                                            <input type="text" class="form-control" id="ticket_name"
                                                placeholder="Contoh: Half Marathon" name="ticket_name[]"
                                                value="" required/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="fv-row mb-6">
                                            <label class="fs-6 fw-bold form-label" for="ticket_quota">
                                                <span class="text-dark">Kuota Tiket</span>
                                            </label>
                                            <input type="text" class="form-control" id="ticket_quota"
                                                placeholder="Contoh: 30" name="ticket_quota[]"
                                                value="" required/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="fv-row mb-6">
                                            <label class="fs-6 fw-bold form-label" for="price_ticket">
                                                <span class="text-dark">Harga Tiket</span>
                                            </label>
                                            <input type="text" name="price_ticket[]" value="" class="form-control input-money" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="fv-row mb-6">
                                            <label class="fs-6 fw-bold form-label" for="start_date">
                                                <span class="text-dark">Tanggal Mulai Tiket</span>
                                            </label>
                                            <input type="datetime-local" class="form-control" name="start_date[]" value="" required/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="fv-row mb-6">
                                            <label class="fs-6 fw-bold form-label" for="end_date">
                                                <span class="text-dark">Tanggal Selesai Tiket</span>
                                            </label>
                                            <input type="datetime-local" class="form-control" name="end_date[]" value="" required/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row mb-6" id="event-ticket-form">
                            @if (isset($event->eventTickets))
                            @foreach ($event->eventTickets as $key => $ticket)
                            <div class="event-ticket">
                                <div class="card-header" id="kt_chat_contacts_header">
                                    <!--begin::Card title-->
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-3">Kategori Tiket</span>
                                    </h3>
                                    <!--end::Card title-->
                                </div>
                                <div class="card-body">
                                    <div class="event-ticket">
                                        <div class="row">
                                            <input type="hidden" name="ticket_id[]" value="{{ $ticket->id }}">
                                            @if ($key == 0)
                                            <div class="col-lg-12">
                                                <div class="fv-row mb-6">
                                                    <label class="fs-6 fw-bold form-label" for="ticket_name">
                                                        <span class="text-dark">Tiket</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="ticket_name"
                                                        placeholder="Contoh: Half Marathon" name="ticket_name[]"
                                                        value="{{ $ticket->name }}" />
                                                </div>
                                            </div>
                                            @else
                                            <div class="col-lg-12 row">
                                                <div class="fv-row col-9 mb-6">
                                                    <label class="fs-6 fw-bold form-label" for="ticket_name">
                                                        <span class="text-dark">Tiket</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="ticket_name"
                                                        placeholder="Contoh: Half Marathon" name="ticket_name[]"
                                                        value="{{ $ticket->name }}" />
                                                </div>
                                                <div class="fv-row col-3 mb-6">
                                                    <button type="button" class="btn btn-danger btn-sm mt-10" onclick="deleteEventTicketForm(this)">
                                                        <i class="fa fa-trash text-white"></i> Hapus Tiket
                                                    </button>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-lg-6">
                                                <div class="fv-row mb-6">
                                                    <label class="fs-6 fw-bold form-label" for="ticket_quota">
                                                        <span class="text-dark">Kuota Tiket</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="ticket_quota"
                                                        placeholder="Contoh: 30" name="ticket_quota[]"
                                                        value="{{ $ticket->quota }}" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="fv-row mb-6">
                                                    <label class="fs-6 fw-bold form-label" for="price_ticket">
                                                        <span class="text-dark">Harga Tiket</span>
                                                    </label>
                                                    <input type="text" name="price_ticket[]" value="{{ $ticket->price }}" class="form-control input-money">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="fv-row mb-6">
                                                    <label class="fs-6 fw-bold form-label" for="start_date">
                                                        <span class="text-dark">Tanggal Mulai Tiket</span>
                                                    </label>
                                                    <input type="datetime-local" class="form-control" name="start_date[]" value="{{ $ticket->start_date }}" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="fv-row mb-6">
                                                    <label class="fs-6 fw-bold form-label" for="end_date">
                                                        <span class="text-dark">Tanggal Selesai Tiket</span>
                                                    </label>
                                                    <input type="datetime-local" class="form-control" name="end_date[]" value="{{ $ticket->end_date }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>

                        @if (request()->routeIs('event.create'))
                        <div class="form-check form-switch mb-6">
                            <input class="form-check-input" type="checkbox" id="is_broadcast" name="is_broadcast" onchange="broadcastEvent(this.checked)" >
                            <label class="fs-6 fw-bold form-label" for="is_broadcast">
                                <span class="">Broadcast Event ke Semua Member </span>
                            </label>
                        </div>

                        <div id="broadcast-event-form" style="display: none">
                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold form-label" for="broadcast_title">
                                    <span class="required text-dark">Judul</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Judul"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" class="form-control" id="broadcast_title" placeholder="Nama Broadcast"
                                    name="broadcast_title" value="{{ @$event->broadcast_title ?? old('broadcast_title') }}" />
                                <!--end::Input-->
                            </div>

                            <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label for="name" class="fs-6 fw-bold form-label mt-3" for="broadcast_description">
                                    <span class="required text-dark">Isi</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Masukkan Isi"></i>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <textarea class="form-control" id="broadcast_description" name="broadcast_description" rows="4" placeholder="Masukkan Isi Broadcast">
                                    {{ @$event->broadcast_description ?? old('broadcast_description') }}</textarea>
                                <!--end::Input-->
                            </div>
                        </div>
                        @endif
                        <!--begin::Separator-->

                        <!--end::Separator-->
                        <!--begin::Action buttons-->
                        <div class="d-flex justify-content-end">
                            <!--begin::Button-->
                            <a href="{{ route('event.index') }}">
                                <button type="button" data-kt-contacts-type="cancel"
                                    class="btn btn-secondary me-3">Batal</button>
                            </a>
                            <!--end::Button-->
                            <!--begin::Button-->
                            <button type="submit" data-kt-contacts-type="submit" class="btn btn-primary btn-sm"
                                id="btn-submit">
                                <span class="indicator-label">Simpan</span>
                                <span class="indicator-progress">Mohon Tunggu...
                                    <span
                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                            <!--end::Button-->
                        </div>
                        <!--end::Action buttons-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Contacts-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Contacts App- Add New Contact-->
</div>
<!--end::Container-->
</div>
<!--end::Content-->
<!--end::Wrapper-->
@endsection
@push('js')
<script>
    function translateNameEnglish() {
        translate('#name', '#name_en');
    }
    
    function translateNameChinese() {
        translateChinese('#name', '#name_cn');
    }

    function translateDescriptionEnglish() {
        translator('#description', '#description_en');
    }
    
    function translateDescriptionChinese() {
        translateChinesePost('#description', '#description_cn');
    }

    function broadcastEvent(value) {
        if (value == 1) {
            $('#broadcast-event-form').show();
            $('#broadcast_title').attr('required', true);
            $('#broadcast_description').attr('required', true);
        } else {
            $('#broadcast-event-form').hide();
            $('#broadcast_title').removeAttr('required');
            $('#broadcast_description').removeAttr('required');
        }
    }

    $(".input-money").on('keyup', function() {
        var n = parseInt($(this).val().replace(/\D/g, ''), 10) || 0
        if (n > 0) {
            // var value = n.toLocaleString()
            // $(this).val(value);
            var value = n.toLocaleString('en-US')
            $(this).val(value.replace(/\./g, ','));
        } else {
            $(this).val(0);
        }
    });


    $('form').on('submit', function(e) {
        if ($('#latitude').val() === '' || $('#longitude').val() === '') {
            e.preventDefault();
            alert('Silahkan pilih titik map terlebih duhulu');
        }
        $(".input-money").each(function() {
            var str = $(this).val();
            var newValue = str.replace(/,/g, '');
            $(this).val(newValue);
        });
    });

    // Function to handle translation
    function handleTranslation(selector, targetSelector) {
        $(selector).on('change', () => translate(selector, targetSelector));
    }

    function addEventTicketForm() {
        $('#event-ticket-form').append(`
            <div class="event-ticket">
                <div class="card-header" id="kt_chat_contacts_header">
                    <!--begin::Card title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3">Kategori Tiket</span>
                    </h3>
                    <!--end::Card title-->
                </div>
                <div class="card-body">
                    <div class="row">
                        <input type="hidden" name="ticket_id[]" value="">
                        <div class="col-lg-12 row">
                            <div class="fv-row col-9 mb-6">
                                <label class="fs-6 fw-bold form-label" for="ticket_name">
                                    <span class="text-dark">Tiket</span>
                                </label>
                                <input type="text" class="form-control" id="ticket_name"
                                    placeholder="Contoh: Half Marathon" name="ticket_name[]"
                                    value="" />
                            </div>
                            <div class="fv-row col-3 mb-6">
                                <button type="button" class="btn btn-danger btn-sm mt-10" onclick="deleteEventTicketForm(this)">
                                    <i class="fa fa-trash text-white"></i> Hapus Tiket
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="fv-row mb-6">
                                <label class="fs-6 fw-bold form-label" for="ticket_quota">
                                    <span class="text-dark">Kuota Tiket</span>
                                </label>
                                <input type="text" class="form-control" id="ticket_quota"
                                    placeholder="Contoh: 30" name="ticket_quota[]"
                                    value="" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="fv-row mb-6">
                                <label class="fs-6 fw-bold form-label" for="price_ticket">
                                    <span class="text-dark">Harga Tiket</span>
                                </label>
                                <input type="text" name="price_ticket[]" value="" class="form-control input-money" >
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="fv-row mb-6">
                                <label class="fs-6 fw-bold form-label" for="start_date">
                                    <span class="text-dark">Tanggal Mulai Tiket</span>
                                </label>
                                <input type="datetime-local" class="form-control" name="start_date[]" value="" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="fv-row mb-6">
                                <label class="fs-6 fw-bold form-label" for="end_date">
                                    <span class="text-dark">Tanggal Selesai Tiket</span>
                                </label>
                                <input type="datetime-local" class="form-control" name="end_date[]" value="" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    function deleteEventTicketForm(element) {
        $(element).closest('.event-ticket').remove();
    }

    // Event handlers
    $(document).ready(function() {
        $('#add_tiket').on('click', addEventTicketForm);
        $('#delete_variant').on('click', deleteLastEventTicketForm);
    });
</script>

<script text="text/javascript">
    // MAP
    let latitude = "{{ $event->latitude ?? '-6.175389999999936' }}";
    let longitude = "{{ $event->longitude ?? '106.82704000000007' }}";
    function validationLocation() {
        var form_lng = $('#latitude').val();
        if (form_lng.length < 3) {
            Swal.fire('Gagal menyimpan', 'Silakan Melengkapi Titik Lokasi Kantor Terlebih Dahulu', 'info');
        }
    }

    const apiKey = "AAPKb22a3f2a79c44e7faf92f7c2175410835pyhwZf9KZfY4WtfUz8bLzwmHltYsHcsY2QYuJz_JPvBfKeddWZmRc1Ecfmo4DeT";
    const basemapEnum = "ArcGIS:Navigation";
    const map = L.map("map", {
        minZoom: 2

    }).setView([latitude, longitude], 14);

    L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
        maxZoom: 18
    }).addTo(map);

    const searchControl = L.esri.Geocoding.geosearch({
        position: "topright",
        placeholder: "Cari Lokasi anda",
        useMapBounds: false,
        providers: [L.esri.Geocoding.arcgisOnlineProvider({
            apikey: apiKey,
            nearby: {
                lat: latitude,
                lng: longitude
            },
        })]
    }).addTo(map);

    var marker = L.marker([latitude, longitude], {
        draggable: true
    }).addTo(map);
    const results = L.layerGroup().addTo(map);

    searchControl.on("results", (data) => {
        map.removeLayer(marker)
        results.clearLayers();
        for (let i = data.results.length - 1; i >= 0; i--) {
            const lngLatString = `${Math.round(data.results[i].latlng.lng * 100000)/100000}, ${Math.round(data.results[i].latlng.lat * 100000)/100000}`;
            const marker = L.marker(data.results[i].latlng, {
                draggable: true
            });
            marker.bindPopup(`<b>${lngLatString}</b><p>${data.results[i].properties.LongLabel}</p>`)
            results.addLayer(marker);
            marker.openPopup();

            marker.on('dragend', function(e) {
                document.getElementById('latitude').value = e.target._latlng.lat;
                document.getElementById('longitude').value = e.target._latlng.lng;
            });


            document.getElementById('latitude').value = data.results[i].latlng.lat;
            document.getElementById('longitude').value = data.results[i].latlng.lng;
        }
    });

    marker.on('dragend', function(e) {

        document.getElementById('latitude').value = e.target._latlng.lat;
        document.getElementById('longitude').value = e.target._latlng.lng;
    });

    $(document).ready(function() {

        $('.pointer').show();

        setTimeout(function() {
            $('.pointer').fadeOut('slow');
        }, 3400);

    });
</script>
@endpush
