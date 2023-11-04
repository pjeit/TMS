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
                <button type="submit" class="btn btn-primary btn-responsive radiusSendiri" id="buatInvoice">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Buat Invoice
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-5 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="">Customer<span class="text-red">*</span></label>
                            <select class="form-control selectpicker" name="customer" id="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                <option value="">─ Pilih Customer ─</option>
                                @foreach ($customer as $item)
                                    <option value="{{ $item->getCustomer->id }}" kode="{{ $item->getCustomer->kode }}">{{ $item->getCustomer->nama }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="kode" name="kode">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <label>Tanggal Invoice<span style="color:red">*</span></label>
                        <div class="input-group mb-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" autocomplete="off" name="tanggal_invoice" class="form-control date" id="tanggal_invoice" placeholder="dd-M-yyyy" required>
                            <input type="hidden" id="total_nominal" name="total_nominal" >
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
                                    <th>Nominal</th>
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

<script>
    $(document).ready(function() {
        $('#saveInvoice').submit(function(event) {
            // Calculate totals
            event.preventDefault();

            // get total
                let totals = document.querySelectorAll('.total');
                let total = 0;

                totals.forEach(function(input) {
                    total += normalize(input.value);
                });
                $('#total_nominal').val(total);
            //

            // get kode 
                const selectElement = document.getElementById('customer'); // Assuming the select element has the ID 'customer'
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const kodeAttribute = selectedOption.getAttribute('kode');
                $('#kode').val(kodeAttribute);
            //

            // cek centang
                let parents = document.querySelectorAll('.parent');
                let is_ok = [];

                parents.forEach(function(checkboxParent, i) {
                    if (checkboxParent.checked) {
                        is_ok[i] = false;
                        var childrens = document.querySelectorAll('.children_of_' + checkboxParent.value);
                        childrens.forEach(function(checkboxChildren) {
                            if (checkboxChildren.checked) {
                                is_ok[i] = true;
                            }
                        });
                    }
                });
                console.log('is_ok', is_ok);

                if (is_ok.some(value => value === false)) {
                    event.preventDefault(); 
                    Swal.fire({
                        icon: 'error',
                        title: 'Periksa kembali data anda!',
                        text: 'Nominal sudah diisi namun kontainer belum dicentang!',
                    });
                    event.preventDefault();
                    return;
                }

            //
            
            Swal.fire({
                title: 'Apakah Anda yakin data sudah benar?',
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
                    // return;
                }
            })
        });
    });
</script>

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
                    $("#hasil").empty();

                    for (var i = 0; i < data.length; i++) {
                        let parent = $("<tr></tr>");
                        parent.append(`<td colspan="2" style="background: #d9d9d9">${data[i].no_bl} - ${data[i].kapal} - ${data[i].voyage}</td>`);
                        parent.append(`<td style="background: #d9d9d9"><input type="text" class="form-control numaja total uang nom_${data[i].id}" id="nom_${data[i].id}" name="data[${data[i].id}][nominal]" readonly /></td>`);
                        parent.append(`<td style="background: #d9d9d9">
                                          <div style="display: flex; justify-content: center; align-items: center;">
                                             <input type="checkbox" class="form-check parent parent_${data[i].id}" value="${data[i].id}" />
                                          </div>
                                       </td>`);

                        $("#hasil").append(parent);
                        for (var j = 0; j < data[i].get_details.length; j++) {
                            let child = $("<tr></tr>");
                            child.append(`<td>${data[i].get_details[j].get_tujuan.nama_tujuan}</td>`);
                            child.append(`<td>${data[i].get_details[j].no_kontainer}</td>`);
                            child.append(`<td></td>`);
                            child.append(`<td>
                                            <div style="display: flex; justify-content: center; align-items: center;">
                                                <input type="checkbox" name="data[${data[i].id}][idJOD][]" class="form-check children children_of_${data[i].id}" parent="${data[i].id}" value="${data[i].get_details[j].id}" />
                                            </div>
                                        </td>`);

                            $("#hasil").append(child);
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
        };

        $(document).on('click', '.parent', function(event){
            const id = this.value;
            const isReadonly = $('#nom_'+id).prop('readonly');
            const isRequired = $('#nom_'+id).prop('required');
            const isParentChecked = $(this).prop('checked');
            if(isParentChecked == false){
                $('.children_of_'+id).prop('checked', false);
            }

            $('#nom_'+id).prop('readonly', !isReadonly);
            $('#nom_'+id).prop('required', !isRequired);
            $('#nom_'+id).val('');
        });

        $(document).on('click', '.children', function(event){
            const parrentId = $(this).attr('parent');
            const isReadonly = $('#nom_'+parrentId).prop('readonly');
            const isRequired = $('#nom_'+parrentId).prop('required');
            
            if(isReadonly == true){
                $('#nom_'+parrentId).prop('readonly', !isReadonly);
                $('#nom_'+parrentId).prop('required', !isRequired);
                $('#nom_'+parrentId).val('');
                $('.parent_'+parrentId).prop('checked', true);
            }
        });
    });
</script>

@endsection
