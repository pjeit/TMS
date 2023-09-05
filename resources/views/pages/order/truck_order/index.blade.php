
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')

@endsection

@section('content')
<br>
<style>
   
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card radiusSendiri">
                <div class="card-header">
                    <a href="{{route('truck_order.create')}}" class="btn btn-primary btn-responsive float-left radiusSendiri">
                        <i class="fa fa-plus-circle" aria-hidden="true"> </i> Tambah Data
                    </a> 
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>Aksi</th>
                              <th>No. Polisi Kendaraan</th>
                              <th>No. Sewa</th>
                              <th>Tgl Berangkat</th>
                              <th>Tujuan</th>
                              <th>Driver</th>
                            </tr>
                          </thead>
                        <tbody>
                             <tr>
                                <td>                                    
                                    <div class="dropdown custom-dropdown">
                                        <a href="#" data-toggle="dropdown" class="btn btn-default btn-sm dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                                            {{-- <span class="fa fa-bolt "></span> --}}
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a href="" class="dropdown-item">
                                                <span class="fas fa-edit mr-3"></span> Edit
                                            </a>
                                            <a href="" method="get" rel="noopener" target="_blank"  class="dropdown-item">
                                                <span class="fas fa-print mr-3"></span> Photoshop PDF
                                            </a>
                                            <a href="" class="dropdown-item" data-confirm-delete="true">
                                                <span class="fas fa-trash mr-3"></span> Delete
                                            </a>
                                            
                                        </div>
                                    </div>
                                </td>
                                <td>L 9990 KK</td>
                                <td>2023/CUST/VII/004</td>
                                <td>21-Jul-2023</td>
                                <td>** PT. Multi Bintang - Bir Bintang 20</td>
                                <td>SUPIR GENSA (081123123123)</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
