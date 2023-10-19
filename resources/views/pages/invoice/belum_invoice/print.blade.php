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
            padding: 0;
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
        }
        .text-right{
            text-align: right;
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

    
        {{-- <img src="{{ asset('img/LOGO_PJE.jpg') }}" alt=""> --}}
        {{-- <div class="kontener">
            <img src="{{ public_path("img/LOGO_PJE_WARNA.jpg") }}"  width="300" height="300" style="margin-left: -50px;">
            <h3>PRIMATRANS JAYA EXPRESS</h3>
            <p>Jl. Ikan Mungsing VII No. 61, Surabaya</p>
            <p>Telp: 0896-0301-1919</p> --}}
            {{-- <div id="qrcode">
                <img src="data:image/png;base64,{{ base64_encode($qrcode) }}" alt="QR Code" >
            </div> --}}

            {{-- <h2 class="text" style="">INVOICE</h2> --}}
            
        {{-- </div> --}}
        <table  autosize='1' style="width:100%;" >
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
                    <th colspan='4' style="text-align:left;"><img style="position: absolute;margin-top: -170px;margin-left: -70px;" src="{{ public_path("img/LOGO_PJE_DOANG.png") }}"  width="500" height="500"></th>
                    <th colspan='11' style="text-align:left;">
                        <h2 >
                            <span style="color:#1f55a2"> PRIMATRANS JAYA EXPRESS</span>
                            <br>
                            <span style="font-size:30px; font-weight:normal; margin-top:-20px;">Jl. Ikan Mungsing VII No. 61, Surabaya</span>
                            <br>
                            {{-- <span style="font-size:20px; font-weight:normal">Telp: 0896-0301-1919</span> --}}
                            
                        </h2>
                    </th>
                    <td colspan='5' style="text-align:right;">
                        {{-- <img src="data:image/png;base64,{{ base64_encode($qrcode) }}" alt="QR Code" > --}}
                        @php
                            $cekTarif = false;
                        @endphp
                        @php
                            foreach ($data->invoiceDetails as $i => $detail) {
                                if( $detail->tarif>0 )
                                {
                                    //FLAG KALO KETEMU KELUAR LOOPING
                                    $cekTarif = true;
                                    break;
                                }
                            }
                        @endphp 
                        @if ($cekTarif)
                    
                            <h1>INVOICE</h1>
                        @else
                            <h1>INVOICE PISAH</h1>

                        @endif
                            
                        {{-- <span style="color:#1f55a2">{{ $data['no_invoice'] }}</span> --}}
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
                    <td width='30%' style=" padding-left: 10px; text-align:left;vertical-align:top;" rowspan="4">{{ ($data->getBillingTo->nama) }}</td>
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

        <table class="border-table"  style='margin-bottom: 50px;'>
            <thead >
                <tr class="bg-gray text-center text-bold">
                    <td style="border: 1px solid black; border-collapse: collapse;">NO</td>
                    <td style="border: 1px solid black; border-collapse: collapse;">TGL. BERANGKAT <br> TUJUAN</td>
                    <td style="border: 1px solid black; border-collapse: collapse;">NO. CONTAINER
                        {{-- <br>NO. SURAT JALAN --}}
                        <br>NO. SEGEL
                    </td>
                    <td style="border: 1px solid black; border-collapse: collapse;">NOPOL</td>
                    {{-- @foreach ($data->invoiceDetails as $i => $detail) --}}

                        @if ($cekTarif)

                            <td style="border: 1px solid black; border-collapse: collapse;">HARGA</td>
                            <td style="border: 1px solid black; border-collapse: collapse;">BIAYA TAMBAHAN</td>
                             <td style="border: 1px solid black; border-collapse: collapse;">DISKON</td>

                        @else
                            <td style="border: 1px solid black; border-collapse: collapse;">HARGA</td>

                        @endif
                        
                    {{-- @endforeach --}}
               
                    <td style="border: 1px solid black; border-collapse: collapse;">SUBTOTAL</td>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                @endphp
                @foreach ($data->invoiceDetails as $i => $detail)
                @php
                    $i++;
                @endphp
                <tr>
                    <td class="text-center">{{ $i }}</td>
                    <td>
                        {{ date("d-M-Y", strtotime($detail->sewa->tanggal_berangkat)) }}
                        <br>{{ $detail->sewa->nama_tujuan }}
                    </td>
                    <td>
                        {{-- {{ $detail->sewa->getJOD->no_kontainer }} --}}
                        {{ $detail->sewa->no_kontainer }}

                        {{-- <br>{{ $detail->sewa->no_surat_jalan }} --}}
                        <br>{{ $detail->sewa->seal_pelayaran }}
                    </td>
                    <td class="text-center">{{ $detail->sewa->no_polisi }} <br>( {{ $detail->sewa->tipe_kontainer.'"' }} )</td>
                    
                    @if ($detail->tarif>0)

                        <td class="text-right">{{ number_format($detail->tarif) }}</td>
                        <td class="text-right" {{--rowspan="27"--}}> <br>
                        @if (isset($dataOperasional))
                            @foreach ($dataOperasional as $DO)

                                @if ($detail->id_sewa == $DO->id_sewa)
                                        
                                        <span style="font-size: 20px;">({{$DO->deskripsi}})</span>  {{ number_format($DO->total)}} </br>
                                {{-- @else
                                        <span>0</br> --}}

                                @endif
                                
                            @endforeach

                        @else
                            -
                            
                        @endif

                        </td>
                        <td class="text-right">{{ number_format($detail->diskon) }}</td>

                    @else
                        <td class="text-right" {{--rowspan="27"--}}> <br>
                        @if (isset($dataOperasional))
                            @foreach ($dataOperasional as $DO)

                                @if ($detail->id_sewa == $DO->id_sewa)
                                        
                                        <span style="font-size: 20px;">({{$DO->deskripsi}})</span>  {{ number_format($DO->total)}} </br>
                                {{-- @else
                                        <span >0</br> --}}

                                @endif
                                
                            @endforeach
                        @else
                            -
                            
                        @endif
                        </td>
                    @endif

                    <td class="text-right" style="padding-right: 20px;">{{ number_format($detail->sub_total) }}</td>
                </tr>
                @php
                    $total += $detail->sub_total;                 
                @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    {{-- @foreach ($data->invoiceDetails as $i => $detail) --}}

                        @if ($cekTarif)
                            <td colspan="7" class="text-right" style="padding-right: 15px; border-top: 1px solid black; border-collapse: collapse;"><strong>Total</strong></td>
                        @else
                            <td colspan="5" class="text-right" style="padding-right: 15px; border-top: 1px solid black; border-collapse: collapse;"><strong>Total</strong></td>
                        @endif
                        
                    {{-- @endforeach --}}
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
            <br>atas nama: <b><u>PT. PRIMATRANS JAYA EXPRESS</u></b></br>
            <!-- </br></br><img src="data:image/png;base64,{{ base64_encode($qrcode) }}" alt="QR Code" > -->

            {{-- {{$qrcode}} --}}
            {{-- <img src="{{ public_path("img/") }}{{ $qrcode }}" alt="QR Code"> --}}
            {{-- <br><br><img src="data:image/png;base64,{{ base64_encode($qrcode) }}" alt="QR Code" > --}}
        </span>    
    
        <table class="table-bawah" style="margin-top: 50px;">
            <tbody> 
                <tr>
                    <td width="800px;">&nbsp;</td>
                    <td class="text-right" style="padding-right: 50px;">Hormat Kami,</td>
                </tr>
            </tbody>
            <br>
            <tfoot>
                <tr>
                    <td width="800px;">&nbsp;</td>
                    <td class="text-right" style="padding-right: 50px;"><img src="data:image/png;base64,{{ base64_encode($qrcode) }}" alt="QR Code" ></td>
                </tr>
                <tr>
                    <td width="800px;">&nbsp;</td>
                    {{-- <td class="text-right" >(..................................)</td> --}}
                    <td class="text-right" style="padding-right: 50px;">({{Auth::user()->username}})</td>

                </tr>
            </tfoot>
        </table>
    @endif
    
</body>

</html>
