
<form id="form-confirm-event" method="post">
    @csrf
    @method('POST')
    <div class="modal fade" id="modal-confirm-event" tabindex="-1" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-cs-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Informasi Tiket Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="event_ticket_order_id" id="event_ticket_order_id">
                    <input type="hidden" name="event_id" id="event_id">
                    <input type="hidden" name="code_number" id="code_number_value">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>User Event Detail</h5>
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr>
                                        <td>Avatar</td>
                                        <td id="avatar_user"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama User</td>
                                        <td id="username"></td>
                                    </tr>
                                    <tr>
                                        <td>Event</td>
                                        <td id="event_name"></td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Event</td>
                                        <td id="date_event"></td>
                                    </tr>
                                    <tr>
                                        <td>Tempat Event</td>
                                        <th id="place_name"></th>
                                    </tr>                                
                                    <tr>
                                        <td>Kode Scan Qrcode</td>
                                        <td id="code_number_signed"></td>
                                    </tr>
                                    <tr>
                                        <td>Kode Pembayaran</td>
                                        <td id="payment_code"></td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Pembelian</td>
                                        <td id="payment_date"></td>
                                    </tr>
                                    <tr>
                                        <td>Status Tiket</td>
                                        <td id="status"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <h5>Jenis Tiket</h5>
                                <table class="table table-sm table-striped table-bordered w-100" id="table-ticket-group">
                                </table>
                            </div>
                            <div class="mb-2">
                                <h5>Tiket Detail</h5>
                                <table class="table table-sm table-striped table-bordered" id="table-ticket-detail" width="100%">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-batal" data-bs-dismiss="modal"
                        id="cancel-confirm-event">Batal</button>
                    <button type="submit" class="btn btn-sm d-flex gap-2 align-items-center btn-import" id="submit-confirm-event">
                        Konfirmasi Validasi Tiket</button>
                </div>
            </div>
        </div>
    </div>
</form>
