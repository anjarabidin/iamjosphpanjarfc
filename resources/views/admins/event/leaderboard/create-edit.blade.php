@extends('layouts.master', ['main' => 'Data Event Leaderboard', 'title' => 'Edit Event Leaderboard'])
@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-DZ6VrRg9dAeS5JYFFz9Q8kBJfZwN05AI+lTfjiN8PgnI22yEYDmdKz5uJ5RmtkIt9QBKjqufZjh3h7g5x1SAXQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                        <span class="card-label fw-bold fs-3">Edit Event Leaderboard {{ $event->name }}</span>
                    </h3>
                    <!--end::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-5">
                    <!--begin::Form-->
                    <x-alert.alert-validation />
                    <form id="shop-product" action="{{ route('event-leaderboard.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <x-form.put-method />

                        <input type="hidden" name="event_id" value="{{ $event->id }}">

                        <table class="table table-hover table-bordered align-middle text-center gy-5 gs-5">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col" style="min-width: 100px">Avatar User</th>
                                    <th scope="col" style="min-width: 170px">Nama User</th>
                                    <th scope="col" style="min-width: 170px">Kode Ticket</th>
                                    <th scope="col" style="min-width: 70px">Nomor Urutan Leaderboard</th>
                                </tr>
                            </thead>
                            <tbody class="table-light">
                                @foreach ($event_users as $index => $item)
                                @php
                                    $leaderboard = $event->eventLeaderboards->where('user_id', $item->id)->first();
                                    $number = null;
                                    if ($leaderboard) {
                                        $number = $leaderboard->order;
                                    }
                                @endphp
                                <input type="hidden" name="user_id[]" value="{{ $item->id }}">
                                <tr height=20px>
                                    <td>
                                        <img src="{{ $item->avatar }}" class="h-50px w-50px rounded-circle shadow-sm border" alt="User Avatar">
                                    </td>
                                    <td class="fw-bold">{{ $item->name }}</td>
                                    <td class="fw-bold">
                                        @foreach ($item->ticket as $ticket)
                                            <li>{{ $ticket }}</li>                                            
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <input type="number" name="order[]" value="{{ $number }}" class="text-center w-50 order-input" style="border: none" min="1" required>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div id="error-message-duplicate" class="alert alert-danger mt-3 d-none" role="alert">
                            Nomor urut tidak boleh sama. Silakan periksa kembali.
                        </div>
                        
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-5HzlTtVXhBzCNfqOqPO9FLPmyun8WEwlSAIDMhUXHs7LfxC/BpJsgvEecMRTe+do5Q1sIvMIbpU6A31IxfkqJg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderInputs = document.querySelectorAll('.order-input');
        let errorToast; // Toast instance to manage display

        orderInputs.forEach(input => {
            input.addEventListener('input', checkDuplicateOrders);
        });

        function checkDuplicateOrders() {
            const values = Array.from(orderInputs).map(input => input.value);
            const hasDuplicates = values.some((value, index) => values.indexOf(value) !== index && value !== "");

            if (hasDuplicates && !errorToast) {
                errorToast = toastr.error("Nomor urut Leaderboard tidak boleh sama. Silakan periksa kembali.");
            } else if (!hasDuplicates && errorToast) {
                toastr.clear(errorToast); // Clear the error toast when resolved
                errorToast = null;
            }
        }

        // Toastr configuration
        toastr.options = {
            "timeOut": "0",           // Disable auto-dismiss
            "extendedTimeOut": "0"     // Prevent dismissal on hover
        };
    });
</script>
@endpush
