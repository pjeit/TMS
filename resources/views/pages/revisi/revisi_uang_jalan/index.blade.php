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
            <div class="card-header">

            </div>
            <div class="card-body">
                <section class="col-lg-12" id="show_report">
                    <div class="col-sm-12 col-md-3 col-lg-3 ">
                        <div class="form-group">
                            <label for="">Revisi</label>
                            <select class="form-control selectpicker" required name="item" id="item"
                                data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                <option value="TAMBAHAN UJ">Tambah UJ</option>
                                <option value="KEMBALIKAN UJ">Kembalikan UJ</option>
                            </select>
                        </div>
                    </div>
                    <hr>

                    <table id="rowGroup" class="table table-bordered table-hover" width="100%">
                        <thead>
                            <tr>
                                <th>Grup</th>
                                <th>Customer</th>
                                <th>Tujuan</th>
                                <th>Total</th>
                                <th width="300"></th>
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

<script>
    $(document).ready(function() {
        var item = $('#item').val();
        showTable(item);

        $(document).on('change', '#item', function(e) {  
            showTable(this.value);
		});   
        
        function showTable(item){
            var baseUrl = "{{ asset('') }}";
            var url = baseUrl+`revisi_uang_jalan/load_data/${item}`;

            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    $("#rowGroup").dataTable().fnDestroy();
                    // $('#rowGroup tbody').dataTable().fnDestroy();
                    $("#loading-spinner").hide();
                    var data = response.data;
                    console.log('data', data);

                    $("#hasil").html("");
                    console.log('data.length', data.length);
                    for (var i = 0; i <data.length; i++) {
                        var row = $("<tr></tr>");
                        row.append(`<td style='background: #efefef'><b> <div> <span> ${data[i].nama_grup}</span> <span class='float-right mr-1'> </span> </div> </b></td>`);
                        row.append(`<td style='background: #efefef'><b> <div> <span>► ${data[i].customer}</span> <span class='float-right mr-1'> </span> </div> </b></td>`);
                        row.append(`<td> ${data[i].nama_tujuan} / ${data[i].no_polisi} / ${data[i].nama_panggilan} </td>`);
                        row.append(`<td> ${moneyMask(Math.abs(data[i].uj_tujuan - data[i].uj_sewa))} </td>`);
                        var text = '';
                        var btnClass = '';
                        var input = '';
                        if(item == 'TAMBAHAN UJ'){
                            text = 'Cairkan';
                            btnClass = 'btn-success';
                            input = `<a href="${baseUrl}revisi_uang_jalan/cairkan/${data[i].id_sewa}" class="btn `+btnClass+` radiusSendiri">
                                    <span class="fa fa-credit-card"></span> `+text+` UJ
                                </a>`;
                        }else{
                            text = 'Kembalikan';
                            btnClass = 'btn-danger';
                            input = `<a href="${baseUrl}revisi_uang_jalan/kembalikan/${data[i].id_sewa}" class="btn `+btnClass+` radiusSendiri">
                                    <span class="fa fa-credit-card"></span> `+text+` UJ
                                </a>`;
                        }
                        row.append(`
                            <td>
                             `+input+`  
                            </td>
                            `);
                        $("#hasil").append(row);
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
                                targets: [3,4],
                                orderable: false, // matiin sortir kolom centang
                            },
                            { 
                                targets: 4,
                                width: "10%", 
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
    });
</script>
@endsection