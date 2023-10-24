
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')

@endsection

@section('content')
<br>
<style>
   
</style>
<div class="container-fluid">
     @if ($errors->any())
        @foreach ($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endforeach

    @endif
    <form id="post_data" action="{{ route('pencairan_komisi_customer.store') }}" method="post">
        @csrf
        <div class="card radiusSendiri">
            <div class="card-header" style="border: 2px solid #bbbbbb;">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="tanggal_pencairan">Tanggal Pencairan</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                <input type="text" name="tanggal_pencairan" autocomplete="off" class="date form-control" id="tanggal_pencairan" placeholder="dd-M-yyyy" value="{{ date("d-M-Y") }}" disabled>  
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="customer">Customer<span class="text-red">*</span></label>
                                <select class="form-control select2" name="customer" id="customer" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    <option value="">── Pilih Customer ──</option>
                                    @foreach ($dataCustomer as $data)
                                        <option value="{{$data->id}}" valueCustomer ="{{ $data->nama }} - ({{$data->kode}})">{{ $data->nama }} - ({{$data->kode}})</option>
                                    @endforeach
                                </select>
                                    <input type="hidden" name="valueCustomer" id="valueCustomer">

                            </div>
                                <div class="row" >
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="periode">Tanggal Berangkat</label>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                            <input type="text" name="tanggal_awal" autocomplete="off" class="date form-control" id="tanggal_awal" placeholder="dd-M-yyyy" value="{{ date("d-M-Y") }}">  
                                            <span style="margin-left: 20px;">-</span>   
                                            {{-- <label style="margin-left: 20px;">&nbsp; s/d &nbsp;</label> --}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="periode" style="opacity: 0%;">Tanggal Akhir</label>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text" name="tanggal_akhir" autocomplete="off" class="date  form-control" id="tanggal_akhir" placeholder="dd-M-yyyy" value="{{ date("d-M-Y") }}">  
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4" style="margin-top: 33px;">
                                        <button type="button" id="btnFilter" class="btn btn-primary radiusSendiri" ><i class="fas fa-search"></i> <b> Filter</b></button>

                                    </div>
                                
                                </div>
                        </div>
                        <div class="col-6">
                            <label for="Total">Total</label>
                            <ul class="list-group mb-1">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Total (IDR)</span>
                                       <input type="hidden" name="total_komisi_customer" value="">
                                        <strong id="html_komisi_customer"></strong>
                                </li>
                            </ul>
                            <label for="pembayaran">Pilih Kas/Bank</label>
                            <div class="input-group" style="gap: 10px;">
                                <select class="form-control select2" name="pembayaran" id="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    @foreach ($kasBank as $kb)
                                        <option value="{{$kb->id}}" <?= $kb->id == 1 ? 'selected':''; ?> >{{ $kb->nama }} - {{$kb->tipe}}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-success radiusSendiri" id="bttonBayar"><i class="fa fa-credit-card" aria-hidden="true"></i> Pencairan</button>
                            </div>
                        </div>
                    </div>
            </div><!-- /.card-header -->
            <div class="card-body" style="overflow: auto;">
                <table class="table table-bordered table-striped" style="border: 2px solid #bbbbbb;">
                    <thead>
                        <tr>
                            {{-- <th></th> --}}
                            <th style="width:1px; white-space: nowrap;">Tgl. Berangkat</th>
                            <th>Nama Tujuan</th>
                            <th>Alamat Tujuan</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Komisi</th>
                        </tr>
                    </thead>
                    <tbody id="dataTabel">
                            <tr id="loading-spinner" style="display: none;">
                                <td colspan="4"><i class="fas fa-spinner fa-spin"></i> Harap tunggu data sedang di proses...</td>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>

</div>
<script>
     $(document).ready(function() {
        var today = new Date();

        $('#tanggal_awal').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
        });
        $('#tanggal_akhir').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
        });
        $('#tanggal_pencairan').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            startDate: today,   
        });
        
        var baseUrl = "{{ asset('') }}";
        var Toast = Swal.mixin({
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
        function HitungTotalKomisi() {
            let totalKomisi = 0;
            $('.komisi').each(function () {
                totalKomisi += parseFloat(removePeriod($(this).val(), ','));
            });
            // console.log($('.komisi').length);
            $('#html_komisi_customer').text('Rp. ' + addPeriodType(totalKomisi, ','));
            $('input[name="total_komisi_customer"]').val(totalKomisi);
        }

        HitungTotalKomisi();
        $('body').on('click','#btnFilter', function (){
     
            if ($('#customer').val()=='') {
                event.preventDefault(); 
                /*Swal*/Toast.fire({
                    icon: 'warning',
                    // title: 'TIDAK ADA KOMISI YANG DICAIRKAN!',
                    text: 'PILIH CUSTOMER TERLEBIH DAHULU!',

                });
                return;
            }
            $("#loading-spinner").show();
            $.ajax({
                    url: `${baseUrl}pencairan_komisi_customer/load_data`, 
                    method: 'GET', 
                    data: {
                        tanggal_awal: $('#tanggal_awal').val(),
                        tanggal_akhir: $('#tanggal_akhir').val(),
                        customer: $('#customer').val()
                    },
                    success: function(response) {
                        if(response)
                        {
                            console.log(response);
                            $('#dataTabel').html('');
                             $('#dataTabel').append(`
                                 <tr id="loading-spinner" style="display: none;">
                                    <td colspan="4"><i class="fas fa-spinner fa-spin"></i> Harap tunggu data sedang di proses...</td>
                                </tr>
                             `);
                            var data = response.data;

                            if(data.length>0)
                            {
                                 $("#loading-spinner").hide();
                                 console.log(data);
                                for (let i = 0; i < data.length; i++) {
                                    const value = data[i];
                                    $('#dataTabel').append(`
                                        <tr>
                                            <td>${dateMask(value.tanggal_berangkat)}
                                                <input type="hidden" name="data[${i}][id_sewa]" value="${value.id_sewa}">
                                                <input type="hidden" name="data[${i}][tanggal_kembali]" value="${value.tanggal_berangkat}">
                                            </td>
                                            <td>${value.nama_tujuan}
                                                <input type="hidden" name="data[${i}][nama_tujuan]" value="${value.nama_tujuan}">
                                            </td>
                                            <td>${value.alamat_tujuan}
                                                <input type="hidden" name="data[${i}][alamat_tujuan]" value="${value.alamat_tujuan}">
                                            </td>
                                            <td>Rp. ${addPeriodType(value.total_komisi,',')}
                                                <input type="hidden" name="data[${i}][komisi_customer]" class='komisi' value="${value.total_komisi}">
                                            </td>
                                        </tr>
                                    `)
                                }   
                                 HitungTotalKomisi();

                            }
                            else
                            {
                                $('#dataTabel').append(`
                                    <tr>
                                        <td colspan='4' style='text-align: center'>
                                            Tidak ada data
                                        </td>
                                        
                                    </tr>
                                `)
                                 HitungTotalKomisi();

                            }
                            
                        }

                       
                      
            
                    },
                    error: function(xhr, status, error) {
                        console.log("XHR status:", status);
                        console.log("Error:", error);
                        console.log("Response:", xhr.responseJSON);
                    }
                });
           
        });

        $('body').on('change','#customer',function()
		{
            var selectedOption = $(this).find('option:selected');
            var valueCustomer = selectedOption.attr('valueCustomer');
            
            $('#valueCustomer').val(valueCustomer);
            console.log($('#valueCustomer').val());

		});

        $('#post_data').submit(function(event) {
          
            // console.log($('input[name="total_komisi_driver"]').val());
            if ($('input[name="total_komisi_customer"]').val()==0 || $('input[name="total_komisi_customer"]').val()=="0") {
                event.preventDefault(); 
                /*Swal*/Toast.fire({
                    icon: 'error',
                    // title: 'TIDAK ADA KOMISI YANG DICAIRKAN!',
                    text: 'TIDAK ADA KOMISI YANG DICAIRKAN!',

                });
                return;
            }
            event.preventDefault();
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
                }
            })
        });
                
    });

</script>
@endsection
