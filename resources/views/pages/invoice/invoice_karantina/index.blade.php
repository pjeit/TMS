@extends('layouts.home_master')

@section('content')
@include('sweetalert::alert')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<style>

</style>

<div class="container-fluid">
    <form action="{{ route('invoice_karantina.create') }}" method="post" enctype="multipart/form-data" id="saveInvoice">
        @csrf @method('GET')
        {{-- sticky header --}}
        <div class="sticky-top radiusSendiri" style="margin-bottom: -15px;">
            <div class="card radiusSendiri radiusSendiri" style="">
                <div class="card-header " style="border-bottom: none;">
                    <button type="submit" class="btn btn-primary btn-responsive radiusSendiri" id="buatInvoice">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i> Buat Invoice
                    </button>
                </div>
            </div>
        </div>
        <div class="card radiusSendiri">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-5 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label for="">Customer<span class="text-red">*</span></label>
                            <select class="form-control selectpicker" name="customer" id="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                <option value="">─ Pilih Customer ─</option>
                                @foreach ($customer as $item)
                                    <option value="{{ $item->id_customer }}" >{{ $item->nama }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="kode" name="kode">
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- <div class="m-3" style="overflow-x:auto; overflow-y:hidden"> --}}
                    <div class="col-12">
                        <table id="tabel_invoice_karantina" class="table table-bordered table-hover" width='100%'>
                            <thead>
                                <tr>
                                    <th>Grup</th>
                                    <th>Customer</th>
                                    <th>No. BL</th>
                                    <th>Kapal / Voyage</th>
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
    // $(document).ready(function() {
    //     $('#saveInvoice').submit(function(event) {
    //         // Calculate totals
    //         event.preventDefault();

    //         // get total
    //             // let totals = document.querySelectorAll('.total');
    //             // let total = 0;

    //             // totals.forEach(function(input) {
    //             //     let val = !isNaN(normalize(input.value)) == true? normalize(input.value):0;
    //             //     total += val;
    //             // });
    //             // $('#total_nominal').val(total);
    //         //

    //         // get kode 
    //             // const selectElement = document.getElementById('customer'); // Assuming the select element has the ID 'customer'
    //             // const selectedOption = selectElement.options[selectElement.selectedIndex];
    //             // const kodeAttribute = selectedOption.getAttribute('kode');
    //             // $('#kode').val(kodeAttribute);
    //         //

    //         // cek centang
    //             // let parents = document.querySelectorAll('.parent');
    //             // let is_ok = [];

    //             // parents.forEach(function(checkboxParent, i) {
    //             //     if (checkboxParent.checked) {
    //             //         is_ok[i] = false;
    //             //         var childrens = document.querySelectorAll('.children_of_' + checkboxParent.value);
    //             //         childrens.forEach(function(checkboxChildren) {
    //             //             if (checkboxChildren.checked) {
    //             //                 is_ok[i] = true;
    //             //             }
    //             //         });
    //             //     }
    //             // });
    //             // console.log('is_ok', is_ok);

    //             // if (is_ok.some(value => value === false)) {
    //             //     event.preventDefault(); 
    //             //     Swal.fire({
    //             //         icon: 'error',
    //             //         title: 'Periksa kembali data anda!',
    //             //         text: 'Nominal sudah diisi namun kontainer belum dicentang!',
    //             //     });
    //             //     event.preventDefault();
    //             //     return;
    //             // }

    //         //
            
    //         Swal.fire({
    //             title: 'Apakah Anda yakin data sudah benar?',
    //             text: "Periksa kembali data anda",
    //             icon: 'warning',
    //             showCancelButton: true,
    //             cancelButtonColor: '#d33',
    //             confirmButtonColor: '#3085d6',
    //             cancelButtonText: 'Batal',
    //             confirmButtonText: 'Ya',
    //             reverseButtons: true
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 this.submit();
    //             }else{
    //                 const Toast = Swal.mixin({
    //                     toast: true,
    //                     position: 'top',
    //                     timer: 2500,
    //                     showConfirmButton: false,
    //                     timerProgressBar: true,
    //                     didOpen: (toast) => {
    //                         toast.addEventListener('mouseenter', Swal.stopTimer)
    //                         toast.addEventListener('mouseleave', Swal.resumeTimer)
    //                     }
    //                 })

    //                 Toast.fire({
    //                     icon: 'warning',
    //                     title: 'Batal Disimpan'
    //                 })
    //                 event.preventDefault();
    //                 // return;
    //             }
    //         })
    //     });
    // });
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
            var baseUrl = "{{ asset('') }}";
            var url = baseUrl+'invoice_karantina/load_data/'+id;

            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    let data = response;
                    $("#tabel_invoice_karantina").dataTable().fnDestroy();

                    $("th").remove();

                    $("thead tr").append(`<th>Grup</th>
                                          <th>Customer</th>
                                          <th>No. BL</th>
                                          <th>Kapal / Voyage</th>
                                          <th>Nominal</th>
                                          <th style="width: 50px"></th>
                                        `);
                    $("#hasil").empty();

                    console.log('data', data);
                    for (var i = 0; i < data.length; i++) {
                        let parent = $("<tr></tr>");
                        parent.append(`<td>${data[i].get_customer.get_grup.nama_grup}</td>`);
                        parent.append(`<td>${data[i].get_customer.nama}</td>`);
                        parent.append(`<td>${data[i].get_j_o.no_bl}</td>`);
                        parent.append(`<td>${data[i].get_j_o.kapal} ( ${data[i].get_j_o.voyage} )</td>`);
                        parent.append(`<td>${(data[i].total_dicairkan)?moneyMask(data[i].total_dicairkan):'belum dicairkan'}</td>`);
                        parent.append(`<td style="background: #d9d9d9">
                                          <div style="display: flex; justify-content: center; align-items: center;">
                                             <input type="checkbox" class="form-check parent parent_${data[i].id}" name="idKarantina[]" value="${data[i].id}" />
                                          </div>
                                       </td>`);

                        $("#hasil").append(parent);
                        
                        // for (var j = 0; j < data[i].get_details.length; j++) {
                        //     let child = $("<tr></tr>");
                        //     child.append(`<td>${data[i].get_details[j].get_tujuan.nama_tujuan}</td>`);
                        //     child.append(`<td>${data[i].get_details[j].no_kontainer}</td>`);
                        //     child.append(`<td></td>`);
                        //     child.append(`<td>
                        //                     <div style="display: flex; justify-content: center; align-items: center;">
                        //                         <input type="checkbox" name="data[${data[i].id}][idJOD][]" class="form-check children children_of_${data[i].id}" parent="${data[i].id}" value="${data[i].get_details[j].id}" />
                        //                     </div>
                        //                 </td>`);

                        //     $("#hasil").append(child);
                        // }
                    }
                    new DataTable('#tabel_invoice_karantina', {
                        order: [
                            [0, 'asc'],
                            [1, 'asc']
                        ],
                        rowGroup: {
                            dataSrc: [0,1]
                        },
                        columnDefs: [
                            {
                                targets: [0,1],
                                visible: false
                            },
                            { orderable: false, targets: -1 }
                        ]
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

@if (session()->has('id_invoice'))
<script>
    var idInvoiceSession = "<?= session()->has('id_invoice') ? session()->get('id_invoice') : null ?>";
    console.log('idInvoiceSession', idInvoiceSession);
    window.open(`/invoice_karantina/print/${idInvoiceSession}`, "_blank");
    // di set null biar ga open new tab terus2an 
    setTimeout(function() {
        sessionStorage.setItem('id_invoice', null);
    }, 1000); // Adjust the delay (in milliseconds) as needed
</script>
@endif

@endsection
