<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>QR Code {{ $data->name }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        /*@font-face {*/
        /*    font-family: "Inter";*/
        /*    src: url("{{ asset('/fonts/Inter.ttf') }}");*/
        /*    font-weight: 400;*/
        /*    font-style: normal;*/
        /*}*/

        /*@font-face {*/
        /*    font-family: "Inter";*/
        /*    src: url("{{ asset('fonts/Inter-SemiBold.ttf') }}");*/
        /*    font-weight: 600;*/
        /*    font-style: normal;*/
        /*}*/

        /*@font-face {*/
        /*    font-family: "Inter";*/
        /*    src: url("{{ asset('fonts/Inter-Bold.ttf') }}");*/
        /*    font-weight: 700;*/
        /*    font-style: normal;*/
        /*}*/

        /*@font-face {*/
        /*    font-family: "Inter";*/
        /*    src: url("{{ asset('fonts/Inter-ExtraBold.ttf') }}");*/
        /*    font-weight: 800;*/
        /*    font-style: normal;*/
        /*}*/

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif !important;
            background: url("{{ asset('assets/media/patterns/ID_CARD.png') }}");
            background-size: cover;
            color: black;
            text-align: center;
            background-repeat: no-repeat;
            background-position: center;
        }

        .container_code {
            width: 40rem;
            height: 40rem;
            position: relative;
            margin-top: 3rem !important;
            margin-bottom: 4rem !important;
            margin: auto;
            background-size: cover;
            background-position: center;
            border-radius: 32px;
            background-repeat: no-repeat;
        }

        .code {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .code img {
            background-color: white;
            border-radius: 12px;
            padding: 12px;
            width: 30rem;
        }

        h1.username {
            display: inline;
            border-radius: 8px;
            color: #141414;
            text-align: center;
            letter-spacing: -0.054px;
            margin-top: 3.5rem;
            font-size: 3rem !important;
            font-weight: 800 !important;
            width: max-content;
            text-transform: uppercase !important;
        }

        h2 {
            font-size: 2.5rem !important;
            font-weight: 600;
            text-transform: uppercase !important;
        }

        h2.fw-bold {
            font-weight: 700 !important;
        }

        .circle {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 50%;
            background-color: white;
        }

        .mt-2 {
            margin-top: 3rem;
        }

        .mt-5 {
            margin-top: 5rem;
        }

        table {
            margin: 0 auto;
        }

        table tr td {
            width: 100%;
            padding: 0 0.25rem;
        }
    </style>
</head>

<body>
    <div style="text-align: center; margin-top: auto">
        <div class="container_code">
            <div class="code">
                <img src="data:image/svg;base64,{!! base64_encode(
                    QrCode::format('png')->size(290)->generate($data->qr_code),
                ) !!}" />
            </div>
        </div>
        <h1 class="username mt-2">{{ $data?->personal_trainer_name }}</h1>
        <div class="mt-5">
            <h2 class="gym_class fw-bold">{{ $data->name }}</h2>
            <table style="vertical-align: middle;">
                <tr>
                    <td>
                        <h2>{{ $data->date }}</h2>
                    </td>
                    <td>
                        <div class="circle"></div>
                    </td>
                    <td>
                        <h2>{{ $data->start_time . '-' . $data->end_time }}</h2>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
