@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')

@endsection

@section('content')
@include('sweetalert::alert')

<style>
#preview_foto_nota {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}

#preview_foto_barang {
  transition: transform 0.8s ease; /* Add a transition to the 'transform' property */
  border-radius: 5px;
}

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

    <div class="card radiusSendiri">
        <div class="card-header">

            <button class="btn btn-primary btn-responsive float-left radiusSendiri" data-toggle="modal" data-target="#modal_tambah">
                <i class="fa fa-plus-circle"> </i> Tambah Data

            </button>
        </div>
        <div class="card-body">
            <table id="tabel_transaksi_lain" class="table table-bordered table-striped" width="100%">
                <thead>
                    <tr>
                        <th>Tgl. Transaksi</th>
                        <th>Jenis</th>
                        <th>Kas/Bank</th>
                        <th>Total Nominal</th>
                        <th>Catatan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @if (isset($dataKasLain))
                        @foreach ($dataKasLain as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-M-Y')}} </td>
                            <td>{{$item->nama_jenis}}</td>
                            <td>
                                    @foreach ($dataKas as $kas)

                                    @if ($kas->id == $item->kas_bank_id)
                                        {{ $kas->nama}}
                                    @endif
                                    
                                @endforeach
                            </td>
                            <td>Rp. {{number_format($item->total,2)  }}</td>
                            <td>{{ $item->catatan }}</td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{route('transaksi_lain.edit',[$item->id])}}" class="dropdown-item ">
                                            <span class="fas fa-edit mr-3"></span> Edit
                                        </a>
                                        <a href="{{ route('transaksi_lain.destroy', [$item->id]) }}" class="dropdown-item" data-confirm-delete="true">
                                            <span class="fas fa-trash mr-3"></span> Hapus
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif --}}
                </tbody>
            </table>
            {{-- <span style="float: right;">{{ $dataKasLain->links('pagination::bootstrap-4') }}</span>  --}}

        </div>
    </div>
