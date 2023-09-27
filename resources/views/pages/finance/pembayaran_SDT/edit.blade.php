
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
  
@endsection

@section('content')
<style >
   .tinggi{
    height: 20px;
   }
</style>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $error }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endforeach
    @endif
    <form action="{{ route('pembayaran_sdt.update',[$data['JO']->id]) }}" id="post_data" method="POST" >
      @csrf
        @method('PUT')

        <div class="row m-2">
        
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('pembayaran_sdt.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    </div>
                    <div class="card-body" >
                        <div class="col-12">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="">No. JO <span class="text-red">*</span></label>
                                    <input  type="text" class="form-control" value="{{$data['JO']->no_jo}}" readonly>
                                </div>  
                                <div class="form-group col-6">
                                    <label for="">No. BL <span class="text-red">*</span></label>
                                    <input  type="text" class="form-control" name="no_bl" value="{{$data['JO']->no_bl}}" readonly>
                                </div>  
                                <div class="form-group col-6">
                                    <label for="">Pengirim<span class="text-red">*</span></label>
                                    <input type="text" class="form-control" name="pengiriman"value="{{$data['JO']->getCustomer->kode}} - {{$data['JO']->getCustomer->nama}}" readonly>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Pelayaran</label>
                                    <input type="text" class="form-control" name="pelayaran"value="{{$data['JO']->getSupplier->nama}}" readonly>
                                </div>

                            </div>
                        </div>
                        <hr>
                        <div class="col-12">
                            <table class="table table-hover table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align: center;">#</th>
                                        <th scope="col" style="text-align: center;">No Container</th>
                                        <th scope="col" style="text-align: center;">Storage</th>
                                        <th scope="col" style="text-align: center;">Demurage</th>
                                        <th scope="col" style="text-align: center;">Detention</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($data['biaya'])
                                        @php
                                            $t_storage = 0;
                                            $t_demurage = 0;
                                            $t_detention = 0;
                                        @endphp
                                        @foreach ($data['biaya'] as $key => $item)
                                        <tr>
                                            <input type="hidden" name="array_id[{{$item->id}}]" value="{{$item->id}}">
                                            <input type="hidden" name="no_kontainer[]" value="{{$item->no_kontainer}}">

                                            <th style="text-align: center;" scope="row">{{$key+1}}</th>
                                            <td style="text-align: center;">{{$item->no_kontainer}}</td>
                                            <td style="text-align: right;">{{ number_format($item->storage, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($item->demurage, 2) }}</td>
                                            <td style="text-align: right;">{{ number_format($item->detention, 2) }}</td>
                                        </tr>
                                        @php
                                            $t_storage += $item->storage;
                                            $t_demurage += $item->demurage;
                                            $t_detention += $item->detention;
                                            $t_all = $t_storage+$t_demurage+$t_detention;
                                        @endphp
                                        @endforeach
                                    @endisset
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="pl-4">Total</th>
                                        <th style="text-align: right;">{{ number_format($t_storage, 2) }}</th>
                                        <th style="text-align: right;">{{ number_format($t_demurage, 2) }}</th>
                                        <th style="text-align: right;">{{ number_format($t_detention, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="pl-4">Grand Total</th>
                                        <th colspan="3"> <span class="float-right">{{ number_format($t_all, 2) }}</span></th>
                                        <input type="hidden" name="total" id="total" value="{{$t_all}}">
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                        <div class="col-12">
                            {{-- <h4 class="d-flex justify-content-between align-items-center mb-3"> --}}
                                {{-- <span class="badge bg-primary rounded-pill">3</span> --}}
                            {{-- </h4> --}}
                            <ul class="list-group mb-3">
                                <div class="list-group-item">
                                    <div class="row">
                                        <div class="col-2 mt-2">
                                            <p class="text-primary "><strong>PILIH PEMBAYARAN :</strong></p>
                                            <label for=""></label>
                                            <p class="text-primary "><strong>CATATAN :</strong></p>
                                        </div>
                                        <div class="col-10">
                                             <div class="input-group" style="gap: 5px;">
                                                    <select class="form-control select2"  id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                                        <option value="">--PILIH KAS--</option>
                                                        @foreach ($dataKas as $kas)
                                                            <option value="{{$kas->id}}">{{ $kas->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                <button type="submit" class="btn btn-success"><i class="fa fa-credit-card" aria-hidden="true"></i> Bayar</button>
                                             </div>
                                             <div class="form-group">
                                                <label for="catatan"></label>
                                                <input type="text" id="catatan" name="catatan" class="form-control" value="">                         
                                            </div>  
                                        </div>
                                      
                                    </div>
                                </div>
                            </ul>
                           
                        </div>
                    </div>
                </div> 
            </div>
        
        </div>
 
    </form>
<script type="text/javascript">
    $(document).ready(function() {
        $('#post_data').submit(function(event) {
            var kas = $('#pembayaran').val();
            if (kas == '' || kas == null) {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: 'error',
                    text: 'KAS PEMBAYARAN WAJIB DIPILIH!',
                })
                return;
            }
            event.preventDefault();

            Swal.fire({
                title: 'Apakah Anda yakin data sudah benar ?',
                text: "Periksa kembali data anda",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'success',
                        title: 'Data Disimpan'
                    })

                    setTimeout(() => {
                        this.submit();
                    }, 800); // 2000 milliseconds = 2 seconds
                }else{
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'warning',
                        title: 'Batal Disimpan'
                    })
                    event.preventDefault();
                }
            })
        });
    });
</script>

@endsection


