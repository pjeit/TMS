<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">
   
        img.watermark {
            position: absolute;
            margin-top: 500px;
            margin-left: 470px;
            z-index: -1;
            opacity: 20%;
             transform: rotate(-45deg);
        }
    
        :root {
            margin: 55px;
            padding: 2px;
            font-size: 30px;
            font-family: Arial, sans-serif;

        }
        table {
            width: 100%;
        }
        
        .border-table{
             /* Optional: Set table width */    
            border: 1px solid #000000; /* Border around the table */
            border-collapse: collapse;
        }
        .bg-gray{
            background-color: rgb(225, 225, 225);
        }
        .bg-blue{
            background-color: rgb(35, 83, 154);
        }
        .bg-red{
            background-color: red;
        }
        .text-center{
            text-align: center;
            padding: 40px;
        }
        .text-right{
            text-align: right;
            padding: 20px;
        }
          .text-left{
            text-align: left;
            padding: 40px;
        }
        .text-bold{
            font-weight: bold;
        }
        .kontener{
            display: flex;
            justify-content: start; 
        }
        thead{
            display:table-header-group;
        }
    </style>
</head>
<body>
    <hr style=" border: 10px solid rgb(54, 78, 163);margin-top: -55px;">
    @if ($data)
        <table  autosize='1' style="width:100%; " >
            <thead >
                <tr>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
                    <th style="width:5%;"></th>
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
                        @php
                            $is_reimburse = FALSE;
                            $noInvc = substr($data->no_invoice, -2);
                            if($noInvc == '/I'){
                                $is_reimburse = TRUE;
                            }
                        @endphp
                        @if ($is_reimburse == FALSE)
                            <h1>INVOICE</h1>
                        @else
                            <h1>INVOICE REIMBURSE</h1>
                        @endif
                    </td>
                </tr>
            </thead>
        </table>
        <hr style=" border: 1px solid rgb(76, 76, 76);margin-top: 30px;">
        <table class="border-table">
            <thead class="border-table">
                <tr style="">
                    <td style="padding-left: 10px; "><b>Kepada Yth :</b> </td>
                    <td></td>
                    <td width='30%'>&nbsp;</td>
                    <td style=""><b>No Invoice</b></td>
                    <td style=""><b>:</b> {{ $data['no_invoice'] }}</td>
                </tr>
                <tr>
                    <td width='30%' style=" padding-left: 10px; text-align:left;vertical-align:top;" rowspan="4">{{ ($data->getBillingTo->nama) }} <br>{{ ($data->getBillingTo->alamat) }}</td>
                    <td></td>
                    <td width='30%'>&nbsp;</td>
                    <td style=""><b>Tanggal</b> </td>
                    <td style=""><b>:</b> {{ date("d-M-Y", strtotime($data['tgl_invoice'])) }}</td>
                </tr>
                <tr>
                    <td style=""width='30%' colspan="2">&nbsp;</td>
                    <td style=""><b>Jatuh Tempo</b> </td>
                    <td style=""><b>:</b> {{ date("d-M-Y", strtotime($data['jatuh_tempo'])) }}</td>
                </tr>
                <tr>
                    <td style="" width='30%' colspan="2">&nbsp;</td>
                    <td style=" text-align:left;vertical-align:top;"><b>Catatan</b> </td>
                    <td style=""><b>:</b> {{ $data['catatan'] }}</td>
                </tr>
                <tr>
                    <td width='30%' colspan="2">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </thead>
        </table>
        {{-- <img class="watermark" src="{{ public_path("img/belum_lunas_invoice.png") }}" width="1000" height="600"> --}}
        {{-- <img style="position: absolute;margin-top: 500px;margin-left: 470px;z-index:-1;opacity:20%;" src="{{ public_path("img/belum_lunas_invoice.png") }}" width="1000" height="600"> --}}
        <table class="border-table"  style='margin-bottom: 50px;'>
            <thead >
                <tr class="bg-gray text-center text-bold">
                    <td style="border: 1px solid black; border-collapse: collapse;padding: 20px;">NO</td>
                    <td style="border: 1px solid black; border-collapse: collapse;padding: 20px;">TGL. BERANGKAT <br> TUJUAN</td>
                    <td style="border: 1px solid black; border-collapse: collapse;padding: 20px;">
                        @if ($data->invoiceDetails[0]->sewa->jenis_tujuan == 'LTL')
                            NO. KOLI
                            <br>NO. SURAT JALAN 
                        @else
                            NO. CONTAINER
                            <br>NO. SEGEL
                        @endif
                    </td>
                    <td style="border: 1px solid black; border-collapse: collapse;padding: 20px;">NOPOL</td>
                        {{-- @if ($is_reimburse == TRUE)
                            @if ($data->invoiceDetails[0]->sewa->jenis_tujuan == 'LTL')
                                <td style="border: 1px solid black; border-collapse: collapse;">JUMLAH MUATAN</td>
                                <td style="border: 1px solid black; border-collapse: collapse;">HARGA</td>
                            @else
                                <td style="border: 1px solid black; border-collapse: collapse;">HARGA</td>
                                <td style="border: 1px solid black; border-collapse: collapse;">DISKON</td>
                            @endif
                        @else  --}}
                    @if ($is_reimburse == FALSE)
                        <td style="border: 1px solid black; border-collapse: collapse;padding: 20px;">DISKON</td>
                        <td style="border: 1px solid black; border-collapse: collapse;padding: 20px;">HARGA</td>
                        <td style="border: 1px solid black; border-collapse: collapse;padding: 20px;">BIAYA TAMBAHAN</td>
                    @else
                        <td style="border: 1px solid black; border-collapse: collapse;padding: 20px;">HARGA REIMBURSE</td>
                    @endif
                    <td style="border: 1px solid black; border-collapse: collapse; padding: 20px;">SUBTOTAL</td>
                </tr>
            </thead>
            <tbody>
                
                @php
                    $total = 0;
                    $diskon = 0;
                @endphp
                @foreach ($data->invoiceDetails as $i => $detail)
                @php
                    $i++;
                @endphp
                <tr style="{{ $i % 2 == 0 ? 'background-color: rgb(232, 229, 229);' : '' }}">
                    <td class="text-left">{{ $i }} <br> &nbsp;</td>
                    <td>
                        {{ date("d-M-Y", strtotime($detail->sewa->tanggal_berangkat)) }}
                        <br>{{ $detail->sewa->nama_tujuan }}
                    </td>
                        <td class="text-left">
                            {{ $detail->sewa->no_kontainer }}
                            <br>
                            @if ($data->invoiceDetails[0]->sewa->jenis_tujuan == 'LTL')
                                {{ $detail->sewa->no_surat_jalan }}
                            @else
                                {{ $detail->sewa->seal_pelayaran }}
                            @endif
                        </td>
                        <td class="text-center">{{ $detail->sewa->no_polisi }}  
                            @isset($detail->sewa->tipe_kontainer)
                            <br>( {{ $detail->sewa->tipe_kontainer . '"' }} )
                            @endisset 
                        </td>
                        @if ($is_reimburse == FALSE)
                            <td class="text-right">{{ number_format($detail->diskon) }}</td>
                            <td class="text-right">{{ number_format($detail->tarif) }}</td>
                        @endif
                        <td class="text-right">
                            @if ($detail->invoiceDetailsAddCost != null)
                                @foreach ($detail->invoiceDetailsAddCost as $key => $add_cost)
                                    @if ($key != 0)
                                        <br> 
                                    @endif
                                    <span style="font-size: 20px;">({{$add_cost->sewaOperasional->deskripsi}})</span>  {{ number_format($add_cost->sewaOperasional->total_operasional)}}
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                    <td class="text-right" style="padding-right: 20px;">{{ number_format($detail->sub_total) }} </td>
                </tr>
                @php
                    $total += $detail->sub_total;                 
                @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                        @php
                            if($is_reimburse == TRUE){
                                $span = 5;
                            }else{
                                $span = 7;
                            }
                        @endphp
                        <td colspan="{{ $span }}" class="text-right" style="padding-right: 15px; border-top: 1px solid black; border-collapse: collapse;"><strong>Total</strong></td>
                        {{-- @if ($is_reimburse == TRUE)
                            <td colspan="7" class="text-right" style="padding-right: 15px; border-top: 1px solid black; border-collapse: collapse;"><strong>Total</strong></td>
                        @else
                            <td colspan="5" class="text-right" style="padding-right: 15px; border-top: 1px solid black; border-collapse: collapse;"><strong>Total</strong></td>
                        @endif --}}
                    <td class="text-right"  style="padding-right: 20px; border-top: 1px solid black; border-collapse: collapse;""><strong>{{ number_format($total) }}</strong></td>
                </tr>
            </tfoot>
        </table>

        <span>
            <!-- Display the QR code -->
            {{-- <img src="{{ public_path("img/LOGO_PJE.jpg") }}"  width="250" height="250" style="filter: grayscale(100%)"> --}}
            <!-- Display the QR code -->

            Pembayaran dapat dilakukan pembukaan cek atas nama <b><u>PT. PRIMATRANS JAYA EXPRESS</u></b>
            <br>Atau transfer ke rekening
            <br>BCA: <b><u>51308 14141</u></b> / Mandiri: <b><u>14000 41415 135</u></b>
            <br>atas nama: <b><u>PT. PRIMATRANS JAYA EXPRESS</u></b>{{--</br>
            </br>
            </br><img src="data:image/png;base64,{{ base64_encode($qrcode) }}" alt="QR Code" > --}}

        </span>    
    
        <table class="table-bawah" style="margin-top: 50px;" >
            <tbody> 
                <tr>
                    <td><img src="data:image/png;base64,{{ base64_encode($qrcode) }}" alt="QR Code" ></td>
                    <td width="800px;">&nbsp;</td>
                    <td class="text-right" style="padding-right: 50px;">Hormat Kami,</td>
                </tr>
            </tbody>
            <br>
            <br>
            <tfoot>
                <tr>
                    <td></td>
                    <td width="800px;">&nbsp;</td>
                    {{-- <td class="text-right" >(..................................)</td> --}}
                    <td class="text-right" style="padding-right: 50px;">({{Auth::user()->username}})</td>
                </tr>
            </tfoot>
        </table>
    @endif
    
</body>

</html>