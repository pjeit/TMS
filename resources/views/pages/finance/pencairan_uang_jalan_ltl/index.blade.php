
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
            @csrf
            <div class="card-header ">
                <div class="card-header" style="border: 2px solid #bbbbbb;">
                    <ul class="list-inline">
                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-lg-4 bg-white pb-3">
                                <div class="form-group">
                                    <label for="">Kendaraan</label> 
                                    <select class="form-control selectpicker" required name="item" id="item" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                        <option value="">­­— PILIH KENDARAAN —</option>
                                        @foreach ($data as $item)
                                            @if ($item['total_uang_jalan'] == 0)
                                                <option value="{{ $item['no_polisi'] }}">{{ $item['no_polisi'] }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </ul>
                </div>
            </div>
        
            <div class="card-body">
                <table id="ltl" class="table table-bordered table-hover" width="100%">
                    <thead>
                        <tr>
                            <th>Suplier</th>
                            <th style="width:200px">Customer</th>
                            <th style="width:200px">Tujuan</th>
                            <th style="width:200px">Tanggal Berangkat</th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                    </tbody>
                </table>
            </div>
    </div>
</div>

<div class="modal fade" id="modal_detail" tabindex='-1'>
    <form id="post_data" action="{{ route('pencairan_uang_jalan_ltl.store') }}" method="POST">
    @csrf
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Input Uang Jalan LTL</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id='form_add_detail'>
                    <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}

                    <div class='row'>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Kas<span class="text-red">*</span> </label>
                                    <select name="id_kas" id="id_kas" class="form-control select2" required>
                                        <option value="">── PILIH KAS ──</option>
                                        @foreach ($kas as $item)
                                            <option value="{{ $item->id }}" {{$item->id==1?'selected':''}}>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Uang Jalan<span class="text-red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    <input type="text" id="uang_jalan" name="uang_jalan" class="form-control uang numaja" required readonly>

                                    </div>
                                </div>
                                 <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Tol<span class="text-red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    <input type="text" id="tol" name="tol" class="form-control uang numaja" >
                                    </div>
                                </div>
                                 <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Bensin<span class="text-red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    <input type="text" id="bensin" name="bensin" class="form-control uang numaja" >
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12 is_total_hutang">
                                    <label for="">Total Hutang</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    <input type="text" id="total_hutang" name="total_hutang" class="form-control uang numaja" readonly>

                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-sm-12 is_potong_hutang">
                                    <label for="">Potong Hutang</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    <input type="text" id="potong_hutang" name="potong_hutang" class="form-control uang numaja" >

                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Diterima</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                    <input type="text" id="diterima" name="diterima" class="form-control uang numaja" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Catatan</label>
                                    <input type="text" id="catatan" name="catatan" class="form-control" >
                                </div>
                            </div>
                        </div>
                    
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" style='width:85px' data-dismiss="modal">BATAL</button>
                <button type="submit" class="btn btn-sm btn-success save_detail" style='width:85px'>OK</button> 
            </div>
        </div>
    </div>
    
    </form>
</div>

{{-- logic save --}}
<script type="text/javascript">
    $(document).ready(function() {
        // $('#save').submit(function(event) {
        //     var item = $('#item').val();
        //     var isOk = 0;

        //     // check apakah sudah ada yg dicentang?
        //         var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        //         checkboxes.forEach(function(checkbox) {
        //             if (checkbox.checked) {
        //                 isOk = 1;
        //             }
        //         });
        //     //

        //     // validasi sebelum di submit
        //         if (item == '' || item == null || isOk == 0) {
        //             event.preventDefault(); // Prevent form submission
        //             Swal.fire({
        //                 icon: 'error',
        //                 text: 'Harap pilih item dahulu!',
        //             })
        //             return;
        //         }
        //     //
        //     event.preventDefault(); // Prevent form submission

        //     Swal.fire({
        //         title: 'Apakah Anda yakin data sudah benar ?',
        //         text: "Periksa kembali data anda",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         cancelButtonColor: '#d33',
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonText: 'Batal',
        //         confirmButtonText: 'Ya',
        //         reverseButtons: true
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             this.submit();
        //         }else{
        //             const Toast = Swal.mixin({
        //                 toast: true,
        //                 position: 'top',
        //                 timer: 2500,
        //                 showConfirmButton: false,
        //                 timerProgressBar: true,
        //                 didOpen: (toast) => {
        //                     toast.addEventListener('mouseenter', Swal.stopTimer)
        //                     toast.addEventListener('mouseleave', Swal.resumeTimer)
        //                 }
        //             })

        //             Toast.fire({
        //                 icon: 'warning',
        //                 title: 'Batal Disimpan'
        //             })
        //             event.preventDefault();
        //         }
        //     })
        // });
    });
