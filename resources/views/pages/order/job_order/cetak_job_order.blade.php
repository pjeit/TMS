<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Document</title>
    <style type="text/css">
         :root {
            font-family: Arial, sans-serif;

        }
        .border-table{
            width: 100%; /* Optional: Set table width */    
       
             border-spacing: 0;
        }
        thead {
            margin-bottom: 200px; 
        }
        .text{
            text-transform: uppercase;
            font-family: Arial, sans-serif;
            font-size: 25px;
        }
        .text-kecil{
            font-family: Arial, sans-serif;
            font-size: 25px;
        }
        .p-50{
            padding-left: 50px;
        }
        .td-atas{
            /* text-transform: uppercase; */
            font-family: Arial, sans-serif;
            font-size: 25px;
            
        }
       tr,td{
            border: 1px solid  #ccc; 
            
       }
        .custom-table tr,
        .custom-table td {
            border: none;
        }
        .custom-border{
            border: 1px solid  #ccc; 
        }
       .p-10{
             padding: 10px;
       }
       .bold{
        font-weight: 300;
       }
       .align-uang{
        text-align: left;
       }
       .garis-bawah{
        text-decoration: underline;
       }
       .th-kontainer{
            border: 1px solid  #ccc; /* Border around the table */
             padding: 30px;
       }
       .tabel-kontainer{
             padding: 30px;
            text-align: center;
            font-size: 25px;
       }
       .border-table-kontainer{
            width: 100%; /* Optional: Set table width */    
        }
       .float-left{
            float: left;
            font-size: 25px;
            width: 19%;
            /* border-collapse: collapse; */    
            border-spacing: 0;
       }
       
       table {
            /* Optional: Add table-specific styles */
            /* border-collapse: collapse; */
        }   
    </style>
