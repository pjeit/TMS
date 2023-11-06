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
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">

                    {{-- <button class="btn btn-primary btn-responsive float-left radiusSendiri bukakModalCreate">
                        <i class="fa fa-plus-circle"> </i> Tambah Data

                    </button> --}}
                    {{-- <a href="{{route('karyawan.create')}}" >
                    </a>  --}}
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="TabelKlaim" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>Supir</th>
                                <th>Jenis Klaim</th>
                                <th>Tanggal Klaim</th>
                                <th>Jumlah Klaim</th>
                                <th>Jumlah Dicairkan</th>
                                <th>Status Klaim</th>
                                <th>Keterangan</th>
                                <th></th>
                            </tr>
                          </thead>
                        <tbody>
                            @if (isset($dataKlaimSupir))
                                @foreach ($dataKlaimSupir as $item)
                                <tr>
                                    <td>{{ $item->nama_supir}} ({{ $item->telp}})</td>
                                    <td>{{ $item->jenis_klaim}}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_klaim)->format('d-M-Y') }}</td>
                                    <td>Rp. {{number_format($item->total_klaim,2)  }}
                                        {{-- <br>
                                        @if($item->status_klaim == 'ACCEPTED')
                                            <b>Total dicairkan : Rp. {{number_format($item->total_pencairan,2) }}</b> 
                                        @endif --}}

                                    </td>
                                     <td>
                                        @if($item->status_klaim == 'ACCEPTED')
                                            Rp. {{ number_format($item->total_pencairan,2) }}
                                        @else
                                         -
                                        @endif 
                                    </td>
                                    <td>
                                        @if ($item->status_klaim == 'PENDING')
                                         <span class="badge badge-warning">
                                            MENUNGGU PERSETUJUAN
                                            <i class="fas fa-solid fa-clock"></i>
                                        </span>
                                        @elseif($item->status_klaim == 'ACCEPTED')
                                         <span class="badge badge-success">
                                            DITERIMA
                                             <i class="fas fa-regular fa-thumbs-up"></i>
                                         </span>
                                        @else
                                         <span class="badge badge-danger">
                                            DITOLAK
                                             <i class="fas fa-regular fa-thumbs-down"></i>
                                        
                                        </span>
                                            
                                        @endif

                                    </td>
                                   

                                    <td>{{ $item->keterangan_klaim }}</td>
                                    <td>
                                        <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-rounded btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                               
                                                
                                                {{-- @if ($item->status_klaim == 'PENDING')
                                                    <a href="{{route('klaim_supir.edit',[$item->id_klaim])}}" class="dropdown-item ">
                                                        <span class="fas fa-edit mr-3"></span> Edit
                                                    </a>
                                                    <a href="{{ route('klaim_supir.destroy', [$item->id_klaim]) }}" class="dropdown-item" data-confirm-delete="true">
                                                        <span class="fas fa-trash mr-3"></span> Hapus
                                                    </a>
                                                    <a href="{{route('pencairan_klaim_supir.edit',[$item->id_klaim])}}" class="dropdown-item ">
                                                        <span class="nav-icon fas fa-dollar-sign mr-3"></span> Pencairan
                                                    </a>
                                                @else --}}
                                                    <a href="{{route('pencairan_klaim_supir_revisi.edit',[$item->id_klaim])}}" class="dropdown-item ">
                                                        <span class="nav-icon fas fa-dollar-sign mr-3"></span> Edit Pencairan
                                                    </a>
                                                {{-- @endif --}}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                           
                        </tbody>
                        
                    </table>
                  

                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>

<script type="text/javascript">
$(document).ready(function () {

    $('#tanggal_klaim').datepicker({
            autoclose: true,
            format: "dd-M-yyyy",
            todayHighlight: true,
            language:'en',
            // endDate: "0d"
        });
    function readURLNota(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#preview_foto_nota').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }
    function readURLBarang(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#preview_foto_barang').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }
    $("#foto_barang").change(function() {
        readURLBarang(this);
    });
    $("#foto_nota").change(function() {
        readURLNota(this);
    });
    $(".bukakModalCreate").click(function () {
            $("#modal").modal("show");
        });
    var cekerror= <?php echo json_encode($errors->any()); ?>;
    
    if (cekerror) {
            $("#modal").modal("show");
        
    }
    let isScaled1 = false; // Track the current state
    let isScaled2 = false; // Track the current state
    // Define a variable to track the current scale factor
    $('body').on('click','#preview_foto_barang',function()
    {
            if (isScaled1) {
                // If the element is already scaled, reset it to normal size
                $(this).css('transform', 'scale(1)');
                 $('#div_foto_barang').show();

            } else {
                // If the element is not scaled, apply the scaling effect
                $(this).css('transform', 'scale(3.5)');
                 $('#div_foto_barang').hide();

            }

            // Toggle the state
            isScaled1 = !isScaled1;

    });
     $('body').on('click','#preview_foto_nota',function()
    {
         if (isScaled2) {
                // If the element is already scaled, reset it to normal size
                $(this).css('transform', 'scale(1)');
                $('#div_foto_nota').show();

            } else {
                // If the element is not scaled, apply the scaling effect
                $(this).css('transform', 'scale(3.5)');
                 $('#div_foto_nota').hide();

            }

            // Toggle the state
            isScaled2 = !isScaled2;

    });
    $('body').on('change','#select_kendaraan',function()
    {
        var idKendaraan = $(this).val();
        var selectedOption = $(this).find('option:selected');
        var idChassis = selectedOption.attr('idChassis');
        var nopol = selectedOption.attr('noPol');
        var supir = selectedOption.attr('idDriver');
        
        $('#kendaraan_id').val(idKendaraan);
        $('#no_polisi').val(nopol);
        
        $('#select_chassis').val(idChassis).trigger('change');
        $('#select_driver').val(supir).trigger('change');

    });
     $('body').on('change','#select_driver',function()
		{
            var selectedOption = $(this).find('option:selected');
            var nama_driver = selectedOption.attr('nama_driver');
            
            $('#driver_nama').val(nama_driver);

		});
    new DataTable('#TabelKlaim', {
        order: [
            [0, 'asc'],
        ],
        rowGroup: {
            dataSrc: [0]
        },
        columnDefs: [
            {
                targets: [0],
                visible: false
            },
            {
                "orderable": false,
                "targets": [0,1,2,3,4,5,6,7]
            }
    
        ],
    }); 
    $('#post_data').submit(function(event) {
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