</script>
<script>
    $(document).ready(function() {

        $(document).on('change', '#item', function(e) {  
            var item = $('#item').val();
            if(item != ''){
                showTable(item);
            }else{
                // $('#ltl').dataTable().fnClearTable();
            }
		});        

        function showTable(item){
            $.ajax({
                method: 'GET',
                url: `pencairan_uang_jalan_ltl/getData/${item}`,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    // $("#hasil").empty();
                    $('#ltl').dataTable().fnClearTable();
                    $("#ltl").dataTable().fnDestroy();

                    $("th").remove();
                    $("thead tr").append(`<th></th>
                                            <th style="width:200px">Customer</th>
                                            <th style="width:200px">Tujuan</th>                    
                                            <th style="width:200px">Tanggal Berangkat</th>
                                        `);

                    var data = response.data;
                    console.log('data', data);
                    if(data.length > 0){
                        for (var i = 0; i <data.length; i++) {
                            if(data[i].total_dicairkan == null){
                                if (data[i] && data[i].get_karyawan && data[i].get_karyawan.get_hutang !== null) {
                                    // The property data[i].get_karyawan.get_hutang exists and is not null
                                    // You can perform further actions here
                                    hutangKaryawan = data[i].get_karyawan.get_hutang.total_hutang;
                                } else {
                                    // The property data[i].get_karyawan.get_hutang is either null or doesn't exist
                                    // Handle this case as needed
                                    hutangKaryawan = 0;
                                }
                                $("#hasil").append(
                                    `<tr>
                                        <td style='background: #efefef' > 
                                            <div class="d-flex justify-content-between ">
                                                <div>
                                                    <b> <span>► </span> (${data[0].no_polisi}) - ${data[0].nama_driver == null? 'Driver Rekanan':data[0].nama_driver} </b>
                                                    <input type="hidden" value="${hutangKaryawan}" id="hutang" />
                                                </div>
                                                <div>
                                                    <button class="btn btn-primary btn-sm radiusSendiri openModal" value="${data[0].id_sewa}">
                                                        <span class="fas fa-sticky-note mr-1"></span> Input UJ
                                                    </button>
                                                </div>
                                            </div>
                                            <input type="hidden" id="driver_${data[0].id_sewa}" value="${data[0].nama_driver}" />
                                        </td>
                                        <td>${data[i].get_customer.nama}</td>
                                        <td>${data[i].nama_tujuan}</td>
                                        <td>${dateMask(data[i].tanggal_berangkat)}</td>
                                    </tr>`
                                );
                            }
                             new DataTable('#ltl', {
                                searching: false, paging: false, info: false, ordering: false,
                                order: [
                                        [0, 'asc'],
                                    ],
                                rowGroup: {
                                    dataSrc: [0]// di order grup dulu, baru customer
                                },
                                columnDefs: [
                                    {
                                        targets: [0], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                                        visible: false
                                    },
                                    {
                                    "targets": [0, 1, 2]
                                        // orderable: false, // matiin sortir kolom centang
                                    },
                                ],
                            });
                        }
                        else
                        {
                            $("#hasil").append(
                                `<tr>
                                    <td colspan='4'>Tidak ditemukan Data</td>
                                </tr>`
                            );
                        }
                    }
                },error: function (xhr, status, error) {
                    // $('#ltl').dataTable().fnClearTable();

                    if ( xhr.responseJSON.result == 'error') {
                        console.log("Error:", xhr.responseJSON.message);
                        console.log("XHR status:", status);
                        console.log("Error:", error);
                        console.log("Response:", xhr.responseJSON);
                    } else {
                        toastr.error("Terjadi kesalahan saat menerima data. " + error);
                    }
                }
            });
        }

        $(document).on('keyup', '#uang_jalan, #potong_hutang', function(e) {  
            hitung();
            hitungHutangMax();

		}); 
         $(document).on('keyup', '#tol, #bensin', function(e) {  
            
            var tol = !isNaN(normalize($('#tol').val()))? normalize($('#tol').val()):0;
            var bensin = !isNaN(normalize($('#bensin').val()))? normalize($('#bensin').val()):0;

            $('#uang_jalan').val(moneyMask(tol+bensin));
            hitung();

		});  
        function hitung(){
            var uj = !isNaN(normalize($('#uang_jalan').val()))? normalize($('#uang_jalan').val()):0;
            var ph = !isNaN(normalize($('#potong_hutang').val()))? normalize($('#potong_hutang').val()):0;

            // console.log('uj :'+uj);
            // console.log('ph :'+ph);
            // console.log('ph1 :'+$('#potong_hutang').val());

             if($('#total_hutang').val()!=''){
                var total_hutang =escapeComma($('#total_hutang').val());
            }else{
                var total_hutang =0;
            }
            if(parseFloat(ph)>parseFloat(total_hutang)){
                ph = total_hutang;
            }
            if(parseFloat(ph)>parseFloat(uj) && parseFloat(total_hutang)>parseFloat(uj)){
                ph = uj;
            }
            total_diterima=parseFloat(uj)-parseFloat(ph);
            if(total_diterima!=0){
                $('#diterima').val(addPeriodType(total_diterima,','));
            }else{
                $('#diterima').val(0);
            }
            // $('#diterima').val(moneyMask(uj-ph));
        }
        function hitungHutangMax(){
             if($('#total_hutang').val()!=''){
                var total_hutang =escapeComma($('#total_hutang').val());
            }else{
                var total_hutang =0;
            }
            if($('#uang_jalan').val()!=''){
                var total_uang_jalan=escapeComma($('#uang_jalan').val());
            }else{
                var total_uang_jalan=0;
            }
            
            if($('#potong_hutang').val()!=''){
                var potong_hutang=escapeComma($('#potong_hutang').val());
            }else{
                var potong_hutang=0;
            }
            var potong_hutang = removePeriod($('#potong_hutang').val(),',');
            if(parseFloat(potong_hutang)>parseFloat(total_hutang)){
                $('#potong_hutang').val(moneyMask(total_hutang));
            }
            else{
                $('#potong_hutang').val(moneyMask(potong_hutang));
            }
            //kalau hutang misal 500k dan uang jalannya 300k, jadi maks pencairan yang 300k bukan 500k,
            //karena kalau 500k nanti jadi minus, kalau 300k, berarti gak tf sama sekali cuman potong hutang, gausah milih kas bank
            if(parseFloat(potong_hutang)>parseFloat(total_uang_jalan) && parseFloat(total_hutang)>parseFloat(total_uang_jalan)){
                $('#potong_hutang').val(moneyMask(total_uang_jalan));
            }

        }

        $(document).on('click', '.openModal', function(event){
            var id = this.value;
            $('#key').val(id);
            let driver = $('#driver_'+id).val();
            if(driver == "null"){
                $('.is_potong_hutang').hide();
                $('.is_total_hutang').hide();
            }else{
                $('.is_potong_hutang').show();
                $('.is_total_hutang').show();
            }
            

            $('#total_hutang').val( moneyMask($('#hutang').val()) );

            $('#modal_detail').modal('show');
        });
        $('#post_data').submit(function(event) {
            // uang_jalan
            // tol
            // bensin
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
                        });

            if($("#uang_jalan").val().trim()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `NOMINAL UANG JALAN WAJIB TIDAK BOLEH 0 / KOSONG!`,
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