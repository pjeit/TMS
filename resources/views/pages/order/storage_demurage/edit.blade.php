
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
  
@endsection

@section('content')
<style>
    .card-header:first-child{
        border-radius:inherit;
    }
    /* .tabelJO {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #ddd;
    } */
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
    <form action="{{ route('storage_demurage.update', ['storage_demurage' => $data['detail'] ]) }}" id='send' method="POST" >
        @method('PUT')
        @csrf
        <div class="row m-2">
            <div class="col-12 radiusSendiri sticky-top " style="margin-bottom: -15px;">
                <div class="card radiusSendiri" style="">
                    <div class="card-header ">
                        <a href="{{ route('storage_demurage.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>

                        <button type="button" name="add" id="add" class="btn btn-primary radiusSendiri float-right"><i class="fa fa-plus-circle"></i> <strong >Tambah Data</strong></button> 
                    </div>
                </div>
            </div>
             <div class="col-12">
                <div class="card radiusSendiri">
                    {{-- <div class="card-header">
                        <a href="{{ route('job_order.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" id='submitButton' class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    </div> --}}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4" >
                                <div class="form-group" style="" >
                                    <label for="">Pengirim</label>
                                    <input type="text" value="{{$data['JO']->getCustomer->nama}}" class="form-control" disabled>
                                    <input type="hidden" value="{{$data['JO']->id}}" class="form-control" name="id_jo">
                                    <input type="hidden" value="{{$data['detail']->id}}" class="form-control" name="id_jo_detail">
                                </div>
                            </div>
                            <div class="col-4" >
                                <div class="form-group" style="pointer-events: none;" >
                                    <label for="">Pelayaran</label>
                                    <input type="text" value="{{$data['JO']->getSupplier->nama}}" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group ">
                                    <label for="">No. BL</label>
                                    <input required type="text" name="no_bl" class="form-control" value="{{$data['JO']->no_bl}}" readonly disabled >
                                </div>           
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label for="">No. Kontainer</label>
                                    <input required type="text" name="no_bl" class="form-control" value="{{$data['detail']->no_kontainer}}" readonly disabled >
                                </div>           
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label for="">Seal</label>
                                    <input required type="text" name="no_bl" class="form-control" value="{{$data['detail']->seal}}" readonly disabled >
                                </div>           
                            </div>
                            <div class="col-md-4">
                                <div class="form-group ">
                                    <label for="">Tujuan</label>
                                    <input required type="text" name="no_bl" class="form-control" value="{{isset($data['detail']->getTujuan->id)? $data['detail']->getTujuan->id:null}}" readonly disabled >
                                </div>           
                            </div>
                        </div>
                        <hr>

                        <div class="table-responsive p-0">
                            <form name="add_name" id="add_name">
                                <table class="table table-hover table-bordered table-striped text-nowrap" id="tabel">
                                    <thead>
                                        <tr class="">
                                            <th style="">Storage</th>
                                            <th style="">Demurage</th>
                                            <th style="">Detention</th>
                                            <th style="">Repair</th>
                                            <th style="">Washing</th>
                                            <th style="">Status</th>
                                            <th style="width:30px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($data['biaya']))
                                            @foreach ($data['biaya'] as $key => $item)
                                            <tr id="row_{{$key}}">
                                                <td style="width: 15%">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp.</span>
                                                        </div>
                                                        <input type="text" class="form-control numaja uang" value="{{ number_format($item->storage, 2) }}" disabled>
                                                    </div>
                                                </td>
                                                <td style="width: 15%">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp.</span>
                                                        </div>
                                                        <input type="text" class="form-control numaja uang" value="{{ number_format($item->demurage, 2) }}" disabled>
                                                    </div>
                                                </td>
                                                <td style="width: 15%">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp.</span>
                                                        </div>
                                                        <input type="text" class="form-control numaja uang" value="{{ number_format($item->detention, 2) }}" disabled>
                                                    </div>
                                                </td>
                                                <td style="width: 15%">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp.</span>
                                                        </div>
                                                        <input type="text" class="form-control numaja uang" value="{{ number_format($item->repair, 2) }}" disabled>
                                                    </div>
                                                </td>
                                                 <td style="width: 15%">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp.</span>
                                                        </div>
                                                        <input type="text" class="form-control numaja uang" value="{{ number_format($item->washing, 2) }}" disabled>
                                                    </div>
                                                </td>
                                                <td style="width: 50%" >
                                                    <input type="text" class="form-control" value="{{$item->status_bayar}}" disabled>
                                                </td>
                                                <td style="width: 5%"></td>
                                            </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </form>
                    </div>

                        <hr>

                    </div>
                </div> 
            </div>
            
          
        </div>
    </form>

