
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
                    <div class="row" >
                        <div class="col-6">
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
                        <div class="col-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success mt-4" ><span class="fas fa-save"></span> <b>Simpan</b></button>
                            </div>
                        </div>
                     
                        {{-- <div class="col-9">
                            <div class="row">
                                <div class="col-6">
                                    <ul class="list-group mb-3">
                                        <label for="">Total</label>
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>Total (IDR)</span>
                                             <input type="hidden" name="total_sblm_dooring" value="">
                                                 <strong>Rp. 15,000,000.00</strong>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Pilih Kas</label>
                                        <div class="row">
                                            <div class="col-9">
                                                <select class="form-control selectpicker"  id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                                    <option value="">--PILIH PEMBAYARAN--</option>
                                                    @foreach ($dataKas as $data)
                                                        <option value="{{$data->id}}">{{ $data->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <button type="button" class="btn btn-success " id="bttonBayar"><i class="fa fa-credit-card" aria-hidden="true" ></i> Bayar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        
            <div class="card-body">
                <section class="col-lg-12" id="show_report">

                <table id="biaya_operasional" class="table table-bordered table-hover">
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
            if (item == '' || item == null) {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: 'error',
                    text: 'Harap pilih item dahulu!',
                })
                return;
            }

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
            $.ajax({
                method: 'GET',
                url: `biaya_operasional/load_data/${item}`,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    $("#loading-spinner").hide();
                    var item = $('#item').val();
                    var data = response.data;

                    $("th").remove();
                    $("thead tr").append("<th>###</th><th>Tipe</th>");
                    $("thead tr").append("<th>Jenis</th>");
              
                    if(item == 'TALLY'){
                        $("thead tr").append("<th>Tally</th>");
                        $("thead tr").append("<th class='text-center'><input class='' id='check_all' type='checkbox'></th>");
                    }else if(item == 'OPERASIONAL'){
                        $("thead tr").append("<th>TPS</th>");
                        $("thead tr").append("<th class='text-center'><input class='' id='check_all_tps' type='checkbox'></th>");
                        $("thead tr").append("<th>TTL</th>");
                        $("thead tr").append("<th class='text-center'><input class='' id='check_all_ttl' type='checkbox'></th>");
                        $("thead tr").append("<th>DEPO</th>");
                        $("thead tr").append("<th class='text-center'><input class='' id='check_all_depo' type='checkbox'></th>");
                    }else if(item == 'TIMBANG'){
                        $("thead tr").append("<th>Timbang</th>");
                        $("thead tr").append("<th class='text-center'><input class='' id='check_all' type='checkbox'></th>");
                    }else if(item == 'BURUH'){
                        $("thead tr").append("<th>Buruh</th>");
                        $("thead tr").append("<th class='text-center'><input class='' id='check_all' type='checkbox'></th>");
                    }                    
                    $("#hasil").html("");

                    var dataCustomer = null;
                    for (var i = 0; i <data.length; i++) {
                        if(data[i].deskripsi_so != item ){
                            if (data[i].id_customer !== dataCustomer) {
                                var row = $("<tr></tr>");
                                var colspan = 4;
                                if(item == 'TALLY'){
                                    row.append(`<td colspan='${colspan}' style='background: #efefef'><b> ${data[i].customer} </b></td>`);
                                    row.append(`<td style='background: #efefef' class='text-center'>
                                                    <input class='check_item check_cust' cust_parent='${data[i].id_customer}' 
                                                         type='checkbox'> 
                                                </td>`);
                                }else if(item == 'OPERASIONAL'){
                                    row.append(`<td colspan='${colspan}' style='background: #efefef'><b> ${data[i].customer} </b></td>`);
                                    row.append(`<td style='background: #efefef' class='text-center'>
                                                    <input class='check_item_tps check_cust_tps check_cust_tps_${data[i].id_customer}' cust_parent='${data[i].id_customer}' 
                                                        opr='tps' type='checkbox'> 
                                                </td>`);
                                    row.append(`<td style='background: #efefef'></td>`);
                                    row.append(`<td style='background: #efefef' class='text-center'>
                                                    <input class='check_item_ttl check_cust_ttl check_cust_ttl_${data[i].id_customer}' cust_parent='${data[i].id_customer}' 
                                                        opr='ttl' type='checkbox'> 
                                                </td>`);
                                    row.append(`<td style='background: #efefef'></td>`);
                                    row.append(`<td style='background: #efefef' class='text-center'>
                                                    <input class='check_item_depo check_cust_depo check_cust_depo_${data[i].id_customer}' cust_parent='${data[i].id_customer}' 
                                                        opr='depo' type='checkbox'> 
                                                </td>`);
                                }else if(item == 'TIMBANG'){
                                    row.append(`<td colspan='${colspan}' style='background: #efefef'><b> ${data[i].customer} </b></td>`);
                                    row.append(`<td style='background: #efefef' class='text-center'>
                                                    <input class='check_item check_cust check_tb_${data[i].id_sewa}' cust_parent='${data[i].id_customer}' 
                                                         type='checkbox'> 
                                                </td>`);
                                }else if(item == 'BURUH'){
                                    row.append(`<td colspan='${colspan}' style='background: #efefef'><b> ${data[i].customer} </b></td>`);
                                    row.append(`<td style='background: #efefef' class='text-center'>
                                                    <input class='check_item check_cust check_tb_${data[i].id_sewa}' cust_parent='${data[i].id_customer}' 
                                                         type='checkbox'> 
                                                </td>`);
                                }
                                $("#hasil").append(row);
                                dataCustomer = data[i].id_customer;
                            }
                            var row = $("<tr class='hoverEffect'></tr>");
    
                            row.append(`<td> ${data[i].nama_tujuan} / ${data[i].no_polisi} / ${data[i].nama_panggilan} / ${data[i].id_oprs} / ${data[i].deskripsi_so} </td>`);
                            row.append(`<td> ${data[i].tipe_kontainer}" </td>`);
                            row.append(`<td><b> ${data[i].jenis_order} </b></td>`);
    
                            if(data[i].jenis_order == 'INBOUND'){
                                if(data[i].tipe_kontainer=='20'){
                                    var ttl = 0;
                                    var tps = 0;
                                    var depo = 15000;
                                }else{
                                    var ttl = 0;
                                    var tps = 0;
                                    var depo = 25000;
                                }
                            }else{
                                if(data[i].tipe_kontainer=='20'){
                                    var ttl = 15000;
                                    var tps = 15000;
                                    var depo = 15000;
                                }else{
                                    var ttl = 25000;
                                    var tps = 25000;
                                    var depo = 25000;
                                }
                            }
                            if(item == 'TALLY'){
                                row.append(`<td> ${data[i].tally.toLocaleString()} 
                                                <input type="hidden" value='${data[i].tally}' name='data[${data[i].id_sewa}][nominal]' /> 
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <input class='check_item check_container'  
                                                    id_sewa="${data[i].id_sewa}" cust_child='${data[i].id_customer}' name="data[${data[i].id_sewa}][item]" type='checkbox'> 
                                            </td>`);
                            } else if(item == 'OPERASIONAL'){
                                row.append(`<td> ${tps.toLocaleString()}
                                                <input type="hidden" value='${tps}' name='data[${data[i].id_sewa}][TPS][nominal]' /> 
                                                <input type="hidden" value='TPS' name='data[${data[i].id_sewa}][TPS][jenis]' /> 
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <input class='check_item_tps check_container_tps'  
                                                    opr='tps' id_sewa="${data[i].id_sewa}" cust_child_tps='${data[i].id_customer}' name="data[${data[i].id_sewa}][TPS][item]" type='checkbox'> 
                                            </td>`);
                                row.append(`<td> ${ttl.toLocaleString()} 
                                                <input type="hidden" value='${ttl}' name='data[${data[i].id_sewa}][TTL][nominal]' /> 
                                                <input type="hidden" value='TTL' name='data[${data[i].id_sewa}][TTL][jenis]' /> 
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <input class='check_item_ttl check_container_ttl'  
                                                    opr='ttl' id_sewa="${data[i].id_sewa}" cust_child_ttl='${data[i].id_customer}' name="data[${data[i].id_sewa}][TTL][item]" type='checkbox'> 
                                            </td>`);
                                row.append(`<td> ${depo.toLocaleString()}
                                                <input type="hidden" value='${depo}' name='data[${data[i].id_sewa}][DEPO][nominal]' /> 
                                                <input type="hidden" value='DEPO' name='data[${data[i].id_sewa}][DEPO][jenis]' /> 
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <input class='check_item_depo check_container_depo'  
                                                    opr='depo' id_sewa="${data[i].id_sewa}" cust_child_depo='${data[i].id_customer}' name="data[${data[i].id_sewa}][DEPO][item]" type='checkbox'>  
                                            </td>`);
                            } else if(item == 'TIMBANG'){
                                row.append(`<td> 
                                                <input type="text" class="uang numaja form-control open_cust_${data[i].id_customer}" id='open_${data[i].id_sewa}' name='data[${data[i].id_sewa}][nominal]' readonly/> 
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <input class='check_item check_container'  
                                                    id_sewa="${data[i].id_sewa}" cust_child='${data[i].id_customer}' name="data[${data[i].id_sewa}][item]" type='checkbox'> 
                                            </td>`);
                            } else if(item == 'BURUH'){
                                row.append(`<td> 
                                                <input type="text" class="uang numaja form-control open_cust_${data[i].id_customer}" id='open_${data[i].id_sewa}' name='data[${data[i].id_sewa}][nominal]' readonly/> 
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <input class='check_item check_container'  
                                                    id_sewa="${data[i].id_sewa}" cust_child='${data[i].id_customer}' name="data[${data[i].id_sewa}][item]" type='checkbox'> 
                                            </td>`);
                            }
                            $("#hasil").append(row);
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
        
        // check all
            function toggleReadonlyAll() {
                var isChecked = $('#check_all').prop('checked');
                $('[id^="open_"]').prop('readonly', !isChecked);
            }
            $(document).on('change', '#check_all', function() {  
                toggleReadonlyAll();
                $(".check_item").prop('checked', $(this).prop('checked'));
            });
            $(document).on('change', '#check_all_tps', function() {  
                $(".check_item_tps").prop('checked', $(this).prop('checked'));
                // toggleReadonlyAllTps();
            });
            $(document).on('change', '#check_all_ttl', function() {  
                $(".check_item_ttl").prop('checked', $(this).prop('checked'));
                // toggleReadonlyAllTtl();
            });
            $(document).on('change', '#check_all_depo', function() {  
                $(".check_item_depo").prop('checked', $(this).prop('checked'));
                // toggleReadonlyAllDepo();
            });
        //

        // check per customer
            function toggleReadonlyCust(cust_id) {
                var checkbox = $(`.check_cust[cust_parent="${cust_id}"]`);
                var inputElements = $('.open_cust_' + cust_id);
                if (checkbox.prop('checked')) {
                    inputElements.prop('readonly', false);
                } else {
                    inputElements.prop('readonly', true);
                }
            }
            $(document).on('change', '.check_cust, .check_cust_tps, .check_cust_ttl, .check_cust_depo', function() {
                var opr = $(this).attr('opr');
                var cust_id = $(this).attr('cust_parent');
                var child = $(`input[cust_child="${cust_id}"]`);
                var child_opr = $(`input[cust_child_${opr}="${cust_id}"]`);
                child.prop('checked', $(this).prop('checked'));
                child_opr.prop('checked', $(this).prop('checked'));
                $("#check_all").prop('checked', false);

                toggleReadonlyCust(cust_id);
            });
        //

        // uncheck all
            function toggleReadonly(inputId) {
                var isChecked = $(`input[id_sewa='${inputId}']`).prop('checked');
                $(`#open_${inputId}`).prop('readonly', !isChecked);
            }
            $(document).on('click', '.check_container', function (event) {
                $("#check_all").prop('checked', false);
                var cust_id = $(this).attr('cust_child');
                var cust_x = $(this).attr('cust_child');
                var id_sewa = $(this).attr('id_sewa');

                $(`input[cust_parent="${cust_id}"]`).prop('checked', false);

                toggleReadonly(id_sewa);
            });
            
            $(document).on('click', '.check_container_tps, .check_container_ttl, .check_container_depo', function (event) {
                var opr = $(this).attr('opr');
                var cust_id = $(this).attr('cust_child');
                $(`#check_all_${opr}`).prop('checked', false);
                $(`.check_cust_${opr}_${cust_id}`).prop('checked', false);
            });
        //
        
    });
</script>
@endsection