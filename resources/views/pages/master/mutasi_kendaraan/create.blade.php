
@extends('layouts.home_master')
@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif
@section('pathjudul')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item">Master</li>
<li class="breadcrumb-item"><a href="{{route('mutasi_kendaraan.index')}}">Customer</a></li>
<li class="breadcrumb-item">Create</li>

@endsection

@section('content')
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
    <form action="{{ route('mutasi_kendaraan.store') }}" method="POST" id='post' >
    @csrf
  
    <div class="row">
        <div class="col-12">
            <div class="radiusSendiri sticky-top " style="margin-bottom: -15px;">
                <div class="card radiusSendiri" style="">
                    <div class="p-3">
                        <a href="{{ route('mutasi_kendaraan.index') }}"class="btn btn-secondary radiusSendiri"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</a>
                        <button type="submit" id="submitButton" class="btn btn-success radiusSendiri ml-2"><i class="fa fa-fw fa-save"></i> Simpan</button>
                    </div>
                </div>
            </div>
            <div class="card radiusSendiri">
                <div class="card-body">
                    <div class='row'>
                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label for="">Cabang Asal</label>
                            <select class="form-control select2" style="width: 100%;" id='cabang_asal' name="cabang_asal" required>
                                <option value="">-- Pilih Cabang --</option>
                                @foreach ($cabang as $city)
                                    <option value="{{$city->id}}">{{ $city->nama }}</option>
                                @endforeach
                            </select>
                        </div>  
                        <div class="form-group col-sm-12 col-md-3 col-lg-3">
                            <label for="">Cabang Tujuan</label>
                            <select class="form-control select2" style="width: 100%;" id='cabang_tujuan' name="cabang_tujuan" required>
                                <option value="">-- Pilih Cabang --</option>
                                @foreach ($cabang as $city)
                                    <option value="{{$city->id}}">{{ $city->nama }}</option>
                                @endforeach
                            </select>
                        </div>  
                        <div class="form-group col-sm-12 col-md-6 col-lg-6">
                            <label for="">Catatan</label>
                            <textarea class="form-control" name="catatan" rows="1"></textarea>
                          </div>
                    </div>
                    <div class="row">
                        <table class="table table-bordered table-hover" id="tbl">
                            <thead>
                                <tr>
                                    <th scope="col" width='50'>#</th>
                                    <th scope="col" width='180' class="text-center"><input class="kendaraan_all" type="checkbox" id="kendaraan_all"> </th>
                                    <th scope="col" width='100'>Jenis</th>
                                    <th scope="col" width='150'>Nopol</th>
                                    <th scope="col">Chasis</th>
                                    <th scope="col" width='180' class="text-center"><input class="chassis_all" type="checkbox" id="chassis_all"> </th>
                                </tr>
                            </thead>
                            <tbody id='hasil'>
                                {{-- @isset($dataKendaraan)
                                    @foreach ($dataKendaraan as $key => $item)
                                        <tr>
                                            <th scope="row">{{$key+1}}</th>
                                            <td>
                                                <div class="form-check text-center">
                                                    <input class="form-check-input kendaraan_{{$key}} kendaraan" type="checkbox" value="" id="kendaraan_{{$key}}">
                                                </div>
                                            </td>
                                            <td>{{$item->kategori}}</td>
                                            <td>{{$item->no_polisi}}</td>
                                            <td>{{$item->kode}} - {{$item->karoseri}}</td>
                                            <td>
                                                @isset($item->chassis_id)
                                                    <div class="form-check text-center">
                                                        <input class="form-check-input chassis_{{$key}} chassis" type="checkbox" value="" id="chassis_{{$key}}">
                                                    </div>
                                                @endisset
                                            </td>
                                        </tr>
                                    @endforeach
                                @endisset --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

       
    </div>

    </form>

</div>
{{-- sweet save --}}
<script>
    $(document).ready(function() {
        $('#post').submit(function(event) {
            if($('#cabang_asal').val() == $('#cabang_tujuan').val()){
                Swal.fire(
                    'Terjadi kesalahan',
                    'Cabang asal dan cabang tujuan tidak boleh sama',
                    'warning'
                )
                event.preventDefault();
                return false;
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
                    }, 1000); // 2000 milliseconds = 2 seconds
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
<script>
    $( document ).ready(function() {
        // $(".kendaraan, .chassis, .kendaraan_all, .chassis_all").prop('checked', true);

        $(document).on('change', '#cabang_asal', function (event) {
            // $('#kendaraan_all').prop('checked', true);
            // $('#chassis_all').prop('checked', true);

            // $("#loading-spinner").show();
            var id = this.value;
            showTable(id);
            $("#hasil").empty(); // Assuming that "#tbd" is the ID of your tbody element

            function showTable(id){
                $.ajax({
                    method: 'GET',
                    url: `{{ url('mutasi_kendaraan/get_data')}}/${id}`,
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function(response) {
                        // $("#loading-spinner").hide();
                        var data = response.dataKendaraan;
                        var nomor = 0;
                        $("#tbd").html(" ");

                        for (var i = 0; i < data.length; i++) {
                            var j=i+1;
                            var nomor = i+1;
                            var row = $("<tr scope='row'></tr>");
                            row.append(`
                                    <th scope="row">${j}</th>
                                    <td>
                                        <div class="form-check text-center">
                                            <input class="form-check-input kendaraan_${i} kendaraan" name="data[${i}][centang_kendaraan]" type="checkbox"  id="kendaraan_${i}">
                                            <input type="hidden" name="data[${i}][kendaraan]" value="${data[i].id}">
                                        </div>
                                    </td>
                                    <td>${data[i].kategori}</td>
                                    <td>${data[i].no_polisi}</td>
                                    <td>${data[i].kode ?? ''} - ${data[i].karoseri ?? ''}</td>
                                    <td>
                                        ${data[i].chassis_id != null ? `
                                            <div class="form-check text-center">
                                                <input class="form-check-input chassis_${i} chassis" name="data[${i}][centang_chassis]" type="checkbox"  id="chassis_${i}">
                                                <input type="hidden" name="data[${i}][chassis]" value="${data[i].chassis_id}">
                                            </div>
                                        ` : ''}
                                    </td>
                            `); 
                            
                            $("#hasil").append(row);
                        }
                        // chassis 
                        var dc = response.dataChassis;
                        $("#tbd").html(" ");
                            for (var i = 0; i < dc.length; i++) {
                            var j=i+1;
                            var nomor = nomor+1;

                            var row = $("<tr scope='row'></tr>");
                            row.append(`
                                    <th scope="row">${nomor}</th>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>${dc[i].kode ?? ''} - ${dc[i].karoseri ?? ''}</td>
                                    <td>
                                        ${dc[i].id != null ? `
                                            <div class="form-check text-center">
                                                <input class="form-check-input chassis_${nomor-1} chassis" name="data[${nomor-1}][centang_chassis]" type="checkbox"  id="chassis_${nomor-1}">
                                                <input type="hidden" name="data[${nomor-1}][chassis]" value="${dc[i].id}">
                                            </div>
                                        ` : ''}
                                    </td>                            `); 
                            
                            $("#hasil").append(row);
                        }
                    },error: function (xhr, status, error) {
                            $("#loading-spinner").hide();
                        if ( xhr.responseJSON.result == 'error') {
                            console.log("Error:", xhr.responseJSON.message);
                            console.log("XHR status:", status);
                            console.log("Error:", error);
                            console.log("Response:", xhr.responseJSON);
                        } else {
                            toastr.error("Terjadi kesalahan saat menerima data. " + error);
                        }
                    }
                });
            }
            
        });
    });

    $("#kendaraan_all").click(function () {
        $(".kendaraan").prop('checked', $(this).prop('checked'));
    });
    $("#chassis_all").click(function () {
        $(".chassis").prop('checked', $(this).prop('checked'));
    });

    $(document).on('click', '.kendaraan', function (event) {
        $("#kendaraan_all").prop('checked', false);
    });
    $(document).on('click', '.chassis', function (event) {
        $("#chassis_all").prop('checked', false);
    });
</script>
@endsection
