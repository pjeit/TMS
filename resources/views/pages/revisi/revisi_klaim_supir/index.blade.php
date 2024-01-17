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
 var table = $('#TabelKlaim').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('klaim_supir_revisi.load_data_revisi_server') }}",
           
            columns: [
                {data: 'Supir', name: 'Supir'},
                {data: 'Jenis_Klaim', name: 'Jenis_Klaim'},
                {data: 'Tanggal_Klaim', name: 'Tanggal_Klaim'},
                {data: 'Jumlah_Klaim', name: 'Jumlah_Klaim'},
                {data: 'Jumlah_Dicairkan', name: 'Jumlah_Dicairkan'},
                {data: 'Status_Klaim', name: 'Status_Klaim'},
                {data: 'Keterangan', name: 'Keterangan'},
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false
                },
            ],
             order: [
                    [0, 'asc'],
                ],
            rowGroup: {
                dataSrc: ['Supir']//grouping per supir pake nama datanya, kalo bukan serverside nembak index
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
    //             "targets": [0,1,2,3,4,5,6,7]
    //         }
    
    //     ],
    // }); 
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


