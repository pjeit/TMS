{{-- @extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')

@endsection

@section('content')
@include('sweetalert::alert')
--}}
<title>Primatrans Jaya Express</title>

<style>
#preview_foto_nota {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}

#nambah_height {
height:100px;
}
</style> 
<div id='printable'>
            <table class='table_cetak' style='border:solid 1px black;width:600px;height:600px;border-collapse: collapse;'>
                <tr >
                    <td colspan='3' style='text-align:center ;' id="nambah_height"><b style='font-size:16pt'>VOUCHER DRIVER</b></td>
                </tr>
                <tr style='border-top: solid 1px lightgray;'>
                    <td style='padding-left:2.5px;height:100px;'id="nambah_height">&nbsp;Tanggal</td>
                    <td>:</td>
                    <td style='padding-right:2.5px;'>{{\Carbon\Carbon::parse($data_uang_jalan->tanggal_pencatatan)->format('d-M-Y')}}</td>
                </tr>
                <tr style='border-top: solid 1px lightgray;'>
                    <td style='padding-left:2.5px;'id="nambah_height">&nbsp;No. Voucher</td>
                    <td>:</td>
                    <td>{{ $data_uang_jalan->no_sewa }}</td>
                </tr>
                <tr style='border-top: solid 1px lightgray;'>
                    <td style='padding-left:2.5px;'id="nambah_height">&nbsp;Customer</td>
                    <td>:</td>
                    <td>{{$data_uang_jalan->nama_tujuan}}({{ $data_uang_jalan->nama_cust}})</td>
                </tr>
                <tr style='border-top: solid 1px lightgray;'>
                    <td style='padding-left:2.5px;'id="nambah_height">&nbsp;No. Polisi</td>
                    <td>:</td>
                    <td> {{$data_uang_jalan->no_polisi}}  ({{ $data_uang_jalan->supir}} )</td>
                </tr>
                @if (isset($data_sewa_biaya))
                        @foreach ($data_sewa_biaya as $item)
                        <tr style='border-top: solid 1px lightgray;'>
                            <td style='padding-left:2.5px;'id="nambah_height">{{$item->deskripsi}}</td>
                            <td>:</td>
                            <td>Rp. {{number_format($item->biaya,2)  }}</td>
                        </tr>
                        @endforeach
                    @endif
                
                <tr style='border-top: solid 1px lightgray;'>
                    <td style='padding-left:2.5px;'id="nambah_height">&nbsp;Potongan</td>
                    <td>:</td>
                    <td>Rp. {{number_format($data_uang_jalan->potong_hutang,2)  }}</td>
                </tr>
                <tr style='border-top: solid 1px lightgray;'>
                    <td style='padding-left:2.5px;'id="nambah_height">&nbsp;Diterima</td>
                    <td style='border-top: solid 2px black'>:</td>
                    <td style='border-top: solid 2px black'>Rp. {{number_format(($data_uang_jalan->total_uang_jalan+$data_uang_jalan->total_tl) - $data_uang_jalan->potong_hutang,2)  }}</td>
                </tr>
                <tr>
                    <td style='height:48px;padding:0px' colspan='3'>
                        <table style='width:100%;border-collapse: collapse;'>
                            <tr>
                                <td style="width:149px;border: solid 2px black;"id="nambah_height">
                                    &nbsp;
                                </td>
                                <td style="width:149px;border: solid 2px black;"id="nambah_height">
                                    &nbsp;
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <br>
        {{-- <div>
            <button class='btn btn-default' id="print" onclick='printDiv();' style='width:298px'>Cetak</button>
        </div> --}}
<script type='text/javascript'>
function printDiv() 
{

  var divToPrint=document.getElementById('printable');

  var newWin=window.open('','Print-Window');

  newWin.document.open();

  newWin.document.write('<html><style>.table_cetak td{padding:1.5px;}</style><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

  newWin.document.close();

  setTimeout(function(){newWin.close();},10);

}
</script>
{{-- @endsection --}}





<!--Tgl             = tanggal-->
<!--No. Voucher     = no_sewa-->
<!--Exportir        = nama_customer-->
<!--Bongkar/muat    = nama_tujuan-->
<!--No. Lambung     = no_polisi-->
<!--Driver          = panggilan_driver-->

<!--Urutan detail uang-->
<!--1. Deskripsi        = deskripsi & biaya-->
<!--2. Total            = total_uang_jalan-->
<!--3. Potongan         = potong_hutang-->
<!--4. Total Diterima   = total_diterima-->


