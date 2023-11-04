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
                    <td style=""><b>:</b> [No-Invoice]</td>
                </tr>
                <tr class="borderDebug">
                    <td width='30%' style=" padding-left: 10px; text-align:left;vertical-align:top;" rowspan="4">[billing_to]</td>
                    <td></td>
                    <td width='30%'>&nbsp;</td>
                    <td style=""><b>Tanggal</b> </td>
                    <td style=""><b>:</b> [Tanggal Invoice]</td>
                </tr>
                <tr class="borderDebug">
                    <td style=""width='30%' colspan="2">&nbsp;</td>
                    <td style=""><b>Jatuh Tempo</b> </td>
                    <td style=""><b>:</b> [Jatuh Tempo]</td>
                </tr>
                <tr class="borderDebug">
                    <td style="" width='30%' colspan="2">&nbsp;</td>
                    <td style=" text-align:left;vertical-align:top;"><b>Catatan</b> </td>
                    <td style=""><b>:</b> [catatan]</td>
                </tr>
                <tr class="borderDebug">
                    <td width='30%' colspan="2" >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                </tr>
            </thead>
        </table>

<table>
    <tr>
        <th colspan="4" class="header">Kapal 1</th>
        {{-- <th colspan="3" class="header">Total Karantina : Rp.200.000,00</th> --}}

    </tr>
    <tr>
        <th>Kontainer</th>
        <th>Tipe Kontainer</th>
        <th>Segel</th>
        <th>Muatan</th>
    </tr>
    <tr>
        <td>Kontainer 1</td>
        <td>[Tipe 1]</td>
        <td>[Segel 1]</td>
        <td>[Muatan 1]</td>
    </tr>
    <tr>
        <td>Kontainer 2</td>
        <td>[Tipe 2]</td>
        <td>[Segel 2]</td>
        <td>[Muatan 2]</td>
    </tr>
    <tr class="subtotal">
        <td colspan="3">Subtotal Karantina Kapal 1:</td>
        <td>Rp.100.000,00</td>
    </tr>
</table>
<br>
<table>
    <tr>
        <th colspan="4" class="header">Kapal 2</th>
        {{-- <th colspan="3" class="header">Total Karantina : Rp.200.000,00</th> --}}

    </tr>
    <tr>
        <th>Kontainer</th>
        <th>Tipe Kontainer</th>
        <th>Segel</th>
        <th>Muatan</th>
    </tr>
    <tr>
        <td>Kontainer 1</td>
        <td>[Tipe 1]</td>
        <td>[Segel 1]</td>
        <td>[Muatan 1]</td>
    </tr>
    <tr>
        <td>Kontainer 2</td>
        <td>[Tipe 2]</td>
        <td>[Segel 2]</td>
        <td>[Muatan 2]</td>
    </tr>
    <tr class="subtotal">
        <td colspan="3">Subtotal Karantina Kapal 1:</td>
        <td>Rp.100.000,00</td>
    </tr>
</table>
<br>
<table>
    
    <tr class="total" >
        <td >Total Karantina : </td>
        <td ><span style="opacity: 0%;">...............</span></td>
        <td ><span style="opacity: 0%;">................</span></td>
        <td >Rp.200.000,00</td>
    </tr>
</table>
<br>
<span>
    Pembayaran dapat dilakukan pembukaan cek atas nama <b><u>PT. PRIMATRANS JAYA EXPRESS</u></b>
    <br>Atau transfer ke rekening
    <br>BCA: <b><u>51308 14141</u></b> / Mandiri: <b><u>14000 41415 135</u></b>
    <br>atas nama: <b><u>PT. PRIMATRANS JAYA EXPRESS</u></b></br>
</span>    

<table class="table-bawah" style="margin-top: 50px;" class="borderDebug">
            <tbody> 
                <tr>
                    <td colspan='4' >&nbsp;</td>
                    <td class="text-right" >Hormat Kami,</td>
                </tr>
            </tbody>
            <br>
            <tfoot>
                <tr>
                    <td colspan='4' >&nbsp;</td>
                    <td class="text-right" ><img src="data:image/png;base64,{{ base64_encode($qrcode) }}" alt="QR Code" ></td>
                </tr>
                <tr>
                    <td  colspan='4'>&nbsp;</td>
                    {{-- <td class="text-right" >(..................................)</td> --}}
                    <td class="text-right" >({{Auth::user()->username}})</td>
                </tr>
            </tfoot>
        </table>
</body>

</html>
