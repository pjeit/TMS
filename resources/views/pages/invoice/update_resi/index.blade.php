@extends('layouts.home_master')

@if(session()->has('message'))
<div class="alert alert-success alert-dismissible">
    {{ session()->get('message') }}
</div>
@endif

@section('content')
@include('sweetalert::alert')
<style>

</style>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">

    {{-- sticky header --}}
    <div class="sticky-top radiusSendiri" style="margin-bottom: -15px;">
        <div class="card radiusSendiri radiusSendiri" style="">
            <div class="card-header " style="border-bottom: none;">
                <button type="submit" class="btn btn-primary btn-responsive radiusSendiri" id="bayarInvoice">
                    <i class="fa fa-credit-card"></i> Bayar
                </button>
            </div>
        </div>
    </div>
    <div class="card radiusSendiri">
        <div class="card-body">

            <div style="overflow: auto;">
                <table id="tabelInvoice" class="table table-bordered" width='100%'>
                    <thead id="thead">
                        <tr style="margin-right: 0px;">
                            <th>Grup</th>
                            <th>Customer</th>
                            <th>No. Invoice</th>
                            <th>Tgl Invoice</th>
                            <th>Jatuh Tempo</th>
                            <th>Sisa Tagihan</th>
                            <th>Catatan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="hasil">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- modal edit --}}
<div class="modal fade" id="modal_detail" tabindex='-1'>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Resi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('update_resi.store') }}" method="POST" enctype="multipart/form-data" id="updResi">
                <div class="modal-body">
                    <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id --}}
                    <div class='row'>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="sewa">No. Invoice</label>
                                    <input type="text" class="form-control" id="modal_no_invoice" name="no_invoice"
                                        readonly>
                                    <input type="hidden" class="form-control" id="modal_id_invoice" name="id_invoice"
                                        readonly>
                                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                                </div>

                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">No. Resi</label>
                                    <input type="text" class="form-control" id="modal_resi" name="resi">
                                </div>

                                <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                    <label for="">Jatuh Tempo</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" mplete="off" class="form-control date" id="modal_jatuh_tempo"
                                            name="jatuh_tempo">
                                    </div>
                                </div>

                                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                    <label for="">Catatan</label>
                                    <input type="text" class="form-control" id="modal_catatan" name="catatan">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" style='width:85px'
                        data-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-sm btn-success save_detail" id="simpanResi"
                        style='width:85px'>OK</button>
                </div>
            </form>

        </div>
        <!-- /.modal-content -->
    </div>
</div>

{{-- modal loading --}}
<div class="modal" id="modal-loading" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="cv-spinner">
                    <span class="loader"></span>
                </div>
                <div>Harap Tunggu Sistem Sedang Memproses....</div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('body').on('click', '.update_resi', function () {
            clear();
            var idInvoice = this.value;
            var tambah_waktu = $('#ketentuan_bayar_'+idInvoice).val() != 'null'? parseFloat($('#ketentuan_bayar_'+idInvoice).val()):0;
            var no_invoice = $('#no_invoice_'+idInvoice).val() != 'null'? $('#no_invoice_'+idInvoice).val():'';
            var resi = $('#resi_'+idInvoice).val() != 'null'? $('#resi_'+idInvoice).val():'';
            var jatuh_tempo = $('#jatuh_tempo_'+idInvoice).val() != 'null'? $('#jatuh_tempo_'+idInvoice).val():'';
            var catatan = $('#catatan_'+idInvoice).val() != 'null'? $('#catatan_'+idInvoice).val():'';
            $('#modal_no_invoice').val( no_invoice );
            $('#modal_resi').val( resi );
            $('#modal_catatan').val( catatan );
            $('#modal_id_invoice').val( idInvoice );
            
            let today = new Date();
            $('#modal_jatuh_tempo').datepicker({
                autoclose: true,
                todayHighlight: true,
                language: 'en',
                orientation: 'bottom auto',
            }).datepicker("setDate", dateMask(today.setDate(today.getDate() + tambah_waktu)));

            $('#modal_detail').modal('show');
        });

        showTable("BELUM LUNAS");

        function showTable(status){
            var baseUrl = "{{ asset('') }}";
            var url = baseUrl+`update_resi/load_data`;

            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    var data = response;
                    $('#hasil').empty();
                    $("#loading-spinner").hide();
                    var table = $('#tabelInvoice').DataTable();
                    table.clear().destroy();
                    
                    var newHeader = `
                                <tr style="margin-right: 0px;">
                                    <th>Grup</th>
                                    <th>Customer</th>
                                    <th>No. Invoice</th>
                                    <th width='100'>Tgl Invoice</th>
                                    <th width='100'>Jatuh Tempo</th>
                                    <th>Sisa Tagihan</th>
                                    <th >Catatan</th>
                                    <th width="5px;"></th>
                                </tr>
                    `;

                var thead = document.getElementById("thead");

                if (thead) {
                    thead.innerHTML = newHeader;
                }

                    for (var i = 0; i < data.length; i++) {
                        if(data[i].total_sisa > 0){
                            var row = $("<tr></tr>");
                            let btn_edit = '';
    
                            row.append(`<td>${data[i].nama_grup}</td>`);
                            row.append(`<td>• ${data[i].nama_cust}</td>`);
                            row.append(`<td>${data[i].no_invoice}
                                            <input type="hidden" placeholder='id' id="id_${data[i].id}" value="${data[i].id}" >
                                            <input type="hidden" placeholder='noInvoice' id="no_invoice_${data[i].id}" value="${data[i].no_invoice}" >
                                            <input type="hidden" placeholder='jatuhTempo' id="jatuh_tempo_${data[i].id}" value="${data[i].jatuh_tempo}" >
                                            <input type="hidden" placeholder='resi' id="resi_${data[i].id}" value="${data[i].resi}" >
                                            <input type="hidden" placeholder='catatan' id="catatan_${data[i].id}" value="${data[i].catatan}" >
                                        </td>`);
                            row.append(`<td>${dateMask(data[i].tgl_invoice)}</td>`);
                            row.append(`<td>${dateMask(data[i].jatuh_tempo)}</td>`);
                            row.append(`<td> ${ data[i].total_sisa.toLocaleString()}</td>`);
                            row.append(`<td>${data[i].catatan == null? '':data[i].catatan}</td>`);
                            row.append(`<td class='text-center' style="text-align:center">
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu" >
                                            <button class="dropdown-item update_resi" value="${data[i].id}">
                                                <span class="fas fa-sticky-note mr-3"></span> Update Resi
                                            </button>
                                            <input type="text" id="ketentuan_bayar_${data[i].id}" value="${data[i].ketentuan_bayar}" hidden />
                                        </div>
                                    </div>
                                </td>`);
                            $("#hasil").append(row);
                        }
                    }
                    new DataTable('#tabelInvoice', {
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
                            icon: 'error',
                            title: "Terjadi kesalahan saat menerima data. <br><br>" + error
                        })
                    }
                }
            });
        };

        function clear() {
            $('#modal_id_invoice').val('');
            $('#modal_no_invoice').val('');
            $('#modal_resi').val('');
            $('#modal_jatuh_tempo').val('');
            $('#modal_catatan').val('');
        }

        // $("#modal_jatuh_tempo" ).datepicker({
        //     format: 'dd-M-yyyy'
        // });
       
    });
</script>
@endsection