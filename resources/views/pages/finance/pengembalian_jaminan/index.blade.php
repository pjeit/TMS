
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
        <div class="card-header">

        </div>
        
        <div class="card-body">
            {{-- <div style="overflow: auto;"> --}}
                <table id="datatableSD" class="table table-bordered table-striped" width='100%'>
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Customer</th>
                            <th>Supplier</th>
                            <th>No. BL</th>
                            <th>Catatan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal Request</th>
                            <th style="width:30px"></th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                        @foreach ($data as $key => $item)
                            <tr>
                                <td id="status_{{ $item->id }}">
                                    @if ($item->jaminan->status == 'DIBAYARKAN')
                                        MENUNGGU REQUEST
                                    @endif
                                    @if ($item->jaminan->status == 'REQUEST' || $item->jaminan->status == 'KEMBALI')
                                    {{$item->jaminan->status}}
                                    @endif
                                </td>
                                <td id="customer_{{ $item->id }}">{{ $item->getCustomer->nama }}</td>
                                <td id="supplier_{{ $item->id }}">{{ $item->getSupplier->nama }}</td>
                                <td id="no_bl_{{ $item->id }}">{{ $item->no_bl }}</td>
                                <td id="catatan_{{ $item->id }}">{{ $item->catatan }}</td>
                                <td id="total_{{ $item->id }}">{{ number_format($item->jaminan->nominal) }}</td>
                                <td id="status_{{ $item->id }}">{{ $item->status }}</td>
        {{-- dd($data); --}}
                                <td id="tgl_request_{{ $item->id }}">{{ isset($item->jaminan->tgl_request)? date("d-M-Y", strtotime($item->jaminan->tgl_request)):'' }}</td>
                                <td>
                                    <div class="btn-group btn-sm dropleft">
                                        <button type="button" class="btn btn-sm btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu" >
                                            @if ($item->jaminan->status == 'DIBAYARKAN')
                                                <button value="{{ $item->id }}" class="btn-sm dropdown-item showRequest"> 
                                                    <i class="fa fa-sticky-note mr-2"> </i> <b>Request</b>
                                                </button>
                                            @endif
                                            @if ($item->jaminan->status == 'REQUEST' )
                                                <button value="{{ $item->id }}" class="btn-sm dropdown-item showBayar"> 
                                                    <i class="fas fa-dollar-sign mr-2"> </i> <b>Pengembalian</b>
                                                </button>
                                            @endif
                                            @if ( $item->jaminan->status == 'KEMBALI')
                                                <a href="{{route('pengembalian_jaminan.edit',[$item->jaminan->id])}}" class="dropdown-item ">
                                                    <span class="fas fa-edit mr-3"></span> Revisi Pengembalian
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            {{-- </div> --}}
        </div>
    </div>
    <div class="modal fade" id="modal" tabindex='-1'>
        <div class="modal-dialog modal-lg">
            <form action="{{ route('pengembalian_jaminan.store') }}" id="save_pengembalian_jaminan" method="POST" >
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}
                        <div class='row'>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="form-group col-lg-4 col-md-12 col-sm-12">
                                        <label for="">Customer</label>
                                        <input type="text" class="form-control" id="customer" name="customer" readonly>
                                        <input type="hidden" id="id_jo" name="id_jo" readonly>
                                    </div>   
                                    <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                        <label for="">Supplier</label>
                                        <input type="text" class="form-control" id="supplier" name="supplier" readonly>
                                    </div>   
                                    <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                        <label for="">No. BL</label>
                                        <input type="text" class="form-control" id="no_bl" name="no_bl" readonly> 
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label>Tanggal Kembali</label>
                                        <div class="input-group mb-0 ">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" name="tgl_kembali" class="date form-control" id="tgl_kembali" autocomplete="off" >
                                        </div>
                                    </div>
                                   
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">Total Jaminan</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp.</span>
                                            </div>
                                            <input type="text" class="form-control" id="total_jaminan" name="total_jaminan" readonly> 
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                        <label for="">Kas Bank<span class="text-red">*</span></label>
                                        <select name="id_kas" class="form-control select2" required>
                                            <option value="">──PILIH KAS──</option>
                                            @foreach ($bank as $item)
                                                <option value="{{ $item->id }}" {{ $item->id == 1? 'selected':'' }}>{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>   

                                    <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                        <label for="">Potongan</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp.</span>
                                            </div>
                                            <input type="text" name="potongan" class="form-control numaja uang" id="potongan" placeholder="" readonly> 
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><input type="checkbox" id="check_potongan" name="check_potongan"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                        <label for="">Total<span class="text-red">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp.</span>
                                            </div>
                                            <input type="text" class="form-control numaja uang" id="total" name="total" required required>
                                        </div>
                                    </div>   
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Catatan</label>
                                        <input type="text" class="form-control" id="catatan" name="catatan">
                                    </div>   
                                    
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal"><b>BATAL</b></button>
                        <button type="submit" class="btn btn-sm btn-success save_detail" id="" style='width:85px'><b>SIMPAN</b></button> 
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_request" tabindex='-1'>
        <div class="modal-dialog modal-lg">
            <form action="{{ route('pengembalian_jaminan.request') }}" id="save_request" method="POST" >
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Request Pengembalian Jaminan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}
                        <div class='row'>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                                        <label for="">Customer</label>
                                        <input type="text" class="form-control" id="customer_req" name="customer" readonly>
                                        <input type="hidden" id="id_jo_req" name="id_jo" readonly>
                                    </div>   

                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="">Supplier</label>
                                        <input type="text" class="form-control" id="supplier_req" name="supplier" readonly>
                                    </div>   

                                    <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                        <label for="">No. BL</label>
                                        <input type="text" class="form-control" id="no_bl_req" name="no_bl" readonly> 
                                    </div>

                                    <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                        <label for="">Total Jaminan</label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp.</span>
                                            </div>
                                            <input type="text" class="form-control" id="total_jaminan_req" name="total_jaminan" readonly> 
                                        </div>
                                    </div>
                                   
                                    <div class="form-group col-lg-4 col-md-6 col-sm-12">
                                        <label>Tanggal Request</label>
                                        <div class="input-group mb-0 ">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" name="tgl_request" class="date form-control" id="tgl_request" autocomplete="off" >
                                        </div>
                                    </div>
                                    
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="">Catatan</label>
                                        <input type="text" class="form-control" id="catatan_req" name="catatan">
                                    </div>   
                                    
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal"><b>BATAL</b></button>
                        <button type="submit" class="btn btn-sm btn-success save_detail" id="" style='width:85px'><b>SIMPAN</b></button> 
                    </div>
                </div>
            </form>
        </div>
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

        $(".showBayar").click(function (event) {
            clear();
            var customer = $('#customer_'+this.value).html();
            var supplier = $('#supplier_'+this.value).html();
            var no_bl    = $('#no_bl_'+this.value).html();
            var total    = $('#total_'+this.value).html();
            var catatan  = $('#catatan_'+this.value).html();

            $('#id_jo').val(this.value);
            $('#customer').val(customer);
            $('#supplier').val(supplier);
            $('#no_bl').val(no_bl);
            $('#total_jaminan').val(total);
            $('#catatan').val(catatan);
     
            var checkbox = document.getElementById("check_potongan");
            var input = document.getElementById("potongan");
            setDate();
            checkbox.addEventListener("change", function() {
                if (checkbox.checked) {
                    input.removeAttribute("readonly");
                    totalChange($('#total').val());
                    potChange($('#potongan').val());
                } else {
                    input.setAttribute("readonly", "readonly");
                    $('#potongan').val('');
                }
            });

            $("#modal").modal("show");
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

        function setDate(){
            $('#tgl_request').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                orientation: 'bottom auto',
                endDate: today
            }).datepicker('setDate', today);
            $('#tgl_kembali').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                orientation: 'bottom auto',
                endDate: today
            }).datepicker('setDate', today);
        }

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