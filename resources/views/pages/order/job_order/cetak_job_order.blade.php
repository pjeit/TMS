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
            /*border: 1px solid  #ccc;*/ /*Border around the table*/
             /* background-color: #ccc; */
        }
        thead {
            margin-bottom: 200px; 
        }
        .text{
            text-transform: uppercase;
            font-family: Arial, sans-serif;
            font-size: 20px;
        }
        .text-kecil{
            font-family: Arial, sans-serif;
            font-size: 20px;
        }
        .p-50{
            padding-left: 50px;
        }
        .td-atas{
            text-transform: uppercase;
            font-family: Arial, sans-serif;
            font-size: 20px;
            
        }
       tr,td{
            border: 1px solid  #ccc; /* Border around the table */
            
       }
       .th-kontainer{
            border: 1px solid  #ccc; /* Border around the table */
             padding: 30px;

       }
       .tabel-kontainer{
             padding: 30px;
            text-align: center;
            font-size: 20px;
       }
       .border-table-kontainer{
            width: 100%; /* Optional: Set table width */    
        }
       .flex{
            display: flex;
            flex-direction: row;
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
                <th colspan='4' style="text-align:left;"><img style="position: absolute;margin-top:-20px;margin-left: -40px;" src="{{ public_path("img/LOGO_PJE_DOANG1.png") }}"   width="250" height="250"></th>
                <th colspan='11' style="text-align:left;">
                    <h2>
                        <span style="color:#1f55a2;font-size:15px; margin-top:53px;position: absolute;"> PRIMATRANS JAYA EXPRESS</span>
                        <br>
                        <span style="font-size:15px; font-weight:normal; margin-top:15px;position: absolute;">Jl. Ikan Mungsing VII No. 61, Surabaya</span>
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
            <tr>
                {{-- customer --}}
                <td >Pengirim</td> 
                <td >
                    @if($JobOrder->id_customer == $dataCustomer->id)
                        {{$dataCustomer->nama}}
                    @endif
                </td>
                <td >Pelabuhan Muat</td>
                <td>{{$JobOrder->pelabuhan_muat}}</td>
                <td >No. BL</td>
                <td>{{$JobOrder->no_bl}}</td>
            </tr>
            <tr>
                {{-- supplier --}}
                <td >Pelayaran</td>
                <td>
                    @if($JobOrder->id_supplier == $dataSupplier->id)
                        {{$dataSupplier->nama}}
                    @endif
                </td>
                <td >Pelabuhan Bongkar</td>
                <td>{{$JobOrder->pelabuhan_bongkar}}</td>
                <td >Tanggal Sandar</td>
                <td>{{\Carbon\Carbon::parse($JobOrder->tgl_sandar)->format('d-M-Y')}}</td>
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
            <tr class="tabel-kontainer">
                <td>No. Kontainer</td> 
                <td>Seal</td> 
                <td>Tujuan</td> 
                <td>Nopol / Driver</td> 
                <td>Tgl Dooring</td> 
                <td>Storage</td> 
                <td>Demurage</td> 
                <td>Detention</td> 
                <td>Repair</td> 
                <td>Washing</td> 
            </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>

    <div class="flex">

        <table  >
            <thead>
                <tr>
                    <th colspan="2">Biaya Sebelum Dooring</th>
                </tr>
            </thead>
            <tbody > 
                @if ($JobOrder->thc)
                    
                <tr>
                    <td>THC</td>
                    <td>: Rp. {{number_format($JobOrder->thc,2) }}</td>
    
                </tr>
                @endif
                    @if ($JobOrder->lolo)
                    
                    <tr>
                        <td>LOLO</td>
                        <td>: Rp. {{number_format($JobOrder->lolo,2)}}</td>
    
                    </tr>
                @endif
                    @if ($JobOrder->apbs)
                    
                    <tr>
                        <td>APBS</td>
                        <td>: Rp. {{number_format($JobOrder->apbs,2)}}</td>
    
                    </tr>
                @endif
                    @if ($JobOrder->cleaning)
                    
                    <tr>
                        <td>CLEANING</td>
                        <td>: Rp. {{number_format($JobOrder->cleaning,2)}}</td>
    
                    </tr>
                @endif
                    @if ($JobOrder->doc_fee)
                    
                    <tr>
                        <td>DOC FEE</td>
                        <td>: Rp. {{number_format($JobOrder->doc_fee,2)}}</td>
    
                    </tr>
                @endif
                <tr>
                    <td>SUB TOTAL</td>
                    <td>: Rp. {{number_format($TotalBiayaRev,2)}}</td>
    
                </tr>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    
        <table  >
            <thead>
                <tr>
                    <th colspan="2">Biaya Saat Dooring</th>
                </tr>
            </thead>
            <tbody > 
                @if ($JobOrder->thc)
                    
                <tr>
                    <td>STORAGE</td>
                    <td>: Rp. {{number_format($JobOrder->thc,2) }}</td>
    
                </tr>
                @endif
                @if ($JobOrder->lolo)
                    <tr>
                        <td>DEMURAGE</td>
                        <td>: Rp. {{number_format($JobOrder->lolo,2)}}</td>
                    </tr>
                @endif
                @if ($JobOrder->apbs)
                    <tr>
                        <td>DETENTION</td>
                        <td>: Rp. {{number_format($JobOrder->apbs,2)}}</td>
                    </tr>
                @endif
                @if ($JobOrder->cleaning)
                    <tr>
                        <td>REPAIR</td>
                        <td>: Rp. {{number_format($JobOrder->cleaning,2)}}</td>
                    </tr>
                @endif
                @if ($JobOrder->doc_fee)
                    <tr>
                        <td>WASHING</td>
                        <td>: Rp. {{number_format($JobOrder->doc_fee,2)}}</td>
                    </tr>
                @endif
                <tr>
                    <td>SUB TOTAL</td>
                    <td>: Rp. {{number_format($TotalBiayaRev,2)}}</td>
                </tr>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
          
        @if($dataJaminan)
            <table  >
                    <thead>
                        {{-- <tr>
                            <th colspan="2">Biaya Sebelum Dooring</th>
                        </tr> --}}
                    </thead>
                    <tbody > 
                        @if($JobOrder->id == $dataJaminan->id_job_order)
                            <tr>
                                @php $total = $dataJaminan->nominal+$TotalBiayaRev @endphp
    
                                <td width='40%'>Biaya Jaminan</td>
                                <td>: Rp. {{number_format($dataJaminan->nominal,2) }}</td>
            
                            </tr>
                            <tr>
                                <td>Tanggal Jaminan</td>
                                <td>: {{\Carbon\Carbon::parse($JobOrder->tgl_bayar)->format('d-M-Y')}}</td>
                            </tr>
                            @endif
                        {{-- <tr>
                            <td><span style="opacity: 0%">SUB TOTAL</span></td>
                            <td><span style="opacity: 0%">: Rp. {{number_format($TotalBiayaRev,2)}}</span></td>
                        </tr> --}}
                    </tbody>
                    <tfoot>
                    </tfoot>
            </table>
        @endif

    </div>
     
        
<br/>

</body>

</html>