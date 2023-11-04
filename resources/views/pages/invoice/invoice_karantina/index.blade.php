@extends('layouts.home_master')

@section('content')
@include('sweetalert::alert')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>

</style>

<div class="container-fluid">
    <form action="{{ route('invoice_karantina.store') }}" method="POST" enctype="multipart/form-data" id="saveInvoice">
        @csrf
        <div class="card radiusSendiri">
            <div class="card-header">
                <div class="">
                    <button type="submit" class="btn btn-primary btn-responsive radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i> Buat Invoice
                    </button>
                    {{-- <a href="{{route('invoice_karantina.print')}}" class="btn btn-primary btn-responsive radiusSendiri float-right">debug print</a> --}}
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-5 col-md-7 col-sm-12">
                        <div class="form-group">
                            <label for="">Customer<span class="text-red">*</span></label>
                            <select class="form-control selectpicker" name="customer" id="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                <option value="">─ Pilih Customer ─</option>
                                @foreach ($customer as $item)
                                    <option value="{{ $item->getCustomer->id }}">{{ $item->getCustomer->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- <div class="m-3" style="overflow-x:auto; overflow-y:hidden"> --}}
                    <div class="col-12">
                        <table id="tabel_invoice_karantina" class="table table-bordered table-hover" width='100%'>
                            <thead>
                                <tr>
                                    <th>Tujuan</th>
                                    <th>No. Kontainer</th>
                                    <th style="width: 200px">Nominal</th>
                                    <th style="width: 50px"></th>
                                </tr>
                            </thead>
                            <tbody id='hasil'>
                            
                            </tbody>
                        </table>
                    </div>
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </form>
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
        $('#customer').change(function(event){
            const id = this.value;
            if(id != null){
                showTable(this.value);
            }
        });

        function showTable(id){
            const url = 'invoice_karantina/load_data/'+id;

            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    let data = response;
                    console.log('data', data);

                    for (var i = 0; i < data.length; i++) {
                        let parent = $("<tr></tr>");
                        parent.append(`<td colspan="3">${data[i].no_bl} - ${data[i].kapal} - ${data[i].voyage}</td>`);
                        parent.append(`<td>
                                          <div style="display: flex; justify-content: center; align-items: center;">
                                             <input type="checkbox" class="form-check parent parent_${data[i].id}" value="${data[i].id}" />
                                          </div>
                                       </td>`);

                        $("#hasil").append(parent);
                        for (var j = 0; j < data[i].get_details.length; j++) {
                            let child = $("<tr></tr>");
                            child.append(`<td>${data[i].get_details[j].get_tujuan.nama_tujuan}</td>`);
                            child.append(`<td>${data[i].get_details[j].no_kontainer}</td>`);
                            child.append(`<td><input type="text" class="form-control" id="nom_${data[i].get_details[j].id}" name="data[${data[i].get_details[j].id}][nominal]" readonly/></td>`);
                            child.append(`<td>
                                            <div style="display: flex; justify-content: center; align-items: center;">
                                                <input type="checkbox" name="data[${data[i].get_details[j].id}][idJOD]" class="form-check children children_of_${data[i].id}" parent="${data[i].id}" value="${data[i].get_details[j].id}" />
                                            </div>
                                        </td>`);

                            $("#hasil").append(child);
                        }

                        
                    }
                    // new DataTable('#tabelInvoice', {
                    //     order: [
                    //         [0, 'asc'], // 0 = grup
                    //     ],
                    //     rowGroup: {
                    //         dataSrc: [0] // di order grup dulu, baru customer
                    //     },
                    //     columnDefs: [
                    //         {
                    //             targets: [0], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                    //             visible: false
                    //         },
                    //     ],
                    // });
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
        };

        $(document).on('click', '.parent', function(event){
            const id = this.value;
            const isParentChecked = $(this).prop('checked');

            $('.children_of_'+id).prop('checked', isParentChecked);
        });

        $(document).on('click', '.children', function(event){
            const id = this.value;
            const parrentId = $(this).attr('parent');
            const isReadonly = $('#nom_'+id).prop('readonly');

            $('#nom_'+id).prop('readonly', !isReadonly);
            $('.parent_'+parrentId).prop('checked', false);
        });
    });
</script>

@endsection