</head>
<body>
    
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
                <th style="text-align:left;"><img style="position: absolute;margin-top:-50px;margin-left: -40px;" src="{{ public_path("img/LOGO_PJE_DOANG1.png") }}"   width="300" height="300"></th>
                <th colspan='10' style="text-align:left;">
                    <h2>
                        <span style="color:#1f55a2;font-size:30px; margin-top:33px;position: absolute;"> PRIMATRANS JAYA EXPRESS</span>
                        <br>
                        <span style="font-size:30px; font-weight:normal; margin-top:15px;position: absolute;">Jl. Ikan Mungsing VII No. 61, Surabaya</span>
                        <br>
                    </h2>
                </th>
            </tr>
        </thead>
    </table>

    <h2 class="text" style="text-align: center;margin-top:-10px; background-color: rgb(54, 78, 163);color:aliceblue;">JOB ORDER</h2>
    <table class="border-table">
        <thead>
            {{-- <tr>
                <td>
                    <h1 class="text">BILLING TO</h1>
                    <h5 class="text" > {{$JobOrder->no_jo}}</h5>
                </td>
            </tr> --}}
        </thead>
        <tbody  class="td-atas"> 
            <tr >
                {{-- customer --}}
                <td class="p-10 bold">Pengirim</td> 
                <td class="p-10 garis-bawah">
                    @if($JobOrder->id_customer == $dataCustomer->id)
                        {{$dataCustomer->nama}}
                    @endif
                </td>
                <td class="p-10 bold">Pelabuhan Muat</td>
                <td class="p-10 garis-bawah">{{$JobOrder->pelabuhan_muat}}</td>
                <td class="p-10 bold">No. BL</td>
                <td class="p-10 garis-bawah">{{$JobOrder->no_bl}}</td>
            </tr>
            <tr>
                {{-- supplier --}}
                <td class="p-10 bold">Pelayaran</td>
                <td class="p-10 garis-bawah">
                    @if($JobOrder->id_supplier == $dataSupplier->id)
                        {{$dataSupplier->nama}}
                    @endif
                </td>
                <td class="p-10 bold">Pelabuhan Bongkar</td>
                <td class="p-10 garis-bawah">{{$JobOrder->pelabuhan_bongkar}}</td>
                <td class="p-10 bold">Tanggal Sandar</td>
                <td class="p-10 garis-bawah">{{\Carbon\Carbon::parse($JobOrder->tgl_sandar)->format('d-M-Y')}}</td>
            </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>

    <h2 class="text" style="text-align: center; background-color: rgb(54, 78, 163);color:aliceblue;">KONTAINER</h2>
    <table class="border-table">
        <thead>
            <tr class="tabel-kontainer">
                <th class="th-kontainer">No. Kontainer</th> 
                <th class="th-kontainer">Seal</th> 
                <th class="th-kontainer">Tujuan</th> 
                <th class="th-kontainer">Nopol / Driver</th> 
                <th class="th-kontainer">Tgl Dooring</th> 
                <th class="th-kontainer">Storage</th> 
                <th class="th-kontainer">Demurage</th> 
                <th class="th-kontainer">Detention</th> 
                <th class="th-kontainer">Repair</th> 
                <th class="th-kontainer">Washing</th> 
            </tr>
        </thead>
        <tbody > 
            @php
                $total_storage = 0;
                $total_demurage = 0;
                $total_detention = 0;
                $total_repair = 0;
                $total_washing = 0;

            @endphp
            @foreach ( $data_kontainer as $kont)
                <tr class="tabel-kontainer">
                    <td class="p-10">{{$kont->no_kontainer}}</td> 
                    <td class="p-10">{{$kont->seal}}</td> 
                    <td class="p-10">{{$kont->nama_tujuan}}</td> 
                    <td class="p-10">{{$kont->no_polisi}}</td> 
                    <td class="p-10">{{$kont->tanggal_berangkat?\Carbon\Carbon::parse($kont->tanggal_berangkat)->format('d-M-Y'):''}}</td> 
                    <td class="p-10 align-uang">Rp. {{number_format( $kont->storage)}}</td> 
                    <td class="p-10 align-uang">Rp. {{number_format( $kont->demurage)}}</td> 
                    <td class="p-10 align-uang">Rp. {{number_format( $kont->detention)}}</td> 
                    <td class="p-10 align-uang">Rp. {{number_format( $kont->repair)}}</td> 
                    <td class="p-10 align-uang">Rp. {{number_format( $kont->washing)}}</td> 
                </tr>
                @php
                $total_storage += $kont->storage;
                $total_demurage += $kont->demurage;
                $total_detention += $kont->detention;
                $total_repair += $kont->repair;
                $total_washing +=  $kont->washing;
                @endphp
            @endforeach
             
        </tbody>
        <tfoot>
        </tfoot>
    </table>

        <table class="float-left" style="margin-top: 40px;border:1px solid#ccc;">
            <thead>
                <tr>
                    <th colspan="2">Biaya Sebelum Dooring</th>
                </tr>
            </thead>
            <tbody > 
                {{-- @if ($JobOrder->thc) --}}
                    {{-- <tr>
                        <td class="p-10 bold">THC</td>
                        <td class="p-10 align-uang">Rp. {{number_format($JobOrder->thc) }}</td>
                    </tr> --}}
                {{-- @endif --}}
                {{-- @if ($JobOrder->lolo) --}}
                    {{-- <tr>
                        <td class="p-10 bold">LOLO</td>
                        <td class="p-10 align-uang">Rp. {{number_format($JobOrder->lolo)}}</td>
                    </tr> --}}
                {{-- @endif --}}
                {{-- @if ($JobOrder->apbs) --}}
                    <tr>
                        <td class="p-10 bold">APBS</td>
                        <td class="p-10 align-uang">Rp. {{number_format($JobOrder->apbs)}}</td>
                    </tr>
                {{-- @endif --}}
                {{-- @if ($JobOrder->cleaning) --}}
                    <tr>
                        <td class="p-10 bold">Cleaning</td>
                        <td class="p-10 align-uang">Rp. {{number_format($JobOrder->cleaning)}}</td>
                    </tr>
                {{-- @endif --}}
                {{-- @if ($JobOrder->doc_fee) --}}
                    <tr>
                        <td class="p-10 bold">Docfee</td>
                        <td class="p-10 align-uang">Rp. {{number_format($JobOrder->doc_fee)}}</td>
                    </tr>
                {{-- @endif --}}
                @if (isset($JobOrderBiaya))
                    @foreach ($JobOrderBiaya as $value)
                        <tr>
                            <td class="p-10 bold">{{$value->deskripsi}}</td>
                            <td class="p-10 align-uang">Rp. {{number_format($value->biaya)}}</td>
                                {{-- <input type="text" class="form-control" value="Rp. {{number_format($value->biaya)}}" readonly> --}}
                        </tr>
                    @endforeach
                @endif
                <tr>
                    <td class="p-10 bold">Subtotal</td>
                    <td class="p-10 align-uang">Rp. {{number_format($TotalBiayaRev)}}</td>
                </tr>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    
        <table class="float-left" style="margin-left: 30px;margin-top: 40px;border:1px solid#ccc;">
            <thead>
                <tr>
                    <th colspan="2">Biaya Saat Dooring</th>
                </tr>
            </thead>
            <tbody > 
                <tr>
                    <td class="p-10">Storage</td>
                    <td class="p-10 align-uang"> Rp. {{number_format($total_storage,2) }}</td>
                </tr>
                <tr>
                    <td class="p-10">Demurage</td>
                    <td class="p-10 align-uang"> Rp. {{number_format($total_demurage,2)}}</td>
                </tr>
                <tr>
                    <td class="p-10">Detention</td>
                    <td class="p-10 align-uang"> Rp. {{number_format($total_detention,2)}}</td>
                </tr>
                <tr>
                    <td class="p-10">Repair</td>
                    <td class="p-10 align-uang"> Rp. {{number_format($total_repair,2)}}</td>
                </tr>
                <tr>
                    <td class="p-10">Washing</td>
                    <td class="p-10 align-uang"> Rp. {{number_format($total_washing,2)}}</td>
                </tr>
                <tr>
                    <td class="p-10">Subtotal</td>
                    <td class="p-10 align-uang"> Rp. {{number_format($total_storage+$total_demurage+$total_detention+$total_repair+$total_washing,2)}}</td>
                </tr>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
        @if($dataJaminan)
            <table class="float-left" style="margin-left: 30px;border:1px solid#ccc;margin-top: 40px;">
                    <thead>
                        <tr>
                            <th colspan="2">Jaminan</th>
                        </tr>
                    </thead>
                    <tbody > 
                        @if($JobOrder->id == $dataJaminan->id_job_order)
                            <tr>
                                <td width='40%' class="p-10">Tanggal Bayar Jaminan</td>
                                <td class="p-10"> {{\Carbon\Carbon::parse($dataJaminan->tgl_bayar)->format('d-M-Y')}}</td>
                            </tr>
                            <tr>
                                <td width='40%' class="p-10">Total Jaminan</td>
                                <td class="p-10 align-uang"> Rp. {{number_format($dataJaminan->nominal,2) }}</td>
                            </tr>
                            <tr>
                                <td width='40%' class="p-10">Potongan Jaminan</td>
                                <td class="p-10 align-uang">
                                    @if ($dataJaminan->potongan_jaminan)
                                        Rp.{{ number_format($dataJaminan->potongan_jaminan, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td width='40%' class="p-10">Nominal Jaminan Kembali</td>
                                <td class="p-10 align-uang">
                                    @if ($dataJaminan->nominal_kembali)
                                        Rp.{{ number_format($dataJaminan->nominal_kembali, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr><tr>
                                <td width='40%' class="p-10">Tgl Jaminan Kembali</td>
                                <td class="p-10">{{$dataJaminan->tgl_kembali?\Carbon\Carbon::parse($dataJaminan->tgl_kembali)->format('d-M-Y'):'-'}}</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                    </tfoot>
            </table>
        @endif
        <table class="float-left custom-border" style="margin-left: 20px;margin-top: 40px;">
            <thead>
            </thead>
            <tbody > 
                <tr class="custom-table">
                    <td colspan="2"  class="custom-table"><span style="opacity: 0%;">...</span></td>
                </tr>
                <tr class="custom-table">
                    <td colspan="2"  class="custom-table"><span style="opacity: 0%;">...</span></td>
                </tr>
                <tr class="custom-table">
                    <td colspan="2"  class="custom-table"><span style="opacity: 0%;">...</span></td>
                </tr>
                <tr class="custom-table">
                    <td colspan="2"  class="custom-table"><span style="opacity: 0%;">...</span></td>
                </tr>
                <tr class="custom-table">
                    <td colspan="2"  class="custom-table"><span style="opacity: 0%;">...</span></td>
                </tr>
                <tr class="custom-table">
                    <td colspan="2" style="text-align: center;" class="custom-table">ADMIN</td>
                </tr>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
        <table class="float-left custom-border" style="margin-left: 30px;margin-top: 40px;">
            <thead>
            </thead>
            <tbody style=""> 
                <tr class="custom-table">
                    <td colspan="2" class="custom-table"><span style="opacity: 0%;">...</span></td>
                </tr>
                <tr class="custom-table">
                    <td colspan="2" class="custom-table"><span style="opacity: 0%;">...</span></td>
                </tr>
                <tr class="custom-table">
                    <td colspan="2" class="custom-table"><span style="opacity: 0%;">...</span></td>
                </tr>
                <tr class="custom-table">
                    <td colspan="2" class="custom-table"><span style="opacity: 0%;">...</span></td>
                </tr>
                <tr class="custom-table">
                    <td colspan="2" class="custom-table"><span style="opacity: 0%;">...</span></td>
                </tr>
                <tr class="custom-table">
                    <td colspan="2" style="text-align: center;" class="custom-table">FINANCE</td>
                </tr>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
<br/>

</body>

</html>