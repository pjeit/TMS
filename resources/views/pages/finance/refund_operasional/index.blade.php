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
        <div class="card-header sticky-top" >
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12 bg-white pb-3">
                    <label for="">&nbsp;</label>
                    <div class="form-group" >
                        <select class="form-control selectpicker" required name="item" id="item"
                            data-live-search="true" data-show-subtext="true" data-placement="bottom">
                            <option value="">­­— PILIH DATA —</option>
                            <option value="ALAT">ALAT</option>
                            <option value="TALLY">TALLY</option>
                            <option value="SEAL PELAYARAN">SEAL PELAYARAN</option>
                            <option value="BIAYA DEPO">BIAYA DEPO</option>
                            <option value="BURUH">BURUH</option>
                            <option value="TIMBANG">TIMBANG</option>
                            <option value="LEMBUR">LEMBUR</option>
                            {{-- <option value="KARANTINA">KARANTINA</option> --}}
                            {{-- <option value="LAIN-LAIN">LAIN-LAIN</option> --}}
                        </select>
                        <input type="hidden" id="alasan" name="alasan" value="">
                        <input type="hidden" id="kembali" name="alasan" value="">
                        <input type="hidden" id="type" name="type" value="">
                    </div>
                </div>
                    <div class="col-lg-2 col-md-2 col-sm-12 bg-white pb-3">
                    <div class="form-group">
                        <label for="">Tanggal Mulai<span style="color:red">*</span></label>
                        <div class="input-group mb-0">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" autocomplete="off" name="tanggal_mulai" class="form-control date" id="tanggal_mulai" value="{{\Carbon\Carbon::now()->subDay()->format('d-M-Y')}}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12 bg-white pb-3">
                    <div class="form-group">
                        <label for="">Tanggal Akhir<span style="color:red">*</span></label>
                        <div class="input-group mb-0">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" autocomplete="off" name="tanggal_akhir" class="form-control date" id="tanggal_akhir" value="{{date("d-M-Y")}}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 bg-white pb-3">
                    <label for="">&nbsp;</label>
                    <div class="d-flex">
                        
                        <div class="form-group">
                            <button type="button" class="btn btn-primary radiusSendiri "   id="btnFilter">
                                <i class="fas fa-search"></i> 
                                <b> Tampilkan Data</b>
                            </button>
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
    </div>
</div>

