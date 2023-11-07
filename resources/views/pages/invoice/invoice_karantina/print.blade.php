<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">
  
        :root {
            margin: 55px;
            padding: 2px;
            font-size: 30px;
            font-family: Arial, sans-serif;

        }
        table {
        width: 100%;
        border-collapse: collapse;
    }

    /* table, th, td {
        border: 1px solid black;
    } */

    th, td {
        padding: 10px;
        text-align: left;
    }

    .header {
        font-weight: bold;
        background-color: #eee;
    }

    .subtotal {
        background-color: #f0f0f0;
    }

    .total {
        background-color: #ccc;
    }
    .borderDebug{
        /* border: 1px solid black; */
    }
    </style>
</head>
<body>
    <hr style=" border: 10px solid rgb(54, 78, 163);margin-top: -55px;">

  <table  autosize='1' style="width:100%; " >
            <thead >
                <tr >
                    <th style="width:5%;" class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                    <th style="width:5%;"class="borderDebug"></th>
                </tr>
                 <tr>
                    <th colspan='4' style="text-align:left;"><img style="position: absolute;margin-top: -170px;margin-left: -70px;" src="{{ public_path("img/LOGO_PJE_DOANG1.png") }}"  width="500" height="500"></th>
                    <th colspan='11' style="text-align:left;">
                        <h2 >
                            <span style="color:#1f55a2"> PRIMATRANS JAYA EXPRESS</span>
                            <br>
                            <span style="font-size:30px; font-weight:normal; margin-top:-20px;">Jl. Ikan Mungsing VII No. 61, Surabaya</span>
                            <br>
                        </h2>
                    </th>
                    <td colspan='5' style="text-align:right;">
                            <h1>INVOICE KARANTINA</h1>
                    </td>
    			</tr>
            </thead>
        </table>
        <hr style=" border: 1px solid rgb(76, 76, 76);margin-top: 30px;">
        <table class="border-table">
            <thead class="border-table">
                <tr class="borderDebug">
                    <td style="padding-left: 10px; "><b>Kepada Yth :</b> </td>
                    <td></td>
                    <td width='30%'>&nbsp;</td>
                    <td style=""><b>No Invoice</b></td>
                    <td style=""><b>:</b> {{$invoiceKarantina->no_invoice_k}}</td>
                </tr>
                <tr class="borderDebug">
                    <td width='30%' style=" padding-left: 10px; text-align:left;vertical-align:top;" rowspan="4">{{$invoiceKarantina->nama_customer}}</td>
                    <td></td>
                    <td width='30%'>&nbsp;</td>
                    <td style=""><b>Tanggal</b> </td>
                    <td style=""><b>:</b> {{\Carbon\Carbon::parse($invoiceKarantina->tgl_invoice)->format('d-M-Y')}}</td>
                </tr>
                {{-- <tr class="borderDebug">
                    <td style=""width='30%' colspan="2">&nbsp;</td>
                    <td style=""><b>Jatuh Tempo</b> </td>
                    <td style=""><b>:</b> [Jatuh Tempo]</td>
                </tr> --}}
                <tr class="borderDebug">
                    <td style="" width='30%' colspan="2">&nbsp;</td>
                    <td style=" text-align:left;vertical-align:top;"><b>Catatan</b> </td>
                    <td style=""><b>:</b> {{$invoiceKarantina->catatan?$invoiceKarantina->catatan:'-'}}</td>
                </tr>
                <tr class="borderDebug">
                    <td width='30%' colspan="2" >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                </tr>
            </thead>
        </table>

<table>
    @php
        $total = 0;
    @endphp
    @foreach ($invoiceKarantinaDetail as $value)
        <tr>
            <th colspan="3" class="header">{{$value->getKarantina->getJO->kapal}} - ({{$value->getKarantina->getJO->voyage}})</th>
        </tr>
        <tr>
            <th>Kontainer</th>
            <th>Tipe Kontainer</th>
            <th>Segel</th>
        </tr>
        @foreach ($value->getKarantina->details as $detail)
          {{-- @if ($dataKapal->id==$dataKontainer->id_invoice_k_detail) --}}
            <tr>
                <td>{{$detail->getJOD->no_kontainer}}</td>
                <td>{{$detail->getJOD->tipe_kontainer}}"</td>
                <td>{{$detail->getJOD->seal}}</td>
            </tr>
          {{-- @endif --}}
        @endforeach
        <tr class="subtotal">
            <td colspan="2">Subtotal :</td>
            <td><b>Rp {{number_format($value->getKarantina->total_dicairkan)}}</b></td>
            @php
                $total += $value->getKarantina->total_dicairkan;
            @endphp
        </tr>
        <br>
    @endforeach
</table>
<br>
<table>
    <tr class="total" >
        <td style="font-size: 1.5em;"><b>Total Karantina :</b> </td>
        <td><span style="opacity: 0%;">...............</span></td>
        <td><span style="opacity: 0%;">................</span></td>
        <td style="font-size: 1.5em;"><b>Rp {{ number_format($total); }}</b></td>
    </tr>
</table>
<br>
<span>
    Pembayaran dapat dilakukan pembukaan cek atas nama <b><u>PT. PRIMATRANS JAYA EXPRESS</u></b>
    <br>Atau transfer ke rekening
    <br>BCA: <b><u>51308 14141</u></b> / Mandiri: <b><u>14000 41415 135</u></b>
    <br>atas nama: <b><u>PT. PRIMATRANS JAYA EXPRESS</u></b></br>
</span>    

    <table class="" style="margin-top: 50px; " class="">
        <tbody> 
            <tr style="">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td style="background: #fff; width: 20%; text-align: center">Hormat Kami,</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td style="background: #fff; width: 20%; text-align: center"><img src="data:image/png;base64,{{ base64_encode($qrcode) }}" alt="QR Code" ></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td style="background: #fff; width: 20%; text-align: center">({{Auth::user()->username}})</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
