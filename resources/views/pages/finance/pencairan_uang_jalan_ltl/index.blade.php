
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
                                            <option value="{{ $item['no_polisi'] }}">{{ $item['no_polisi'] }}</option>
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
                            <th>Customer</th>
                            <th style="width:200px">No. Sewa</th>
                            <th style="width:200px">Tanggal Berangkat</th>
                            <th style="width:200px">Tujuan</th>
                        </tr>
                    </thead>
                    <tbody id="hasil">
                    </tbody>
                </table>
            </div>
    </div>
</div>

<div class="modal fade" id="modal_detail" tabindex='-1'>
    <form id="save" action="{{ route('pencairan_uang_jalan_ltl.store') }}" method="POST">
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
                                    <label for="">Kas</label>
                                    <select name="id_kas" id="id_kas" class="form-control select2" required>
                                        <option value="">── PILIH KAS ──</option>
                                        @foreach ($kas as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Uang Jalan</label>
                                    <input type="text" id="uang_jalan" name="uang_jalan" class="form-control uang numaja" required>
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Potong Hutan</label>
                                    <input type="text" id="potong_hutang" name="potong_hutang" class="form-control uang numaja" >
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Diterima</label>
                                    <input type="text" id="diterima" name="diterima" class="form-control uang numaja" readonly>
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
                $('#ltl').dataTable().fnClearTable();
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
                    $("#ltl").dataTable().fnDestroy();

                    var data = response.data;
                    console.log('data', data);
                    if(data.length > 0){
                        for (var i = 0; i <data.length; i++) {
                            if(data[i].total_dicairkan == null){
                                var row = $("<tr></tr>");
                                row.append(`<td style='background: #efefef' > 
                                        <div class="d-flex justify-content-between ">
                                            <div>
                                                <b> <span>► ${data[i].get_customer.nama}</span> (${data[i].no_polisi}) - ${data[i].nama_driver} </b>
                                            </div>
                                            <div>
                                                <button class="btn btn-primary btn-sm radiusSendiri openModal" value="${data[0].id_sewa}">
                                                    <span class="fas fa-sticky-note mr-1"></span> Input UJ
                                                </button>
                                            </div>
                                        </div>
                                    </td>`);
                                row.append(`<td>${data[i].no_sewa}</td>`);
                                row.append(`<td>${dateMask(data[i].tanggal_berangkat)}</td>`);
                                row.append(`<td>${data[i].nama_tujuan}</td>`);
                                $("#hasil").append(row);
                            }
                        }
                        
                        new DataTable('#ltl', {
                            searching: false, paging: false, info: false, ordering: false,
                            rowGroup: {
                                dataSrc: [0] // di order grup dulu, baru customer
                            },
                            columnDefs: [
                                {
                                    targets: [0], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                                    visible: false
                                },
                                {
                                    // targets: [ord, ord-1],
                                    // orderable: false, // matiin sortir kolom centang
                                },
                            ],
                        });
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

        $(document).on('keyup', '#uang_jalan, #potong_hutang', function(e) {  
            hitung();
		});        

        function hitung(){
            var uj = !isNaN(normalize($('#uang_jalan').val()))? normalize($('#uang_jalan').val()):0;
            var ph = !isNaN(normalize($('#potong_hutang').val()))? normalize($('#potong_hutang').val()):0;

            $('#diterima').val(moneyMask(uj-ph));
        }

        $(document).on('click', '.openModal', function(event){
            var id = this.value;
            $('#key').val(id);

            $('#modal_detail').modal('show');

            
            // var html = `<input id="swal-input1" class="swal2-input">
            //             <input id="id" name class="swal2-input" value="${id}">`;
            // Swal.fire({
            //     title: 'Uang Jalan',
            //     html: html,
            //     focusConfirm: false,
            //     preConfirm: () => {
            //         return fetch(`//api.github.com/users/${login}`)
            //         .then(response => {
            //             if (!response.ok) {
            //             throw new Error(response.statusText)
            //             }
            //             return response.json()
            //         })
            //         .catch(error => {
            //             Swal.showValidationMessage(
            //             `Request failed: ${error}`
            //             )
            //         })
            //     },
            //     allowOutsideClick: () => !Swal.isLoading()
            //     }).then((result) => {
            //     if (result.isConfirmed) {
            //         Swal.fire({
            //             title: `${result.value.login}'s avatar`,
            //             imageUrl: result.value.avatar_url
            //         })
            //     }
            // })

        });
    });        
</script>
@endsection