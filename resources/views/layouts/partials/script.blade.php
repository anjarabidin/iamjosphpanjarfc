<script src="{{ url('https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js" type="text/javascript"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
<script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
<script src="{{ asset('assets\js\vendors\plugins\sweetalert2.init.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="{{ url('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js') }}"></script>
<script src="{{ url('https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js') }}"></script>

<!--end::Global Javascript Bundle-->
<!--begin::Vendors Javascript(used for this page only)-->
<script src="{{ url('https://cdn.amcharts.com/lib/5/index.js') }}"></script>
<script src="{{ url('https://cdn.amcharts.com/lib/5/themes/Animated.js') }}"></script>
<script src="{{ url('https://cdn.amcharts.com/lib/5/xy.js') }}"></script>
<script src="{{ url('https://cdn.amcharts.com/lib/5/percent.js') }}"></script>
<script src="{{ url('https://cdn.amcharts.com/lib/5/radar.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<livewire:scripts />

<script>
    // default light mode
    const theme = sessionStorage.getItem('data-bs-theme')
    if (theme === 'dark') {
        sessionStorage.setItem('data-bs-theme', 'dark')
    }

    // Translate input title to title_en and description to description_en when input title and description
    const translate = (input, output) => {
        if ($(input).val() != '') {
            axios.get("{{ route('translate') }}", {
                params: {
                    text: $(input).val(),
                }
            }).then(function(response) {
                $(output).val(response.data)
            })
        }
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on('click', '.btn-delete', function(e) {
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

{{-- <script>
    const translator = (input, output) => {
        if ($(input).val() != '') {
            axios.get('{{ route('translator.post') }}', {
                params: {
                    text: $(input).val(),
                }
            }).then(function(response) {
                $(output).val(response.data)
            })
        }
    }
    const translateChinese = (input, output) => {
        if ($(input).val() != '') {
            axios.get("{{ route('translate.chinese') }}", {
                params: {
                    text: $(input).val(),
                }
            }).then(function(response) {
                $(output).val(response.data)
            })
        }
    }
    const translateChinesePost = (input, output) => {
        if ($(input).val() != '') {
            axios({
                method: "POST",
                url: "{{ route('translate_post.chinese') }}",
                data: {
                    text: $(input).val(),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            }).then(function(response) {
                $(output).val(response.data)
            })
        }
    }
    const translateChineseName = (input, output) => {
        if ($(input).val() != '') {
            axios.post("{{ route('translate_name.chinese') }}", {
                text: $(input).val(),
            }, {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).then(function(response) {
                $(output).val(response.data);
            }).catch(function(error) {
                console.error("Translation error:", error);
            });
        }
    }
</script> --}}
@stack('js')
