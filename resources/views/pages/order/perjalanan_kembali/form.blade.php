
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
    <form action="{{ route('perjalanan_kembali.store',[$sewa->id_sewa]) }}" id="post_data" method="POST" >
      @csrf

        <div class="row m-2">
        
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        <a href="{{ route('perjalanan_kembali.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                        <span style="font-size:11pt;" class="badge bg-dark float-right m-2">{{$sewa->jenis_order}} ORDER</span>
                    </div>
                    <div class="card-body" >
                        {{-- <div class="d-flex" style="gap: 20px;width:100%;"> --}}
                            <div class="row">
                                <div class="col-6">
                                   <div class="form-group ">
                                       <label for="tanggal_pencairan">Tanggal Berangkat<span style="color:red">*</span></label>
                                       <div class="input-group mb-0">
                                           <div class="input-group-prepend">
                                           <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                           </div>
                                           <input disabled type="text" autocomplete="off" name="tanggal_berangkat" class="form-control date" id="tanggal_berangkat" placeholder="dd-M-yyyy" value="{{ \Carbon\Carbon::parse($sewa->tanggal_berangkat)->format('d-M-Y')}}">
                                       </div>
                                   </div>  
   
                                   <div class="form-group ">
                                       <label for="no_akun">Customer</label>
                                       <input type="text" id="customer" name="customer" class="form-control" value="{{$sewa->nama_cust}}" readonly>                         
                                   </div>  
   
                                   <div class="form-group ">
                                       <label for="no_akun">Tujuan</label>
                                       <input type="text" id="tujuan" name="tujuan" class="form-control" value="{{$sewa->nama_tujuan}}" readonly>                         
                                   </div>  
   
                                    <div class="form-group ">
                                       <label for="no_akun">Catatan</label>
                                       <input type="text" id="catatan" name="catatan" class="form-control" value="{{$sewa->catatan}}" >                         
                                   </div> 


                                </div>
                                <div class="col-6">
                                       <div class="row">
                                           {{-- <div class="form-group col-12">
                                               Data Kendaraan
                                            <hr>
           
                                           </div> --}}
                                           <div class="form-group col-6">
                                               <label for="no_akun">Kendaraan</label>
                                               <input type="text" id="kendaraan" name="kendaraan" class="form-control" value="{{$sewa->no_polisi}}" readonly>                         
                                           </div>  
           
                                           {{-- @if ($sewa->supir) --}}
                                           <div class="form-group col-6">
                                               <label for="no_akun">Driver</label>
                                               <input type="text" id="driver" name="driver" class="form-control" value="{{$sewa->supir}} ({{$sewa->telpSupir}})" readonly>     
                                               <input type="hidden" name="id_karyawan" id="id_karyawan">                    
                                           </div> 
                                           {{-- @endif --}}
    
                                       </div>
                                       
                                        <div class="form-group">
                                            <label for="no_akun">No. Kontainer</label>
                                            <input type="text" id="no_kontainer" name="no_kontainer" class="form-control" {{$sewa->no_kontainer?'readonly':''}} value="{{$sewa->no_kontainer}}" >                         
                                        </div> 
                                        <div class="form-group">
                                            <label for="tanggal_pencairan">Tgl. Kembali Surat Jalan<span style="color:red">*</span></label>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                     <span class="input-group-text"><input type="checkbox" name="cekTglKembali" id="cekTglKembali"></span>
                                                    
                                                </div>
                                                <input disabled type="text" autocomplete="off" name="tanggal_berangkat" class="form-control date" id="tanggal_berangkat" placeholder="dd-M-yyyy" value="">
                                            </div>
                                        </div> 
        
                                        <div class="form-group">
                                            <label for="no_akun">No. Surat Jalan</label>
                                            <input type="text" id="surat_jalan" name="surat_jalan" class="form-control" value="" >                         
                                        </div> 
                                        <input type="hidden" name="id_jo_detail_hidden" id="id_jo_detail_hidden" value="{{$sewa->id_jo_detail}}">
        
                                        <input type="hidden" name="add_cost_hidden" id="add_cost_hidden">

                                        
                                        <div class="row">
                                            <div class="form-group col-6">
                                                <label for="no_akun">Seal</label>
                                                <input type="text" id="seal" name="seal" class="form-control"value="" >                         
                                            </div> 
            
                                            <div class="form-group col-6">
                                                <label for="tanggal_pencairan">Seal PJE<span style="color:red">*</span></label>
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                            <span class="input-group-text"><input type="checkbox" name="cek_seal_pje" id="cek_seal_pje"></span>
                                                    </div>
                                                    <input disabled type="text"  name="seal_pje" class="form-control" id="seal_pje" value="">
                                                </div>
                                            </div> 
                                        </div>
    
                                      
                                   
                               </div>
                            </div>
                            {{-- <div class="row">


                            </div> --}}
                        {{-- </div> --}}
                    </div>
                </div> 
            </div>
        {{-- <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <h3 class="card-title">Foto</h3>
                    </div>
                    <input type="hidden" name="default_foto_kontainer" id="default_foto_kontainer" value="">
                    <input type="hidden" name="default_foto_surat_jalan" id="default_foto_surat_jalan" value="">
                    <input type="hidden" name="default_foto_segel_pelayaran_1" id="default_foto_segel_pelayaran_1" value="">
                    <input type="hidden" name="default_foto_segel_pelayaran_2" id="default_foto_segel_pelayaran_2" value="">
                    <input type="hidden" name="default_foto_segel_pje" id="default_foto_segel_pje" value="">
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-2 col-md-6 mx-auto" name="div_foto_kontainer" id="div_foto_kontainer">
                                    <div class="form-group text-center">
                                        <a href="#" class="pop">
                                            <img src="{{asset('img/foto_default.jpg')}}" class="img-fluid" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_kontainer">
                                        </a>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input form-control" id="foto_kontainer" name="foto_kontainer" accept="image/jpeg" value="" hidden="">
                                            <label class="btn btn-primary" for="foto_kontainer" style="text-align: center">Pilih
                                                Foto Kontainer</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 mx-auto" name="div_foto_surat_jalan" id="div_foto_surat_jalan">
                                    <div class="form-group text-center">
                                        <a href="#" class="pop">
                                            <img src="{{asset('img/foto_default.jpg')}}" class="img-fluid" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_surat_jalan">
                                        </a>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input form-control" id="foto_surat_jalan" name="foto_surat_jalan" accept="image/jpeg" value="" hidden="">
                                            <label class="btn btn-primary" for="foto_surat_jalan" style="text-align: center">Pilih Foto Surat Jalan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 mx-auto" name="div_foto_segel_pelayaran_1" id="div_foto_segel_pelayaran_1">
                                    <div class="form-group text-center">
                                        <a href="#" class="pop">
                                            <img src="{{asset('img/foto_default.jpg')}}" class="img-fluid" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_pelayaran_1">
                                        </a>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input form-control" id="foto_segel_pelayaran_1" name="foto_segel_pelayaran_1" accept="image/jpeg" value="" hidden="">
                                            <label class="btn btn-primary" for="foto_segel_pelayaran_1" style="text-align: center">Pilih Foto Segel Pelayaran 1</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 mx-auto" name="div_foto_segel_pelayaran_2" id="div_foto_segel_pelayaran_2">
                                    <div class="form-group text-center">
                                        <a href="#" class="pop">
                                            <img src="{{asset('img/foto_default.jpg')}}" class="img-fluid" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_pelayaran_2">
                                        </a>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input form-control" id="foto_segel_pelayaran_2" name="foto_segel_pelayaran_2" accept="image/jpeg" value="" hidden="">
                                            <label class="btn btn-primary" for="foto_segel_pelayaran_2" style="text-align: center">Pilih Foto Segel Pelayaran 2</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-12 mx-auto" name="div_foto_segel_pje" id="div_foto_segel_pje">
                                    <div class="form-group text-center">
                                        <a href="#" class="pop">
                                            <img src="{{asset('img/foto_default.jpg')}}" class="img-fluid" style="width:150px;height:150px; object-fit: cover;" id="preview_foto_segel_pje">
                                        </a>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input form-control" id="foto_segel_pje" name="foto_segel_pje" accept="image/jpeg" value="" hidden="">
                                            <label class="btn btn-primary" for="foto_segel_pje" style="text-align: center">Pilih
                                                Foto Segel PJE</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>--}}
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-body">
                        <table class="table table-bordered card-outline card-primary " id="sortable" >
                              <thead>
                                  <tr>
                                      <th colspan="6">COST/INAP</th>
                                  </tr>
                                <tr>
                                    <th style="width: 30px;"></th>
                                    <th >Deskripsi</th>
                                    <th>Jumlah</th>
                                    <th >Ditagihkan</th>
                                    <th>Dipisahkan</th>
                                    <th>Catatan</th>
                                </tr>
                              </thead>
                              <tbody > 
                                <tbody>
                                    <tr id="0">
                                        <td>
                                            {{-- <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                </button>
                                                <ul class="dropdown-menu" style="">
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail(0)"><span class="fas fa-edit" style="width:24px"></span>Ubah</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail(0)"><span class="fas fa-eraser" style="width:24px"></span>Hapus</a></li>
                                                </ul>
                                            </div> --}}
                                            {{-- <input type="checkbox" class="checkitem" name="checkbox_seal" id="thc_cekbox"> --}}
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="checkboxPrimary1" >
                                                <label for="checkboxPrimary1"></label>
                                            </div>
                                           
                                        </td>
                                        <td id="sewa_reimburse_id_0" hidden="">1161</td>
                                        <td id="deskripsi_0">SEAL</td>
                                        <td style=" white-space: nowrap; text-align:right;" id="total_reimburse_0">
                                             <input type="text" name="nominal" id="nominal" value="50000" class="form-control uang numaja" readonly>
                                        </td>
                                        <td style="width:1px; white-space: nowrap; text-align:center;" id="ditagihkan_0">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="checkTagih" >
                                                <label for="checkTagih"></label>
                                            </div>
                                        </td>
                                        <td style="width:1px; white-space: nowrap; text-align:center;" id="dipisahkan_0">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="checkPisah" >
                                                <label for="checkPisah"></label>
                                            </div>
                                        </td>
                                        <td id="catatan_0">
                                             <input type="text" name="nominal" id="nominal" value="" class="form-control">
                                        </td>
                                    </tr>
                                      <tr id="1">
                                        <td>
                                            {{-- <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                </button>
                                                <ul class="dropdown-menu" style="">
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="open_detail(0)"><span class="fas fa-edit" style="width:24px"></span>Ubah</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="delete_detail(0)"><span class="fas fa-eraser" style="width:24px"></span>Hapus</a></li>
                                                </ul>
                                            </div> --}}
                                            {{-- <input type="checkbox" class="checkitem" name="checkbox_seal" id="thc_cekbox"> --}}
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="checkboxPrimary1" >
                                                <label for="checkboxPrimary1"></label>
                                            </div>
                                           
                                        </td>
                                        <td id="sewa_reimburse_id_0" hidden="">1161</td>
                                        <td id="deskripsi_1">SEAL</td>
                                        <td style=" white-space: nowrap; text-align:right;" id="total_reimburse_0">
                                             <input type="text" name="nominal" id="nominal" value="50000" class="form-control uang numaja" readonly>
                                        </td>
                                        <td style="width:1px; white-space: nowrap; text-align:center;" id="ditagihkan_0">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="checkTagih_1" >
                                                <label for="checkTagih_1"></label>
                                            </div>
                                        </td>
                                        <td style="width:1px; white-space: nowrap; text-align:center;" id="dipisahkan_0">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="checkPisah_1" >
                                                <label for="checkPisah_1"></label>
                                            </div>
                                        </td>
                                        <td id="catatan_0">
                                             <input type="text" name="nominal" id="nominal" value="" class="form-control">
                                        </td>
                                    </tr>
                            
                                 
                                
                              </tbody>
                              <tfoot>
                              </tfoot>
                        </table>

                    </div>
                </div>
                
            </div>
           
        </div> 
         
 
    </form>
