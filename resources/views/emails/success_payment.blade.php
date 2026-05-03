@extends('emails.layouts', ['title' => 'Pembayaran Berhasil'])
@section('content')
    <div style="background-color: #f0f6ff">
        {{-- header --}}
        <table class="collapse_table" width="100%" style="border-spacing: 0px; background-color: #ffffff">
            <tr style="border-collapse: collapse">
                <td>
                    <!-- header -->
                    <table align="center" cellspacing="0" cellpadding="0" style="width: 100%">
                        <tr>
                            <td align="center" bgcolor="#F0F6FF" style="padding: 0; margin: 0; background-color: #f0f6ff">
                                <table class="es-header-body" align="center" cellspacing="0" cellpadding="0">
                                    <tr style="border-collapse: collapse">
                                        <td
                                            style="margin: 0; padding-bottom: 0; padding-left: 20px; padding-right: 20px; padding-top: 0">
                                            <table style="border-collapse: collapse; border-spacing: 0px">
                                                <tr>
                                                    <td align="center" valign="top"
                                                        style="padding: 0; margin: 0; width: 560px">
                                                        <table>
                                                            <tr>
                                                                <td align="center">
                                                                    <a href="{{ env('APP_URL') }}" target="_blank"><img
                                                                            src="{{ asset('assets/media/logos/logo.webp') }}"
                                                                            alt="Logo" style="display: block; padding: 1rem 0;"
                                                                            title="Logo" height="80" /></a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- content -->
                    <table align="center" cellspacing="0" cellpadding="0"
                        style="table-layout: fixed !important; width: 100%">
                        <tr style="border-collapse: collapse">
                            <td align="center" bgcolor="#F0F6FF" style="padding: 0; margin: 0; background-color: #f0f6ff">
                                <table class="table_border" style="width: 600pxl; border-radius: 12px; overflow: hidden"
                                    align="center" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
                                    <tr style="border-collapse: collapse">
                                        <td align="left" bgcolor="#ffffff"
                                            style="
                                        margin: 0;
                                        padding-top: 32px;
                                        padding-left: 40px;
                                        padding-right: 40px;
                                        background-color: #ffffff;
                                    ">
                                            <table width="100%" cellspacing="0" cellpadding="0"
                                                style="border-collapse: collapse; border-spacing: 0px">
                                                <tr style="border-collapse: collapse">
                                                    <td align="left" style="padding: 0; margin: 0; width: 518px">
                                                        <table>
                                                            <tr style="border-collapse: collapse">
                                                                <td>
                                                                    <h2
                                                                        style="margin: 0; font-size: 14px; font-weight: normal; color: #333333">
                                                                        Halo,
                                                                        <strong>{{ $transaction->user->name }}!</strong>
                                                                    </h2>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p
                                                                        style="margin-bottom: 12px; line-height: 22px; color: #333333; font-size: 13px">
                                                                        Selamat! Pembayaran pesanan Anda di NEST GYM telah berhasil
                                                                        pada tanggal {{ date('d/m/Y') }} pukul
                                                                        {{ date('H:i') }} WIB. Yuk, segera masuk aplikasi untuk mengecek jadwalnya.
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <table
                                                                    style="
                                                                    background: #f5f7fa;
                                                                    border-radius: 12px;
                                                                    overflow: hidden;
                                                                    padding: 12px 8px;
                                                                "
                                                                    width="100%" cellspacing="4px" cellpadding="8px">
                                                                    <tr>
                                                                        <td class="text_style">Status Pembayaran</td>
                                                                        <td class="badge-success" align="right">
                                                                            <span>LUNAS</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text_style">Nomor Pesanan</td>
                                                                        <td class="text_style" align="right">
                                                                            {{ $transaction->payment_code }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text_style">Metode Pembayaran</td>
                                                                        <td class="text_style" align="right">
                                                                            {{ $transaction->payment_method->name }}</td>
                                                                    </tr>
                                                                </table>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p
                                                                        style="
                                                                    margin-bottom: 12px;
                                                                    line-height: 22px;
                                                                    color: #333333;
                                                                    font-size: 16px;
                                                                    font-weight: 600;
                                                                    ">
                                                                        Ringkasan Pesanan
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            <tr style="border-collapse: collapse">
                                                                <td style="padding-bottom: 2rem">
                                                                    <table width="100%" cellspacing="0" cellpadding="8px"
                                                                        style="border: 1px solid #dae2ed; border-radius: 12px; overflow: hidden">
                                                                        <thead
                                                                            style="padding: 12px 8px; background: #f5f7fa">
                                                                            <th class="text_style" width="50%"
                                                                                style="border-bottom: 1px solid #dae2ed; margin-bottom: 12px">
                                                                                Paket Pembelian
                                                                            </th>
                                                                            <th class="text_style"
                                                                                style="border-bottom: 1px solid #dae2ed; margin-bottom: 12px">
                                                                                Harga
                                                                            </th>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($transaction->transaction_details as $transactionDetail)
                                                                                <tr style="line-height: 22px">
                                                                                    <td class="text_style">
                                                                                        {{ $transactionDetail->parent->name }}
                                                                                    </td>
                                                                                    <td class="text_style" align="right">
                                                                                        Rp<span>@money($transactionDetail->parent->selling_price)</span>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                            <tr style="line-height: 22px">
                                                                                <td class="text_style">Sub Total</td>
                                                                                <td class="text_style" align="right">
                                                                                    Rp<span>@money($transaction->sub_total)</span></td>
                                                                            </tr>
                                                                            @if ($transaction?->discount_product > 0)
                                                                                <tr style="line-height: 22px">
                                                                                    <td class="text_style">Diskon Produk
                                                                                    </td>
                                                                                    <td class="text_style" align="right">
                                                                                        -Rp<span>@money($transaction?->discount_product ?? 0)</span>
                                                                                    </td>
                                                                                </tr>
                                                                            @endif
                                                                            @if ($transaction?->discount_promo > 0)
                                                                                <tr style="line-height: 22px">
                                                                                    <td class="text_style">Diskon Promo
                                                                                    </td>
                                                                                    <td class="text_style" align="right">
                                                                                        -Rp<span>@money($transaction?->discount_promo ?? 0)</span>
                                                                                    </td>
                                                                                </tr>
                                                                            @endif
                                                                            <tr
                                                                                style="line-height: 22px; background: #f5f7fa">
                                                                                <td class="text_style"
                                                                                    style="border-top: 1px solid #dae2ed; font-weight: 600">
                                                                                    Total Bayar
                                                                                </td>
                                                                                <td class="text_style"
                                                                                    style="border-top: 1px solid #dae2ed; font-weight: 600"
                                                                                    align="right">
                                                                                    Rp<span>@money($transaction->pay_amount)</span>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                {{-- footer --}}
                                <table class="collapse_table" width="100%"
                                    style="border-spacing: 0px; background-color: #ffffff">
                                    <tr style="border-collapse: collapse">
                                        <td style="padding: 0; margin: 0; background-color: #f0f6ff" bgcolor="#f0f6ff"
                                            align="center">
                                            <table cellspacing="0" cellpadding="0" align="center">
                                                <tr style="border-collapse: collapse">
                                                    <td align="center" style="padding-top: 32px; padding-bottom: 10px">
                                                        <table cellspacing="0" cellpadding="0">
                                                            <tr style="border-collapse: collapse">
                                                                <td style="padding-right: 20px">
                                                                    <a href="https://facebook.com/">
                                                                        <div class="icon_button">
                                                                            <img title="Facebook"
                                                                                src="{{ asset('assets/media/icons/fb.png') }}"
                                                                                alt="Facebook" width="36"
                                                                                height="36" />
                                                                        </div>
                                                                    </a>
                                                                </td>
                                                                <td style="padding-right: 20px">
                                                                    <a href="https://youtube.com/">
                                                                        <div class="icon_button">
                                                                            <img title="Youtube"
                                                                                src="{{ asset('assets/media/icons/yt.png') }}"
                                                                                alt="Youtube" width="36"
                                                                                height="36" />
                                                                        </div>
                                                                    </a>
                                                                </td>
                                                                <td style="padding-right: 20px">
                                                                    <a href="https://instagram.com/" target="_blank">
                                                                        <div class="icon_button">
                                                                            <img title="Instagram"
                                                                                src="{{ asset('assets/media/icons/ig.png') }}"
                                                                                alt="Instagram" width="36"
                                                                                height="36" />
                                                                        </div>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a href="https://twitter.com/" target="_blank">
                                                                        <div class="icon_button">
                                                                            <img title="X"
                                                                                src="{{ asset('assets/media/icons/x.png') }}"
                                                                                alt="X" width="36"
                                                                                height="36" />
                                                                        </div>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr style="padding-bottom: 20px">
                                                    <td align="center">
                                                        <p style="margin: 0 0 32px 0; color: #8e9094; font-size: 13px">
                                                            © {{ date('Y') }} Nest Gym. All Rights Reserved.
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
@endsection
