<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Slip Gaji</title>

    <style>
        @page {
            margin: 20px 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 4px 0 0 0;
        }

        .line {
            border-bottom: 2px solid #333;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info {
            margin-bottom: 15px;
        }

        .info td {
            padding: 3px 0;
        }

        .info .label {
            width: 120px;
        }

        .info .colon {
            width: 15px;
        }

        .gaji-table {
            margin-top: 10px;
        }

        .gaji-table th,
        .gaji-table td {
            border: 1px solid #aaa;
            padding: 6px;
        }

        .gaji-table th {
            background-color: #eeeeee;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .kategori {
            background-color: #f3f3f3;
            font-weight: bold;
        }

        .total {
            background-color: #eeeeee;
            font-weight: bold;
        }

        .grand-total {
            background-color: #dddddd;
            font-weight: bold;
            font-size: 12px;
        }

        .footer {
            margin-top: 35px;
        }

        .signature td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            border: none;
        }

        .signature-space {
            height: 60px;
        }

        .note {
            margin-top: 20px;
            font-size: 9px;
            font-style: italic;
        }
    </style>

</head>

<body>


{{-- ================================
     HEADER
================================ --}}

<div class="header">

    <h2>SLIP GAJI KARYAWAN</h2>

    @if($periode)

        <p>
            Periode
            {{ \Carbon\Carbon::parse($periode->mulai)->format('d-m-Y') }}

            s/d

            {{ \Carbon\Carbon::parse($periode->selesai)->format('d-m-Y') }}
        </p>

    @endif

</div>

<div class="line"></div>


{{-- ================================
     INFORMASI KARYAWAN
================================ --}}

<table class="info">

    <tr>
        <td class="label">
            Nama Karyawan
        </td>

        <td class="colon">:</td>

        <td>
            <strong>
                {{ $nama_lengkap }}
            </strong>
        </td>
    </tr>


    <tr>
        <td class="label">
            Jabatan
        </td>

        <td class="colon">:</td>

        <td>
            {{ $nama_jabatan ?? '-' }}
        </td>
    </tr>


    <tr>
        <td class="label">
            Bulan
        </td>

        <td class="colon">:</td>

        <td>
            {{ $bulan }}
        </td>
    </tr>


    <tr>
        <td class="label">
            Tahun
        </td>

        <td class="colon">:</td>

        <td>
            {{ $tahun }}
        </td>
    </tr>

</table>


@php

    $totalPendapatan = 0;

    $totalPotongan = 0;

@endphp


{{-- ================================
     DETAIL GAJI
================================ --}}

<table class="gaji-table">

    <thead>

        <tr>

            <th
                style="width: 35px;"
                class="text-center">

                No

            </th>


            <th>
                Item Gaji
            </th>


            <th
                style="width: 120px;"
                class="text-right">

                Nominal

            </th>


            <th
                style="width: 55px;"
                class="text-center">

                Qty

            </th>


            <th
                style="width: 130px;"
                class="text-right">

                Total

            </th>

        </tr>

    </thead>


    <tbody>


    @foreach($kategori as $kat)

        @php

            $items = $transGaji->filter(
                function ($trans) use ($kat) {

                    return optional(
                        $trans->itemGaji
                    )->kategori_item_id == $kat->id;

                }
            );

        @endphp


        @if($items->count() > 0)


            {{-- =========================
                 NAMA KATEGORI
            ========================== --}}

            <tr class="kategori">

                <td colspan="5">

                    {{
                        $kat->nama_kategori
                        ?? $kat->nama
                        ?? '-'
                    }}

                </td>

            </tr>


            @foreach($items as $item)


                @php

                    /*
                    |--------------------------------------------------------------------------
                    | NOMINAL
                    |--------------------------------------------------------------------------
                    */

                    $nominal =
                        (float) ($item->nominal ?? 0);


                    /*
                    |--------------------------------------------------------------------------
                    | QTY
                    |--------------------------------------------------------------------------
                    */

                    $qty =
                        (float) ($item->qty ?? 0);


                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL ITEM
                    |--------------------------------------------------------------------------
                    |
                    | Jika nominal adalah harga satuan:
                    |
                    | nominal 42.000
                    | qty 22
                    | total 924.000
                    |
                    */

                    $totalItem =
                        $nominal * $qty;


                    /*
                    |--------------------------------------------------------------------------
                    | NAMA KATEGORI
                    |--------------------------------------------------------------------------
                    */

                    $namaKategori = strtolower(

                        $kat->nama_kategori
                        ?? $kat->nama
                        ?? ''

                    );


                    /*
                    |--------------------------------------------------------------------------
                    | PENDAPATAN / POTONGAN
                    |--------------------------------------------------------------------------
                    */

                    if (
                        str_contains(
                            $namaKategori,
                            'potongan'
                        )
                    ) {

                        $totalPotongan +=
                            $totalItem;

                    } else {

                        $totalPendapatan +=
                            $totalItem;

                    }

                @endphp


                <tr>


                    {{-- NO --}}

                    <td class="text-center">

                        {{ $loop->iteration }}

                    </td>


                    {{-- ITEM GAJI --}}

                    <td>

                        {{
                            optional(
                                $item->itemGaji
                            )->nama_item_gaji
                            ?? '-'
                        }}

                    </td>


                    {{-- NOMINAL --}}

                    <td class="text-right">

                        Rp

                        {{
                            number_format(
                                $nominal,
                                0,
                                ',',
                                '.'
                            )
                        }}

                    </td>


                    {{-- QTY --}}

                    <td class="text-center">

                        {{
                            number_format(
                                $qty,
                                0,
                                ',',
                                '.'
                            )
                        }}

                    </td>


                    {{-- TOTAL --}}

                    <td class="text-right">

                        Rp

                        {{
                            number_format(
                                $totalItem,
                                0,
                                ',',
                                '.'
                            )
                        }}

                    </td>


                </tr>


            @endforeach


        @endif


    @endforeach



    {{-- ================================
         TOTAL PENDAPATAN
    ================================= --}}

    <tr class="total">

        <td colspan="4">

            Total Pendapatan

        </td>


        <td class="text-right">

            Rp

            {{
                number_format(
                    $totalPendapatan,
                    0,
                    ',',
                    '.'
                )
            }}

        </td>

    </tr>



    {{-- ================================
         TOTAL POTONGAN
    ================================= --}}

    <tr class="total">

        <td colspan="4">

            Total Potongan

        </td>


        <td class="text-right">

            Rp

            {{
                number_format(
                    $totalPotongan,
                    0,
                    ',',
                    '.'
                )
            }}

        </td>

    </tr>



    {{-- ================================
         GAJI DITERIMA
    ================================= --}}

    <tr class="grand-total">

        <td colspan="4">

            GAJI DITERIMA

        </td>


        <td class="text-right">

            Rp

            {{
                number_format(
                    $totalPendapatan
                    -
                    $totalPotongan,
                    0,
                    ',',
                    '.'
                )
            }}

        </td>

    </tr>


    </tbody>

</table>



{{-- ================================
     TANDA TANGAN
================================ --}}

<div class="footer">

    <table class="signature">

        <tr>

            <td>
                Penerima,
            </td>

            <td>
                HR / Finance,
            </td>

        </tr>


        <tr>

            <td class="signature-space"></td>

            <td class="signature-space"></td>

        </tr>


        <tr>

            <td>

                <strong>
                    {{ $nama_lengkap }}
                </strong>

            </td>


            <td>

                ______________________

            </td>

        </tr>

    </table>

</div>


<div class="note">

    Slip gaji ini dibuat secara otomatis oleh sistem.

</div>


</body>
</html>