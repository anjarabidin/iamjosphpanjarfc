@extends('layouts.master', [
    'main' => 'Data Role',
    'title' => request()->routeIs('role.create')
        ? 'Tambah Role'
        : 'Edit
Role',
])
@section('content')
    <!--begin::Content-->
    <div class="content pt-6 d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Container-->
        <div id="kt_content_container" class="app-container container-xxl">
            <!--begin::Contacts App- Add New Contact-->
            <div class="row g-7">
                <!--begin::Content-->
                <div class="col-xl-12">
                    <!--begin::Contacts-->
                    <div class="card h-lg-100" id="kt_contacts_main">
                        <!--begin::Card header-->
                        <div class="card-header">
                            <!--begin::Card title-->
                            <h3 class="card-title align-items-start flex-column">
                                <span
                                    class="card-label fw-bold fs-3">{{ request()->routeIs('role.create') ? 'Tambah Role' : 'Edit Role' }}</span>
                            </h3>
                            <!--end::Card title-->
                        </div>
                        <!--end::Card header-->
                        <x-alert.alert-validation />
                        <form class="form"
                            action="{{ request()->routeIs('role.create') ? route('role.store') : route('role.update', $role->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <x-form.put-method />
                            <!--begin::Card body-->
                            <div class="card-body">
                                <!--begin::Form-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold form-label" for="name">
                                        <span class="required text-dark">Nama Role</span>
                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                            title="Nama Role yang akan digunakan"></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ @$role->name ?? old('name') }}" />
                                    <!--end::Input-->
                                </div>
                                <div class="fv-row mb-6">
                                    <!--begin::Label-->
                                    <label class="fs-6 fw-bold form-label" for="select2">
                                        <span class="required text-dark">Permission</span>
                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                            title="Silahkan memilih akses yang diberikan"></i>
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <select name="permissions[]" class="form-select mb-3" id="select2"
                                        data-control="select2" data-allow-clear="true" multiple="multiple" required>
                                        @foreach ($permissions as $permission)
                                            <option value="{{ $permission->name }}"
                                                @if (in_array(@$permission->id, @$permissionValue)) selected @endif>
                                                {{ $permission->label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="d-flex gap-3">
                                        <input type="checkbox" id="select-all">
                                        <label style="font-size: 14px;" class="cursor-pointer" for="select-all">Select
                                            All</label>
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <!--end::Input group-->
                                {{-- <div class="fv-row mb-6">
                                <!--begin::Label-->
                                <label class="fs-6 fw-bold form-label" for="select2">
                                    <span class="text-dark">IJIN CRUD</span>
                                </label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <div class="row">
                                    <div class="col-auto gap-2">
                                        <input type="checkbox" {{ @$role->is_allowed_create ? 'checked' : '' }}
                                        id="is_allowed_create" name="is_allowed_create" value="1">
                                        <label for="is_allowed_create">Create</label>
                                    </div>
                                    <div class="col-auto gap-2">
                                        <input type="checkbox" {{ @$role->is_allowed_edit ? 'checked' : '' }}
                                        id="is_allowed_edit" name="is_allowed_edit" value="1">
                                        <label for="is_allowed_edit">Edit</label>
                                    </div>
                                    <div class="col-auto gap-2">
                                        <input type="checkbox" {{ @$role->is_allowed_delete ? 'checked' : '' }}
                                        id="is_allowed_delete" name="is_allowed_delete" value="1">
                                        <label for="is_allowed_delete">Delete</label>
                                    </div>
                                    <div class="col-auto gap-2">
                                        <input type="checkbox" {{ @$role->is_superadmin ? 'checked' : '' }}
                                        id="is_superadmin" name="is_superadmin" value="1">
                                        <label for="is_superadmin">Akses Superadmin</label>
                                    </div>
                                </div>
                                <!--end::Input-->
                            </div> --}}
                            </div>
                            <!--end::Card body-->
                            <!--begin::Action buttons-->
                            <div class="card-footer d-flex justify-content-end py-6 px-9">
                                <!--begin::Button-->
                                <a href="{{ route('role.index') }}">
                                    <button type="button" data-kt-contacts-type="cancel"
                                        class="btn btn-secondary me-3">Batal</button>
                                </a>
                                <!--end::Button-->
                                <!--begin::Button-->
                                <button type="submit" data-kt-contacts-type="submit" class="btn btn-primary btn-sm">
                                    <span class="indicator-label">Simpan</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <!--end::Button-->
                            </div>
                            <!--end::Action buttons-->
                        </form>
                        <!--end::Form-->
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
        $(".select2").select2();
        $(document).ready(function() {
            $("#select-all").click(function() {
                if ($("#select-all").is(':checked')) { //select all
                    $(".form-select").find('option').prop("selected", true);
                    $(".form-select").trigger('change');
                } else { //deselect all
                    $(".form-select").find('option').prop("selected", false);
                    $(".form-select").trigger('change');
                }
            });

            $('#select2').on('change', function() {
                let selected = $(this).val();
                if (selected == '') {
                    $("#select-all").prop('checked', false);
                }
            })
        })
    </script>
@endpush
