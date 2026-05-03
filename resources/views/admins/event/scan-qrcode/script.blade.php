@push('js')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js"></script>
<script>
        function table() {
            var table = $('#datatable').DataTable({
                ordering: true,
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                ajax: {
                    url: "{{ route('event-scan-qrcode.index') }}",
                    type: 'GET',
                    data: {
                        start_date: $("#start_date").val(),
                        end_date: $("#end_date").val(),
                        gym_place_id: $('#gym_place_id').val()
                    }
                },
                language: {
                    "paginate": {
                        "next": "<i class='fa fa-angle-right'>",
                        "previous": "<i class='fa fa-angle-left'>"
                    },
                    "loadingRecords": "Loading...",
                    "processing": "Processing...",
                },
                columns: [{
                        "data": null,
                        "sortable": false,
                        "searchable": false,
                        responsivePriority: -3,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'user.name',
                        name: 'user.name',
                        responsivePriority: -2
                    },
                    {
                        data: 'code_number',
                        name: 'code_number',
                        sortable: false,
                    },
                    {
                        data: 'event.name',
                        name: 'event.name',
                        sortable: false,
                    },
                    {
                        data: 'validated_at',
                        name: 'validated_at',
                    },
                    {
                        data: 'status',
                        name: 'status',
                        sortable: false,
                        searchable: false,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        responsivePriority: -1
                    },
                ]
            });
        }

        // read when do action cancel to refocus for form QR Code
        document.getElementById('cancel-confirm-event').addEventListener('click', refocusQrcode);

        // Select all elements with the class "btn-close"
        const closeButtons = document.querySelectorAll('.btn-close');

        // Add event listener to each close button
        closeButtons.forEach(closeButton => {
            closeButton.addEventListener('click', refocusQrcode);
        });

        function refocusQrcode() {
            // Get the "qrcode" input element
            var qrcodeInput = document.getElementById('qrcode');

            // Clear the value inside the input element
            qrcodeInput.value = '';

            // Refocus the "qrcode" input element
            qrcodeInput.focus();
        }

        function beep() {
            var snd = new Audio(
                "data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU="
            );
            snd.play();
        }

        const html5QrCode = new Html5Qrcode("qr-reader");
        const config = {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            }
        };

        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            beep();
            stopCamera()
            const code_number = `${decodedText}`;
            axios.post("{{ route('event-scan-qrcode.event-detail') }}", {
                    code_number: code_number
                })
                .then(function(response) {
                    if (response.data.success) {
                        toastr.success(response.data.message)
                        if (response.data.type == 'EVENT') {
                            showConfirmEvent(response.data.data)
                        }
                    } else {
                        toastr.error(response.data.message)
                        scanBackCamera()
                    }
                })
                .catch(function(error) {
                    console.log(error);
                    toastr.error(error.message)
                    scanBackCamera()
                });
        }

        function scanBackCamera() {
            html5QrCode.start({
                facingMode: "environment"
            }, config, qrCodeSuccessCallback);
        }

        function giftPermission() {
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    var cameraId = devices[0].id;
                    toastr.success('Permission kamera berhasil dapatkan')
                }
            }).catch(err => {
                toastr.error('Gagal meminta permission kamera')
            });
        }

        function stopCamera() {
            html5QrCode.stop().then((ignore) => {

            }).catch((err) => {

            });
        }

        function checkData(){
            axios.post("{{ route('event-scan-qrcode.event-detail') }}", {
                    code_number: $('#code_number').val()
                })
                .then(function(response) {
                    if (response.data.success) {
                        toastr.success(response.data.message)
                        if (response.data.type == 'EVENT') {
                            showConfirmEvent(response.data.data)
                        }
                    } else {
                        toastr.error(response.data.message)
                        scanBackCamera()
                    }
                })
                .catch(function(error) {
                    console.log(error);
                    toastr.error(error.message)
                    scanBackCamera()
                });
        }

        function showConfirmEvent(data) {
            let actionUrl = '{{ route('event-scan-qrcode.store') }}';
            $('#form-confirm-event').attr('action', actionUrl);

            if (data.user.avatar) { // Jika ada, tampilkan avatar pengguna
                $('#avatar_user').html(`<img src="${data.user.avatar}" class="img-fluid" style="width: 100px;">`);
            } else {
                // Jika tidak, tampilkan avatar default
                $('#avatar_user').html(
                    `<img src="/assets/media/avatars/blank.png" class="img-fluid" style="width: 100px;">`);
            }

            $('#username').text(data.user.name)
            $('#event_name').text(data.event.name)
            $('#date_event').text(data.event.start_date)
            $('#place_name').text(data.event.place_name)
            $('#code_number_signed').text(data.code_number)
            $('#payment_code').text(data.transaction_payment_code)
            $('#payment_date').text(data.transaction_created_at)
            $('#status').html(data.status_span)
            
            $('#event_ticket_order_id').val(data.id)
            $('#event_id').val(data.event.id)
            $('#code_number_value').val(data.code_number)

            if (data.status == "VALIDATED") {
                $('#submit-confirm-event').addClass('d-none');
            } else {
                $('#submit-confirm-event').removeClass('d-none');
            }


            let table = document.getElementById("table-ticket-group");
            let event_ticket_order_detail_groups = data.event_ticket_order_detail_groups;
            let html =
                "<tr>\
                    <th class='text-center'>No</th>\
                    <th class='text-center'>Nama Tiket</th>\
                    <th class='text-center'>Total Tiket</th>\
                </tr>";
            let i = 1;
            if (event_ticket_order_detail_groups.length > 0) {
                event_ticket_order_detail_groups.forEach(function(item) {
                    html += "<tr>";
                    html += "<td class='text-center' style='width: 5%'>" + (i++) + "</td>";
                    html += "<td class='text-center' style='width: 40%'>" + item.event_ticket.name + "</td>";
                    html += "<td>" + item.total_quantity + "</td>";
                    html += "</tr>";
                });
                table.innerHTML = html;
            } else {
                html += "<tr>";
                html += "<td colspan='3' class='text-center'>-- Tidak Ada Data Notes--</td>";
                html += "</tr>";
                table.innerHTML = html;
            }


            let table_j = document.getElementById("table-ticket-detail");
            let event_ticket_order_details = data.event_ticket_order_details;
            let html_j =
                "<tr>\
                    <th class='text-center'>No</th>\
                    <th class='text-center'>Nama Ticket</th>\
                    <th class='text-center'>Kode Ticket</th>\
                </tr>";
            let j = 1;
            if (event_ticket_order_details.length > 0) {
                event_ticket_order_details.forEach(function(item) {
                    html_j += "<tr>";
                    html_j += "<td class='text-center' style='width: 5%'>" + (j++) + "</td>";
                    html_j += "<td class='text-center' style='width: 40%'>" + item.event_ticket.name + "</td>";
                    html_j += "<td>" + item.code_number + "</td>";
                    html_j += "</tr>";
                });
                table_j.innerHTML = html_j;
            } else {
                html_j += "<tr>";
                html_j += "<td colspan='3' class='text-center'>-- Tidak Ada Data Notes--</td>";
                html_j += "</tr>";
                table_j.innerHTML = html_j;
            }

            $("#modal-confirm-event").modal("show")
        }

        function showDetail(id) {
            let action = "{{ route('event-scan-qrcode.show', ':id') }}".replace(':id', id);
            axios.get(action)
                .then(function(response) {
                    console.log(response.data);
                    showConfirmEvent(response.data.data)
                })
                .catch(function(error) {
                    console.error(error);
                    toastr.error(error.message)
                    refocusQrcode()
                });
        }
</script>
@endpush
