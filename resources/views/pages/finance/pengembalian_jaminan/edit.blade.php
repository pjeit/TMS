
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
   
</style>
<div class="container-fluid">
    <div class="card radiusSendiri">
        <form action="{{ route('pengembalian_jaminan.update',[$data->jaminan->id]) }}" id="save_pengembalian_jaminan" method="POST" >
                @csrf
                @method('PUT')
                <div class="card-header">
                    <a href="{{ route('pengembalian_jaminan.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    <button type="submit" class="btn btn-sm btn-success radiusSendiri save_detail"  style='width:85px'><i class="fa fa-credit-card" aria-hidden="true"> Simpan</button> 
                </div>
                <div class="card-body">
                    <div class='row'>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="form-group col-lg-4 col-md-12 col-sm-12">
                                    <label for="">Customer</label>
                                    <input type="text" class="form-control" id="customer" name="customer" readonly value="{{ $data->getCustomer->nama }}">
                                    <input type="hidden" id="id_jo" name="id_jo" readonly>
                                </div>   
                                <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                    <label for="">Supplier</label>
                                    <input type="text" class="form-control" id="supplier" name="supplier" readonly value="{{ $data->getSupplier->nama }}">
                                </div>   
                                <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                    <label for="">No. BL</label>
                                    <input type="text" class="form-control" id="no_bl" name="no_bl" readonly value="{{ $data->no_bl}}"> 
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label>Tanggal Kembali</label>
                                    <div class="input-group mb-0 ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" name="tgl_kembali" class="date form-control" id="tgl_kembali" autocomplete="off" value="{{ date("d-M-Y", strtotime($data->jaminan->tgl_kembali))}}">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Total Jaminan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" class="form-control" id="total_jaminan" name="total_jaminan" readonly value="{{number_format($data->jaminan->nominal)}}"> 
                                    </div>
                                </div>

                                <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                    <label for="">Kas Bank<span class="text-red">*</span></label>
                                    <select name="id_kas" class="form-control select2" required>
                                        <option value="">──PILIH KAS──</option>
                                        @foreach ($bank as $item)
                                            <option value="{{ $item->id }}" {{ $item->id == $data->jaminan->id_kas? 'selected':'' }}>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>   

                                <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                    <label for="">Potongan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" name="potongan" class="form-control numaja uang" id="potongan" placeholder="" {{!$data->jaminan->potongan_jaminan?"readonly":""}} value="{{number_format($data->jaminan->potongan_jaminan)}}"> 
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><input type="checkbox" id="check_potongan" name="check_potongan" {{$data->jaminan->potongan_jaminan?"checked":""}}></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                    <label for="">Total<span class="text-red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp.</span>
                                        </div>
                                        <input type="text" class="form-control numaja uang" id="total" name="total" required value="{{number_format($data->jaminan->nominal_kembali)}}">
                                    </div>
                                </div>   
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Catatan</label>
                                    <input type="text" class="form-control" id="catatan" name="catatan" value="{{$data->jaminan->catatan_kembali}}">
                                </div>   
                                
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        // save data
        
        $('#datatableSD').DataTable({
                    // order: [
                    //     [0, 'asc'],
                    // ],
                    rowGroup: {
                        dataSrc: [0] // kalau mau grouping pake ini
                    },
                    columnDefs: [
                        {
                            targets: [0],
                            visible: false
                        },
                        { orderable: true, targets: 0 }, // Enable ordering for the first column (index 0)
                        { orderable: false, targets: '_all' } // Disable ordering for all other columns
                    ],
                    info: false,
                    searching: true,
                    paging: true,
                    language: {
                        emptyTable: "Data tidak ditemukan."
                    }
        });
        $('#save_pengembalian_jaminan').submit(function(event) {
            event.preventDefault();
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
                    // return;
                }
            })
        });

        var checkbox = document.getElementById("check_potongan");
            var input = document.getElementById("potongan");

            checkbox.addEventListener("change", function() {
                if (checkbox.checked) {
                    input.removeAttribute("readonly");
                    totalChange($('#total').val());
                    potChange($('#potongan').val());
                } else {
                    input.setAttribute("readonly", "readonly");
                    $('#potongan').val(0);
                }
            });

        $('#save_request').submit(function(event) {
        });

        $(".showRequest").click(function (event) {
            clear();
            var customer = $('#customer_'+this.value).html();
            var supplier = $('#supplier_'+this.value).html();
            var no_bl    = $('#no_bl_'+this.value).html();
            var total    = $('#total_'+this.value).html();
            var catatan  = $('#catatan_'+this.value).html();
            console.log('first:', this.value);

            $('#id_jo_req').val(this.value);
            $('#customer_req').val(customer);
            $('#supplier_req').val(supplier);
            $('#no_bl_req').val(no_bl);
            $('#total_jaminan_req').val(total);
            $('#catatan_req').val(catatan);
    
            setDate();
            $("#modal_request").modal("show");   
        });


            $('#tgl_kembali').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                orientation: 'bottom auto',
                endDate: today
            })/*.datepicker('setDate', today)*/;
        

        // potongan
            // trigger ketika angkat di ketik
            $(document).on('keyup', '#potongan', function(){ 
                potChange(this.value);
            });
            // trigger ketika focus lepas dari input form
            $('#potongan').blur(function() {
                potChange(this.value);
            });

            function potChange(val){
                var total = normalize($('#total_jaminan').val());
                val = normalize(val);
                if(val > total){
                    $('#potongan').val(moneyMask(total));
                }

                if( $('#potongan').val() != ''){
                    $('#total').val(moneyMask(total-normalize($('#potongan').val())));
                }
            }
        //

        $(document).on('keyup', '#total', function(){ 
            totalChange(this.value);
        });
        $('#total').blur(function() {
            totalChange(this.value);
        });

        function totalChange(val){
            var total = normalize($('#total_jaminan').val());
            val = normalize(val);
            if(val > total){
                $('#total').val(moneyMask(total));
            }

            if( $('#total').val() != ''){
                var isChecked = $("#check_potongan").prop("checked");
                if (isChecked) {
                    $('#potongan').val(moneyMask(total-normalize($('#total').val())));
                }
            }
        }

        function clear(){
            $('#customer').val('');
            $('#supplier').val('');
            $('#no_bl').val('');
            $('#total_jaminan').val('');
            $('#potongan').val('');
            $('#total').val('');
            $('#catatan').val('');
        };
    });
</script>
@endsection