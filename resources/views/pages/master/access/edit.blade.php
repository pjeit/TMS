@extends('layouts.home_master')

@section('content')

<div class="container-fluid">
    <form action="{{ route('access.update', [$role['id']]) }}" method="POST" id="post">
        @csrf @method('PUT')
        <div class="card radiusSendiri">
            <div class="card-header">
                <a href="{{ route('access.index') }}" class="btn btn-secondary radiusSendiri"><strong><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Kembali</strong></a>
                <button type="submit" name="save" id="save" value="save" class="btn ml-2 btn-success radiusSendiri"><strong><i class="fa fa-fw fa-save"></i> Simpan</strong></button>
            </div>
            <div class="card-body">
                <div class="form-group col-md-12">
                    <label for="">Role</label>
                    <input required type="text" id="role" name="role" class="form-control " value="{{ $role['name'] }}" readonly>                         
                </div>
                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    <div style="overflow: auto;">
                        <table class="table table-hover table-bordered table-striped " width='100%' >
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th class="d-flex align-items-center justify-content-center"><div><input type="checkbox" id="check_all"></div></th>
                                    <th colspan="4"><span class="ml-4">Akses</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissions as $item)
                                    <tr>
                                        <td><small>{{ $item->menu }}</small></td>
                                        <td class="d-flex align-items-center justify-content-center"> <div><input class="check per_menu parent_{{ $item->menu }}" parent_menu="{{ $item->menu }}" type="checkbox"></div> </td>
                                        @php
                                            $userRoleId = $role['id'];
                                        @endphp
                                        @foreach ($item->permissions($item->menu, $userRoleId) as $permission)
                                            <td>
                                                <div class="form-check">
                                                    <input class="check per_item {{ $item->menu }} mr-2" child_menu="{{ $item->menu }}" name="data[{{ $item->menu }}][]" value="{{ $permission->id }}" type="checkbox" {{ $permission->permission_id && $permission->role_id != null? 'checked':'' }} />
                                                    <label for="centang" class="form-check-label"><small>{{ $permission->name }}</small></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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

        $('#check_all').on('click', function(e) {
            $(".check").prop('checked', this.checked);
        });

        $('.per_menu').on('click', function(e) {
            let menu = this.getAttribute('parent_menu');

            $("."+menu).prop('checked', this.checked);
            if(this.checked == false && $("#check_all").prop('checked', true)){
                $("#check_all").prop('checked', false);
            }
        });

        $('.per_item').on('click', function(e) {
            let child_menu = this.getAttribute('child_menu');

            $(".parent_"+child_menu).prop('checked', false);
            $("#check_all").prop('checked', false);
        });

        // $('#tablee').dataTable( {
        //     "scrollX": true
        // } );
    });
</script>
@endsection
