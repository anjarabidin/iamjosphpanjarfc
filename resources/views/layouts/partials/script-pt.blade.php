<script src="{{ url('https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
{{-- <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js" type="text/javascript"></script> --}}
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js" type="text/javascript"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
<script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
{{-- <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script> --}}
<script src="{{ asset('assets\js\vendors\plugins\sweetalert2.init.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="{{ url('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js') }}"></script>
<script src="{{ url('https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<livewire:scripts />
<script>
    // default light mode
    const theme = sessionStorage.getItem('data-bs-theme')
    if(theme === 'dark') {
        sessionStorage.setItem('data-bs-theme', 'dark')
    } 

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on('click', '.btn-destroy', function(e) {
        var form = $("#" + e.target.dataset.id);
        Swal.fire({
            title: 'Hapus Data',
            text: 'Anda yakin akan menghapus data ini? Data yang telah dihapus tidak dapat dikembalikan.',
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-sm fw-semibold btn-primary',
                cancelButton: 'btn btn-sm fw-semibold btn-active-light-primary'
            }
        }).then((res) => {
            if (res.isConfirmed) {
                console.log(form);
                form.submit();
            } else {
                return false;
            }
        });
        return false;
    })
</script>
@foreach (['success', 'error', 'warning', 'info'] as $message)
@if (session($message))
<script>
    Swal.fire({
        title: '{{ ucfirst($message) }}',
        text: "<?= session($message) ?>",
        icon: '{{ $message }}',
        confirmButtonText: 'Ok'
    }) 
</script>
@endif
@endforeach
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>

<script>
    // Progressive Enhancement (SW supported)
    if (navigator.serviceWorker) {
        // Register the SW
        navigator.serviceWorker.register('/firebase-messaging-sw.js').then(function(registration){
        }).catch(console.log);
    }

    var firebaseConfig = {
        apiKey: "AIzaSyBwho1s6v2JthKjW-3CbJZ16LY1Vr95OIg",
        authDomain: "nest-gym.firebaseapp.com",
        databaseURL: "https://nest-gym-default-rtdb.asia-southeast1.firebasedatabase.app",
        projectId: "nest-gym",
        storageBucket: "nest-gym.appspot.com",
        messagingSenderId: "1080911476180",
        appId: "1:1080911476180:web:6ffea5585b8452073e5241",
        measurementId: "G-L1WR8QPYZQ"
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);
 
    const messaging = firebase.messaging();

    if(window.Notification) {
        if(Notification.permission === 'granted') {
        } else if(Notification.permission !== 'denied') {
            Notification.requestPermission(permission => {
            if(permission === 'granted') {
            }
            })
        }
    }

    const memberElement = (data) => {
        let total_unread = data.total_unread_pt > 0 ? `<button class="btn-status bg-gray text-blue unread mt-1 ms-4">${data.total_unread_pt}</button>` : ''
        return `<div data-room="${data.name}" class="member cursor-pointer mb-8">
                    <div class="d-flex w-100 align-items-center gap-4">
                        <div class="symbol symbol-45px symbol-md-50px symbol-circle"
                            data-bs-toggle="tooltip" title="${data.user?.name}">
                            <img alt="Profile Picture"
                                src="${data.user?.avatar ?? 'assets/media/avatars/blank.png'}"" />
                        </div>
                        <div class="d-flex flex-column justify-content-center w-100 overflow-hidden">
                            <div class="d-flex justify-content-between">
                                <h1 id="username" class="text-dark fs-7">${data.user?.name}</h1>
                                <p class="text-grey2 text-nowrap fw-400 mb-0 time">${data.time_last_message || ''}</p>
                            </div>
                            <div class="d-flex justify-content-between footer_message">
                                <p class="fw-400 message text-grey mb-0 mt-1">${data.last_message || 'Belum ada pesan'}</p>
                                ${total_unread}
                            </div>
                        </div>
                    </div>
                </div>`
    }

    function initFirebaseMessagingRegistration() {
        messaging.requestPermission().then(function () {
            return messaging.getToken()
        }).then(function(token) {
            axios.post("{{ route('fcmToken') }}",{
                _method:"PATCH",
                token
            }).then(({data})=>{
            }).catch(({response:{data}})=>{
                console.error(data)
            })
        }).catch(function (err) {
            console.log(`Token Error :: ${err}`);
        });
    }

    initFirebaseMessagingRegistration();

    messaging.onMessage(function ({data}) {
        const data_chat = JSON.parse(data.data)
        console.log(data_chat);
        const url = `/trainer/chat?user_id=${data_chat.user_id}`
        const title = data.title;
        const options = {
            body: data.body,
            icon: '/assets/media/logos/logo.webp'
        };
        
        if (!window.location.toString().includes("chat")) {
            let notification = new Notification(title, options);

            notification.onclick = function() {
                window.location.href = url;
            }
            setTimeout(notification.close.bind(notification), 7000);
        } else { 
            // update list member when received message
            const element = $(`.member[data-room='${data_chat.name}']`)
            if(element.length>0) { 
                const last_message = element.find('.message')
                const footer_message = element.find('.footer_message')
                const time = element.find('.time')
                const unread = footer_message.find('.unread')
    
                last_message.text(data_chat.last_message)
                time.text(data_chat.time_last_message)
    
                if(unread.length===1) {
                    unread.html(data_chat.total_unread_pt)
                } else {
                    const total = `<button class="btn-status bg-gray text-blue unread mt-1 ms-4">${data_chat.total_unread_pt}</button>`
                    footer_message.append(total)
                }

                // add member at top list member
                $('#wrap_people').prepend(element)
                $("#wrap_people").animate({ scrollTop: 0 }, "fast")
            } else {
                console.log('not found');
                const result = memberElement(data_chat)
                $('#wrap_people').prepend(result)
                $("#wrap_people").animate({ scrollTop: 0 }, "fast")
            }

        }
    });

   
</script>
<script>
    const translator = (input, output) => {
        if ($(input).val() != '') {
            axios.get('{{ route("translator.post") }}', {
                params: {
                    text: $(input).val(),
                }
            }).then(function(response) {
                $(output).val(response.data)
            })
        }
    }
</script>
@stack('js')
