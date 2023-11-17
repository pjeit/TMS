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
        .table-bawah{
            float: left;
            font-family: Arial, sans-serif;
            font-size: 20px;
        }
        .border-table{
            width: 100%; /* Optional: Set table width */    
            border: 1px solid  #ccc; /* Border around the table */
             /* background-color: #ccc; */
        }
        /* .kontener{
            display: flex;
            justify-content: space-between;
        } */
        .align-left{
            text-align: left;
            font-weight: 10;   
        }
        .align-right{
            text-align: right;
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
       
    </style>
</head>
<body>
    {{-- <img src="{{ asset('img/LOGO_PJE.jpg') }}" alt=""> --}}
    {{-- <img src="{{ public_path("img/LOGO_PJE.jpg") }}" alt=""  width="100" height="100" style="filter: grayscale(100%)"> --}}
    {{-- <hr style=" border: 10px solid rgb(54, 78, 163);margin-top: -95px;"> --}}
    
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
    <h2 class="text" style="text-align: center;margin-top:-10px; background-color: rgb(54, 78, 163);color:aliceblue;">BILLING JO</h2>
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
                <td class="align-left text ">ID. Billing</td> 
                <td >: {{$JobOrder->no_jo}}</td>
            </tr>
            <tr>
                {{-- supplier --}}
                <td class="align-left text">Pelayaran</td>
                <td>:
                    @if($JobOrder->id_supplier == $dataSupplier->id)
                        {{$dataSupplier->nama}}
                    @endif
                </td>
            </tr>
            <tr>
                {{-- customer --}}
                <td class="align-left text">Pengirim</td> 
                <td >:
                    @foreach ($dataCustomer as $dc)
                        @if($JobOrder->id_customer == $dc->id)
                            {{$dc->nama}}
                        @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <td class="align-left text">No. BL</td>
                <td>: {{$JobOrder->no_bl}}</td>
            </tr>
           
            <tr>
                <td class="align-left text">Tanggal Sandar</td>
                <td>: {{\Carbon\Carbon::parse($JobOrder->tgl_sandar)->format('d-M-Y')}}</td>
            </tr>
            <tr>
                <td class="align-left text">Pelabuhan Muat</td>
                <td>: {{$JobOrder->pelabuhan_muat}}</td>
            </tr>
            <tr>
                <td class="align-left text">Pelabuhan Bongkar</td>
                <td>: {{$JobOrder->pelabuhan_bongkar}}</td>
            </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
    {{-- <h3 class="text">Container</h3>
    <table>
        <thead>
            <tr>
                <td>No. Container</td>
                <td>No Pol Kendaraan</td>
                <td>Tipe Container</td>
                <td>Test tambah</td>
                <td>Test asdsad</td>

            </tr>
        </thead>
        <tbody> 
            @foreach ($dataJoDetail as $Jod)
                @if($JobOrder->id == $Jod->id_jo)
                <tr>
                    <td class="align-left text">{{$Jod->no_kontainer}}</td>
                    <td class="align-left text">{{$Jod->nopol_kendaraan}}</td>
                    <td class="align-left text">{{$Jod->tipe_kontainer}} ft</td>
                <td>Test tambah</td>
                <td>Test asdas</td>


                </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
        </tfoot>
    </table> --}}

    <h3 class="text" style="text-align: center;background-color: rgb(54, 78, 163);color:aliceblue;">Biaya pelayaran</h3>
    <table class="border-table td-atas"  id="sortable" >
        <thead>
            {{-- <tr>
                <th colspan="2">Biaya Sebelum Dooring</th>
            </tr> --}}
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
        @php
            $total = 0;
        @endphp
        @if($dataJaminan)
        
            <h3 class="text" style="margin-top: 1rem;text-align: center;background-color: rgb(54, 78, 163); color:aliceblue;" >Biaya Jaminan</h3>
            <table class="border-table td-atas"  id="sortable" >
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
            {{-- <table class="border-table td-atas" id="sortable" >
                <thead>
                    <tr>
                        <th><span style="opacity: 0%">Biaya Sebelum Dooring</span> </th>
                        <th><span style="opacity: 0%">Biaya Sebelum Dooring</span> </th>
                    </tr>
                </thead>
                <tbody > 
                        @if($JobOrder->id == $dataJaminan->id_job_order)
                        <tr>
                            @php $total = $dataJaminan->nominal+$TotalBiayaRev @endphp

                            <td style="border: 1px solid black">THX</td>
                            <td style="border: 1px solid black">: Rp. {{number_format($dataJaminan->nominal,2) }}</td>
        
                        </tr>
                        <tr>
                            <td style="border: 1px solid black">ASD</td>
                            <td style="border: 1px solid black">: {{\Carbon\Carbon::parse($JobOrder->tgl_bayar)->format('d-M-Y')}}</td>
                        </tr>
                        @endif
                    
                </tbody>
                <tfoot>
                </tfoot>
            </table> --}}
             <p class="text">Total Biaya : Rp. {{number_format($total,2) }}</p>
        @else
             @php $total = $TotalBiayaRev @endphp
              <p class="text">Total Biaya : Rp. {{number_format($total,2) }}</p>
        @endif
        @if($JobOrder->id_supplier == $dataSupplier->id)
            <p class="text-kecil">Biaya Pelayaran, dan Jaminan dapat di transfer ke rekening <b>{{$dataSupplier->bank}} </b><br> 
            atas nama : <b>{{$dataSupplier->rek_nama}} </b><br>
            dengan nomor {{$dataSupplier->is_virtual_acc == "Y"?'virtual account':'rekening'}} : <b><u>{{$dataSupplier->no_rek}}</u></b></p>
        @endif
        {{-- <div style="display: flex; justify-content: space-between;">
            <div style="flex-basis: 49%;">
                <p style="text-align: left;">Finance,</p>
                <br/>
                <p style="text-align: left;">(.........................)</p>
            </div>

            <div style="flex-basis: 49%;">
                <p style="text-align: right;">keterangan,</p>
                <br/>
                <br/>
                <p style="text-align: right;">(.........................)</p>
            </div>
        </div> --}}
<br/>
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