
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
   .tinggi{
    height: 20px;
   }
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

        <div class="row m-2">
        
            <div class="col">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('pencairan_uang_jalan_ftl.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                    </div>
                    <div class="card-body" >
                        <div class="d-flex" style="gap: 20px">
                            <div class="row">
                                 {{-- <div class="form-group col-12">
                                    Data Sewa
                                 <hr>

                                </div> --}}
                                <div class="form-group col-12">
                                    <label for="tanggal_pencairan">Tanggal Pencairan<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" autocomplete="off" name="tanggal_pencairan" class="form-control date" id="tanggal_pencairan" placeholder="dd-M-yyyy" value="">
                                    </div>
                                </div>  
                                {{-- <div class="form-group col-6">
                                    <label for="tanggal_pencairan">Tanggal Pencatatan<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input disabled type="text" autocomplete="off" name="tanggal_pencatatan" class="form-control date" id="tanggal_pencatatan" placeholder="dd-M-yyyy" value="">
                                    </div>
                                </div>     --}}
                                <div class="form-group col-12">
                                    <label for="select_customer">No. Sewa<span style="color:red">*</span></label>
                                    <select class="form-control select2" style="width: 100%;" id='select_sewa' name="select_sewa" disabled>
                                        {{-- <option value="">Pilih Sewa</option> --}}
                                        {{-- @foreach ($sewa as $s) --}}
                                            <option  selected value="{{$sewa->id_sewa}}">{{ $sewa->supir }} / {{ $sewa->nama_tujuan }} - {{ $sewa->no_sewa }} ({{ \Carbon\Carbon::parse($sewa->tanggal_berangkat)->format('d-M-Y') }}) </option>
                                        {{-- @endforeach --}}
                                    </select>
                                    <input type="hidden" value="{{$sewa->no_sewa}}" id="no_sewa" name="no_sewa">
                                    <input type="hidden" value="{{$sewa->id_sewa}}" id="id_sewa_defaulth" name="id_sewa_defaulth">
                                </div>

                                <div class="form-group col-12">
                                    <label for="tanggal_pencairan">Tanggal Berangkat<span style="color:red">*</span></label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input disabled type="text" autocomplete="off" name="tanggal_berangkat" class="form-control date" id="tanggal_berangkat" placeholder="dd-M-yyyy" value="">
                                    </div>
                                </div>  

                                <div class="form-group col-12">
                                    <label for="no_akun">Customer</label>
                                    <input type="text" id="customer" name="customer" class="form-control" value="" readonly>                         
                                </div>  

                                <div class="form-group col-12">
                                    <label for="no_akun">Tujuan</label>
                                    <input type="text" id="tujuan" name="tujuan" class="form-control" value="" readonly>                         
                                </div>  
                                {{-- @if (isset($sewaBiayaTelukLamong))
                                    <div class="form-group col-12">
                                        <label for="no_akun" style="opacity: 0%">handle white space</label>
                                        <input type="text" id="white" name="white" class="form-control" value="" readonly style="opacity: 0%">                         
                                    </div>  
                                @endif --}}
                            </div>
                            <div class="row">
                                {{-- <div class="form-group col-12">
                                    Data Kendaraan
                                 <hr>

                                </div> --}}
                                <div class="form-group col-6">
                                    <label for="no_akun">Kendaraan</label>
                                    <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="" readonly>                         
                                </div>  

                                <div class="form-group col-6">
                                    <label for="no_akun">Driver</label>
                                    <input type="text" id="driver" name="driver" class="form-control" value="" readonly>     
                                    <input type="hidden" name="id_karyawan" id="id_karyawan">                    
                                </div> 

                        

                                <div class="form-group col-6">
                                    <label for="total_hutang">Total Hutang</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_hutang" name="total_hutang" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>

                                <div class="form-group col-6">
                                    <label for="potong_hutang">Potong Hutang</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" onkeyup="cek_potongan_hutang();hitung_total();" maxlength="100" id="potong_hutang" name="potong_hutang" class="form-control uang numajaMinDesimal" value="" >                         
                                    </div>
                                </div>

                             

                                <div class="form-group col-{{isset($sewaBiayaTelukLamong)?'4':'6'}}">
                                    <label for="uang_jalan">Biaya Uang Jalan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="uang_jalan" name="uang_jalan" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                            @if (isset($sewaBiayaTelukLamong))
                                <div class="form-group col-4">
                                    <label for="uang_jalan">Biaya Teluk Lamong</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="teluk_lamong" name="teluk_lamong" class="form-control uang numajaMinDesimal" value="{{number_format($sewaBiayaTelukLamong->biaya)}}" readonly>                         
                                    </div>
                                </div>
                            @endif
                                <div class="form-group col-{{isset($sewaBiayaTelukLamong)?'4':'6'}}">
                                    <label for="total_diterima">Total Diberikan</label>
                                    <div class="input-group mb-0">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" maxlength="100" id="total_diterima" name="total_diterima" class="form-control uang numajaMinDesimal" value="" readonly>                         
                                    </div>
                                </div>
                            

                                <div class="form-group col-12">
                                    <label for="">PILIH PEMBAYARAN</label>      
                                    <div class="d-flex" style="gap: 10px;">
                                        <select class="form-control select2" style="width: 100%;" id='pembayaran' name="pembayaran" data-live-search="true" data-show-subtext="true" data-placement="bottom">
                                            <option value="">--PILIH KAS--</option>
                                            @foreach ($dataKas as $kas)
                                                <option value="{{$kas->id}}" {{$kas->id == 1? 'selected':''}}>{{ $kas->nama }}</option>
                                            @endforeach
                                        </select>

                                        <button type="submit" class="btn btn-success radiusSendiri"><i class="fa fa-credit-card" aria-hidden="true"></i> Bayar</button>
                                    </div>  
                                </div>  
                                   <div class="form-group col-12">
                                    <label for="no_akun">Catatan</label>
                                    <input type="text" id="catatan" name="catatan" class="form-control" value="" >                         
                                </div> 
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
{{-- 
            <div class="col-6">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        Driver & Uang Jalan
                    </div>
                    <div class="card-body">
                           
                    </div>
                </div>
            </div> --}}
        
        </div>
 
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
            var cekTL= <?php echo json_encode($sewaBiayaTelukLamong); ?>;
            var total_diterima=0;
            if(cekTL)
            {
                if($('#teluk_lamong').val()!='' || $('#teluk_lamong').val()!= undefined){
                    var teluk_lamong=removePeriod($('#teluk_lamong').val(),',');
                }else{
                    var teluk_lamong=0;
                }
               total_diterima=(parseFloat(total_uang_jalan)+parseFloat(teluk_lamong))-parseFloat(potong_hutang);
            }
            else
            {
               total_diterima=parseFloat(total_uang_jalan)-parseFloat(potong_hutang);
            }
            if(total_diterima!=0){
                $('#total_diterima').val(addPeriodType(total_diterima,','));
            }else{
                $('#total_diterima').val(0);
            }
        }
   
        function cek_potongan_hutang(){
            if($('#total_hutang').val()!=''){
                var total_hutang =removePeriod($('#total_hutang').val(),',');
            }else{
                var total_hutang =0;
            }
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
            var cekTL= <?php echo json_encode($sewaBiayaTelukLamong); ?>;
            var total_uj=0;
            if(cekTL)
            {
                if($('#teluk_lamong').val()!='' || $('#teluk_lamong').val()!= undefined){
                    var teluk_lamong=removePeriod($('#teluk_lamong').val(),',');
                }else{
                    var teluk_lamong=0;
                }
               total_uj=(parseFloat(total_uang_jalan)+parseFloat(teluk_lamong));
            }
            else
            {
               total_uj=parseFloat(total_uang_jalan);
            }
            
            var potong_hutang = removePeriod($('#potong_hutang').val(),',');
            if(parseFloat(potong_hutang)>parseFloat(total_hutang)){
                $('#potong_hutang').val(addPeriodType(total_hutang,','));
            }
            else{
                $('#potong_hutang').val(addPeriodType(potong_hutang,','));
            }

            if(parseFloat(potong_hutang)>parseFloat(total_uj) && parseFloat(total_hutang)>parseFloat(total_uj)){
                $('#potong_hutang').val(addPeriodType(total_uj,','));
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
                // startDate: today,
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
                        var biayaTL =  response.SewaBiayaTL;

                        // console.log(biayaTL.biaya);


                        $('#tanggal_berangkat').val( ubahTanggal(dataSewaDetail.tanggal_berangkat));
                        $('#customer').val(dataSewaDetail.nama_cust);
                        $('#tujuan').val(dataSewaDetail.nama_tujuan);
                        $('#id_karyawan').val(dataSewaDetail.id_karyawan);


                        $('#kendaraan').val(dataSewaDetail.no_polisi);
                        $('#driver').val(dataSewaDetail.supir);
                        var total_hutang = 0;
                        if (dataHutangKaryawan !== null && dataHutangKaryawan.total_hutang !== null) {
                            total_hutang = dataHutangKaryawan.total_hutang;
                        } 
                        var total_uang_jalan = 0;
                        if (dataSewaDetail !== null && dataSewaDetail.total_uang_jalan !== null) {
                            total_uang_jalan = dataSewaDetail.total_uang_jalan;
                        } 
                        if(total_hutang == 0){
                            $('#potong_hutang').attr('readonly', 'readonly');
                        }
                        $('#total_hutang').val(addPeriodType(total_hutang,','));
                        $('#uang_jalan').val(addPeriodType(total_uang_jalan,','));


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
            if (kas == '' && $('#total_diterima').val()!=0 || kas == null && $('#total_diterima').val()!=0) {
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


