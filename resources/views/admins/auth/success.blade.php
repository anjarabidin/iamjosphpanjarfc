<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Email Terkirim! | {{ env('APP_NAME') }}</title>
<meta charset="utf-8" />
<meta name="description" content="{{ env('APP_NAME') }}">
<meta name="author" content="{{ env('APP_NAME') }}">
<meta name="robots" content="noindex, nofollow">

<!-- Open Graph Meta -->
<meta property="og:title" content="{{ env('APP_NAME') }}">
<meta property="og:site_name" content="{{ env('APP_NAME') }}">
<meta property="og:description" content="{{ env('APP_NAME') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="">
<meta property="og:image" content="">

<link rel="canonical" href="KSP" />
<link rel="shortcut icon" href="assets/media/logos/favicon.png" />
<!--begin::Fonts-->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
<style>
    .h-logo {
        height: 4.5rem !important;
        margin-bottom: 4rem;
        position: absolute;
        top: 3.5rem;
        left: 50%;
        transform: translateX(-50%);
    }
    #kt_app_root {
        min-height: 45rem !important;
        height: 100vh !important;
        max-height: 64rem !important;
    }
    .ilustration {
        width: 13rem;
        height: 13rem;
        margin-bottom: 1.5rem;
    }
    h1.title {
        font-size: 2rem;
        line-height: 3.125rem; /* 128.205% */
        letter-spacing: -0.00056rem;
        margin-bottom: 0.5rem;
    }
    p {
        font-size: 14px !important;
        font-weight: 500;
        line-height: 1.375rem; /* 157.143% */
        letter-spacing: -0.00006rem;
        color: #8C8C8C;
    }


    /* responsive */
    @media only screen and (max-width: 1199.98px) {
        h1.title {
            font-size: 2.125rem;
        }
        .ilustration {
            width: 12rem;
            height: 12rem;
        }
    }
    @media only screen and (max-width: 991.98px) {
        .h-logo {
            margin-bottom: 0;
        }
        .ilustration {
            width: 11rem;
            height: 11rem;
        }
        h1.title {
            font-size: 2rem;
            line-height: 3rem;
        }
        .input_form {
            max-width: 24rem;
        }
    }
    @media only screen and (max-width: 767.98px) {
        h1.title {
            font-size: 1.5rem;
            line-height: 2.5rem;
        }
        .ilustration {
            width: 10rem;
            height: 10rem;
        }
    }

</style>
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body" class="container app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center overflow-x-hidden">
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light"; 
        var themeMode; 
        if ( document.documentElement ) { 
            if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { 
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); 
            } else { 
                if ( localStorage.getItem("data-bs-theme") !== null ) { 
                    themeMode = localStorage.getItem("data-bs-theme"); 
                } else { themeMode = defaultThemeMode; } 
            } if (themeMode === "system") { 
                    themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; 
                } document.documentElement.setAttribute("data-bs-theme", themeMode); 
        }
    </script>
    <!--end::Theme mode setup on page load-->
    <!--begin::Root-->
    <img src="{{ asset('assets/media/logos/logo.svg') }}" class="h-logo" alt="">
    <div id="kt_app_root" class="container d-flex justify-content-center align-items-center flex-column">
        <!--begin::Page bg image-->
        <style>body { background-image: url('assets/media/auth/bg10.jpeg'); } [data-bs-theme="dark"] body { background-image: url('assets/media/auth/bg10-dark.jpeg'); }</style>
        <!--end::Page bg image-->
        <img src="{{ asset('assets/media/success_password.svg') }}" class="ilustration" alt="">
        <h1 class="title">Email Terkirim</h1>
        <p class="text-center">Silahkan cek emailmu untuk melakukan verifikasi</p>
    </div>
    <!--end::Root-->
    <!--begin::Javascript-->
    <script>var hostUrl = "assets/";</script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <!--end::Javascript-->
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
    <script>
        "use strict";

        // Class definition
        var KTSigninGeneral = function() {
            // Elements
            var form;
            var submitButton;
            var validator;

            // Handle form
            var handleForm = function(e) {
                validator = FormValidation.formValidation(
                    form, {
                        fields: {
                            'email': {
                                validators: {
                                    notEmpty: {
                                        message: 'Email harus diisi'
                                    },
                                    emailAddress: {
                                        message: 'Format email tidak sesuai'
                                    },

                                }
                            },
                            'password': {
                                validators: {
                                    notEmpty: {
                                        message: 'Password harus diisi'
                                    }
                                }
                            }
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap: new FormValidation.plugins.Bootstrap5({
                                rowSelector: '.fv-row'
                            })
                        }
                    }
                );

                // Handle form submit
                submitButton.addEventListener('click', function(e) {
                    // Prevent button default action
                    e.preventDefault();

                    // Validate form
                    validator.validate().then(function(status) {
                        if (status == 'Valid') {
                            // Show loading indication
                            submitButton.setAttribute('data-kt-indicator', 'on');

                            // Disable button to avoid multiple click
                            submitButton.disabled = true;
                            form.submit();
                        } else {
                            Swal.fire({
                                text: "Maaf, Inputan anda tidak valid, silahkan cek kembali",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });
                });
            }

            // Public functions
            return {
                // Initialization
                init: function() {
                    form = document.querySelector('#kt_sign_in_form');
                    submitButton = document.querySelector('#kt_sign_in_submit');

                    handleForm();
                }
            };
        }();

        // On document ready
        KTUtil.onDOMContentLoaded(function() {
            KTSigninGeneral.init();
        });
    </script>
</body>

</html>
