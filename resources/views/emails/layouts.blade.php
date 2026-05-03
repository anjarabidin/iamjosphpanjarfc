<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{env("APP_NAME")}} - {{$title}}</title>
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;300;400;500;600;700;800&display=swap");
            * {
                box-sizing: border-box;
            }
            body {
                width: 100%;
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
                font-family: "Plus Jakarta Sans", sans-serif !important;
                padding: 0;
                margin: 0;
            }
            .badge-warning span {
                padding: 4px 8px;
                background-color: rgb(255, 234, 194) !important;
                border-radius: 4px;
                border: 1px solid orange;
                color: orange;
                width: max-content;
                font-size: 11px;
            }
            .badge-success span {
                padding: 4px 8px;
                background-color:rgb(219 255 216) !important;
                border-radius: 4px;
                border: 1px solid #3dea97;
                color: #3dea97;
                width: max-content;
                font-size: 11px;
            }
            table.collapse_table {
                border-collapse: collapse;
            }
            .text_style {
                font-size: 13px;
                color: #333333;
            }
            table.table_border {
                position: relative;
            }
            table.table_border::before {
                position: absolute;
                content: "";
                width: 100%;
                height: 8px;
                background-color: #b18d41;
            }
        </style>
    </head>
    <body>

    @yield('content')

    {{-- @include('emails.components.footer') --}}
    @include('emails.components.script')
    </body>
</html>
