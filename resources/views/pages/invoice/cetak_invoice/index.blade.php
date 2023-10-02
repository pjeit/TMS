@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('content')
@include('sweetalert::alert')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>
#button{
  display:block;
  margin:20px auto;
  padding:10px 30px;
  background-color:#eee;
  border:solid #ccc 1px;
  cursor: pointer;
}
#overlay{	
  position: fixed;
  top: 0;
  z-index: 100;
  width: 100%;
  height:100%;
  /* display: none; */
  background: rgba(0,0,0,0.6);
}
.cv-spinner {
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;  
}
.loader {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  display: block;
  margin:15px auto;
  position: relative;
  background: #FFF;
  box-shadow: -24px 0 #FFF, 24px 0 #FFF;
  box-sizing: border-box;
  animation: shadowPulse 2s linear infinite;
}

@keyframes shadowPulse {
  33% {
    background: #FFF;
    box-shadow: -24px 0 #2631ff, 24px 0 #FFF;
  }
  66% {
    background: #2631ff;
    box-shadow: -24px 0 #FFF, 24px 0 #FFF;
  }
  100% {
    background: #FFF;
    box-shadow: -24px 0 #FFF, 24px 0 #2631ff;
  }
}

</style>

<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <div class="">
                        {{-- <a href="{{ route("invoice.create") }}" class="btn btn-primary btn-responsive radiusSendiri"  id="sewaAdd">
                            <i class="fa fa-plus-circle" aria-hidden="true"> </i> Buat Invoice
                        </a>  --}}
                          <button type="submit" class="btn btn-primary btn-responsive radiusSendiri" id="sewaAdd">
                             <i class="fa fa-plus-circle" aria-hidden="true"></i> Buat Invoice
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tabelInvoiceCetak" class="table table-bordered" width='100%'>
                        <thead>
                            <tr>
                                <th>Grup</th>
                                <th>Customer</th>
                                <th>No. Polisi Kendaraan</th>
                                <th>No. Sewa</th>
                                <th>Tgl Berangkat</th>
                                <th>Tujuan</th>
                                <th>Driver</th>
                                <th></th>
                                {{-- <th>Status</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($dataSewa))
                                @foreach($dataSewa as $item)
                                    <tr>
                                        <td >{{ $item->nama_grup }} <span class="float-right"><input type="checkbox" style="margin-right: 0.9rem;" class="grup_centang" id_grup="{{ $item->id_grup }}"></span> </td>
                                        <td >{{ $item->nama_cust }} <span class="float-right"><input type="checkbox" style="margin-right: 0.9rem;" class="customer_centang" id_customer="{{ $item->id_customer }}" id_customer_grup="{{ $item->id_grup }}"></span> </td>
                                        <td>
                                            {{ $item->no_polisi }}
                                        </td>
                                        <td>{{ $item->no_sewa }}</td>
                                        <td>{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }}</td>
                                        <td>{{ $item->nama_tujuan }}</td>
                                        <td>{{ $item->supir }} ({{ $item->telpSupir }})
                                            <div class="btn-group dropleft float-right">
                                                <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                                <div class="dropdown-menu" >
                                                    <form action="{{route('invoiceKembali.set')}}" method="POST" class="btn btn-responsive">
                                                                @csrf
                                                                <button class="dropdown-item" >
                                                                    <span class="fas fa-reply" style="width:24px"></span>Kembalikan ke Admin
                                                                </button>
                                                                <input type="hidden" name="idCust[]" placeholder="idCust">
                                                                <input type="hidden" name="idGrup[]" placeholder="idGrup">
                                                                <input type="hidden" name="idSewa" value="{{$item->id_sewa}}">
                                                                <input type="hidden" name="idJo" value="{{$item->id_jo}}">
                                                                <input type="hidden" name="idJo_detail" value="{{$item->id_jo_detail}}">
                                                    </form>  
                                                    {{-- <a class="dropdown-item" href="{{route('perjalanan_kembali.edit',[$item->id_sewa])}}"><span class="fas fa-reply" style="width:24px"></span>Kembalikan ke Admin</a> --}}
                                                    {{-- <a class="dropdown-item" href="{{route('invoiceKembali.set')}}"><span class="fas fa-reply" style="width:24px"></span>Kembalikan ke Admin</a>
                                                    <input type="hidden" name="idCust[]" placeholder="idCust">
                                                    <input type="hidden" name="idGrup[]" placeholder="idGrup">
                                                    <input type="hidden" name="idSewa" value="{{$item->id_sewa}}">
                                                    <input type="hidden" name="idJo" value="{{$item->id_jo}}">
                                                    <input type="hidden" name="idJo_detail" value="{{$item->id_jo_detail}}"> --}}


                                                </div>
                                            </div>
                                        </td>
                                        {{-- <td>{{ $item->status }}</td> --}}
                                        <td style="text-align: center;"> <input type="checkbox" name="idSewa[]" class="sewa_centang" custId="{{ $item->id_customer }}" grupId="{{ $item->id_grup }}" value="{{ $item->idSewanya }}"></td>
                                        
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal loading --}}
<div class="modal" id="modal-loading" data-backdrop="static">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body text-center">
        <div class="cv-spinner">
            <span class="loader"></span>
         </div>
         <div>Harap Tunggu Sistem Sedang Memproses....</div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
 $(document).ready(function () {
      
     new DataTable('#tabelInvoiceCetak', {
        order: [
            [0, 'asc'],
            [1, 'asc']
        ],
        rowGroup: {
            dataSrc: [0, 1]
        },
        columnDefs: [
            {
                targets: [0, 1],
                visible: false
            },
            {
                "orderable": false,
                "targets": [0,1,2,3,4,5,6,7]
            }
       
        ],
    });

});

</script>



@endsection
