
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
    <form action="{{ route('perjalanan_kembali.update',[$sewa->id_sewa]) }}" id="post_data" method="POST" >
      @csrf

        <div class="row m-2">
        
            <div class="col-12">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('perjalanan_kembali.index') }}" class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left"></i> Kembali</a>
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                        <span style="font-size:11pt;" class="badge bg-dark float-right m-2">{{$sewa->jenis_order}} ORDER {{$sewa->jenis_tujuan}}</span>
                    </div>
                    <div class="card-body" >
                        {{-- <div class="d-flex" style="gap: 20px;width:100%;"> --}}
                            <div class="row">
                                <div class="col-6" style=" border-right: 1px solid rgb(172, 172, 172);">
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
                                            @if ($sewa->no_kontainer_jod)
                                                <input type="text" id="no_kontainer" name="no_kontainer" class="form-control" readonly value="{{$sewa->no_kontainer_jod}}" >                         
                                            @else
                                                <input type="text" id="no_kontainer" name="no_kontainer" class="form-control" value="{{$sewa->no_kontainer}}" >                         

                                            @endif
                                        </div> 
                                       
                                        <div class="form-group">
                                            <label for="tanggal_pencairan">Tgl. Kembali Surat Jalan<span style="color:red">*</span></label>
                                            <div class="input-group mb-0">
                                                <div class="input-group-prepend">
                                                     <span class="input-group-text"><input {{$sewa->is_kembali=='N'?'':'checked'}} type="checkbox" name="check_is_kembali" id="check_is_kembali"></span>
                                                </div>
                                                <input type="hidden" id="is_kembali" name='is_kembali' value="{{$sewa->is_kembali}}">
                                                <input {{$sewa->is_kembali=='N'?'disabled':''}} type="text" autocomplete="off" name="tanggal_kembali" class="form-control date" id="tanggal_kembali" placeholder="dd-M-yyyy" value="{{$sewa->is_kembali=='Y'?\Carbon\Carbon::parse($sewa->tanggal_kembali)->format('d-M-Y'):''}}">
                                            </div>
                                        </div> 
        
                                        <div class="form-group">
                                            <label for="no_akun">No. Surat Jalan</label>
                                            <input type="text" id="surat_jalan" name="surat_jalan" class="form-control" value="{{$sewa->no_surat_jalan}}" >                         
                                        </div> 
                                        <input type="hidden" name="id_jo_detail_hidden" id="id_jo_detail_hidden" value="{{$sewa->id_jo_detail}}">
                                        <input type="hidden" name="add_cost_hidden" id="add_cost_hidden">
                                        <input type="hidden" id='jenis_tujuan' value='{{$sewa->jenis_tujuan}}'>

                                        
                                        <div class="row" name="div_segel" id="div_segel">
                                            <div class="form-group col-6">
                                                <label for="seal">Seal</label>
                                                @if ($sewa->seal_pelayaran_jod)
                                                    <input readonly type="text" id="seal" name="seal" class="form-control"value="{{$sewa->seal_pelayaran_jod}}" >
                                                @else
                                                    <input type="text" id="seal" name="seal" class="form-control"value="{{$sewa->seal_pelayaran}}" >
                                                @endif
                                            </div> 
            
                                            <div class="form-group col-6">
                                                <label for="seal_pje">Seal PJE<span style="color:red">*</span></label>
                                                <div class="input-group mb-0">
                                                    <div class="input-group-prepend">
                                                            <span class="input-group-text"><input {{$sewa->seal_pje?'checked':''}}type="checkbox" name="cek_seal_pje" id="cek_seal_pje"></span>
                                                    </div>
                                                <input readonly {{$sewa->seal_pje?'':'readonly'}}type="text" name="seal_pje" class="form-control" id="seal_pje" value="{{$sewa->seal_pje}}">
                                                </div>
                                            </div> 
                                        </div>

                                        <div class="row" name="lcl_selected" id="lcl_selected" >
                                            <div class="col-4 col-md-12 col-lg-4">
                                                <label for="muatan_ltl">Jumlah Muatan<span style="color:red;">*</span></label>
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        <input readonly type="text" class="form-control numajaCheckDesimal" name="muatan_ltl"
                                                            id="muatan_ltl">
                                                        <div class="input-group-append">
                                                            <div class="input-group-text">Kg</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-8 col-md-12 col-lg-8">
                                                <label for="total_harga_lcl">Total Harga</label>
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Rp.</span>
                                                        </div>
                                                        <input type="text" class="form-control numaja uang" name="total_harga_lcl"
                                                            id="total_harga_lcl" readonly>
                                                        <input type="hidden" id="min_muatan"
                                                            value='{{isset($sewa->min_muatan)?$sewa->min_muatan:''}}'>
                                                        <input type="hidden" id="harga_per_kg"
                                                            value='{{isset($sewa->harga_per_kg)?$sewa->harga_per_kg:''}} '>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
    
                                      
                                   
                               </div>
                            </div>
                          
                    </div>
                </div> 
            </div>
   
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
                              <tbody>
                                @foreach ($dataOpreasional as $key => $value)
                                    <tr id="{{$key}}">
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
                                                <input type="checkbox" id="checkboxPrimary_{{$key}}" >
                                                <label for="checkboxPrimary_{{$key}}"></label>
                                            </div>
                                            
                                        </td>
                                        <td id="sewa_reimburse_id_{{$key}}" hidden="">
                                            <input type="hidden" name="id_sewa_operasional[{{$key}}]" value="">
                                        </td>
                                        <td id="deskripsi_{{$key}}">PLASTIK</td>
                                        <td style=" white-space: nowrap; text-align:right;" id="total_reimburse_{{$key}}">
                                                <input type="text" name="nominal[{{$key}}]" id="nominal_{{$key}}" value="50000" class="form-control uang numaja" readonly>
                                        </td>
                                        <td style="width:1px; white-space: nowrap; text-align:center;" id="ditagihkan_{{$key}}">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="checkTagih_{{$key}}" >
                                                <label for="checkTagih_{{$key}}"></label>
                                            </div>
                                        </td>
                                        <td style="width:1px; white-space: nowrap; text-align:center;" id="dipisahkan_{{$key}}">
                                            <div class="icheck-primary d-inline">
                                                <input type="checkbox" id="checkPisah_{{$key}}" >
                                                <label for="checkPisah_{{$key}}"></label>
                                            </div>
                                        </td>
                                        <td id="catatan_{{$key}}">
                                            <input type="text" name="nominal[{{$key}}]" id="nominal_{{$key}}" value="" class="form-control">
                                        </td>
                                    </tr>
                                @endforeach
                                
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
    
        function ubahTanggal(dateString) {
            var dateObject = new Date(dateString);
            var day = dateObject.getDate();
            var month = dateObject.toLocaleString('default', { month: 'short' });
            var year = dateObject.getFullYear();

            return day + '-' + month + '-' + year;
        }

        


    $(document).ready(function() {
        // console.log($('#select_sewa').val());
        
        $('#tanggal_kembali').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            endDate: "0d"
        });
        $('#muatan_ltl').keyup(function(e) {
            let muatan = e.target.value;
            const temp = muatan.split(".");
            muatan = parseFloat(muatan);
            if(temp.length > 1 ){
                if(temp[1].length > 2){
                    console.log(parseFloat(muatan.toFixed(2)));
                    $('#muatan_ltl').val(muatan.toFixed(2));
                }
            }

            let total_harga = hitung_total_harga_dari_muatan(muatan.toFixed(2));
            $('#total_harga_lcl').val(total_harga);

        });
        if ($('#jenis_tujuan').val() != "LTL") {
            $('#lcl_selected').css('display', 'none');
            $('#div_segel').show();
        } else {
            $('#lcl_selected').css('display', '');
            $('#div_segel').hide();
            // $('#div_foto_segel_pelayaran_1').hide();
            // $('#div_foto_segel_pelayaran_2').hide();
            // $('#div_foto_segel_pje').hide();
        }

        function getDate(){
            var today = new Date();
            // var tomorrow = new Date(today);
            // tomorrow.setDate(today.getDate()+1);

            //  $('#tanggal_kembali').datepicker({
            //     autoclose: true,
            //     format: "dd-M-yyyy",
            //     todayHighlight: true,
            //     language:'en',
            //     endDate: "0d"
            // });

            $('#tanggal_kembali').datepicker({
                autoclose: true,
                format: "dd-M-yyyy",
                todayHighlight: true,
                language: 'en',
                startDate: today,
            }).datepicker("setDate", today);
        }
        function get_date_now(){
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.toLocaleString('default', { month: 'short' })).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();
            today = dd + '-' + mm + '-' + yyyy;
            return today;
        }
         $('#check_is_kembali').click(function() {
            if ($(this).is(":checked")) {

                $('#is_kembali').val('Y');
                $('#tanggal_kembali').attr('disabled', false);
                $('#muatan_ltl').attr('readonly', false);
                // getDate();
                $('#tanggal_kembali').val(get_date_now());


            } else if ($(this).is(":not(:checked)")) {

                $('#is_kembali').val('N');
                $('#muatan_ltl').attr('readonly', true);
                $('#tanggal_kembali').attr('disabled', true);
                $('#tanggal_kembali').val('');
            }
        });

        if ($('#check_is_kembali').is(":checked")) {

            $('#is_kembali').val('Y');
            $('#tanggal_kembali').attr('disabled', false);
            $('#muatan_ltl').attr('readonly', false);
            // getDate();
            $('#tanggal_kembali').val(get_date_now());

        };

        $('#cek_seal_pje').click(function() {
            if ($(this).is(":checked")) {

                $('#seal_pje').prop('readonly', false);
          
            } else if ($(this).is(":not(:checked)")) {
        
                $('#seal_pje').prop('readonly', true);
            }
        });
        if ($('#cek_seal_pje').is(":checked")) {

            $('#seal_pje').prop('readonly', false);

        };
       
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

        // getDate();
        

        // hitung_total();
        // cek_potongan_hutang();
        //  $('#select_sewa').select2({
        //     allowClear: true,
        //     minimumInputLength:0
        // })
       
      
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


