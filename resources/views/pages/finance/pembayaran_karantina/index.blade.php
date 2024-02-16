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
a {
    cursor: pointer;
}
</style>
<nav class="navbar navbar-expand-sm bg-light">

<div class="container-fluid " >
    
</div>

</nav>
<div class="container-fluid">
   
    <div class="card radiusSendiri">
        <form id="save" action="{{ route('pembayaran_karantina.store') }}" method="POST">
            @csrf
            <div class="card-header ">
                {{-- style="border: 2px solid #bbbbbb;" --}}
                <div class="card-header" >
                    <ul class="nav nav-tabs mb-3 mt-3 nav-fill" id="justifyTab" role="tablist">
                        <li class="nav-item" style="border-right: 2px solid black">
                            <a class="nav-link nav-link-tab active" data-toggle="tab" id="btn_karantina" aria-selected="false">
                                <span class="text-bold">KARANTINA </span> {{--[Master Karantina]--}}
                            </a>
                        </li>
                    </ul>
                    <ul class="list-inline">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6 bg-white pb-3">
                                <ul class="list-group mt-4">
                                    <li
                                        class="list-group-item d-flex justify-content-between lh-sm card-outline card-primary">
                                        <div>
                                            <span class="text-primary"><b>Grand Total</b></span>
                                        </div>
                                        <span class="text-bold t_total">Rp. 0</span>
                                        <input type="hidden" id='t_total' name='t_total'>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 bg-white pb-3">
                                <div class="input-group mt-4">
                                    <select class="form-control selectpicker" required id='pembayaran' name="pembayaran"
                                        data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                        <option value="">── PILIH PEMBAYARAN ──</option>
                                        @foreach ($dataKas as $kas)
                                        <option value="{{$kas->id}}" {{$kas->id == 1?'selected':''}}>{{ $kas->nama }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-success ml-4" id="bttonBayar"><i class="fa fa-credit-card" aria-hidden="true"></i> Bayar</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{-- select ini dipakai buat akal-akalan menu diatas --}}
                            <div class="form-group" style="display: none;">
                                <select class="form-control selectpicker" required name="item" id="item"
                                    data-live-search="true" data-show-subtext="true" data-placement="bottom" disabled>
                                    {{-- <option value="">­­— PILIH DATA —</option> --}}
                                    <option value="KARANTINA">KARANTINA</option>
                                </select>
                                <br>
                                <br>

                                
                            </div>
                            <div class="customer_div form-group col-sm-12 col-md-6 col-lg-6 bg-white pb-3" id="customer_div">
                                <label for="">Pilih Customer</label>
                                <select class="form-control select2" id='select_customer' name="select_customer"
                                    data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                    {{-- <option value="">── PILIH CUSTOMER ──</option>
                                    @foreach ($dataCustomerSewa as $data)
                                    <option value="{{$data->getCustomer->id}}">{{ $data->getCustomer->nama }} [{{ $data->getCustomer->kode }}]</option>
                                    @endforeach --}}
                                </select>
                                <input type="hidden" name="item_hidden" id="item_hidden" value="ALAT">
                            </div>
                            <div class="customer_div form-group col-sm-12 col-md-6 col-lg-6 bg-white pb-3" id="tujuan_div">
                                    <label for="select_tujuan">Tujuan</label>
                                <select class="form-control select2" style="width: 100%;" id='select_grup_tujuan' name="select_grup_tujuan">
                                </select>
                            </div>
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
            var dicairkan_nol = false;
            var dicairkan_null = false;

            // check apakah sudah ada yg dicentang?
                var checkboxes = document.querySelectorAll('.item');
                checkboxes.forEach(function(checkbox) {
                    if (checkbox.checked) {
                        isOk = 1;
                    }
                });
            //
                var textbox_dicairkan = document.querySelectorAll('.item_dicairkan');
                var textbox_catatan = document.querySelectorAll('.item_catatan');

                for (var i = 0; i < textbox_dicairkan.length; i++) {
                    console.log(checkboxes[i].value);
                    console.log(textbox_dicairkan[i].value=='');
                    console.log(checkboxes[i].value == textbox_dicairkan[i].getAttribute('item')&&checkboxes[i].checked && textbox_dicairkan[i].value == '');
                    // item itu get idnya val itu di checkboxnya
                    //misal val checkbox == 492 sama dengan item attr nya 492, jadi true
                    if (checkboxes[i].value == textbox_dicairkan[i].getAttribute('item') &&checkboxes[i].checked && textbox_dicairkan[i].value == '') {
                        event.preventDefault(); 
                        Swal.fire({
                            icon: 'error',
                            text: 'Jumlah dicairkan harus diisi',
                        });
                        return; 
                    }

                    if (checkboxes[i].value == textbox_dicairkan[i].getAttribute('item') &&checkboxes[i].checked && textbox_dicairkan[i].value == 0&&textbox_catatan[i].value == '') {
                        event.preventDefault(); 
                        Swal.fire({
                            icon: 'error',
                            text: 'Catatan Harus diisi jika pencairan 0 (artinya tidak ada pencairan)',
                        });
                        return; 
                    }
                }
                textbox_dicairkan.forEach(function(dicairkan) {
                    if (dicairkan.value == 0) {
                        dicairkan_nol = true;
                    }
                });
                // console.log(dicairkan_null);
            // if (isOk&&dicairkan_null) {
                   
            //     }
                
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
        $('#customer_div').hide();
        $('#tujuan_div').hide();

        
       
        $(document).on('click', '#btn_karantina', function(e) {  
            $('#item').val('KARANTINA').trigger('change');
            $('#select_customer').empty();
            $('#select_grup_tujuan').empty();

            $('#customer_div').hide();
            $('#tujuan_div').hide();

		});
        $(document).on('change', '#item', function(e) {  
            var item = $('#item').val();
            $('#item_hidden').val(item);
            var totalElement = document.querySelector('.t_total');
            totalElement.textContent = "Rp. 0"; 
            // console.log('item', item);
            var tbody = document.getElementById("hasil");

            if(item == 'KARANTINA'){
                let bank = $('#pembayaran').selectpicker('val', 2);
                showTable(item);
            }else{
                let bank = $('#pembayaran').selectpicker('val', '');
                tbody.innerHTML = "";
            }

            // if(item != null){
            //     showTable(item);
            // }else{
            //     var tbody = document.getElementById("hasil");
            //     tbody.innerHTML = "";
            // }
		});        
        showTable('KARANTINA');
        var date = new Date();
        var options = { day: 'numeric', month: 'short', year: 'numeric' };
        var formattedDate = date.toLocaleDateString('en-US', options);

        // // console.log(date.getDate());
        // // console.log(date.getMonth());
        // console.log(dateMask(date).split('-')[0]-1);
        // console.log(dateMask(date).split('-')[1]);
        // console.log(dateMask(date).split('-')[2]);

        //buat yang alat,tally,seal pelayaran,karantina,biaya depo
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
                var baseUrl = "{{ asset('') }}";
                var url = baseUrl+`biaya_operasional/load_data/${item}`;

                $.ajax({
                    method: 'GET',
                    url: url,
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function(response) {
                        $("#rowGroup").dataTable().fnDestroy();
                        $("th").remove();
                        $("#hasil").empty();
                        var item = $('#item').val();
                        var data = response.data;
                        console.log('data', data);
                        if(item == 'KARANTINA'){
                            $("thead tr").append(`  <th>Grup</th>
                                                    <th>Customer</th>
                                                    <th>No. BL</th>
                                                    <th>Kapal / Voyage</th>
                                                    <th>Ditagihkan</th>
                                                    <th>Dicairkan</th>
                                                    <th>Catatan</th>    
                                                `);
                            $("thead tr").append("<th class='text-center' style='width: 30px;'><input id='check_all' type='checkbox'></th>");
                            
                            if(data.length > 0){
                                for (var i = 0; i <data.length; i++) {
                                    
                                    var row = $("<tr></tr>");
                                    row.append(`<td style='background: #efefef'>
                                                <div class="d-flex justify-content-between" style="margin-right: -13px;">
                                                    <div>${data[i].get_customer.get_grup.nama_grup}</div>
                                                    <div style="width: 55px; text-align: center">                                            
                                                        <input class='grup' id='grup_${data[i].get_customer.get_grup.id}' value="${data[i].get_customer.get_grup.id}" type='checkbox'>
                                                    </div>
                                                </div>
                                                </td>`);
                                    row.append(`<td style='background: #efefef'>
                                                <div class="d-flex justify-content-between" style="margin-right: -13px;">
                                                    <div>► ${data[i].get_customer.nama}</div>
                                                    <div style="width: 55px; text-align: center">                                            
                                                        <input class='grup_${data[i].get_customer.get_grup.id} customer' id="customer_${data[i].get_customer.id}" id_grup="${data[i].get_customer.get_grup.id}" value='${data[i].get_customer.id}' type='checkbox'>
                                                    </div>
                                                </div>
                                                </td>`);
                                    row.append(`<td>${data[i].get_j_o.no_bl}</td>`);
                                    row.append(`<td>${data[i].get_j_o.kapal} ( ${data[i].get_j_o.voyage} )</td>`);
                                    row.append(`<td>${moneyMask(data[i].total_operasional)}</td>`);
                                    row.append(`<td>
                                                    <input type="text" class="form-control uang numaja dicairkan item_dicairkan grup_${data[i].get_customer.get_grup.id} customer_${data[i].get_customer.id} item_${data[i].id}" id="item_${data[i].id}" item="${data[i].id}" name='data[${data[i].id}][dicairkan]' value='${data[i].total_dicairkan == null? '':data[i].total_dicairkan}' readonly/>
                                                    <input type='hidden' id='biaya_${data[i].id}' name='data[${data[i].id}][total_operasional]' value='${data[i].total_operasional}' class='form-control' readonly>
                                                    <input type='hidden' class='cek_cair cek_cair_grup_${data[i].get_customer.get_grup.id} cek_cair_customer_${data[i].get_customer.id} cek_cair_item_${data[i].id}' name='data[${data[i].id}][cek_cair]' value='N' class='form-control' readonly>

                                                </td>`);
                                    row.append(`<td class='text-center'> 
                                                    <input class="form-control item_catatan grup_${data[i].get_customer.get_grup.id} customer_${data[i].get_customer.id} item_${data[i].id}" name='data[${data[i].id}][catatan]' type="text" readonly/> 
                                                </td>`);
                                    row.append(`<td> 
                                                    <div style="text-align: center">
                                                        <input class='item grup_${data[i].get_customer.get_grup.id} customer_${data[i].get_customer.id}' id_grup="${data[i].get_customer.get_grup.id}" id_customer="${data[i].get_customer.id}" name="data[${data[i].id}][item]" value="${data[i].id}" type='checkbox'>
                                                    </div>
                                                </td>`);
                                    $("#hasil").append(row);
                                }
                            }
                        }
                        new DataTable('#rowGroup', {
                            order: [
                                [0, 'asc'], // 0 = grup
                                [1, 'asc'], // 1 = customer
                            ],
                            rowGroup: {
                                dataSrc: [0, 1] // di order grup dulu, baru customer
                            },
                            columnDefs: [
                                {
                                    targets: [0, 1], 
                                    visible: false
                                },
                                {
                                    targets: [-1],
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
        function getCustomer(item)
        {
            var baseUrl = "{{ asset('') }}";
            var select_customer = $('#select_customer');
            $.ajax({
                url: `${baseUrl}biaya_operasional/load_customer_sewa/${item}`, 
                method: 'GET', 
                success: function(response) {
                    // console.log(response);

                    // if(response)
                    // {
                        select_customer.empty(); 
                        select_customer.append('<option value="ALL">── PILIH CUSTOMER ──</option>');
                        response.data.forEach(customer => {
                            const option = document.createElement('option');
                            option.value = customer.get_customer.id;
                            option.textContent = customer.get_customer.nama+ ` [${customer.get_customer.kode} ]`;
                            select_customer.append(option);
                        });
                    // }
                    // else
                    // {
                    //     select_customer.empty(); 
                    //     select_customer.append('<option value="">Tidak ada data</option>');
                    // }
        
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
        function getTujuan(id_customer,item)
        {
            var baseUrl = "{{ asset('') }}";
            var select_grup_tujuan = $('#select_grup_tujuan');
            $.ajax({
                url: `${baseUrl}biaya_operasional/load_tujuan_sewa/${id_customer}/${item}`, 
                method: 'GET', 
                success: function(response) {
                        // console.log(response.data);

                    if(response)
                    {
                        select_grup_tujuan.empty(); 
                        select_grup_tujuan.append('<option value="ALL">── PILIH TUJUAN ──</option>');
                        
                        console.log(response.data);
                        if(id_customer!="")
                        {
                            // row.append(`<td> ${data[i].nama_tujuan} ${ data[i].no_polisi != null? ' #'+data[i].no_polisi:'' } (${data[i].nama_panggilan?data[i].nama_panggilan:'DRIVER REKANAN '+ data[i].namaSupplier}) (${dateMask(data[i].tanggal_berangkat)}) </td>`);
                            // row.append(`<td> ${data[i].tipe_kontainer != null? data[i].tipe_kontainer+'"':''}<b> ${data[i].jenis_order} </b> ${ data[i].pick_up == null? '':'('+data[i].pick_up+')'} </td>`);

                            response.data.forEach(tujuan => {
                                const option = document.createElement('option');
                                option.value = tujuan.id_grup_tujuan;
                                option.textContent = tujuan.nama_tujuan;
                                // option.textContent = tujuan.nama_tujuan+` - [${tujuan.tipe_kontainer}ft]`+ ` ( ${tujuan.nama_driver}[${tujuan.no_polisi}])` + ` (${dateMask(tujuan.tanggal_berangkat)})`;
                                select_grup_tujuan.append(option);
                            });
                        }
                    }
                    // else
                    // {

                    // }
        
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
        $(document).on('change', '#select_customer', function(e) {  
            var item = $('#item_hidden').val();
            var id_customer = $(this).val();
            console.log();

            $('#item_hidden').val(item);
            var totalElement = document.querySelector('.t_total');
            totalElement.textContent = "Rp. 0"; 
            // console.log('item', item);
            var tbody = document.getElementById("hasil");
            getTujuan(id_customer,item);
            if(item == 'TIMBANG' || item == 'BURUH' || item == 'LEMBUR'){
                let bank = $('#pembayaran').selectpicker('val', 1);
                if($(this).val())
                {
                    showTableGabung(item,id_customer,'ALL');
                }
                else
                {
                    // $("#rowGroup").dataTable().fnDestroy();
                    // $("th").remove();
                    // $("#hasil").empty();
                    $("#rowGroup").dataTable().fnDestroy();
                    $("th").remove();
                    $("#hasil").empty();
                    var item = $('#item').val();
                    $("thead tr").append(`<th>Grup<th> <th>Tujuan</th><th>Keterangan</th>`);
                    // if(item != 'TIMBANG' && item != 'BURUH' && item != 'LEMBUR'){
                        $("thead tr").append("<th>Ditagihkan</th>");
                    // }
                    $("thead tr").append(`<th>Dicairkan</th>
                                            <th>Catatan</th>
                                            <th class='text-center'><input id='check_all' type='checkbox'></th>`);
                }
                
            }/*else if(item == 'ALAT' || item == 'TALLY' || item == 'SEAL PELAYARAN' || item == 'KARANTINA'|| item == 'BIAYA DEPO'){
                let bank = $('#pembayaran').selectpicker('val', 2);
            }*/else{
                let bank = $('#pembayaran').selectpicker('val', '');
                tbody.innerHTML = "";
            }

            // if(item != null){
            //     showTable(item);
            // }else{
            //     var tbody = document.getElementById("hasil");
            //     tbody.innerHTML = "";
            // }
		});   

        $(document).on('change', '#select_grup_tujuan', function(e) {  
            var item = $('#item_hidden').val();
            var id_tujuan = $(this).val();
            var id_customer = $('#select_customer').val();
            if(item == 'TIMBANG' || item == 'BURUH' || item == 'LEMBUR'){
                let bank = $('#pembayaran').selectpicker('val', 1);
                if($(this).val())
                {
                    showTableGabung(item,id_customer,id_tujuan);
                }
                else
                {
                    // $("#rowGroup").dataTable().fnDestroy();
                    // $("th").remove();
                    // $("#hasil").empty();
                    $("#rowGroup").dataTable().fnDestroy();
                    $("th").remove();
                    $("#hasil").empty();
                    var item = $('#item').val();
                    $("thead tr").append(`<th>Grup<th> <th>Tujuan</th><th>Keterangan</th>`);
                    // if(item != 'TIMBANG' && item != 'BURUH' && item != 'LEMBUR'){
                        $("thead tr").append("<th>Ditagihkan</th>");
                    // }
                    $("thead tr").append(`<th>Dicairkan</th>
                                            <th>Catatan</th>
                                            <th class='text-center'><input id='check_all' type='checkbox'></th>`);
                }
                
            }/*else if(item == 'ALAT' || item == 'TALLY' || item == 'SEAL PELAYARAN' || item == 'KARANTINA'|| item == 'BIAYA DEPO'){
                let bank = $('#pembayaran').selectpicker('val', 2);
            }*/else{
                let bank = $('#pembayaran').selectpicker('val', '');
                tbody.innerHTML = "";
            }

            // if(item != null){
            //     showTable(item);
            // }else{
            //     var tbody = document.getElementById("hasil");
            //     tbody.innerHTML = "";
            // }
		});   

        
        // check all
        $(document).on('click', '#check_all', function() {  
            let isChecked = this.checked;
            $(".grup").prop('checked', isChecked);
            $(".customer").prop('checked', isChecked);
            $(".item").prop('checked', isChecked);
            // $(".cek_cair_grup_"+id_grup).val('Y');
            // $(".cek_cair_customer_"+customer_id).val('Y');
            $(".cek_cair").val('Y');

            $(".item_dicairkan").prop('readonly', !isChecked);
            $(".item_ditagihkan").prop('readonly', !isChecked);
            $(".item_catatan").prop('readonly', !isChecked);
            hitung();
        });
        //

        // check per grup
        $(document).on('click', '.grup', function() {
            let id_grup = this.value;
            let isChecked = this.checked;

            $(".grup_"+id_grup).prop('checked', isChecked);
            $(".grup_"+id_grup).prop('readonly', !isChecked);
            $(".cek_cair_grup_"+id_grup).val('Y');
            $("#check_all").prop('checked', false);
            hitung();
        });
        //
 
        // check per customer
        $(document).on('click', '.customer', function() {
            let customer_id = this.value;
            let id_grup = this.getAttribute('id_grup');
            let isChecked = this.checked;

            $(".customer_"+customer_id).prop('checked', isChecked);
            $(".customer_"+customer_id).prop('readonly', !isChecked);
            $(".cek_cair_customer_"+customer_id).val('Y');

            $("#check_all").prop('checked', false);
            $("#grup_"+id_grup).prop('checked', false);
            hitung();
        });
        //

        // per item
        $(document).on('click', '.item', function (event) {
            let id = this.value;
            let id_grup = this.getAttribute('id_grup');
            let id_customer = this.getAttribute('id_customer');
            let isChecked = this.checked;

            $(".item_"+id).prop('checked', isChecked);
            $(".item_"+id).prop('readonly', !isChecked);
            $(".item_ditagihkan"+id).prop('readonly', !isChecked);
            $(".cek_cair_item_"+id).val('Y');
            $("#check_all").prop('checked', false);
            $("#grup_"+id_grup).prop('checked', false);
            $("#customer_"+id_customer).prop('checked', false);
            hitung();
        });
        //
        
        $(document).on('keyup', '.dicairkan', function(){
            let id = this.getAttribute('item');
            var inputed = normalize(this.value);

            if(item.value == 'TIMBANG' || item.value == 'BURUH' || item.value == 'LEMBUR'|| item.value == 'ALAT'|| item.value == 'TALLY'|| item.value == 'SEAL PELAYARAN'||item.value == 'BIAYA DEPO'){
                $('#biaya_'+id).val(inputed);
            }else{
                var max = $('#biaya_'+id).val();
                if(inputed > max){
                    $('#item_'+id).val(parseFloat(max).toLocaleString()); 
                }
            }
            hitung();
        });

        function hitung(){
            var totalCair = 0;
            var dicairkan = document.querySelectorAll('.dicairkan');
            var cek_cair = document.querySelectorAll('.cek_cair');
            for (var i = 0; i < dicairkan.length; i++) {
                if(dicairkan[i].readOnly == false)
                {
                    // cek_cair[i].value='N';
                    totalCair += parseFloat(dicairkan[i].value.replace(/,/g, '')) || 0; // Convert to a number or use 0 if NaN
                }
                if(dicairkan[i].readOnly == true)
                {
                    cek_cair[i].value='N';
                }
            }
            var totalElement = document.querySelector('.t_total');
            $('#t_total').val(totalCair);
            totalElement.textContent = "Rp. "+(totalCair).toLocaleString();
        }

        function caps(){
            $("input").focusout(function () {
                this.value = this.value.toLocaleUpperCase();
            });
        }
        
    });
</script>
@endsection