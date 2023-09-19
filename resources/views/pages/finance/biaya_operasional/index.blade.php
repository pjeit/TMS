
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
    tr.group,
    tr.group:hover {
        background-color: #ddd !important;
    }
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
                        <div class="col-4">
                            <div class="form-group">
                                <label for="">ITEM</label>
                                <select class="form-control selectpicker" name="item" id="item" data-live-search="true" data-show-subtext="true" data-placement="bottom" >
                                    <option value="">­­— PILIH DATA —</option>
                                    <option value="TALLY">TALLY</option>
                                    <option value="PLASTIK">PLASTIK</option>
                                    <option value="SEAL">SEAL</option>
                                </select>
                            </div>
                        </div>
        
                        <div class="col-4">
                            <label for="">&nbsp;</label>
                            <div class="d-flex justify-content-start col-12" style="gap: 5px;">
                                {{-- <button type="button" id="btnKu" class=" btn btn-primary radiusSendiri col-6" onclick=""><i class="fas fa-search"></i> <b> Filter</b></button> --}}
                                <button type="submit" class=" btn btn-success radiusSendiri col-6" onclick=""><i class="fa fa-fw fa-save"></i> <b> Simpan</b></button>
                            </div>
                        </div>
                    </div>
                        
                    <div class="form-group">
                        {{-- <button type="button" class="btn btn-sm btn-success" onclick="download_report()"><i class="fas fa-file-excel"></i> Export to Excel</button> --}}
                    </div>
                </div>
            </div>
        
            <div class="card-body">
                <section class="col-lg-12" id="show_report">

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No. Kontainer</th>
                            <th>Status Kontainer</th>
                            <th style="width:30px"><div class="btn-group"></div></th>
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
                url: `{{ url('biaya_operasional/load_data')}}`,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    $("#loading-spinner").hide();
                    var item = $('#item').val();
                    var data = response.data;

                    $("th").remove();
                    $("thead tr").append("<th>###</th><th>Status Kontainer</th>");
              
                    if(item == 'PLASTIK'){
                        $("thead tr").append("<th>Plastik</th>");
                        $("thead tr").append("<th class='text-center'><input class='check_all' type='checkbox' id='check_all'></th>");
                    } else if(item == 'TALLY'){
                        $("thead tr").append("<th>Tally</th>");
                        $("thead tr").append("<th class='text-center'><input class='check_all' type='checkbox' id='check_all'></th>");
                    } else if(item == 'SEAL'){
                        $("thead tr").append("<th>Seal Pelayaran</th>");
                        $("thead tr").append("<th class='text-center'><input class='check_all' type='checkbox' id='check_all'></th>");
                        $("thead tr").append("<th>Seal PJE</th>");
                        $("thead tr").append("<th class='text-center'><input class='check_all_pje' type='checkbox' id='check_all_pje'></th>");
                    }
                    $("#hasil").html(" ");

                    var dataJO = null;
                    for (var i = 0; i < data.length; i++) {
                        if (data[i].id_jo !== dataJO) {
                            var row = $("<tr></tr>");
                            
                            if(item == 'SEAL'){
                                var colspan = 3;
                                row.append(`<td colspan='${colspan}' style='background: #efefef'><b> ${data[i].no_bl} / ${data[i].customer} </b></td>`);
                                row.append(`<td style='background: #efefef' class='text-center'><input class='check_cust check_item check_all${data[i].id_jo} check_cust_${data[i].id_jo}' check='${data[i].id_jo}' id='check_all${data[i].id_jo}' type='checkbox'></td>`);
                                row.append(`<td style='background: #efefef'></td>`);
                                row.append(`<td style='background: #efefef' class='text-center'><input class='check_cust_pje check_item_pje check_all_pje${data[i].id_jo} check_cust_pje${data[i].id_jo}' check_pje='${data[i].id_jo}' id='check_all_pje${data[i].id_jo}' type='checkbox'></td>`);
                            }else{
                                var colspan = 3;
                                row.append(`<td colspan='${colspan}' style='background: #efefef'><b> ${data[i].no_bl} / ${data[i].customer} </b></td>`);
                                row.append(`<td style='background: #efefef' class='text-center'><input class='check_item check_cust check_cust_${data[i].id_jo}' check='${data[i].id_jo}' id='check_all${data[i].id_jo}' type='checkbox'></td>`);
                            }

                            $("#hasil").append(row);
                            dataJO = data[i].id_jo;
                        }
                        var row = $("<tr></tr>");
                        row.append(`<td> ${data[i].no_kontainer} / ${data[i].nama_tujuan} </td>`);
                        row.append(`<td> ${data[i].status_jod} </td>`);
                        if(item == 'PLASTIK'){
                            row.append(`<td> ${data[i].plastik.toLocaleString()} 
                                            <input type="hidden" id="plastik_${data[i].plastik}" value="${data[i].plastik}" />
                                        </td>`);
                            row.append(`<td class='text-center'> 
                                            <input type="hidden" value='${data[i].plastik}' name='data[PLASTIK][${data[i].id_sewa}]' /> 
                                            <input name="data[PLASTIK][${data[i].id_sewa}]" class='check_item check_container' 
                                            ${data[i].id_jo}='cek' id_jo="${data[i].id_jo}" id='${data[i].id_jo}_${data[i].id_sewa}' type='checkbox'> 
                                        </td>`);
                        } else if(item == 'TALLY'){
                            row.append(`<td> ${data[i].tally.toLocaleString()} 
                                            <input type="hidden" id="tally_${data[i].tally}" value="${data[i].tally}" />
                                        </td>`);
                            row.append(`<td class='text-center'> 
                                            <input type="hidden" value='${data[i].tally}' name='data[TALLY][${data[i].id_sewa}]' /> 
                                            <input name="data[TALLY][${data[i].id_sewa}]" class='check_item check_container' ${data[i].id_jo}='cek' 
                                                id_jo="${data[i].id_jo}" id='${data[i].id_jo}_${data[i].id_sewa}' type='checkbox'> 
                                        </td>`);
                        } else if(item == 'SEAL'){
                            row.append(`<td> ${data[i].seal_pelayaran.toLocaleString()} 
                                            <input type="hidden" id="seal_pelayaran_${data[i].seal_pelayaran}" value="${data[i].seal_pelayaran}" />
                                        </td>`);
                            row.append(`<td class='text-center'> 
                                            <input type="hidden" value='${data[i].seal_pelayaran}' name='data[SEAL_PELAYARAN][${data[i].id_sewa}]' /> 
                                            <input name="data[SEAL_PELAYARAN][${data[i].id_sewa}]" class='check_item check_container' ${data[i].id_jo}='cek' 
                                                id_jo="${data[i].id_jo}" id='${data[i].id_jo}_${data[i].id_sewa}' type='checkbox'> 
                                        </td>`);
                            row.append(`<td> ${data[i].seal_pje.toLocaleString()} 
                                            <input type="hidden" id="seal_pje_${data[i].seal_pje}" value="${data[i].seal_pje}" />
                                        </td>`);
                            row.append(`<td class='text-center'> 
                                            <input type="hidden" value='${data[i].seal_pje}' name='data[SEAL_PJE][${data[i].id_sewa}]' /> 
                                            <input name="data[SEAL_PJE][${data[i].id_sewa}]" class='check_item_pje check_container_pje' 
                                                pje_${data[i].id_jo}='cek' id_jo_pje="${data[i].id_jo}" id='${data[i].id_jo}_${data[i].id_sewa}' type='checkbox'> 
                                        </td>`);
                        }
                        $("#hasil").append(row);
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
            $(document).on('change', '#check_all', function() {  
                $(".check_item").prop('checked', $(this).prop('checked'));
            });

            $(document).on('change', '#check_all_pje', function() {  
                $(".check_pje").prop('checked', $(this).prop('checked'));
            });

            $(document).on('change', '#check_all_pje', function() {  
                $(".check_item_pje").prop('checked', $(this).prop('checked'));
            });
        // 

        // check per customer
            $(document).on('change', '.check_cust', function() {
                var cust_id = $(this).attr('check');
                var checkElement = $(`input[${cust_id}="cek"]`);
                checkElement.prop('checked', $(this).prop('checked'));
                $("#check_all").prop('checked', false);
            });
            $(document).on('change', '.check_cust_pje', function() {
                var cust_id = $(this).attr('check_pje');
                var checkElement = $(`input[pje_${cust_id}="cek"]`);
                checkElement.prop('checked', $(this).prop('checked'));
                $("#check_all_pje").prop('checked', false);
            });
        //

        // uncheck all
            $(document).on('click', '.check_container', function (event) {
                var id_jo = $(this).attr('id_jo');
                $("#check_all").prop('checked', false);
                $(`.check_cust_${id_jo}`).prop('checked', false);
            });
            $(document).on('click', '.check_container_pje', function (event) {
                var id_jo = $(this).attr('id_jo_pje');
                $("#check_all_pje").prop('checked', false);
                $(`.check_cust_pje${id_jo}`).prop('checked', false);
            });
        //
    });
</script>
@endsection