
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')

@endsection

@section('content')
<style>
   
</style>
<div class="container-fluid">
    <div class="card radiusSendiri">
        {{-- <div class="row"> --}}
        <form method="POST" action="{{ route('persetujuan_uang_jalan.store') }}" id="post_data">

            <div class="card-header ">
                <button type="submit" id="save" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
            </div>
            
            <div class="card-body">
                    <table id="tabel" class="table table-bordered table-striped" style="overflow-x: auto;">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>No. Sewa</th>
                                <th>No. Polisi</th>
                                <th>Tanggal Berangkat</th>
                                <th>Tujuan</th>
                                <th>Keterangan Pencairan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @if (isset($sewa))
                            @foreach($sewa  as $key => $item)
                                <tr id="{{$key}}">
                                    <th>{{ $item->getCustomer->nama }}</th>
                                    <td>{{ $item->no_sewa }}</td>
                                    <td>
                                        {{ $item->no_polisi}} [{{ $item->getKaryawan->nama_panggilan }} ({{ trim($item->getKaryawan->telp1) }})]
                                    </td>
                                    <td>{{ date('d-M-Y', strtotime($item->tanggal_berangkat)) }}</td>
                                    <td>{{ $item->nama_tujuan }}</td>
                                    <td>
                                        <span class="badge badge-primary" style="font-size: larger;">
                                            {{$item->getUJRiwayat->get_kas_uj->nama}}
                                        </span>
                                        <br><br>
                                        Total uang Jalan = {{number_format( $item->getUJRiwayat->total_uang_jalan)}} <br>
                                        Total teluk lamong = {{number_format($item->getUJRiwayat->total_tl)}}<br>
                                        Total potong hutang = {{number_format($item->getUJRiwayat->potong_hutang)}}<br><br>
                                        Total transfer = <span class="badge badge-success" style="font-size: larger;">{{number_format(($item->getUJRiwayat->total_uang_jalan+$item->getUJRiwayat->total_tl) - $item->getUJRiwayat->potong_hutang)}}
                                        </span>
                                    </td>
                                    <td>
                                        {{-- <form method="POST" action="{{ route('persetujuan_uang_jalan.store') }}"> --}}
                                            <input type="hidden" name="data[{{$key}}][id_uj]" value="{{$item->getUJRiwayat->id}}">
                                            <input type="hidden" name="data[{{$key}}][id_sewa]" value="{{$item->id_sewa}}">
                                            <input type="hidden" name="data[{{$key}}][id_karyawan]" value="{{$item->id_karyawan}}">
                                            <input type="hidden" name="data[{{$key}}][no_sewa]" value="{{$item->no_sewa}}">
                                            <input type="hidden" name="data[{{$key}}][kendaraan]" value="{{$item->no_polisi}}">
                                            <input type="hidden" name="data[{{$key}}][driver]" value="{{$item->getKaryawan->nama_panggilan}}">
                                            <input type="hidden" name="data[{{$key}}][customer]" value="{{$item->getCustomer->nama}}">
                                            <input type="hidden" name="data[{{$key}}][tujuan]" value="{{$item->nama_tujuan}}">
                                            {{-- <input type="hidden" name="data[][uang_jalan]" value="{{$item->getUJRiwayat->total_uang_jalan}}">
                                            <input type="hidden" name="data[][teluk_lamong]" value="{{$item->getUJRiwayat->total_tl}}">
                                            <input type="hidden" name="data[][potong_hutang]" value="{{$item->getUJRiwayat->potong_hutang}}">
                                            <input type="hidden" name="data[][total_diterima]" value="{{($item->getUJRiwayat->total_uang_jalan + $item->getUJRiwayat->total_tl)-$item->getUJRiwayat->potong_hutang}}"> --}}

                                            @csrf
                                            <div class="icheck-primary d-inline ml-3">
                                                <input id="cairkan_{{$key}}" type="radio" name="data[{{$key}}][is_acc]" value="Y" checked class="radio_acc">
                                                <label class="form-check-label_{{$key}}" for="cairkan_{{$key}}">Terima</label>
                                            </div>
                                            <div class="icheck-danger d-inline ml-3 ">
                                                <input id="tolak_{{$key}}" type="radio" name="data[{{$key}}][is_acc]" value="N" class="radio_tolak">
                                                <label class="form-check-label_{{$key}}" for="tolak_{{$key}}">Tolak</label>
                                                <br>
                                                <br>
                                                <div class="form-group">
                                                    <textarea style="display: none;"  name="data[{{$key}}][alasan_tolak]" id="alasan_tolak_{{$key}}" class="form-control" id="catatan" name="data[{{$key}}][alasan_tolak]" rows="8" value=""></textarea>
                                                </div>  
                                            </div>
                                        {{-- </form> --}}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
            </div>
        {{-- </div> --}}
        </form>

    </div>
</div>
<script>
    $(document).ready(function () {
        $(document).on('click', '.radio_tolak', function () {
            var isChecked = $(this).prop("checked");
            var id = $(this).attr("id");
            var remove_string_id = id.replace('tolak_','');
            console.log(remove_string_id);
            $('#alasan_tolak_'+remove_string_id).show();
          
        });
        $(document).on('click', '.radio_acc', function () {
            var isChecked = $(this).prop("checked");
            var id = $(this).attr("id");
            var remove_string_id = id.replace('cairkan_','');
            console.log(remove_string_id);
            $('#alasan_tolak_'+remove_string_id).hide();
        });
        $('#tabel').DataTable( {
            paging:false,
            responsive:true,
            order: [
                [0, 'asc'], // 0 = grup
            ],
            rowGroup: {
                dataSrc: [0] // di order grup dulu, baru customer
            },
            columnDefs: [
                {
                    targets: [0], // ini nge hide kolom grup, harusnya sama customer, tp somehow customer tetep muncul
                    visible: false
                },
                // {
                //     targets: [6, 7],
                //     orderable: false, // matiin sortir kolom centang
                // },
            ],
        });
        // let barisTabel = $("#tabel > tbody tr>td");
        //     console.log(barisTabel[0].className=='dataTables_empty');
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
                    })
            let barisTabel = $("#tabel > tbody tr > td");
            console.log(barisTabel);
            // console.log(barisTabel.length + 'baris tabel');
            if (barisTabel[0].className=='dataTables_empty') {
                event.preventDefault(); 
                Toast.fire({
                    icon: 'error',
                    text: `Tidak ada data pencairan!`,
                })
                return;
            }

            let radioChecked = $('.radio_tolak:checked');
            let cek_alasan = true;
            radioChecked.each(function() {
                let key = $(this).attr('id').split('_')[1];
                let catatan = $('#alasan_tolak_' + key).val();
                if (catatan.trim() === '') {
                    cek_alasan = false;
                    return false; 
                }
            });

            if (!cek_alasan) {
                event.preventDefault();
                Toast.fire({
                    icon: 'error',
                    text: 'Mohon isi catatan untuk opsi Tolak yang dipilih',
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