@extends('layouts.home_master')

@section('content')

<div class="container-fluid">
    <form action="{{ route('permission.update', [$data->id]) }}" method="POST" id="post">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-lg-6">
                <div class="card radiusSendiri">
                    <div class="card-header">
                        <a href="{{ route('permission.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                        <button type="submit" name="save" id="save" value="save" class="btn ml-2 btn-success radiusSendiri"><strong><i class="fa fa-fw fa-save"></i> Simpan</strong></button>
                    </div>
                    <div class="card-body">
                        <div class="form-group col-md-12">
                            <label for="">Menu</label>
                            <input required type="text" id="menu" name="menu" class="form-control " value="{{ $data['menu'] }}" >                         
                        </div>
                        <div class="form-group col-md-12">
                            <label for="">Action</label>
                            <input required type="text" id="action" name="action" class="form-control " value="{{ $data['name'] }}" >                         
                        </div>
                        <div class="form-group col-md-12">
                            <label for="">Guard Name</label>
                            <input required type="text" id="guard_name" name="guard_name" class="form-control " value="{{ $data['guard_name'] }}" >                         
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#post').submit(function(event) {
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

        $('#menu, #action, #guard_name').keyup(function() {
            let inputValue = $(this).val();
            let outputValue = inputValue.replace(/\s+/g, '_');

            $(this).val(outputValue);
        });
    });
</script>
@endsection