</div>
<div class="modal fade" id="modal_tambah" >
    <div class="modal-dialog modal-lg ">
        <form action="{{ route('transaksi_lain.store') }}" id="post_data" method="POST" >
            @csrf
            <div class="modal-content radiusSendiri">
                <div class="modal-header">
                    <h5 class="modal-title">Form Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                <div class="modal-body">

                    <div class="tab-content">
                        {{-- @if ($errors->any())
                            @foreach ($errors->all() as $error)
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ $error }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endforeach

                        @endif --}}

                        <input type="hidden" name="key" id="key"> {{--* dipakai buat simpen id_sewa --}}
                        <div class='row'>
                            <div class="col-lg-12">
                                <div class="row">
                        
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="tanggal_transaksi">Tanggal Transaksi<span style="color:red">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" autocomplete="off" name="tanggal_transaksi" class="form-control date @error('tanggal_transaksi') is-invalid @enderror" id="tanggal_transaksi" placeholder="dd-M-yyyy" value="{{old('tanggal_transaksi')}}">
                                            @error('tanggal_transaksi')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="tipe">Tipe Transaksi</label>
                                        <br>
                                        <div class="icheck-primary d-inline ">
                                            <input id="sudahNikah" type="radio" name="tipe_transaksi" value="pengeluaran" checked>
                                            <label class="form-check-label" for="sudahNikah">Pengeluaran</label>
                                        </div>
                                        <div class="icheck-primary d-inline ml-3">
                                            <input id="belumNikah" type="radio" name="tipe_transaksi" value="penerimaan" >
                                            <label class="form-check-label" for="belumNikah">Penerimaan</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="select_coa">Jenis Transaksi<span style="color:red">*</span></label>
                                        <select class="form-control select2 @error('select_coa') is-invalid @enderror" style="width: 100%;" id='select_coa' name="select_coa">
                                            <option value="">Pilih Jenis Transaksi</option>
                                            @foreach ($dataCOA as $data)
                                                <option value="{{$data->id}}" 
                                                    {{old('select_coa')==$data->id?'selected':''}} 
                                                    id_coa='{{$data->no_akun}}' 
                                                    nama_coa='{{$data->nama_jenis}}' 
                                                    tipe='{{$data->tipe}}'>
                                                    {{ $data->nama_jenis }} ( {{$data->tipe}} )
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="id_coa_hidden" id="id_coa_hidden">
                                        <input type="hidden" name="nama_coa_hidden" id="nama_coa_hidden">

                                        @error('select_coa')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror   
                                    </div>
                                
                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="select_bank">Kas/Bank<span style="color:red">*</span></label>
                                            <select class="form-control select2  @error('select_bank') is-invalid @enderror" style="width: 100%;" id='select_bank' name="select_bank">
                                            <option value="">Pilih Kas/Bank</option>
                                            @foreach ($dataKas as $data)
                                                <option value="{{$data->id}}" {{old('select_bank')==$data->id?'selected':''}} >{{ $data->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('select_bank')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror   
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                        <label for="total">Total Nominal<span style="color:red">*</span></label>
                                        <div class="input-group mb-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Rp</span>
                                            </div>
                                            <input type="text" name="total" class="form-control numaja uang @error('total') is-invalid @enderror" id="total" placeholder="" value="{{old('total')}}">
                                            @error('total')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>  
                                    
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                        <label for="keterangan_klaim">Catatan</label>
                                        <input type="text" class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" value="{{old('catatan')}}">
                                        @error('catatan')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>  
                                </div>
                            </div>

                        </div>
                        
                    </div>
                    
                </div>
                <div class="modal-footer">
                
                    <button type="button" class="btn btn-sm btn-danger radiusSendiri p-2" style='width:85px' data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success radiusSendiri p-2" id="" style='width:85px'><i class="fa fa-fw fa-save"></i> Simpan</button> 
                </div>
            </div>
        </form> 
    </div>
</div>
<script type="text/javascript">
$(document).ready(function () {
    // var table = $('#datatable').DataTable({
    //     "processing": true,
    //     "serverSide": true,
    //     "ajax": {
    //         "url": "{{ route('transaksi_lain.index_server') }}",
    //         "type": "GET",
    //         "data": function (d) {
    //             d.start = d.start || 0;
    //         },
            
    //     },
    //      "columns": [
    //             {data: 'tanggal', name: 'tgl_transaksi'},
    //             {data: 'jenis', name: 'jenis'},
    //             {data: 'kas_bank', name: 'kas_bank'},
    //             {data: 'total_nominal', name: 'total_nominal'},
    //             {data: 'catatan', name: 'catatan'},
    //             {
    //                 data: 'action', 
    //                 name: 'action', 
    //                 orderable: false, 
    //                 searchable: false
    //             },
    //         ],
    // });
    var table = $('#tabel_transaksi_lain').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        // lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]], // Rows per page options
        pageLength: 10, // Default number of rows per page
        // ajax: "{{ route('transaksi_lain.index_server') }}",
        ajax: {
            url: "{{ route('transaksi_lain.index_server') }}",
            type: "GET",
            // data: function (d) {
            //     d.start = d.start || 0;
            // },
        },
        columns: [
            {data: 'tanggal', name: 'tgl_transaksi'},
            {data: 'nama_jenis', name: 'jenis'},
            {data: 'nama_bank', name: 'kas_bank'},
            {data: 'nominal', name: 'total_nominal'},
            {data: 'catatan', name: 'catatan'},
            {data: 'action', name: 'action', orderable: false, searchable: true},
        ],
        order: [
            [0, 'asc'],
        ],
    });
    $('#tanggal_transaksi').datepicker({
        autoclose: true,
        format: "dd-M-yyyy",
        todayHighlight: true,
        language:'en',
        endDate: "0d",
    });
    filterCoa($('input[name="tipe_transaksi"]:checked').val());
    $(document).on('change','input[name="tipe_transaksi"]', function() {
        filterCoa($(this).val());
        
    });
    function filterCoa(selectedTipe) {
        var dataCOA =  <?php echo json_encode($dataCOA); ?>;
        var select_coa = $('#select_coa');
        //select dr db buat filter
        const filterCoa = dataCOA.filter(coa => {
            if (selectedTipe.toLowerCase() === 'penerimaan') {
                return coa.tipe.toLowerCase() === `penerimaan`;
            } else if (selectedTipe.toLowerCase() === 'pengeluaran') {
                return coa.tipe.toLowerCase() === `pengeluaran`;
            }
            return true; 
        });
            
        select_coa.empty();
        select_coa.append('<option value="">Pilih Jenis Transaksi</option>');
        filterCoa.forEach(coaValue => {
            const option = document.createElement('option');
            option.value = coaValue.id;
            option.setAttribute('id_coa', coaValue.no_akun);
            option.setAttribute('nama_coa', coaValue.nama_jenis);
            option.setAttribute('tipe', coaValue.tipe);
            option.textContent = coaValue.nama_jenis + ` (${coaValue.tipe})`;
            select_coa.append(option);
        });
        //di kosongin kalau ganti tipe
        $('#id_coa_hidden').val('');
        $('#nama_coa_hidden').val('');
    }
    var cekerror= <?php echo json_encode($errors->any()); ?>;
    
    if (cekerror) {
            $("#modal_tambah").modal("show");
    }
    $('body').on('change','#select_coa',function()
    {
        var selectedOption = $(this).find('option:selected');
        var id_coa_dari_select = selectedOption.attr('id_coa');
        var nama_coa_dari_select = selectedOption.attr('nama_coa');

        $('#id_coa_hidden').val(id_coa_dari_select);
        $('#nama_coa_hidden').val(nama_coa_dari_select);

    });
    // new DataTable('#TabelKlaim', {
    //     order: [
    //         [0, 'asc'],
    //     ],
    //     rowGroup: {
    //         dataSrc: [0]
    //     },
    //     columnDefs: [
    //         {
    //             targets: [0],
    //             visible: false
    //         },
    //         {
    //             "orderable": false,
    //             "targets": [0,1,2,3,4,5,6]
    //         }
    
    //     ],
    // }); 
    $('#post_data').submit(function(event) {
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
                        });

            if($("#tanggal_transaksi").val().trim()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `TANGGAL TRANSAKSI WAJIB DI ISI!`,
                })
                
                return;
            }
            if($("#select_coa").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `JENIS TRANSAKSI WAJIB DI ISI!`,
                })
                
                return;
            }
            if($("#select_bank").val()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `BANK WAJIB DI PILIH!`,
                })
                return;
            }
            
            if($("#total").val().trim()=='')
            {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `TOTAL NOMINAL WAJIB DI ISI!`,
                })
                return;
            }
            event.preventDefault();
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
                    }, 200); // 2000 milliseconds = 2 seconds
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


