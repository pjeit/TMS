
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
    <form id="post_data" action="{{ route('pencairan_komisi_driver.store') }}" method="post">
        @csrf
        <div class="card radiusSendiri">
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="row">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="tanggal_pencairan">Tanggal Pencairan</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                <input type="text" name="tanggal_pencairan" autocomplete="off" class="date form-control" id="tanggal_pencairan" placeholder="dd-M-yyyy" value="{{ date("d-M-Y") }}" disabled>  
                                </div>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label for="karyawan">Driver<span class="text-red">*</span></label>
                                <select class="form-control select2" name="karyawan" id="karyawan" data-live-search="true" data-show-subtext="true" data-placement="bottom" required>
                                    <option value="">── Pilih Driver ──</option>
                                    @foreach ($dataDriver as $data)
                                        <option value="{{$data->id}}" valueDriver="{{ $data->nama_panggilan }} - ({{$data->telp1}})">{{ $data->nama_panggilan }} - ({{$data->telp1}})</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="valueDriver" id="valueDriver">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-5 col-md-5 col-sm-12">
                                <label for="periode">Tanggal Berangkat</label>
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                <input type="text" name="tanggal_awal" autocomplete="off" class="date form-control" id="tanggal_awal" placeholder="dd-M-yyyy" value="{{ date("d-M-Y") }}">  
                                <span style="margin-left: 20px;">-</span>   
                                </div>
                            </div>
                            <div class="form-group col-lg-4 col-md-4 col-sm-12">
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
                            <div class="col-lg-3 col-md-3 col-sm-12" style="margin-top: 33px;">
                                <button type="button" id="btnFilter" class="btn btn-primary radiusSendiri" ><i class="fas fa-search"></i> <b> Filter</b></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 radiusSendiri p-3" style="background-color: rgb(230, 230, 232);">
                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                            <label for="Total">Total</label>
                            <ul class="list-group mb-1">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Total (IDR)</span>
                                    <input type="hidden" name="total_komisi_driver" id="total_komisi_driver" value="">
                                    <strong id="html_komisi_driver"></strong>
                                </li>
                            </ul>
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                            <label for="no_akun">Total Pencairan</label>
                            <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input onkeyup="cekPenerima();" type="text" id="total_pencairan" name="total_pencairan" class="form-control uang numaja" value="">                         
                                </div>                      
                        </div>  
                        
                           
                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
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
                </div>
            </div>
            <div class="card-body" style="overflow: auto;">
                <table class="table table-bordered table-striped" style="border: 2px solid #bbbbbb;">
                    <thead>
                        <tr>
                            <th style="width:1px; white-space: nowrap;">Tgl. Berangkat</th>
                            <th>Nama Tujuan</th>
                            <th>Alamat Tujuan</th>
                            <th style="width:1px; white-space: nowrap; text-align:right;">Komisi Driver</th>
                        </tr>
                    </thead>
                    <tbody id="dataTabel">
                        <tr id="loading-spinner" style="display: none;">
                            <td colspan="4"><i class="fas fa-spinner fa-spin"></i> Harap tunggu data sedang di proses...</td>
                        </tr>
                        {{-- <tr >
                            <th colspan="3"> Total (IDR)</th>
                            <th colspan="1" style="text-align: right;"> Rp.20.000</th>
                        </tr> --}}
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
<script>
    
        function cekPenerima()
		{
            
            if(total_komisi_driver!=''){
                var total_komisi_driver=$('#total_komisi_driver').val();
            }else{
                var total_komisi_driver=0;
            }

            if(total_pencairan!=''){
                var total_pencairan=escapeComma($('#total_pencairan').val());
            }else{
                var total_pencairan=0;
            }

            if(parseFloat(total_pencairan)>parseFloat(total_komisi_driver)){
                $('#total_pencairan').val(moneyMask(total_komisi_driver,','));
            }
            else{
                $('#total_pencairan').val(moneyMask(total_pencairan,','));
            }

		}
        $(document).ready(function() {
        var today = new Date();
         $('body').on('key','#karyawan',function()
		{
            var selectedOption = $(this).find('option:selected');
            var valueDriver = selectedOption.attr('valueDriver');
            
            $('#valueDriver').val(valueDriver);
            console.log($('#valueDriver').val());

		});
        cekPenerima();

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
            $('#html_komisi_driver').text('Rp. ' + addPeriodType(totalKomisi, ','));
            $('input[name="total_komisi_driver"]').val(totalKomisi);
        }

        HitungTotalKomisi();
        $('body').on('click','#btnFilter', function (){
             cekPenerima();
     
            if ($('#karyawan').val()=='') {
                event.preventDefault(); 
                /*Swal*/Toast.fire({
                    icon: 'warning',
                    // title: 'TIDAK ADA KOMISI YANG DICAIRKAN!',
                    text: 'PILIH DRIVER TERLEBIH DAHULU!',

                });
                return;
            }
            $("#loading-spinner").show();
            $.ajax({
                    url: `${baseUrl}pencairan_komisi_driver/load_data`, 
                    method: 'GET', 
                    data: {
                        tanggal_awal: $('#tanggal_awal').val(),
                        tanggal_akhir: $('#tanggal_akhir').val(),
                        karyawan: $('#karyawan').val()
                    },
                    success: function(response) {
                        if(response)
                        {
                            // console.log(response);
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
                                            <td>Rp. ${addPeriodType(value.total_komisi_driver,',')}
                                                <input type="hidden" name="data[${i}][komisi_driver]" class='komisi' value="${value.total_komisi_driver}">
                                            </td>
                                        </tr>
                                    `)
                                }   
                                 HitungTotalKomisi();
                                cekPenerima();

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
                                cekPenerima();

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

        $('body').on('change','#karyawan',function()
		{
            var selectedOption = $(this).find('option:selected');
            var valueDriver = selectedOption.attr('valueDriver');
            
            $('#valueDriver').val(valueDriver);
            console.log($('#valueDriver').val());

		});

        $('#post_data').submit(function(event) {
          
            // console.log($('input[name="total_komisi_driver"]').val());
            if ($('input[name="total_komisi_driver"]').val()==0 || $('input[name="total_komisi_driver"]').val()=="0") {
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