<script>
    $(document).ready(function() {
       
        $('#tanggal_mulai').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            startDate: "-1d",
            language:'en',
        });
        $('#tanggal_akhir').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            startDate: "0d",
            language:'en',
        });
        $(document).on('change', '#item', function(e) {  
            var item = $(this).val();
            var tanggal_mulai = $('#tanggal_mulai').val();
            var tanggal_akhir = $('#tanggal_akhir').val();
            if(item != '' &&tanggal_mulai!=''&&tanggal_akhir!=''){
                showTable(item,tanggal_mulai,tanggal_akhir);
            }else{
                var table = $('#rowGroup').DataTable();
                table.clear().draw();
            }
		});    
        $(document).on('click', '#btnFilter', function(e) {  
            var item = $('#item').val();
            var tanggal_mulai = $('#tanggal_mulai').val();
            var tanggal_akhir = $('#tanggal_akhir').val();


            if(item != '' &&tanggal_mulai!=''&&tanggal_akhir!=''){
                showTable(item,tanggal_mulai,tanggal_akhir);
            }else{
                var table = $('#rowGroup').DataTable();
                table.clear().draw();
            }
		});  
        function showTable(item,tanggal_mulai,tanggal_akhir){
            var baseUrl = "{{ asset('') }}";
            var url = baseUrl+`revisi_biaya_operasional/load_data/${item}/${tanggal_mulai}/${tanggal_akhir}`;
            // `${baseUrl}refund_biaya_operasional//edit`
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
                            $("thead tr").append(`<th>Grup</th><th>Customer</th><th>Tujuan</th>`);
                            if(item == 'LAIN-LAIN'){
                                $("thead tr").append("<th>Deskripsi</th>");
                            }
                            $("thead tr").append(`
                            <th>Kas Bank</th>
                            <th>Tgl Dicairkan</th>
                            <th>Rincian Order</th>
                            `);
                            $("thead tr").append(`  <th>Dicairkan</th>
                                                    <th>Catatan</th>
                                                `);  // <th class='text-center' style='width: 30px;'></th> // <input id='check_all' type='checkbox'>
                            // if(item == 'OPERASIONAL' || item == 'TALLY' || item == 'SEAL PELAYARAN'){
                                for (var i = 0; i <data.length; i++) {
                                    let cek_status = data[i].get_operasional_detail;
                                    if(cek_status != 'SELESAI' || cek_status != 'MENUNGGU PEMBAYARAN INVOICE'){
                                        console.log('cek_status', data[i].get_operasional_detail);
                                        var row = $("<tr></tr>");
                                        row.append(`<td style='background: #efefef'>
                                                            <b> 
                                                                <span> ${data[i].get_operasional_detail[0].get_sewa_detail.get_customer.get_grup.nama_grup}</span> 
                                                            </b>
                                                    </td>`);
                                        row.append(`<td style='background: #efefef'>
                                                            <b> 
                                                                <span>► ${data[i].get_operasional_detail[0].get_sewa_detail.get_customer.nama}</span> 
                                                            </b>
                                                    </td>`);
                                        row.append(`<td> 
                                            ${data[i].get_operasional_detail[0].get_sewa_detail.nama_tujuan} 
                                            </td>`);
                                        row.append(`<td> 
                                         ${data[i].get_kas.nama} 
                                        </td>`);
                                        row.append(`<td> 
                                            ${dateMask(data[i].tgl_dicairkan)} 
                                        </td>`);
                                        if(item == 'LAIN-LAIN'){
                                            row.append(`<td>
                                                            <span>${data[i].get_operasional_detail[0].deskripsi}</span> 
                                                        </td>`);
                                        }
                                        // row.append(`<td> ${data[i].get_operasional_detail.map(item => `<input type="text" value="${item.get_sewa_detail.no_polisi + ' (' + item.get_sewa_detail.get_karyawan?item.get_sewa_detail.get_karyawan.nama_panggilan:'REKANAN'+ ')'}" class="form-control" title="${item.get_sewa_detail.no_polisi + ' (' + item.get_sewa_detail.get_karyawan?item.get_sewa_detail.get_karyawan.nama_panggilan:'REKANAN'+ ')'}" readonly />`).join('<br>')}</td>`);
                                        row.append(`<td>${data[i].get_operasional_detail.map(item => 
                                        `
                                       <span class="badge badge-primary"> ${item.get_sewa_detail.no_polisi} (${item.get_sewa_detail.get_karyawan ? item.get_sewa_detail.get_karyawan.nama_panggilan : 'REKANAN'} </span><br>
                                       <span class="badge badge-secondary"> ${dateMask(item.get_sewa_detail.tanggal_berangkat)} </span>
                                       <span class="badge badge-success"> ${item.get_sewa_detail.no_sewa} </span>

                                        `
                                        ).join('<br> <br>')}</td>`);
                                        row.append(`<td> <span class="badge badge-warning">Total dicairkan: ${moneyMask(data[i].total_dicairkan) }</span>
                                                                                                </td>`);
                                        row.append(`<td> 

                                            <div class="btn-group dropleft">
                                                <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                            <div class="dropdown-menu" >
                                                <a href="" class="dropdown-item">
                                                        <span class="nav-icon fas fa-edit mr-3"></span> Edit
                                                    </a>
                                                <a href="${baseUrl}refund_biaya_operasional/${data[i].id}/edit" class="dropdown-item btn-danger">
                                                    <span class="nav-icon fas fa-undo mr-3"></span> Refund
                                                </a>
                                                <a href="" class="dropdown-item">
                                                    <span class="nav-icon fas fa-times mr-3"></span> Hapus
                                                </a>
                                            </div>
                                    </div>
                                               
                                            </td>`);
                                        $("#tbodyId").append(row);
                                    }
                                }
                            // }
                                
                            new DataTable('#rowGroup', {
                                responsive: true,
                                paging: true,
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
                // $('#dicairkan_'+id).prop('readonly', false);
                $('#catatan_'+id).prop('readonly', false);
            }else{
                $('#dicairkan_'+id).prop('readonly', true);
                $('#catatan_'+id).prop('readonly', true);

                $('#dicairkan_'+id).val( moneyMask($('#hidden_dicairkan_'+id).val()) );
                $('#catatan_'+id).val( $('#hidden_catatan_'+id).val() );
            }
        });


        // $(document).on('click', '#btnSave', function(e){
        //     var isOk = 0;
        //     var checkboxes = document.querySelectorAll('.centang');
        //         checkboxes.forEach(function(checkbox) {
        //             if (checkbox.checked) {
        //                 isOk = 1;
        //             }
        //         });
        //     if (isOk == 0) {
        //             event.preventDefault(); // Prevent form submission
        //             Swal.fire({
        //                 icon: 'error',
        //                 text: 'Harap pilih item dahulu!',
        //             })
        //             return;
        //         }
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
        //             document.getElementById('type').value = this.value;

        //             // this.submit();
        //             $('#save').submit();
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
        // });

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