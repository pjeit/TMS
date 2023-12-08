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
                <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i
                        class="fa fa-fw fa-save"></i> Simpan</button>
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
                            <div class="col-sm-12 col-md-3 col-lg-3 bg-white pb-3">
                                <div class="form-group">
                                    <label for="">Jenis Biaya</label>
                                    <select class="form-control selectpicker" required name="item" id="item"
                                        data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                        <option value="">­­— PILIH DATA —</option>
                                        <option value="TALLY">TALLY</option>
                                        <option value="SEAL PELAYARAN">SEAL PELAYARAN</option>
                                        <option value="ALAT">ALAT</option>
                                        <option value="TIMBANG">TIMBANG</option>
                                        <option value="BURUH">BURUH</option>
                                        <option value="LEMBUR">LEMBUR</option>
                                        <option value="KARANTINA">KARANTINA</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-4 col-lg-4 bg-white pb-3">
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

                            <div class="col-sm-12 col-md-5 col-lg-5 bg-white pb-3">
                                <div class="input-group mt-4">
                                    <select class="form-control selectpicker" required id='pembayaran' name="pembayaran"
                                        data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                        <option value="">── PILIH PEMBAYARAN ──</option>
                                        @foreach ($dataKas as $kas)
                                        <option value="{{$kas->id}}" {{$kas->id == '1'? 'selected':''}} >{{
                                            $kas->nama }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-success ml-4" id="bttonBayar"><i class="fa fa-credit-card" aria-hidden="true"></i> Bayar</button>
                                </div>
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
                                                    <input type='hidden' id='biaya_${data[i].id}' name='data[${data[i].id}][nominal]' value='${data[i].total_operasional}' class='form-control' readonly>
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
                        }else{
                            $("thead tr").append(`<th>Grup<th> <th>Tujuan</th><th>Keterangan</th>`);
                            if(item != 'TIMBANG' && item != 'BURUH' && item != 'LEMBUR'){
                                $("thead tr").append("<th>Ditagihkan</th>");
                            }
                            $("thead tr").append(`<th>Dicairkan</th>
                                                    <th>Catatan</th>
                                                    <th class='text-center'><input id='check_all' type='checkbox'></th>`);

                            if(data.length > 0){
                                for (var i = 0; i < data.length; i++) {
                                    if(data[i].total_dicairkan == null){
                                        console.log('ok');
                                        var start = data[i].deskripsi_so;
                                        var nominal = 0;
                                        var row = $("<tr></tr>");
                                        row.append(`<td style='background: #efefef'><b> <div> <span> ${data[i].nama_grup}</span> <span class='float-right mr-1'>  <input id="grup_${data[i].grup_id}" class='grup' type='checkbox' value="${data[i].grup_id}"> </span> </div> </b></td>`);
                                        row.append(`<td style='background: #efefef'><b> <div> <span>► ${data[i].customer}</span> <span class='float-right mr-1'>  <input id="customer_${data[i].id_customer}" id_grup="${data[i].grup_id}" class='grup_${data[i].grup_id} customer' type='checkbox' value="${data[i].id_customer}"> </span> </div> </b></td>`);
                                        row.append(`<td> ${data[i].nama_tujuan} ${ data[i].no_polisi != null? ' #'+data[i].no_polisi:'' } (${data[i].nama_panggilan?data[i].nama_panggilan:'DRIVER REKANAN '+ data[i].namaSupplier}) </td>`);
                                        row.append(`<td> ${data[i].tipe_kontainer != null? data[i].tipe_kontainer+'"':''}<b> ${data[i].jenis_order} </b> ${ data[i].pick_up == null? '':'('+data[i].pick_up+')'} </td>`);
                                        if(data[i].jenis_order == 'INBOUND'){
                                            if(data[i].tipe_kontainer=='20'){
                                                if(data[i].pick_up == 'DEPO' || data[i].pick_up == 'TTL'){
                                                    // hanya keluar ketika depo atau TTL tapi kalau TL cuma ketika empty (yg tau nanti adminnya, dibuka saja)
                                                    nominal = 15000;
                                                }
                                            }else{
                                                if(data[i].pick_up == 'DEPO' || data[i].pick_up == 'TTL'){
                                                    // hanya keluar ketika depo atau TTL tapi kalau TL cuma ketika empty (yg tau nanti adminnya, dibuka saja)
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
                                        if(item == 'TIMBANG' || item == 'BURUH' || item == 'LEMBUR'){
                                            nominal = 200000; // set default value biar kalau angka 0 di hidden
                                        }else{
                                            row.append(`<td> ${nominal.toLocaleString()} </td>`);
                                        }
                                        var driver = (data[i].namaSupplier == null)? data[i].nama_panggilan : data[i].namaSupplier;
                                        var keterangan = data[i].nama_tujuan+'/'+data[i].no_polisi+'/'+driver;
                                        
                                        row.append(`<td> 
                                                        <input type="text" class="uang numaja dicairkan form-control item_dicairkan grup_${data[i].grup_id} customer_${data[i].id_customer} item_${data[i].id_sewa}" id="item_${data[i].id_sewa}" item="${data[i].id_sewa}" name='data[${data[i].id_sewa}][dicairkan]' value='${data[i].total_dicairkan == null? '':data[i].total_dicairkan}' readonly/>
                                                        <input type='hidden' id='biaya_${data[i].id_sewa}' name='data[${data[i].id_sewa}][nominal]' value='${(item == 'TIMBANG' || item == 'BURUH' || item == 'LEMBUR') ? '' : nominal}' class='form-control' readonly>
                                                        <input type="hidden" name="data[${data[i].id_sewa}][pick_up]" value="${data[i].pick_up}" />
                                                        <input type="hidden" name="data[${data[i].id_sewa}][keterangan]" value="${keterangan.replace(/"/g, '')}" />
                                                    </td>`);
                                        row.append(`<td class='text-center'> 
                                                        <input class="form-control item_catatan grup_${data[i].grup_id} customer_${data[i].id_customer} item_${data[i].id_sewa}" name='data[${data[i].id_sewa}][catatan]' type="text" readonly/> 
                                                    </td>`);
                                        row.append(`<td class='text-center'> 
                                                        <input class='item grup_${data[i].grup_id} customer_${data[i].id_customer}' name="data[${data[i].id_sewa}][item]" id_grup="${data[i].grup_id}" id_customer="${data[i].id_customer}" type='checkbox' value="${data[i].id_sewa}"> 
                                                        <input type='hidden' name='data[${data[i].id_sewa}][customer]' value='${data[i].customer}' class='form-control' readonly>
                                                        <input type='hidden' name='data[${data[i].id_sewa}][supplier]' value='${data[i].namaSupplier}' class='form-control' readonly>
                                                        <input type='hidden' name='data[${data[i].id_sewa}][tujuan]' value='${data[i].nama_tujuan}' class='form-control' readonly>
                                                        <input type='hidden' name='data[${data[i].id_sewa}][driver]' value='${data[i].nama_panggilan}' class='form-control' readonly>
                                                        <input type='hidden' name='data[${data[i].id_sewa}][nopol]' value='${data[i].no_polisi}' class='form-control' readonly>
                                                    </td>`);
                                                    
                                        // let allowedItems = ['TIMBANG', 'BURUH', 'LEMBUR'];
                                        if(nominal != 0){
                                            $("#hasil").append(row);
                                        }
                                    }
                                }
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
        
        // check all
            $(document).on('click', '#check_all', function() {  
                let isChecked = this.checked;
                $(".grup").prop('checked', isChecked);
                $(".customer").prop('checked', isChecked);
                $(".item").prop('checked', isChecked);
                $(".item_dicairkan").prop('readonly', !isChecked);
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
                $("#check_all").prop('checked', false);
                $("#grup_"+id_grup).prop('checked', false);
                $("#customer_"+id_customer).prop('checked', false);
                hitung();
            });
        //
        
        $(document).on('keyup', '.dicairkan', function(){
            let id = this.getAttribute('item');
            var inputed = normalize(this.value);

            if(item.value == 'TIMBANG' || item.value == 'BURUH' || item.value == 'LEMBUR'){
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

            for (var i = 0; i < dicairkan.length; i++) {
                totalCair += parseFloat(dicairkan[i].value.replace(/,/g, '')) || 0; // Convert to a number or use 0 if NaN
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