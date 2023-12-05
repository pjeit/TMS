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
        <form id="save" action="{{ route('revisi_biaya_operasional.store') }}" method="POST">
            @csrf
            <div class="card-header ">
                <div class="card-header" style="border: 2px solid #bbbbbb;">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-6 bg-white pb-3">
                            <label for="">&nbsp;</label>
                            <div class="form-group" style="width: 400px;">
                                <select class="form-control selectpicker" required name="item" id="item"
                                    data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                    <option value="">­­— PILIH DATA —</option>
                                    <option value="TALLY">TALLY</option>
                                    <option value="SEAL PELAYARAN">SEAL PELAYARAN</option>
                                    <option value="OPERASIONAL">ALAT</option>
                                    <option value="TIMBANG">TIMBANG</option>
                                    <option value="BURUH">BURUH</option>
                                    <option value="LEMBUR">LEMBUR</option>
                                    <option value="KARANTINA">KARANTINA</option>
                                    <option value="LAIN-LAIN">LAIN-LAIN</option>
                                </select>
                                <input type="hidden" id="alasan" name="alasan" value="">
                                <input type="hidden" id="type" name="type" value="">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6 bg-white pb-3">
                            <label for="">&nbsp;</label>
                            <div class="d-flex">
                                <div class="form-group">
                                    <button type="button" class="btn btn-success ml-3 popUp" id="btnSave"
                                        value="save"><i class="fa fa-save" aria-hidden="true"></i> Simpan</button>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-danger ml-3 popUp" id="btnDelete"
                                        value="delete"><i class="fa fa-trash-alt" aria-hidden="true"></i> Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <section class="col-lg-12" id="show_report">
                    <table id="rowGroup" class="table table-bordered table-hover" width="100%">
                        <thead id="theadId">
                            <tr>
                                <th>Revisi Biaya Operasional</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyId">

                        </tbody>
                    </table>
                </section>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // $('#save').submit(function(event) {
        //     console.log('this.value', this.value);

        //     Swal.fire({
        //         title: 'Apakah data sudah benar?',
        //         text: "Periksa kembali data anda",
        //         icon: 'warning',
        //         input: "textarea",
        //         inputLabel: "Berikan alasan revisi",
        //         inputPlaceholder: "...",
        //         inputAttributes: {
        //             "aria-label": "Type your message here"
        //         },
        //         showCancelButton: true,
        //         cancelButtonColor: '#d33',
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonText: 'Batal',
        //         confirmButtonText: 'Ya',
        //         reverseButtons: true
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             const revisionReason = result.value;
        //             document.getElementById('alasan').value = revisionReason;

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
        //             // return;
        //         }
        //     })
        
            
        //     event.preventDefault();
        //     // this.submit();
        // });

        $(document).on('change', '#item', function(e) {  
            var item = $('#item').val();
            if(item != ''){
                showTable(item);
            }else{
                var table = $('#rowGroup').DataTable();
                table.clear().draw();
            }
		});        
        
        function showTable(item){
            var baseUrl = "{{ asset('') }}";
            var url = baseUrl+`revisi_biaya_operasional/load_data/${item}`;

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
                    $("#tbodyId").empty();

                    var item = $('#item').val();
                    var data = response.data;

                    console.log('data', data);
                    if(data != ''){
                        if(item == 'KARANTINA'){
                            $("thead tr").append(`  <th>Grup</th>
                                                    <th>Customer</th>
                                                    <th>No. BL</th>
                                                    <th>Kapal / Voyage</th>
                                                    <th>Biaya</th>
                                                    <th>Dicairkan</th>
                                                    <th>Catatan</th>    
                                                    <th class='text-center' style='width: 30px;'></th>
                                                `); // <input id='check_all' type='checkbox'>

                            for (var i = 0; i <data.length; i++) {
                                var row = $("<tr></tr>");
                                row.append(`<td style='background: #efefef'>
                                                    <b> 
                                                        <span> ${data[i].get_customer.get_grup.nama_grup}</span> 
                                                    </b>
                                            </td>`);
                                row.append(`<td style='background: #efefef'>
                                                    <b> 
                                                        <span>► ${data[i].get_customer.nama}</span> 
                                                    </b>
                                            </td>`);
                                row.append(`<td> ${data[i].get_j_o.no_bl} </td>`);
                                row.append(`<td> <b>${data[i].get_j_o.kapal} / ${data[i].get_j_o.voyage}</b> </td>`);
                                row.append(`<td> 
                                                ${ data[i].total_operasional.toLocaleString() } 
                                                <input type="text" class="uang numaja form-control" id='operasional_${data[i].id}' name='data[${data[i].id}][total_operasional]' value='${data[i].total_operasional == null? 0:data[i].total_operasional}' readonly hidden />
                                            </td>`); 
                                row.append(`<td> 
                                                <input class="uang numaja dicairkan form-control" id='dicairkan_${data[i].id}' idOprs="${data[i].id}" name='data[${data[i].id}][dicairkan]' value='${data[i].total_dicairkan == null? 0:data[i].total_dicairkan.toLocaleString()}' readonly />
                                                <input class="uang numaja form-control" id='hidden_dicairkan_${data[i].id}' name='data[${data[i].id}][dicairkan_old]' value='${data[i].total_dicairkan == null? 0:data[i].total_dicairkan}' readonly hidden />
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <input class="form-control" name='data[${data[i].id}][catatan]' id="catatan_${data[i].id}" value="${data[i].catatan != null? data[i].catatan:''}" readonly/> 
                                                <input class="form-control" id="hidden_catatan_${data[i].id}" value="${data[i].catatan != null? data[i].catatan:''}" readonly hidden /> 
                                            </td>`);
                                row.append(`<td class='text-center'> 
                                                <input type='checkbox' class="centang" name="data[${data[i].id}][check]" value="${data[i].id}">
                                            </td>`);
    
                                $("#tbodyId").append(row);
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
                                        targets: [-1, -2],
                                        orderable: false, // matiin sortir kolom centang
                                    },
                                ],
                            });

                        }else{
                            $("thead tr").append(`<th>Grup</th><th>Customer</th><th>Tujuan</th>`);
                            if(item == 'LAIN-LAIN'){
                                $("thead tr").append("<th>Deskripsi</th>");
                            }
                            $("thead tr").append("<th>Driver</th><th>Total</th>");
                            $("thead tr").append(`  <th>Dicairkan</th>
                                                    <th>Catatan</th>
                                                `);  // <th class='text-center' style='width: 30px;'></th> // <input id='check_all' type='checkbox'>
                            // if(item == 'OPERASIONAL' || item == 'TALLY' || item == 'SEAL PELAYARAN'){
                                for (var i = 0; i <data.length; i++) {
                                    // let cek_status = data[i].get_operasional;
                                    // if(cek_status != 'SELESAI'){
                                        console.log('cek_status', data[i].get_operasional);
                                        var row = $("<tr></tr>");
                                        row.append(`<td style='background: #efefef'>
                                                            <b> 
                                                                <span> ${data[i].get_operasional[0].get_sewa.get_customer.get_grup.nama_grup}</span> 
                                                            </b>
                                                    </td>`);
                                        row.append(`<td style='background: #efefef'>
                                                            <b> 
                                                                <span>► ${data[i].get_operasional[0].get_sewa.get_customer.nama}</span> 
                                                            </b>
                                                    </td>`);
                                        row.append(`<td> ${data[i].get_operasional[0].get_sewa.nama_tujuan} </td>`);
                                        if(item == 'LAIN-LAIN'){
                                            row.append(`<td>
                                                            <span>${data[i].get_operasional[0].deskripsi}</span> 
                                                        </td>`);
                                        }
                                        row.append(`<td> ${data[i].get_operasional.map(item => `<input type="text" value="${item.get_sewa.no_polisi + ' (' + item.get_sewa.get_karyawan.nama_panggilan+ ')'}" class="form-control" title="${item.get_sewa.no_polisi + ' (' + item.get_sewa.get_karyawan.nama_panggilan+ ')'}" readonly />`).join('<br>')}</td>`);
                                        row.append(`<td> ${data[i].get_operasional.map(item => `<input type="text" value="${item.total_operasional.toLocaleString()}" id="operasional_${item.id}" name='data[${item.id_pembayaran}][${item.id}][total_operasional]' class="operasional_${item.id} id_pembayaran_${item.id_pembayaran} form-control numaja uang" readonly />`).join('<br>')}</td>`);
                                        row.append(`<td> ${data[i].get_operasional.map(item => `<input type="text" value="${item.total_dicairkan.toLocaleString()}" id="dicairkan_${item.id}" name='data[${item.id_pembayaran}][${item.id}][total_dicairkan]' idOprs="${item.id}" class="operasional_${item.id} id_pembayaran_${item.id_pembayaran} dicairkan form-control numaja uang" readonly />
                                                                                                <input type="hidden" value="${item.total_dicairkan}" id="hidden_dicairkan_${item.id}" class="operasional_${item.id} id_pembayaran_${item.id_pembayaran} form-control numaja uang" readonly />
                                                                                                `).join('<br>')}</td>`);
                                        row.append(`<td> ${data[i].get_operasional.map(item => `<div class='d-flex'>
                                                                                                    <input type="text" value="${item.catatan != null? item.catatan:''}" id="catatan_${item.id}" name='data[${item.id_pembayaran}][${item.id}][catatan]' class="operasional_${item.id} id_pembayaran_${item.id_pembayaran} form-control" readonly />
                                                                                                    <input type="hidden" value="${item.catatan != null? item.catatan:''}" id="hidden_catatan_${item.id}" class="operasional_${item.id} id_pembayaran_${item.id_pembayaran} form-control" readonly />
                                                                                                    <input type="checkbox" value="${item.id}" name="data[${item.id_pembayaran}][${item.id}][check]" class="ml-3 mt-2 centang" /> 
                                                                                                </div>`).join('<br>')}</td>`);
                                        $("#tbodyId").append(row);
                                    // }
                                }
                            // }
                                
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
                                        targets: [-1, -2],
                                        orderable: false, // matiin sortir kolom centang
                                    },
                                ],
                            });
                        }
                    }else{
                        console.log('else');
                        $("thead tr").append(`<th>Revisi Biaya Operasional</th>`);
                        // $("#rowGroup").dataTable();
                        $('#rowGroup').DataTable().draw();

                        // $('#rowGroup').DataTable().clear().draw();
                    }
                },error: function (xhr, status, error) {
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

        $(document).on('click', '.centang', function(e){
            let id = this.value;

            if(this.checked == true){
                $('#dicairkan_'+id).prop('readonly', false);
                $('#catatan_'+id).prop('readonly', false);
            }else{
                $('#dicairkan_'+id).prop('readonly', true);
                $('#catatan_'+id).prop('readonly', true);

                $('#dicairkan_'+id).val( moneyMask($('#hidden_dicairkan_'+id).val()) );
                $('#catatan_'+id).val( $('#hidden_catatan_'+id).val() );
            }
        });

        $(document).on('click', '#btnDelete', function(e){
            Swal.fire({
                title: 'Apakah data sudah benar?',
                text: "Periksa kembali data anda",
                icon: 'warning',
                input: "textarea",
                inputLabel: "Berikan alasan hapus",
                inputPlaceholder: "...",
                inputAttributes: {
                    "aria-label": "Type your message here"
                },
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const revisionReason = result.value;
                    document.getElementById('alasan').value = revisionReason;
                    document.getElementById('type').value = this.value;

                    // this.submit();
                    $('#save').submit();
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

        $(document).on('click', '#btnSave', function(e){
            Swal.fire({
                title: 'Apakah data sudah benar?',
                text: "Periksa kembali data anda",
                icon: 'warning',
                input: "textarea",
                inputLabel: "Berikan alasan revisi",
                inputPlaceholder: "...",
                inputAttributes: {
                    "aria-label": "Type your message here"
                },
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonColor: '#3085d6',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const revisionReason = result.value;
                    document.getElementById('alasan').value = revisionReason;
                    document.getElementById('type').value = this.value;

                    // this.submit();
                    $('#save').submit();
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

        $(document).on('keyup', '.dicairkan', function(){
            var idOprs = $(this).attr('idOprs');
            var inputed = normalize(this.value);
            var max = normalize( $('#operasional_'+idOprs).val() );

            console.log('idOprs', idOprs);
            if (inputed > max ) {
                $('#dicairkan_'+idOprs).val(parseFloat(max).toLocaleString()); 
            }
        });

        function caps(){
            $("input").focusout(function () {
                this.value = this.value.toLocaleUpperCase();
            });
        }
    });
</script>
@endsection