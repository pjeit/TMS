
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
  
@endsection

@section('content')
<style >
</style>
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
    <form action="{{ route('pencairan_uang_jalan_ftl.store') }}" id="post_data" method="POST" >
      @csrf
        <section class="m-2">
            <div class="">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('invoice.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    </div>
                    <div class="card-body" >
                        <div class="row">
                            <div class="col-6">

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="no_akun">No. Invoice</label>
                                            <input type="text" id="customer" name="customer" class="form-control" value="" placeholder="otomatis" readonly>   
                                        </div>  
                                    </div>
                                        <div class="col-6">
                                        
                                            <div class="form-group">
                                            <label for="tanggal_pencairan">Tanggal Invoice<span style="color:red">*</span></label>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input disabled type="text" autocomplete="off" name="tanggal_pencairan" class="form-control date" id="tanggal_pencairan" placeholder="dd-M-yyyy" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">Customer</label>
                                    <input type="text" id="customer" name="customer" class="form-control" value="" readonly>                         
                                </div>  

                                <div class="form-group">
                                    <label for="tanggal_pencairan">Jatuh Tempo<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input name="jatuh_tempo" id="jatuh_tempo" class="form-control date" type="text" autocomplete="off" placeholder="dd-M-yyyy" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">Catatan</label>
                                    <input type="text" id="catatan" name="catatan" class="form-control" value="">                         
                                </div>  
                            </div>
    
                            <div class="col-6">
                                <div class="form-group ">
                                    <label for="total_hutang">Total Tagihan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_hutang" name="total_hutang" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="potong_hutang">Total Dibayar</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" onkeyup="cek_potongan_hutang();hitung_total();" maxlength="100" id="potong_hutang" name="potong_hutang" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                            
                                <div class="form-group ">
                                    <label for="total_diterima">Total Jumlah Muatan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Kg</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_diterima" name="total_diterima" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="total_diterima">Total Sisa</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_diterima" name="total_diterima" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>

            {{-- <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-body" > --}}
                        <table class="table table-hover table-bordered table-striped " width='100%' id="table_invoice">
                            <thead>
                                <tr>
                                    <th>Tujuan</th>
                                    <th>Sewa</th>
                                    <th>No. Kontainer &amp; SJ</th>
                                    <th style="width:1px; white-space: nowrap; text-align:right">Jumlah Muatan</th>
                                    <th style="width:1px; white-space: nowrap; text-align:right">Tarif</th>
                                    <th style="width:1px; white-space: nowrap; text-align:right">Add Cost/Inap</th>
                                    <th style="width:1px; white-space: nowrap; text-align:right">Diskon</th>
                                    <th style="width:1px; white-space: nowrap; text-align:right">Subtotal</th>
                                    <th>Catatan</th>
                                    <th style="width:30px"></th>
                                </tr>
                            </thead>
                            <tbody>
                            @isset($data)
                                @foreach ($data as $item)
                                    <tr id="0">
                                        <td id="nama_tujuan">{{ $item->nama_tujuan }}</td>
                                        <td id="sewa">{{ date("d-M-Y", strtotime($item->tanggal_berangkat)) }} <br> {{ $item->no_polisi }} ({{ $item->getKaryawan->nama_panggilan }})</td>
                                    <td id="nokontainer_sj"> {{ isset($item->id_jo_detail)? $item->getJOD->no_kontainer:'(OUTBOUND)' }} <br> {{ $item->no_surat_jalan }}</td>
                                        <td id="jumlah_muatan">-</td>
                                        <td style="text-align:right" id="tarif_0">{{ number_format($item->total_tarif) }}</td>
                                        <td style="text-align:right" id="total_reimburse_tidak_dipisahkan_0">0</td>
                                        <td style="text-align:right" id="diskon_0">0</td>
                                        <td style="text-align:right" id="subtotal_0">800,000</td>
                                        <td id="catatan_0"></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                </button>
                                                <ul class="dropdown-menu" style="">
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail(0)"><span class="fas fa-edit" style="width:24px"></span>Ubah</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail(0)"><span class="fas fa-eraser" style="width:24px"></span>Hapus</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endisset
                            </tbody>
                        </table>
                    {{-- </div>
                </div>
            </div> --}}
        
        </section>
 
    </form>
<script type="text/javascript">
        function hitung_total(){
            if($('#uang_jalan').val()!=''){
                var total_uang_jalan=removePeriod($('#uang_jalan').val(),',');
            }else{
                var total_uang_jalan=0;
            }
            
            if($('#potong_hutang').val()!=''){
                var potong_hutang=removePeriod($('#potong_hutang').val(),',');
            }else{
                var potong_hutang=0;
            }
            
            var total_diterima=parseFloat(total_uang_jalan)-parseFloat(potong_hutang);
            if(total_diterima!=0){
                $('#total_diterima').val(addPeriodType(total_diterima,','));
            }else{
                $('#total_diterima').val('');
            }
        }
   
        function cek_potongan_hutang(){
            if($('#total_hutang').val()!=''){
                var total_hutang =removePeriod($('#total_hutang').val(),',');
            }else{
                var total_hutang =0;
            }
            
            var potong_hutang = removePeriod($('#potong_hutang').val(),',');
            if(parseFloat(potong_hutang)>parseFloat(total_hutang)){
                $('#potong_hutang').val(addPeriodType(total_hutang,','));
            }else{
                $('#potong_hutang').val(addPeriodType(potong_hutang,','));
            }
        }
        function ubahTanggal(dateString) {
            var dateObject = new Date(dateString);
            var day = dateObject.getDate();
            var month = dateObject.toLocaleString('default', { month: 'short' });
            var year = dateObject.getFullYear();

            return day + '-' + month + '-' + year;
        }


    $(document).ready(function() {

        // console.log($('#select_sewa').val());
        
        function getDate(){
            var today = new Date();
            // var tomorrow = new Date(today);
            // tomorrow.setDate(today.getDate()+1);

            $('#tanggal_pencairan').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                startDate: today,
            }).datepicker("setDate", today);

            $('#tanggal_pencatatan').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                startDate: today,
            }).datepicker("setDate", today);

            
        }
       
        function getDetailSewa(id){
            var baseUrl = "{{ asset('') }}";

             $.ajax({
                url: `${baseUrl}pencairan_uang_jalan_ftl/getDatasewaDetail/${id}`, 
                method: 'GET', 
                success: function(response) {
                    if(response)
                    {
                        var dataSewaDetail = response.sewaDetail;
                        var dataHutangKaryawan =  response.hutangKaryawan;


                        $('#tanggal_berangkat').val( ubahTanggal(dataSewaDetail.tanggal_berangkat));
                        $('#customer').val(dataSewaDetail.nama_cust);
                        $('#tujuan').val(dataSewaDetail.nama_tujuan);
                        $('#id_karyawan').val(dataSewaDetail.id_karyawan);


                        $('#kendaraan').val(dataSewaDetail.no_polisi);
                        $('#driver').val(dataSewaDetail.supir);
                        $('#total_hutang').val(addPeriodType(dataHutangKaryawan.total_hutang,','));
                        $('#uang_jalan').val(addPeriodType(dataSewaDetail.total_uang_jalan,','));


                        cek_potongan_hutang();
                        hitung_total();
                        console.log(response);

                    }
                    // else
                    // {

                    // }
        
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
        getDate();
        
        getDetailSewa($('#id_sewa_defaulth').val())

        // hitung_total();
        // cek_potongan_hutang();
        //  $('#select_sewa').select2({
        //     allowClear: true,
        //     minimumInputLength:0
        // })
         $('body').on('change','#select_sewa',function()
		{
            var selectedValue = $(this).val();
            getDetailSewa(selectedValue);
		});
      
        $('#post_data').submit(function(event) {
            var kas = $('#pembayaran').val();
            if (kas == '' || kas == null) {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: 'error',
                    text: 'KAS PEMBAYARAN WAJIB DIPILIH!',
                })
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
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })

                    Toast.fire({
                        icon: 'success',
                        title: 'Data Disimpan'
                    })

                    setTimeout(() => {
                        this.submit();
                    }, 800); // 2000 milliseconds = 2 seconds
                }else{
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
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


