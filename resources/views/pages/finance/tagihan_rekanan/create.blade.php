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
<section class="">
    <form action="{{ route('pembayaran_invoice.store') }}" id="save" method="POST" >
        @csrf
        <div class="col-12 radiusSendiri sticky-top " style="margin-bottom: -15px;">
            <div class="card radiusSendiri" style="">
                <div class="card-header radiusSendiri">
                    <a href="{{ route('pembayaran_invoice.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-body" >
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Supplier<span class="text-red">*</span> </label>
                                        <select name="supplier" class="select2" style="width: 100%" id="supplier" required>
                                            <option value="">── PILIH SUPPLIER ──</option>
                                            @foreach ($supplier as $item)
                                                <option value="{{ $item->getSupplier->id }}">{{ $item->getSupplier->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>  
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total Diterima</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_diterima" name="total_diterima" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total PPh 23</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" id="total_pph23" name="total_pph23" class="form-control uang" readonly>                         
                                        <input type="hidden" id="total_dibayar" name="total_dibayar" class="form-control uang" readonly>                         
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="">Tanggal Pembayaran<span style="color:red">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" autocomplete="off" name="tanggal_pembayaran" class="form-control date" id="tanggal_pembayaran" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Pilih Kas<span class="text-red">*</span> </label>
                                    <select name="kas" class="select2" style="width: 100%" id="kas" required>
                                        <option value="">── PILIH KAS ──</option>
                                        @foreach ($dataKas as $kas)
                                            <option value="{{ $kas->id }}" {{ $kas->id == 1? 'selected':''}}>{{ $kas->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Catatan</label>
                                    <input type="text" name="catatan" class="form-control">
                                    <input type="hidden" id="firstId" value="{{ isset($data)? $data[0]['id']:NULL }}">
                                    <input type="hidden" class="form-control" id="di_potong_admin" placeholder="di_potong_admin"> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>

        <div style="overflow: auto;" class="m-3">
            <table class="table table-hover table-bordered table-striped " width='100%' id="tabel_tagihan">
                <thead>
                    <tr>
                        <th>No. Sewa</th>
                        <th style="width: 200px;">Tarif</th>
                        <th style="width: 200px;">Ditagihkan</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="hasil">
              
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="modal_detail" tabindex='-1'>
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Detail Invoice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id='form_add_detail'>
                        <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}

                        <div class='row'>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="sewa">No. Invoice <span style="color:red;">*</span></label>
                                        <select class="select2" style="width: 100%" id="modal_no_invoice" disabled>
                                            <option value="">── Pilih Invoice ──</option>
                                        </select>
                                    </div>   
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Catatan</label>
                                        <textarea name="modal_catatan" class="form-control" id="modal_catatan" rows="4"></textarea>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="tarif">Total Tagihan</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="modal_total_invoice" placeholder="" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">Sisa Invoice</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="modal_sisa_invoice" placeholder="" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">PPh 23</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="modal_pph23" placeholder="" >
                                            <input type="hidden" class="form-control numaja uang" id="modal_dibayar" placeholder="" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="tarif">Diterima<span class="text-red">*</span> </label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="modal_diterima">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
                    <button type="button" class="btn btn-sm btn-success save_detail" id="" style='width:85px'>OK</button> 
                </div>
            </div>
            </div>
        </div>
    </form>
</section>

{{-- logic save --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('#save').submit(function(event) {
            // cek total_dibayar
                var total_dibayar = $('#total_dibayar').val();
                console.log('total_dibayar', total_dibayar);
                if(escapeComma(total_dibayar) == 0 || escapeComma(total_dibayar) == ''){
                    Swal.fire(
                        'Data tidak valid',
                        'Total bayar masih 0, harap periksa kembali data anda!',
                        'warning'
                    )
                    return false;
                }
            //

            event.preventDefault(); // Prevent form submission
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

<script type="text/javascript">
    $(document).ready(function() {

        $(document).on('change', '#supplier', function(){
            if(this.value != null){
                showTable(this.value);
                console.log('this.value', this.value);
            }
        });

        function showTable(supplier){
            $.ajax({
                method: 'GET',
                url: `loadData/${supplier}`,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    var data = response;
                    console.log('data', data);
                    $('#tabel_tagihan').DataTable().clear().destroy();

 
                    for (var i = 0; i < data.length; i++) {
                        var row = $("<tr></tr>");
                        row.append(`<td>${data[i].no_sewa} - ${data[i].get_customer.nama} (${ dateMask(data[i].tanggal_berangkat)})</td>`);
                        row.append(`<td>${moneyMask(data[i].total_tarif)}</td>`)
                        row.append(`<td><input type="text" class="form-control ditagihkan" value="${moneyMask(data[i].total_tarif)}" name="ditagihkan" /></td>`)
                        row.append(`<td class='text-center' style="text-align:center">
                                        <input type="checkbox" name="idInvoice[]" value="${data[i].id}">
                                    </td>`);
                        $("#hasil").append(row);
                    }
                   
                },error: function (xhr, status, error) {
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
                        icon: 'error',
                        title: 'Terjadi kesalahan: '+error
                    })
                }
            });
        };    
    });
</script>

@endsection


