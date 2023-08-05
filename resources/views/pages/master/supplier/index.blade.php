
@extends('layouts.home_master')

@if(session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        {{ session()->get('message') }}
    </div>
@endif

@section('pathjudul')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item">Supplier</li>
    <li class="breadcrumb-item"><a href="{{route('supplier.index')}}">Supplier</a></li>
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
                    <h5 class="card-title">Supplier</h5>
                </div>
                <div class="card-body">
                    halaman supplier
                </div>
            </div>
        </div>
    </div>
</div>

<script>

</script>
@endsection
