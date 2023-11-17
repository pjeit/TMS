
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
                                        <select class="form-control selectpicker" required name="item" id="item" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                            <option value="">­­— PILIH DATA —</option>
                                            <option value="TALLY">TALLY</option>
                                            <option value="SEAL PELAYARAN">SEAL PELAYARAN</option>
                                            <option value="OPERASIONAL">ALAT</option>
                                            <option value="TIMBANG">TIMBANG</option>
                                            <option value="BURUH">BURUH</option>
                                            <option value="LEMBUR">LEMBUR</option>
                                            <option value="KARANTINA">KARANTINA</option>
                                        </select>
                                    </div>
                                </div>
                  
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
                       
                                <div class="col-sm-12 col-md-5 col-lg-5 bg-white pb-3">
                                    <div class="input-group mt-4">
                                        <select class="form-control selectpicker" required id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                            <option value="">── PILIH PEMBAYARAN ──</option>
                                            @foreach ($dataKas as $kas)
                                                <option value="{{$kas->id}}" {{$kas->id == '1'? 'selected':''}} >{{ $kas->nama }}</option>
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
<script>
    $(document).ready(function() {
        $(document).on('change', '#item', function(e) {  
            var item = $('#item').val();
            var totalElement = document.querySelector('.t_total');
            totalElement.textContent = "Rp. 0"; 
            if(item != null){
                showTable(item);
            }else{
                var tbody = document.getElementById("hasil");
                tbody.innerHTML = "";
            }
		});        
        // var textDicairkan = $('.dicairkan');
        
        // timot nambah ini buat yang bug buruh sama timbang gak ke set
        $(document).on('keyup', '.dicairkan', function() {  
            var row = $(this).closest('tr');
            var item = $('#item').val();
            if(item == 'TIMBANG' || item == 'BURUH' || item == 'LEMBUR'){
                // hidden biaya nominal di cet sama dengan nominal dicairkan
                row.find('.hiddenNominal').val(row.find('.dicairkan').val())
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
                        var item = $('#item').val();
                        var data = response.data;
                        console.log('data', data);
                        
                        if(item == 'KARANTINA'){
                            $("th").remove();

                            $("thead tr").append(`<th>Grup</th>
                                                    <th>Customer</th>
                                                    <th>No. BL</th>
                                                    <th>Kapal / Voyage</th>
                                                    <th>Biaya</th>
                                                    <th>Ditagihkan</th>
                                                    <th>Catatan</th>    
                                                `);
                            $("thead tr").append("<th class='text-center' style='width: 30px;'><input id='check_all' type='checkbox'></th>");
                            
                            $("#hasil").empty();
                            if(data.length > 0){
                                for (var i = 0; i <data.length; i++) {
                                    var row = $("<tr></tr>");
                                    row.append(`<td style='background: #efefef'>
                                                <div class="d-flex justify-content-between" style="margin-right: -13px;">
                                                    <div>${data[i].get_customer.get_grup.nama_grup}</div>
                                                    <div style="width: 55px; text-align: center">                                            
                                                        <input class='check_item check_grup' grup_parent='${data[i].get_customer.get_grup.id}' type='checkbox'>
                                                    </div>
                                                </div>
                                                </td>`);
                                    row.append(`<td style='background: #efefef'>
                                                <div class="d-flex justify-content-between" style="margin-right: -13px;">
                                                    <div>► ${data[i].get_customer.nama}</div>
                                                    <div style="width: 55px; text-align: center">                                            
                                                        <input class='check_item check_cust' grup_child='${data[i].get_customer.get_grup.id}' cust_parent='${data[i].get_customer.id}' type='checkbox'>
                                                    </div>
                                                </div>
                                                </td>`);
                                    row.append(`<td>${data[i].get_j_o.no_bl}</td>`);
                                    row.append(`<td>${data[i].get_j_o.kapal} ( ${data[i].get_j_o.voyage} )</td>`);
                                    row.append(`<td>${moneyMask(data[i].total_operasional)}</td>`);
                                    row.append(`<td>
                                                    <input type='hidden' id='biaya_${data[i].id}' name='data[${data[i].id}][nominal]' value='${data[i].total_operasional}' class='form-control hiddenNominal' readonly>
                                                    <input type="text" class="uang numaja dicairkan form-control open_cust_${data[i].get_customer.id} open_grup_${data[i].get_customer.get_grup.id}" id='open_${data[i].id}' name='data[${data[i].id}][dicairkan]' sewaOprs='${data[i].id}' value='${data[i].total_dicairkan == null? '':data[i].total_dicairkan}' readonly/>
                                                </td>`);
                                    row.append(`<td class='text-center'> 
                                                    <input class="form-control open_cust_cttn_${data[i].get_customer.id} open_grup_cttn_${data[i].get_customer.get_grup.id}" id='open_cttn_${data[i].id}' name='data[${data[i].id}][catatan]' sewaOprsCttn='${data[i].id}' type="text" readonly/> 
                                                </td>`);
                                    row.append(`<td> 
                                                    <div style="text-align: center">
                                                        <input class='check_item check_container' id_sewa="${data[i].id}" grup_child='${data[i].get_customer.get_grup.id}' cust_child='${data[i].get_customer.id}'  name="data[${data[i].id}][item]" type='checkbox'>
                                                    </div>
                                                </td>`);
                                    $("#hasil").append(row);
                                }
                            }
                            new DataTable('#rowGroup', {
                                    order: [
                                        [0, 'asc'],
                                        [1, 'asc']
                                    ],
                                    rowGroup: {
                                        dataSrc: [0,1]
                                    },
                                    columnDefs: [
                                        {
                                            targets: [0,1],
                                            visible: false
                                        },
                                        { orderable: false, targets: -1 }

                                    ]
                                });
                        }else{
                            $("th").remove();
                            $("thead tr").append(`<th>Grup<th> <th>Tujuan</th><th>Keterangan</th>`);
                            if(item == 'TIMBANG' || item == 'BURUH' || item == 'LEMBUR'){
                                
                            }else{
                                $("thead tr").append("<th>Total</th>");
                            }
                            $("thead tr").append("<th>Dicairkan</th>");
                            $("thead tr").append("<th>Catatan</th>");
                            $("thead tr").append("<th class='text-center'><input id='check_all' type='checkbox'></th>");
                            $("#hasil").html("");
                            var ord = 7;
                            var dataCustomer = null;
                            console.log('data.length', data);
                            if(data.length > 0){
                                for (var i = 0; i <data.length; i++) {
                                    if(data[i].total_dicairkan == null){
                                        var start = data[i].deskripsi_so;
                                        var row = $("<tr></tr>");
                                        row.append(`<td style='background: #efefef'><b> <div> <span> ${data[i].nama_grup}</span> <span class='float-right mr-1'>  <input class='check_item check_grup' grup_parent='${data[i].grup_id}' type='checkbox'> </span> </div> </b></td>`);
                                        row.append(`<td style='background: #efefef'><b> <div> <span>► ${data[i].customer}</span> <span class='float-right mr-1'>  <input class='check_item check_cust' grup_child='${data[i].grup_id}' cust_parent='${data[i].id_customer}' type='checkbox'> </span> </div> </b></td>`);
                                    
                                        row.append(`<td> ${data[i].nama_tujuan} ${ data[i].no_polisi != null? ' / '+data[i].no_polisi:'' } / ${data[i].nama_panggilan?data[i].nama_panggilan:'DRIVER REKANAN '+ data[i].namaSupplier} </td>`);
                                        row.append(`<td> ${data[i].tipe_kontainer != null? data[i].tipe_kontainer+'"':''}<b> ${data[i].jenis_order} </b> ${ data[i].pick_up == null? '':'('+data[i].pick_up+')'} </td>`);
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
                                        }else if(item == 'SEAL PELAYARAN'){
                                            nominal = data[i].seal_pelayaran;       
                                        }
                                        if(item == 'TIMBANG' || item == 'BURUH' || item == 'LEMBUR' || item == 'TAMBAHAN UJ'){
                                            if(item == 'TAMBAHAN UJ'){
                                                // nominal = data[i].uj_tujuan - data[i].uj_sewa;
                                            }
                                            ord = 6;
                                        }else{
                                            row.append(`<td> ${nominal.toLocaleString()} </td>`);
                                        }
                                        var driver = (data[i].namaSupplier == null)? data[i].nama_panggilan : data[i].namaSupplier;
                                        var keterangan = data[i].nama_tujuan+'/'+data[i].no_polisi+'/'+driver;
                                        var tambahanUJ = '';
        
                                        if(item == 'TAMBAHAN UJ'){
                                            var inputan = `<input type="text" class="uang numaja dicairkan form-control open_cust_${data[i].id_customer} open_grup_${data[i].grup_id}" id='open_${data[i].id_sewa}' name='data[${data[i].id_sewa}][dicairkan]' sewaOprs='${data[i].id_sewa}' value='${moneyMask(data[i].uj_tujuan - data[i].uj_sewa)}' readonly/>`; 
                                            tambahanUJ =`<input type="hidden" name="data[${data[i].id_sewa}][tambahan_uj]" value="Y" />`;
                                        }else{
                                            var inputan = `<input type="text" class="uang numaja dicairkan form-control open_cust_${data[i].id_customer} open_grup_${data[i].grup_id}" id='open_${data[i].id_sewa}' name='data[${data[i].id_sewa}][dicairkan]' sewaOprs='${data[i].id_sewa}' value='${data[i].total_dicairkan == null? '':data[i].total_dicairkan}' readonly/>`; 
                                        }
                                        row.append(`<td> 
                                                        ${inputan}
                                                        ${tambahanUJ}
                                                        <input type="hidden" name="data[${data[i].id_sewa}][pick_up]" value="${data[i].pick_up}" />
                                                        <input type="hidden" name="data[${data[i].id_sewa}][keterangan]" value="${keterangan.replace(/"/g, '')}" />
                                                    </td>`);
                                        row.append(`<td class='text-center'> 
                                                        <input class="form-control open_cust_cttn_${data[i].id_customer} open_grup_cttn_${data[i].grup_id}" id='open_cttn_${data[i].id_sewa}' name='data[${data[i].id_sewa}][catatan]' sewaOprsCttn='${data[i].id_sewa}' type="text" readonly/> 
                                                    </td>`);
                                        row.append(`<td class='text-center'> 
                                                        <input class='check_item check_container' id_sewa="${data[i].id_sewa}" grup_child='${data[i].grup_id}' cust_child='${data[i].id_customer}'  name="data[${data[i].id_sewa}][item]" type='checkbox'> 
                                                        <input type='hidden' id='biaya_${data[i].id_sewa}' name='data[${data[i].id_sewa}][nominal]' value='${(item == 'TIMBANG' || item == 'BURUH' || item == 'LEMBUR') ? $('#open_' + data[i].id_sewa).val() : nominal}' class='form-control hiddenNominal' readonly>
                                                        <input type='hidden' name='data[${data[i].id_sewa}][customer]' value='${data[i].customer}' class='form-control' readonly>
                                                        <input type='hidden' name='data[${data[i].id_sewa}][supplier]' value='${data[i].namaSupplier}' class='form-control' readonly>
                                                        <input type='hidden' name='data[${data[i].id_sewa}][tujuan]' value='${data[i].nama_tujuan}' class='form-control' readonly>
                                                        <input type='hidden' name='data[${data[i].id_sewa}][driver]' value='${data[i].nama_panggilan}' class='form-control' readonly>
                                                        <input type='hidden' name='data[${data[i].id_sewa}][nopol]' value='${data[i].no_polisi}' class='form-control' readonly>
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
                            }
                        }

                        
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
                var item = $('#item').val();
                var isChecked = $('#check_all').prop('checked');
                var readonlyValue = isChecked ? false : true; // Set to true when not checked (isChecked is false)
                if(readonlyValue == true){
                    if(item != 'TAMBAHAN UJ'){
                        $(`[id^="open_"]`).val('');
                    }else{
                        $(`[id^="open_cttn_"]`).val('');
                    }
                }
                if(item != 'TAMBAHAN UJ'){
                    $('[id^="open_"]').prop('readonly', readonlyValue);
                }else{
                    $('[id^="open_cttn_"]').prop('readonly', readonlyValue);
                }
            }
            $(document).on('change', '#check_all', function() {  
                toggleReadonlyAll();
                $(".check_item").prop('checked', $(this).prop('checked'));
                hitung();
            });
            $(document).on('change', '#check_all_pick_up', function() {  
                $(".check_item_pick_up").prop('checked', $(this).prop('checked'));
            });
        //

        // check per grup
            function toggleReadonlyGrup(grup_id) {
                var item = $('#item').val();
                var checkbox = $(`.check_grup[grup_parent="${grup_id}"]`);
                var inputElements = $('.open_grup_' + grup_id);
                var cttnElements = $('.open_grup_cttn_' + grup_id);
                if (checkbox.prop('checked')) {
                    if(item != 'TAMBAHAN UJ'){
                        inputElements.prop('readonly', false);
                    }
                    cttnElements.prop('readonly', false);
                } else {
                    if(item != 'TAMBAHAN UJ'){
                        inputElements.val('');
                    }
                    cttnElements.val('');
                    inputElements.prop('readonly', true);
                    cttnElements.prop('readonly', true);
                    caps();
                }
                hitung();
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
                var item = $('#item').val();
                var checkbox = $(`.check_cust[cust_parent="${cust_id}"]`);
                var inputElements = $('.open_cust_' + cust_id);
                var cttnElements = $('.open_cust_cttn_' + cust_id);

                if (checkbox.prop('checked')) {
                    if(item != 'TAMBAHAN UJ'){
                        inputElements.prop('readonly', false);
                    }
                    cttnElements.prop('readonly', false);
                } else {
                    if(item != 'TAMBAHAN UJ'){
                        inputElements.val('');
                    }
                    cttnElements.val('');
                    inputElements.prop('readonly', true);
                    cttnElements.prop('readonly', true);
                }
                hitung();
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
                var item = $('#item').val();
                
                if(readonlyValue == true){
                    if(item != 'TAMBAHAN UJ'){
                        $(`#open_${inputId}`).val('');
                    }
                    $(`#open_cttn_${inputId}`).val('');
                }
      
                if(item != 'TAMBAHAN UJ'){
                    $(`#open_${inputId}`).prop('readonly', readonlyValue);
                }
                $(`#open_cttn_${inputId}`).prop('readonly', readonlyValue);
                hitung();
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
            var item = $('#item').val();
            console.log('item', item);
            var totalCair = 0;
            var dicairkan = document.querySelectorAll('.dicairkan');

            if(item == 'TAMBAHAN UJ'){
                // Get all checked checkboxes with the class .check_container
                var checkedCheckboxes = document.querySelectorAll('.check_container:checked');

                // Loop through each checked checkbox and get the id_sewa attribute
                checkedCheckboxes.forEach(function(checkbox) {
                    var idSewa = checkbox.getAttribute('id_sewa');
                    totalCair += parseFloat(escapeComma($('#open_'+idSewa).val()));
                });

                var totalElement = document.querySelector('.t_total');
                $('#t_total').val(totalCair);
                totalElement.textContent = "Rp. "+(totalCair).toLocaleString();
            }else{
                for (var i = 0; i < dicairkan.length; i++) {
                    totalCair += parseFloat(dicairkan[i].value.replace(/,/g, '')) || 0; // Convert to a number or use 0 if NaN
                }

                var totalElement = document.querySelector('.t_total');
                $('#t_total').val(totalCair);
                totalElement.textContent = "Rp. "+(totalCair).toLocaleString();
            }
        }

        function caps(){
            $("input").focusout(function () {
                this.value = this.value.toLocaleUpperCase();
            });
        }
    });
</script>
@endsection