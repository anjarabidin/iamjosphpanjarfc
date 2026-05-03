<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <title>Data Transaksi {{ $date }}</title>
    <style>
        * {
            /* margin: 0;
        padding: 0; */
            box-sizing: border-box;
        }

        header {
            position: relative;
        }

        header p {
            text-align: center;
            font-weight: 800;
            font-size: 18px;
            position: absolute;
            top: .25rem;
            left: 50%;
            transform: translate(-50%);
        }

        header {
            position: relative;
        }

        div table tr td {
            font-size: 12px;
            border: 0.25px solid black;
            padding: 4px 8px;
        }

        div table thead tr th {
            text-align: center;
        }

        div table tfoot tr td,
        div table tr th {
            border: 0.25px solid black;
            padding: 8px;
            font-weight: 700;
            font-size: 12px;
        }

        div table {
            border-collapse: collapse;
        }

        footer table {
            font-size: 12px;
            position: absolute;
            bottom: 0;
            left: 0;
        }

        footer table tr td {
            /* padding: 32px; */
            border: none;
        }
    </style>
</head>

<body>
    <div id="paper">

        <!-- header -->
        <header>
            <img src="{{ asset('assets/media/logos/logo.webp') }}" width="65" alt="" />
            <p>Transaksi {{ $date }}</p>

        </header>
        @if ($user)
        <p>Nama: {{ $user->name }}</p>
        <p>Email: {{ $user->email }}</p>
        <p>Phone: {{ $user->phone }}</p>
        @endif
        <!-- main -->
        <div class="mt-4">
            <table width="100%">
                <thead>
                    <tr>
                        <th width="15%">Kode</th>
                        <th width="10%">Metode Pembayaran</th>
                        <th width="12%">Tanggal</th>
                        <th width="">Paket</th>
                        <th width="10%">Sub Harga</th>
                        <th width="10%">Diskon</th>
                        <th width="10%">Xendit</th>
                        <th width="10%">Note</th>
                        <th width="10%">Total Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $total_income = 0;
                    @endphp
                    @foreach ($data as $item)
                    @php
                    $offline_payment = $item->offline_payment_method ? " (" . $item->offline_payment_method . ")" : "";
                    // $total_income += ($item->pay_amount - $item->xendit_fee);
                    $total_income += $item->pay_amount;
                    $promo = $item->discount_product + $item->discount_promo;
                    @endphp
                    <tr>
                        <td style="white-space: nowrap">{{ $item->payment_code }}</td>
                        <td>{{ $item->payment_method->type == "AUTO" ? $item->payment_method->name :
                            $item->payment_method->name . $offline_payment }}</th>
                        <td>{{ date('d/M/Y', strtotime($item->created_at)) }}</td>
                        <td>
                            @if ($item->transaction_details()->exists())
                                @foreach ($item->transaction_details as $detail)
                                    @if ($detail->parentable_type == "App\Models\Event")
                                        <p>{{ $detail->parent?->name . " (Tiket Event)" }}</p>
                                        @foreach ($detail->transaction->event_ticket_order->eventTicketOrderDetailGroups as $eventTicketOrderDetail)
                                            <li style="margin-left: 10px">{{ $eventTicketOrderDetail->eventTicket?->name . " ({$eventTicketOrderDetail->total_quantity}Tiket)"}}</li>
                                        @endforeach
                                    @else
                                        <li style="margin-left: 10px">{{ $detail->parent?->name }}</li>
                                    @endif
                                @endforeach
                            @elseif ($item->user_timeoff_history)
                                <li style="margin-left: 10px">Cuti Membership {{ round($item->user_timeoff_history->duration / 30) }} Bulan</li>
                            @elseif ($item->annual_payment_history)        
                                <li style="margin-left: 10px">{{ $item->annual_payment_history->name }}</li>
                            @endif
                        </td>
                        <td>Rp{{ number_format($item->sub_total, 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($promo, 0, ',', '.') }}</td>
                        <td>Rp{{ number_format($item->xendit_fee, 0, ',', '.') }}</td>
                        <td>{{ $item->note ?? '' }}</td>
                        <td>Rp{{ number_format($item->pay_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" align="right">Total Pendapatan</td>
                        <td>Rp{{ number_format($total_income, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- end main -->
        <!-- footer -->
        <footer>
            <table width="100%" class="pt-3">
                <tr>
                    <td>
                        <i>*Terakhir diupdate: {{ date('d F Y H:i') }} WIB</i>
                    </td>
                    {{-- <td align="right">1/1</td> --}}
                </tr>
            </table>
        </footer>
        <!-- end footer -->
    </div>
</body>

</html>
