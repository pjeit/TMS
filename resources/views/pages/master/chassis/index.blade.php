
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Chassis</li>
    <li class="breadcrumb-item"><a href="{{route('chassis.index')}}">Chassis</a></li>
@endsection

@section('content')
<br>
<style>
   
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Chassis</h5>
                </div>
                <div class="card-body">
                   INI ADALAH HALAMAN CHASSIS XXX
                </div>
            </div>
        </div>
    </div>
</div>

<script>

</script>
@endsection
