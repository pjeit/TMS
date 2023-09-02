

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
   .table-bawah{
    float: left;
      font-family: Arial, sans-serif;
      font-size: 20px;
   }
    .border-table{
        width: 100%; /* Optional: Set table width */    
        border: 1px solid #00000; /* Border around the table */
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
    <div class="kontener">
         <h2 class="text">BILLING JO</h2>
    </div>
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
                <td >:</td>

                <td class="aligh-right" style="margin-left: 20px;">
                    {{$JobOrder->no_jo}}
                </td>
            </tr>
            <tr>
                {{-- supplier --}}
                <td class="align-left text">Pelayaran</td>
                <td>:</td>
                <td class="aligh-right">
                    @foreach ($dataSupplier as $ds)
                        @if($JobOrder->id_supplier == $ds->id)
                            {{$ds->nama}}
                        @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                {{-- customer --}}
                <td class="align-left text">Pengirim</td> 
                <td >:</td>

                <td class="aligh-right" style="margin-left: 20px;">
                    @foreach ($dataCustomer as $dc)
                        @if($JobOrder->id_customer == $dc->id)
                            {{$dc->nama}}
                        @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <td class="align-left text">No. BL</td>
                <td>:</td>

                <td class="aligh-right">{{$JobOrder->no_bl}}</td>
            </tr>
           
            <tr>
                <td class="align-left text">Tanggal Sandar</td>
                <td>:</td>

                <td class="aligh-right">{{\Carbon\Carbon::parse($JobOrder->tgl_sandar)->format('d-M-Y')}}</td>
            </tr>
            <tr>
                <td class="align-left text">Pelabuhan Muat</td>
                <td>:</td>

                <td class="aligh-right">{{$JobOrder->pelabuhan_muat}}</td>
            </tr>
            <tr>
                <td class="align-left text">Pelabuhan Bongkar</td>
                <td>:</td>
                <td class="aligh-right">{{$JobOrder->pelabuhan_bongkar}}</td>
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

    <h3 class="text">Biaya pelayaran</h3>
      <table class="border-table td-atas"  id="sortable" >
            <thead>
                {{-- <tr>
                    <th colspan="2">Biaya Sebelum Dooring</th>
                </tr> --}}
            </thead>
            <tbody > 
                <tr>
                    <td>THC</td>
                    <td>:</td>
                    <td class="aligh-right">Rp. {{number_format($JobOrder->total_thc,2) }}</td>

                </tr>
                <tr>
                    <td>LOLO</td>
                    <td>:</td>
                    <td class="aligh-right">Rp. {{number_format($JobOrder->total_lolo,2)}}</td>

                </tr>
                <tr>
                    <td>APBS</td>
                    <td>:</td>
                    <td class="aligh-right">Rp. {{number_format($JobOrder->total_apbs,2)}}</td>

                </tr>
                <tr>
                    <td>CLEANING</td>
                    <td>:</td>
                    <td class="aligh-right">Rp. {{number_format($JobOrder->total_cleaning,2)}}</td>

                </tr>
                <tr>
                    <td>DOC FEE</td>
                    <td>:</td>
                    <td class="aligh-right">Rp. {{number_format($JobOrder->doc_fee,2)}}</td>

                </tr>
                <tr>
                    <td>SUB TOTAL</td>
                    <td>:</td>
                    <td class="aligh-right">Rp. {{number_format($JobOrder->total_biaya_sebelum_dooring,2)}}</td>

                </tr>
            </tbody>
            <tfoot>
            </tfoot>
      </table>
    
    <h3 class="text" style="margin-top: 1rem;">Biaya Jaminan</h3>

         <table class="border-table td-atas" id="sortable" >
            <thead>
                {{-- <tr>
                    <th colspan="2">Biaya Sebelum Dooring</th>
                </tr> --}}
            </thead>
            <tbody > 
            @foreach ($dataJaminan as $dJ)
                @if($JobOrder->id == $dJ->id_job_order)
                <tr>
                    <td>nominal</td>
                    <td>:</td>
                    <?php $nominal = $dJ->nominal ?>
                    <?php $total = $dJ->nominal+$JobOrder->total_biaya_sebelum_dooring ?>
                    <td class="aligh-right">Rp. {{number_format($dJ->nominal,2) }}</td>

                </tr>
                <tr>
                    <td>Tanggal Bayar</td>
                    <td>:</td>
                    <td class="aligh-right">{{\Carbon\Carbon::parse($JobOrder->tgl_bayar)->format('d-M-Y')}}</td>
                </tr>
                @endif
            @endforeach
             
            </tbody>
            <tfoot>
            </tfoot>
      </table>
      <p class="text">Total Biaya : Rp. {{number_format($total,2) }}</p>
          @foreach ($dataSupplier as $ds)
            @if($JobOrder->id_supplier == $ds->id)

         <p class="text-kecil">Biaya Pelayaran, dan Jaminan dapat di transfer ke rekening <b>{{$ds->bank}} </b><br> 
            atas nama : <b>{{$ds->rek_nama}} </b><br>
            dengan nomor rekening : <b><u>{{$ds->no_rek}}</u></b></p>

               
            @endif
        @endforeach
      
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
                <td style="text-align: left; ">(.........................)</td> 
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