<script type="text/javascript">
    $(document).ready(function() {

        // logic save
        $( document ).on( 'click', '#submitButton', function (event) {
            event.preventDefault();
            // pop up confirmation
                Swal.fire({
                    title: 'Apakah Anda yakin data sudah benar?',
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
                        // const Toast = Swal.mixin({
                        //     toast: true,
                        //     position: 'top-end',
                        //     timer: 2500,
                        //     showConfirmButton: false,
                        //     timerProgressBar: true,
                        //     didOpen: (toast) => {
                        //         toast.addEventListener('mouseenter', Swal.stopTimer)
                        //         toast.addEventListener('mouseleave', Swal.resumeTimer)
                        //     }
                        // })

                        // Toast.fire({
                        //     icon: 'success',
                        //     title: 'Data Disimpan'
                        // })

                        // form.submit();
                        $("#send").submit();
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
                        // return;
                    }
                })
            // pop up confirmation
        });

        $("#add").click(function(){
            var rows = document.querySelectorAll('tr[id^="row_"]');

            // Find the maximum ID number
            var maxIDRB = -1;
            for (var i = 0; i < rows.length; i++) {
                var idStrRB = rows[i].id.replace('row_', ''); // Extract the number part
                var idNumRB = parseInt(idStrRB); // Convert to number
                if (idNumRB > maxIDRB) {
                    maxIDRB = idNumRB;
                }
            }
            // Generate the last ID with the next number
            var lastIDRB = (maxIDRB + 1);

            if(lastIDRB != 0){
                var i = lastIDRB;
            }else{
                var i = 0;
            }

            $('#tabel > tbody:last-child').append(
            `
                <tr id="row_${i}">
                    <td style="text-align: center; vertical-align: middle;">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                            </div>
                            <input type="text" name="data[${i}][storage]" class="form-control numaja uang " id="storage${i}">
                        </div>
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <input type="hidden" name="biaya_id" id="biaya_id${i}" class="form-control"/>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                            </div>
                            <input type="text" name="data[${i}][demurage]" id="demurage${i}" class="form-control numaja uang " />
                        </div>
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                            </div>
                            <input type="text" name="data[${i}][detention]" id="detention${i}" class="form-control numaja uang"/>
                        </div>
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                            </div>
                            <input type="text" name="data[${i}][repair]" id="repair${i}" class="form-control numaja uang"/>
                        </div>
                    </td>
                      <td style="text-align: center; vertical-align: middle;">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp.</span>
                            </div>
                            <input type="text" name="data[${i}][washing]" id="washing${i}" class="form-control numaja uang"/>
                        </div>
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <div class="input-group mb-3">
                            <input type="text" id="status${i}" class="form-control"/ disabled>
                        </div>
                    </td>
                    <td>
                        <button type="button" name="del" id="${i}" class="btn btn-danger btn_remove"><i class="fa fa-trash" aria-hidden="true"></i></button>
                    </td></tr>);  
                </tr>
            `
            );

            $('input[type="text"]').on('input', function() {
                var inputValue = $(this).val();
                var uppercaseValue = inputValue.toUpperCase();
                $(this).val(uppercaseValue);
            });

        });

        $(document).on('click', '.btn_remove', function(){  
            var button_id = $(this).attr("id");

            // get id yg dihapus
            var row = $(this).closest("tr");

            $('#row_'+button_id+'').remove();  
        });

    });
</script>

@endsection


