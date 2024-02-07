
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
    
    <div class="container-fluid">
        <form action="{{ route('pembayaran_sdt.update',[$data['JO']->id]) }}" id="post_data" method="POST" >
            @csrf @method('PUT')
    
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{ route('pembayaran_sdt.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                </div>
                <div class="card-body" >
                    <div class="col-12">
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="">No. JO<span class="text-red">*</span></label>
                                <input  type="hidden" class="form-control" name="id_jo" value="{{$data['JO']->id}}" >
                                <input  type="text" class="form-control" value="{{$data['JO']->no_jo}}" readonly>
                            </div>  
                            <div class="form-group col-6">
                                <label for="">No. BL<span class="text-red">*</span></label>
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
                                    <th scope="col" style="text-align: center;">Repair</th>
                                    <th scope="col" style="text-align: center;">Washing</th>

                                </tr>
                            </thead>
                            <tbody>
                                @isset($data['biaya'])
                                    @php
                                        $t_storage = 0;
                                        $t_demurage = 0;
                                        $t_detention = 0;
                                        $t_repair = 0;
                                        $t_washing = 0;
                                    @endphp
                                    @foreach ($data['biaya'] as $key => $item)
                                    <tr>
                                        <input type="hidden" name="array_id[{{$item->id}}]" value="{{$item->id}}">
                                        <input type="hidden" name="no_kontainer[]" value="{{$item->no_kontainer}}">

                                        <th style="text-align: center;" scope="row">{{$key+1}}</th>
                                        <td style="text-align: center;">{{$item->no_kontainer}}</td>
                                        <td style="text-align: right;">{{ number_format($item->storage) }}</td>
                                        <td style="text-align: right;">{{ number_format($item->demurage) }}</td>
                                        <td style="text-align: right;">{{ number_format($item->detention) }}</td>
                                        <td style="text-align: right;">{{ number_format($item->repair) }}</td>
                                        <td style="text-align: right;">{{ number_format($item->washing) }}</td>


                                    </tr>
                                    @php
                                        $t_storage += $item->storage;
                                        $t_demurage += $item->demurage;
                                        $t_detention += $item->detention;
                                        $t_repair += $item->repair;
                                        $t_washing += $item->washing;
                                        $t_all = $t_storage+$t_demurage+$t_detention+$t_repair+$t_washing;
                                    @endphp
                                    @endforeach
                                @endisset
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="pl-4">Total</th>
                                    <th style="text-align: right;">
                                        {{ number_format($t_storage) }}
                                        <input type="hidden" name="total_storage" value="{{$t_storage}}">
                                    </th>
                                    <th style="text-align: right;">
                                        {{ number_format($t_demurage) }}
                                        <input type="hidden" name="total_demurage" value="{{$t_demurage}}">
                                    </th>
                                    <th style="text-align: right;">
                                        {{ number_format($t_detention) }}
                                        <input type="hidden" name="total_detention" value="{{$t_detention}}">
                                    </th>
                                    <th style="text-align: right;">
                                        {{ number_format($t_repair) }}
                                        <input type="hidden" name="total_repair" value="{{$t_repair}}">
                                    </th>
                                    <th style="text-align: right;">
                                        {{ number_format($t_washing) }}
                                        <input type="hidden" name="total_washing" value="{{$t_washing}}">
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="pl-4">Grand Total</th>
                                    <th colspan="5"> <span class="float-right">{{ number_format($t_all) }}</span></th>
                                    <input type="hidden" name="total" id="total" value="{{$t_all}}">
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                        <label>Tanggal Bayar</label>
                        <div class="input-group mb-0 ">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" name="tgl_bayar" class="date form-control" id="tgl_bayar" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12">
                        {{-- <h4 class="d-flex justify-content-between align-items-center mb-3"> --}}
                            {{-- <span class="badge bg-primary rounded-pill">3</span> --}}
                        {{-- </h4> --}}
                        
                        <ul class="list-group mb-3">
                            <div class="list-group-item">
                                <div class="row">
                                    <div class="col-2 mt-2">
                                        <p class="text-primary "><strong>PILIH PEMBAYARAN</strong></p>
                                        <label for=""></label>
                                        <p class="text-primary "><strong>CATATAN</strong></p>
                                    </div>
                                    <div class="col-10">
                                            <div class="input-group" style="gap: 5px;">
                                                <select class="form-control select2"  id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                                    <option value="">--PILIH KAS--</option>
                                                    @foreach ($dataKas as $kas)
                                                        <option value="{{$kas->id}}" {{$kas->id == '1'? 'selected':''}} >{{ $kas->nama }}</option>
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
        </form>
    </div>

<script type="text/javascript">
    $(document).ready(function() {
       // Get today's date
        var today = new Date();

        // Format the date as "dd-M-yyyy"
        var formattedDate = today.getDate() + '-' + (today.getMonth() + 1) + '-' + today.getFullYear();

        // Set the default date for the date picker
        $('#tgl_bayar').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language: 'en',
            orientation: 'bottom auto',
            endDate: today
        }).datepicker('setDate', formattedDate);
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
                    this.submit();
                }else{
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top',
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