<script type="text/javascript">
        // function hitung_total(){
        //     if($('#uang_jalan').val()!=''){
        //         var total_uang_jalan=removePeriod($('#uang_jalan').val(),',');
        //     }else{
        //         var total_uang_jalan=0;
        //     }
            
        //     if($('#potong_hutang').val()!=''){
        //         var potong_hutang=removePeriod($('#potong_hutang').val(),',');
        //     }else{
        //         var potong_hutang=0;
        //     }
            
        //     var total_diterima=parseFloat(total_uang_jalan)-parseFloat(potong_hutang);
        //     if(total_diterima!=0){
        //         $('#total_diterima').val(addPeriodType(total_diterima,','));
        //     }else{
        //         $('#total_diterima').val('');
        //     }
        // }
   
        // function cek_potongan_hutang(){
        //     if($('#total_hutang').val()!=''){
        //         var total_hutang =removePeriod($('#total_hutang').val(),',');
        //     }else{
        //         var total_hutang =0;
        //     }
            
        //     var potong_hutang = removePeriod($('#potong_hutang').val(),',');
        //     if(parseFloat(potong_hutang)>parseFloat(total_hutang)){
        //         $('#potong_hutang').val(addPeriodType(total_hutang,','));
        //     }else{
        //         $('#potong_hutang').val(addPeriodType(potong_hutang,','));
        //     }
        // }
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
       
        // function getDetailSewa(id){
        //     var baseUrl = "{{ asset('') }}";

        //      $.ajax({
        //         url: `${baseUrl}pencairan_uang_jalan_ftl/getDatasewaDetail/${id}`, 
        //         method: 'GET', 
        //         success: function(response) {
        //             if(response)
        //             {
        //                 var dataSewaDetail = response.sewaDetail;
        //                 var dataHutangKaryawan =  response.hutangKaryawan;


        //                 $('#tanggal_berangkat').val( ubahTanggal(dataSewaDetail.tanggal_berangkat));
        //                 $('#customer').val(dataSewaDetail.nama_cust);
        //                 $('#tujuan').val(dataSewaDetail.nama_tujuan);
        //                 $('#id_karyawan').val(dataSewaDetail.id_karyawan);


        //                 $('#kendaraan').val(dataSewaDetail.no_polisi);
        //                 $('#driver').val(dataSewaDetail.supir);
        //                 $('#total_hutang').val(addPeriodType(dataHutangKaryawan.total_hutang,','));
        //                 $('#uang_jalan').val(addPeriodType(dataSewaDetail.total_uang_jalan,','));


        //                 cek_potongan_hutang();
        //                 hitung_total();
        //                 console.log(response);

        //             }
        //             // else
        //             // {

        //             // }
        
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error:', error);
        //         }
        //     });
        // }
        // getDetailSewa($('#id_sewa_defaulth').val())
        var baseUrl = "{{ asset('') }}";
        var array_add_cost = [];
        $.ajax({
            url: `${baseUrl}truck_order/getDetailJOBiaya/${$('#id_jo_detail_hidden').val()}`, 
            method: 'GET', 
            success: function(response) {
                if(!response)
                {
                    array_tambahan_sdt = [];
                }
                else
                {
                    for (var i in response) {
                        if(response[i].storage || response[i].storage!=0)
                        {
                            var objSTORAGE = {
                                    deskripsi: 'STORAGE',
                                    biaya: response[i].storage,
                                };
                            array_tambahan_sdt.push(objSTORAGE);
                        } 
                        if(response[i].demurage||response[i].demurage!=0)
                        {
                            var objDEMURAGE = {
                                    deskripsi: 'DEMURAGE',
                                    biaya: response[i].demurage,
                                };
                            array_tambahan_sdt.push(objDEMURAGE);
                        } 
                        if(response[i].detention||response[i].detention!=0)
                        {
                            var objDETENTION = {
                                    deskripsi: 'DETENTION',
                                    biaya: response[i].detention,
                                };
                            array_tambahan_sdt.push(objDETENTION);
                        } 
                            
                    }
                    $('#add_cost_hidden').val(JSON.stringify(array_add_cost));
                    console.log('array_add_cost '+array_add_cost);

                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });

        getDate();
        

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


