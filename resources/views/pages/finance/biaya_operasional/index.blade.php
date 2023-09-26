
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
    {{-- <div class="radiusSendiri sticky-top " style="margin-bottom: -15px;">
        <div class="card radiusSendiri" style="">
            <div class="p-3">
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
        </div>
    </div> --}}
    <div class="card radiusSendiri">
        <form id="save" action="{{ route('biaya_operasional.store') }}" method="POST">
            @csrf
            <div class="card-header ">
                <div class="card-header" style="border: 2px solid #bbbbbb;">
                    <ul class="list-inline">
                        <div class="row">
                            {{-- <li class="list-inline-item"> --}}
                                <div class="col-sm-12 col-md-3 col-lg-3 bg-white pb-3">
                                    <div class="form-group">
                                        <label for="">Jenis Biaya</label>
                                        <select class="form-control selectpicker" name="item" id="item" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                            <option value="">­­— PILIH DATA —</option>
                                            <option value="TALLY">TALLY</option>
                                            <option value="OPERASIONAL">OPERASIONAL</option>
                                            <option value="TIMBANG">TIMBANG</option>
                                            <option value="BURUH">BURUH</option>
                                        </select>
                                    </div>
                                </div>
                            {{-- </li> --}}
                            {{-- <li class="list-inline-item"> --}}
                                <div class="col-sm-12 col-md-4 col-lg-4 bg-white pb-3">
                                    <ul class="list-group mt-4">
                                        <li class="list-group-item d-flex justify-content-between lh-sm card-outline card-primary">
                                            <div>
                                                <span class="text-primary"><b>Grand Total</b></span>
                                            </div>
                                            <span class="text-bold t_total">Rp. 0</span>
                                            <input type="hidden" id='t_total' name='t_total'>
                                        </li>
                                    </ul>
                                </div>
                            {{-- </li> --}}
                            {{-- <li class="list-inline-item"> --}}
                                <div class="col-sm-12 col-md-5 col-lg-5 bg-white pb-3">
                                    <div class="input-group mt-4">
                                        <select class="form-control selectpicker"  id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                            <option value="">── PILIH PEMBAYARAN ──</option>
                                            @foreach ($dataKas as $kas)
                                                <option value="{{$kas->id}}">{{ $kas->nama }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-success ml-4" id="bttonBayar"><i class="fa fa-credit-card" aria-hidden="true" ></i> Bayar</button>
                                    </div>
                                </div>
                            {{-- </li> --}}
                        </div>
                    </ul>
                </div>
            </div>
        
            <div class="card-body">
                <section class="col-lg-12" id="show_report">

                <table id="rowGroup" class="table table-bordered table-hover" width="100%">
                    <thead>
                        <tr>
                            <th>Biaya Operasional</th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                    </tbody>
                </table>
                </section>
            </div>
        </form>
    </div>
</div>

{{-- logic save --}}
<script type="text/javascript">
    $(document).ready(function() {
        $('#save').submit(function(event) {
            var item = $('#item').val();
            var isOk = 0;

            // check apakah sudah ada yg dicentang?
                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(function(checkbox) {
                    if (checkbox.checked) {
                        isOk = 1;
                    }
                });
            //

            // validasi sebelum di submit
                if (item == '' || item == null || isOk == 0) {
                    event.preventDefault(); // Prevent form submission
                    Swal.fire({
                        icon: 'error',
                        text: 'Harap pilih item dahulu!',
                    })
                    return;
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
<script>
    $(document).ready(function() {
        $(document).on('change', '#item', function(e) {  
            var item = $('#item').val();
            if(item != null){
                showTable(item);
            }else{
                var tbody = document.getElementById("hasil");
                tbody.innerHTML = "";
            }
		});
        
        function showTable(item){
            if(item == ''){
                var table = document.getElementById("rowGroup");
                table.innerHTML = `
                    <thead>
                        <tr>
                            <th>Biaya Operasional</th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                    </tbody>
                `;
            }else{
                $.ajax({
                    method: 'GET',
                    url: `biaya_operasional/load_data/${item}`,
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function(response) {
                        $("#rowGroup").dataTable().fnDestroy();
                        $("#loading-spinner").hide();
                        var item = $('#item').val();
                        var data = response.data;
    
                        $("th").remove();
                        $("thead tr").append(`<th>Grup<th> <th>Tujuan</th><th>Keterangan</th>`);
                        if(item == 'TIMBANG' || item == 'BURUH'){
                            
                        }else{
                            $("thead tr").append("<th>Total</th>");
                        }
                        $("thead tr").append("<th>Dicairkan</th>");
                        $("thead tr").append("<th>Catatan</th>");
                        $("thead tr").append("<th class='text-center'><input id='check_all' type='checkbox'></th>");
                        $("#hasil").html("");
                        var ord = 7;
                        var dataCustomer = null;
                        for (var i = 0; i <data.length; i++) {
                            if(data[i].total_dicairkan == null){
                                var start = data[i].deskripsi_so;
                                var row = $("<tr class='hoverEffect'></tr>");
                                row.append(`<td style='background: #efefef'><b> <div> <span> ${data[i].nama_grup}</span> <span class='float-right mr-1'>  <input class='check_item check_grup' grup_parent='${data[i].grup_id}' type='checkbox'> </span> </div> </b></td>`);
                                row.append(`<td style='background: #efefef'><b> <div> <span>► ${data[i].customer}</span> <span class='float-right mr-1'>  <input class='check_item check_cust' grup_child='${data[i].grup_id}' cust_parent='${data[i].id_customer}' type='checkbox'> </span> </div> </b></td>`);
                            
                                row.append(`<td> ${data[i].nama_tujuan} / ${data[i].no_polisi} / ${data[i].nama_panggilan} </td>`);
                                row.append(`<td> ${data[i].tipe_kontainer}" <b> ${data[i].jenis_order} </b> ${ data[i].pick_up == null? '':'('+data[i].pick_up+')'} </td>`);
                                var nominal = 0;
                                if(data[i].jenis_order == 'INBOUND'){
                                    if(data[i].tipe_kontainer=='20'){
                                        if(data[i].pick_up == 'DEPO'){
                                            nominal = 15000;
                                        }
                                    }else{
                                        if(data[i].pick_up == 'DEPO'){
                                            nominal = 25000;
                                        }
                                    }
                                }else{
                                    if(data[i].tipe_kontainer=='20'){
                                        nominal = 15000;
                                    }else{
                                        nominal = 25000;
                                    }
                                }
                                if(item == 'TALLY'){
                                    nominal = data[i].tally;       
                                }
                                if(item == 'TIMBANG' || item == 'BURUH'){
                                    ord = 6;
                                }else{
                                    row.append(`<td> ${nominal.toLocaleString()} </td>`);
                                }
                                var keterangan = data[i].nama_tujuan+'/'+data[i].no_polisi+'/'+data[i].nama_panggilan;
                                row.append(`<td> 
                                                <input type="text" class="uang numaja dicairkan form-control open_cust_${data[i].id_customer} open_grup_${data[i].grup_id}" id='open_${data[i].id_sewa}' name='data[${data[i].id_sewa}][dicairkan]' sewaOprs='${data[i].id_sewa}' value='${data[i].total_dicairkan}' readonly/> 
                                                <input type="hidden" name="data[${data[i].id_sewa}][pickup]" value="${data[i].pick_up}" />
                                                <input type="hidden" name="data[${data[i].id_sewa}][keterangan]" value="${keterangan.replace(/"/g, '')}" />
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <input class="form-control open_cust_cttn_${data[i].id_customer} open_grup_cttn_${data[i].grup_id}" id='open_cttn_${data[i].id_sewa}' name='data[${data[i].id_sewa}][catatan]' sewaOprsCttn='${data[i].id_sewa}' type="text" readonly/> 
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <input class='check_item check_container' id_sewa="${data[i].id_sewa}" grup_child='${data[i].grup_id}' cust_child='${data[i].id_customer}'  name="data[${data[i].id_sewa}][item]" type='checkbox'> 
                                                <input type='hidden' id='biaya_${data[i].id_sewa}' name='data[${data[i].id_sewa}][nominal]' value='${nominal}' class='form-control' readonly>
                                            </td>`);
                                $("#hasil").append(row);
                            }
                        }
                        new DataTable('#rowGroup', {
                            order: [
                                [0, 'asc'], // 0 = grup
                                [1, 'asc'] // 1 = customer
                            ],
                            rowGroup: {
                                dataSrc: [0, 1] // di order grup dulu, baru customer
                            },
                            columnDefs: [
                                {
                                    targets: [0, 1], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                                    visible: false
                                },
                                {
                                    targets: [ord, ord-1],
                                    orderable: false, // matiin sortir kolom centang
                                },
                            ],
                        });
                    },error: function (xhr, status, error) {
                        $("#loading-spinner").hide();
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
        }
        
        // check all
            // function toggleReadonlyAll() {
            //     var isChecked = $('#check_all').prop('checked');
            //     $('[id^="open_"]').prop('readonly', !isChecked);
            // }
            function toggleReadonlyAll(inputId) {
                var isChecked = $('#check_all').prop('checked');
                var readonlyValue = isChecked ? false : true; // Set to true when not checked (isChecked is false)
                if(readonlyValue == true){
                    $(`[id^="open_"]`).val('');
                    hitung();
                }
                $('[id^="open_"]').prop('readonly', readonlyValue);
            }
            $(document).on('change', '#check_all', function() {  
                toggleReadonlyAll();
                $(".check_item").prop('checked', $(this).prop('checked'));
            });
            $(document).on('change', '#check_all_pick_up', function() {  
                $(".check_item_pick_up").prop('checked', $(this).prop('checked'));
            });
        //

        // check per grup
            function toggleReadonlyGrup(grup_id) {
                var checkbox = $(`.check_grup[grup_parent="${grup_id}"]`);
                var inputElements = $('.open_grup_' + grup_id);
                var cttnElements = $('.open_grup_cttn_' + grup_id);
                if (checkbox.prop('checked')) {
                    inputElements.prop('readonly', false);
                    cttnElements.prop('readonly', false);
                } else {
                    inputElements.val('');
                    cttnElements.val('');
                    inputElements.prop('readonly', true);
                    cttnElements.prop('readonly', true);
                    hitung();
                    caps();
                }
            }
            $(document).on('change', '.check_grup, .check_grup_tps, .check_grup_ttl, .check_grup_depo', function() {
                var opr = $(this).attr('opr');
                var grup_id = $(this).attr('grup_parent');
                var child = $(`input[grup_child="${grup_id}"]`);
                var child_opr = $(`input[grup_child_${opr}="${grup_id}"]`);
                child.prop('checked', $(this).prop('checked'));
                child_opr.prop('checked', $(this).prop('checked'));
                $("#check_all").prop('checked', false);

                toggleReadonlyGrup(grup_id);
            });
        //
 
        // check per customer
            function toggleReadonlyCust(cust_id) {
                var checkbox = $(`.check_cust[cust_parent="${cust_id}"]`);
                var inputElements = $('.open_cust_' + cust_id);
                var cttnElements = $('.open_cust_cttn_' + cust_id);
                if (checkbox.prop('checked')) {
                    inputElements.prop('readonly', false);
                    cttnElements.prop('readonly', false);
                } else {
                    inputElements.val('');
                    cttnElements.val('');
                    hitung();
                    inputElements.prop('readonly', true);
                    cttnElements    .prop('readonly', true);
                }
            }
            $(document).on('change', '.check_cust, .check_cust_tps, .check_cust_ttl, .check_cust_depo', function() {
                var opr = $(this).attr('opr');
                var cust_id = $(this).attr('cust_parent');
                var child = $(`input[cust_child="${cust_id}"]`);
                var child_opr = $(`input[cust_child_${opr}="${cust_id}"]`);
                child.prop('checked', $(this).prop('checked'));
                child_opr.prop('checked', $(this).prop('checked'));

                var grup_id = $(this).attr('grup_child');
                $("#check_all").prop('checked', false);

                toggleReadonlyCust(cust_id);
            });
        //

        // uncheck all
            // function toggleReadonly(inputId) {
            //     var isChecked = $(`input[id_sewa='${inputId}']`).prop('checked');
            //     $(`#open_${inputId}`).prop('readonly', !isChecked);
            // }
            function toggleReadonly(inputId) {
                var isChecked = $(`input[id_sewa='${inputId}']`).prop('checked');
                var readonlyValue = isChecked ? false : true; // Set to true when not checked (isChecked is false)
                if(readonlyValue == true){
                    $(`#open_${inputId}`).val('');
                    $(`#open_cttn_${inputId}`).val('');
                    hitung();
                }
                $(`#open_${inputId}`).prop('readonly', readonlyValue);
                $(`#open_cttn_${inputId}`).prop('readonly', readonlyValue);
                caps();
            }
            $(document).on('click', '.check_container', function (event) {
                $("#check_all").prop('checked', false);
                var cust_id = $(this).attr('cust_child');
                var grup_id = $(this).attr('grup_child');
                var cust_x = $(this).attr('cust_child');
                var id_sewa = $(this).attr('id_sewa');
                // $(`input[cust_parent="${cust_id}"]`).val();
                $(`input[cust_parent="${cust_id}"]`).prop('checked', false);
                $(`input[grup_parent="${grup_id}"]`).prop('checked', false);

                toggleReadonly(id_sewa);
            });
        //
        
        // 
        $(document).on('keyup', '.dicairkan', function(){
            var idOprs = $(this).attr('sewaOprs');
            var inputed = parseFloat(this.value.replace(/,/g, ''));
            var max = $('#biaya_'+idOprs).val();

            if (inputed > max && item.value != 'TIMBANG' && item.value != 'BURUH') {
                $('#open_'+idOprs).val(parseFloat(max).toLocaleString()); // Explicitly specify the locale
            }
            hitung();
        });

        function hitung(){
            var totalCair = 0;

            var dicairkan = document.querySelectorAll('.dicairkan');

            for (var i = 0; i < dicairkan.length; i++) {
                totalCair += parseFloat(dicairkan[i].value.replace(/,/g, '')) || 0; // Convert to a number or use 0 if NaN
            }

            var totalElement = document.querySelector('.t_total');
            $('#t_total').val(totalCair);
            totalElement.textContent = "Rp. "+(totalCair).toLocaleString(); 
        }

        function caps(){
            $('input[type="text"]').on("input", function () {
                var inputValue = $(this).val();
                var uppercaseValue = inputValue.toUpperCase();
                $(this).val(uppercaseValue);
            });
        }
    });
</script>
@endsection