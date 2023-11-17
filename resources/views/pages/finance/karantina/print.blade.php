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
            font-size: 20px;
            font-family: Arial, sans-serif;

        }
        table {
        width: 100%;
        border-collapse: collapse;
    }

    /* table, th, td {
        border: 1px solid black;
    } */
  .table-bawah{
            float: left;
            font-family: Arial, sans-serif;
            font-size: 20px;
        }
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

   <table  autosize='1' style="width:100%; margin-top:-80px;" >
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
                <th colspan='4' style="text-align:left;"><img style="position: absolute;margin-top:-100px;margin-left: -40px;" src="{{ public_path("img/LOGO_PJE_DOANG1.png") }}"   width="250" height="250"></th>
                <th colspan='11' style="text-align:left;">
                    <h2>
                        <span style="color:#1f55a2;font-size:15px; margin-top:-23px;position: absolute;"> PRIMATRANS JAYA EXPRESS</span>
                        <br>
                        <span style="font-size:15px; font-weight:normal; margin-top:-35px;position: absolute;">Jl. Ikan Mungsing VII No. 61, Surabaya</span>
                        <br>
                    </h2>
                </th>
            </tr>
        </thead>
    </table>
        
<table>
    @php
        $total = 0;
    @endphp
        <tr>
            <th colspan="3" class="header">{{$karantinaData->nama_kapal}} - ({{$karantinaData->voyage}})</th>
        </tr>
        <tr>
            <th>Kontainer</th>
            <th>Tipe Kontainer</th>
            <th>Segel</th>
        </tr>
        @foreach ($karantina_detail as $detail)
          {{-- @if ($dataKapal->id==$dataKontainer->id_invoice_k_detail) --}}
            <tr>
                <td>{{$detail->no_kontainer}}</td>
                <td>{{$detail->tipe_kontainer}}"</td>
                <td>{{$detail->seal}}</td>
            </tr>
          {{-- @endif --}}
        @endforeach
        
</table>
<br>
<table>
    <tr class="total" >
        <td style="font-size: 1.25em;"><b>Total Karantina :</b> </td>
        <td><span style="opacity: 0%;">...............</span></td>
        <td><span style="opacity: 0%;">................</span></td>
        <td style="font-size: 1.25em;"><b>Rp {{ number_format($karantinaData->total_operasional); }}</b></td>
    </tr>
</table>
<br>

<table class="table-bawah" >
      <thead>
        
        </thead>
        <tbody> 
            <tr>
                {{-- customer --}}
                <td style="text-align: left; ">Di siapkan Oleh :</td> 
                <td style="text-align: right; padding-left: 550px;">
                   Di setujui Oleh :
                </td>
            </tr>
            <br>
            <br/>
            <br/>
            <br/>
             <tr>
                {{-- customer --}}
                <td style="text-align: left; ">({{Auth::user()->username}})</td> 
                <td style="text-align: right; padding-left: 550px;">
                   (.........................)
                </td>
            </tr>
        
        </tbody>
        <tfoot>
        </tfoot>
</table>
</body>
</html>
