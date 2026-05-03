@extends('layouts.master', ['title' => 'Detail Term And Condition', 'main' => 'Term And Condition'])

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin="" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
@endpush

@section('content')
    <div class="content pt-6 d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <!--begin::Container-->
            <div id="kt_content_container" class="app-container container-xxl">
                <x-alert.alert-validation />
                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card body--> 
                    <div class="card-body">
                        <div class="d-flex gap-2 align-items-center mb-2">
                            <a href="{{ route('term-and-condition.index') }}" class="mt-1">
                                <span class="menu-icon back pt-1">
                                    <i class="ki-duotone ki-arrow-left">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </a>
                            <select class="form-select w-auto" style="min-width: 250px; max-width: 400px;" onchange="redirectTermAndCondition(this)">
                                @foreach($types as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $type ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!--end::Card body-->
                </div>
                <div class="mt-6">
                    <div class="card-body v2">
                        <div class="card pt-4 mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header border-0">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2>Term And Condition History</h2>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0 pb-5">
                                <div class="table-responsive">
                                    <table class="table table-hover table-row-bordered align-middle">
                                        <thead>
                                            <tr class="fw-bold fs-6 text-gray-800">
                                                <th>No</th>
                                                <th style="width: 15%">Tanggal Berlaku</th>
                                                <th style="width: 15%">Tanggal Berakhir</th>
                                                <th>Konten (ID)</th>
                                                <th>Content (EN)</th>
                                                <th>内容 (CN)</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($term_and_conditions as $term)
                                            <tr style="cursor: pointer">
                                                <td data-bs-toggle="modal" data-bs-target="#modal-term-{{ $term->id }}">{{ $loop->iteration }}</td>
                                                <td data-bs-toggle="modal" data-bs-target="#modal-term-{{ $term->id }}" style="width: 20%">{{ $term->valid_from ? date('d F Y H:i', strtotime($term->valid_from)) : '-' }}</td>
                                                <td data-bs-toggle="modal" data-bs-target="#modal-term-{{ $term->id }}" style="width: 20%">{{ $term->valid_until ? date('d F Y H:i', strtotime($term->valid_until)) : 'Sekarang' }}</td>
                                                <td data-bs-toggle="modal" data-bs-target="#modal-term-{{ $term->id }}"><?= Str::limit(strip_tags($term->content), 50); ?></td>
                                                <td data-bs-toggle="modal" data-bs-target="#modal-term-{{ $term->id }}"><?= Str::limit(strip_tags($term->content_en), 50); ?></td>
                                                <td data-bs-toggle="modal" data-bs-target="#modal-term-{{ $term->id }}"><?= Str::limit(strip_tags($term->content_cn), 50); ?></td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="#" class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="modal" data-bs-target="#modal-edit-{{ $term->id }}">
                                                            <i class="ki-duotone ki-notepad-edit fs-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </a>
                                                        @include('components.action.delete', [
                                                            'action' => route('term-and-condition.history.destroy', $term->id),
                                                            'id' => $term->id,
                                                            'class' => 'btn btn-sm btn-icon btn-light-danger'
                                                        ])
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                            <!-- Modal Detail -->
                                            <div class="modal fade" id="modal-term-{{ $term->id }}" tabindex="-1" aria-labelledby="modal-term-{{ $term->id }}-label" aria-hidden="true">
                                                <div class="modal-dialog" style="max-width: 90%;">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modal-term-{{ $term->id }}-label">Detail Konten</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <h5>Konten (ID)</h5>
                                                                    <div><?= $term->content; ?></div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <h5>Content (EN)</h5>
                                                                    <div><?= $term->content_en; ?></div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <h5>内容 (CN)</h5>
                                                                    <div><?= $term->content_cn; ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Edit -->
                                            <div class="modal fade" id="modal-edit-{{ $term->id }}" tabindex="-1" aria-labelledby="modal-edit-{{ $term->id }}-label" aria-hidden="true">
                                                <div class="modal-dialog" style="max-width: 90%;">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modal-edit-{{ $term->id }}-label">Edit Term and Condition</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('term-and-condition.history.update', $term->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="type" value="{{ $type }}">
                                                            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label>Tanggal Berlaku</label>
                                                                        <input type="datetime-local" name="valid_from" class="form-control" value="{{ $term->valid_from ? date('Y-m-d\TH:i', strtotime($term->valid_from)) : '' }}">
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label>Tanggal Berakhir</label>
                                                                        <input type="datetime-local" name="valid_until" class="form-control" value="{{ $term->valid_until ? date('Y-m-d\TH:i', strtotime($term->valid_until)) : '' }}">
                                                                    </div>
                                                                    <div class="row mb-6">
                                                                        <!--begin::Label-->
                                                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Konten Syarat dan Ketentuan</label>
                                                                        <!--end::Label-->
                                                                        <!--begin::Col-->
                                                                        <div class="col-lg-12">
                                                                            <!--begin::Row-->
                                                                            <div class="row">
                                                                                <!--begin::Col-->
                                                                                <div class="col-lg-12 fv-row">
                                                                                    <textarea id="content-{{ $term->id }}" name="content" class="form-control form-control-lg mb-3 mb-lg-0">{{ $term->content }}</textarea>
                                                                                </div>
                                                                                <!--end::Col-->
                                                                            </div>
                                                                            <!--end::Row-->
                                                                        </div>
                                                                        <!--end::Col-->
                                                                    </div>

                                                                    <div class="row mb-6">
                                                                        <!--begin::Label-->
                                                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Konten Syarat dan Ketentuan (English)</label>
                                                                        <!--end::Label-->
                                                                        <!--begin::Col-->
                                                                        <div class="col-lg-12">
                                                                            <!--begin::Row-->
                                                                            <div class="row">
                                                                                <!--begin::Col-->
                                                                                <div class="col-lg-10 fv-row">
                                                                                    <textarea id="content_en-{{ $term->id }}" name="content_en" class="form-control form-control-lg mb-3 mb-lg-0">{{ $term->content_en }}</textarea>
                                                                                </div>
                                                                                <div class="col-lg-2 fv-row">
                                                                                    <button type="button" onclick="translateDescriptionEnglish('{{ $term->id }}')" class="btn btn-translate">Translate English</button>
                                                                                </div>
                                                                                <!--end::Col-->
                                                                            </div>
                                                                            <!--end::Row-->
                                                                        </div>
                                                                        <!--end::Col-->
                                                                    </div>

                                                                    <div class="row mb-6">
                                                                        <!--begin::Label-->
                                                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Konten Syarat dan Ketentuan (Chinese)</label>
                                                                        <!--end::Label-->
                                                                        <!--begin::Col-->
                                                                        <div class="col-lg-12">
                                                                            <!--begin::Row-->
                                                                            <div class="row">
                                                                                <!--begin::Col-->
                                                                                <div class="col-lg-10 fv-row">
                                                                                    <textarea id="content_cn-{{ $term->id }}" name="content_cn" class="form-control form-control-lg mb-3 mb-lg-0">{{ $term->content_cn }}</textarea>
                                                                                </div>
                                                                                <div class="col-lg-2 fv-row">
                                                                                    <button type="button" onclick="translateDescriptionChinese('{{ $term->id }}')" class="btn btn-translate">Translate Chinese</button>
                                                                                </div>
                                                                                <!--end::Col-->
                                                                            </div>
                                                                            <!--end::Row-->
                                                                        </div>
                                                                        <!--end::Col-->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Wrapper-->
    </div>
@endsection
@push('js')
    <script>
        function redirectTermAndCondition(select) {
            var selectedType = select.value;
            // Redirect langsung ke halaman detail term and condition yang dipilih
            window.location.href = "{{ url('term-and-condition') }}/" + selectedType;
        }
    </script>
    <script src="{{ asset('assets/plugins/custom/tinymce/tinymce.bundle.js') }}"></script>
    <script>
        function translateDescriptionEnglish(id) {
            let content = tinymce.get('content-' + id).getContent();
            $.ajax({
                type: 'POST',
                url: "{{ route('translate_post') }}",
                data: { 
                    text: content,
                    _token: "{{ csrf_token() }}"
                },
                success: function(msg) {
                    tinymce.get('content_en-' + id).setContent(msg);
                    toastr.success('Terjemahan ke Bahasa Inggris berhasil!');
                },
                error: function(data) {
                    console.log('error:', data)
                    toastr.error('Gagal menerjemahkan ke Bahasa Inggris');
                },
            });
        }

        function translateDescriptionChinese(id) {
            let content = tinymce.get('content-' + id).getContent();
            $.ajax({
                type: 'POST',
                url: "{{ route('translate_post.chinese') }}",
                data: { 
                    text: content,
                    _token: "{{ csrf_token() }}"
                },
                success: function(msg) {
                    tinymce.get('content_cn-' + id).setContent(msg);
                    toastr.success('Terjemahan ke Bahasa Mandarin berhasil!');
                },
                error: function(data) {
                    console.log('error:', data)
                    toastr.error('Gagal menerjemahkan ke Bahasa Mandarin');
                },
            });
        }

        @foreach($term_and_conditions as $term)
        tinymce.init({
            selector: '#content-{{ $term->id }}, #content_en-{{ $term->id }}, #content_cn-{{ $term->id }}',
            height: 300,
            menubar: false,
            branding: false,
            plugins: 'lists link image table code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
            content_css: '//www.tiny.cloud/css/codepen.min.css'
        });
        @endforeach
    </script>
@endpush
