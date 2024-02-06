<link rel="icon" href="{{asset('img/LOGO_PJE_CLEAR.jpg')}}" type="image/gif">
  <link href='https://fonts.googleapis.com/css?family=Rubik' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Nunito' rel='stylesheet'>
<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{asset('assets/plugins/fontawesome-free/css/all.min.css')}}">
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet" href="{{asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
<!-- iCheck -->
<link rel="stylesheet" href="{{asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
<!-- JQVMap -->
<link rel="stylesheet" href="{{asset('assets/plugins/jqvmap/jqvmap.min.css')}}">
<!-- Theme style -->
<link rel="stylesheet" href="{{asset('assets/dist/css/adminlte.min.css')}}">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="{{asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">

<!-- summernote -->
<link rel="stylesheet" href="{{asset('assets/plugins/summernote/summernote-bs4.min.css')}}">

<link rel="stylesheet" href="{{asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<!-- Bootstrap4 Duallistbox -->
<link rel="stylesheet" href="{{asset('assets/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')}}">
<!-- BS Stepper -->
<link rel="stylesheet" href="{{asset('assets/plugins/bs-stepper/css/bs-stepper.min.css')}}">
<!-- dropzonejs -->
<link rel="stylesheet" href="{{asset('assets/plugins/dropzone/min/dropzone.min.css')}}">
<!-- Theme style-->
<link rel="stylesheet" href="{{asset('assets/dist/css/adminlte.min.css')}}">
<!-- Toastr  -->
<link rel="stylesheet" href="{{asset('assets/plugins/toastr/toastr.min.css')}}">
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<!-- ion range slider-->
<link rel="stylesheet" href="{{asset('assets/plugins/ion-rangeslider/css/ion.rangeSlider.css')}}">
<link rel="stylesheet" href="{{asset('assets/plugins/ion-rangeslider/css/ion.rangeSlider.min.css')}}">

<!-- script comboboc-->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

{{-- datepicker ambil yang lama --}}
<link rel="stylesheet" href="{{asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')}}">

{{-- multi row grouping datatable --}}
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.4.0/css/rowGroup.dataTables.min.css">

<style>
 
    .radiusSendiri{
      border-radius: 15px;
    }
    .dropdown-menu{
      /* transition: transform 0.1s; Add a transition to the 'transform' property */
      border-radius: 15px;

    }
    /* .btn
    {
      transition: transform 0.5s ease; 
    }
    .btn:hover{
      transform: scale(1.05);
    } */
/* 
    .form-control
    {
      transition: transform 0.5s ease; 
    }
    .form-control:hover{
      transform: scale(1.05);
    } */
    .rubik-heading-1 {
  font-family: "Rubik" !important;
  font-style: normal !important;
  font-weight: 300 !important;
  font-size: 36px !important;
  line-height: 43px !important;
}
.customer-list {
        list-style-type: square; /* Apply a different list style to the customer names */
        margin-left: 20px; /* Adjust margin for better indentation */
    }
body{
  font-family: "Rubik" !important;
  /* font-style: normal !important; */
  font-weight: 300 !important;
  /* font-size: 15px !important; */
  /* line-height: 24px !important; */
}
/* .selectpicker {
  font-family: "Rubik" !important;
  font-style: normal !important;
  font-weight: 300 !important;
  font-size: 15px !important;
  line-height: 24px !important;
} */
.select2 {
  font-family: "Rubik" !important;
  font-style: normal !important;
  font-weight: 300 !important;
  font-size: 15px !important;
  line-height: 24px !important;
}
    .btn-rounded{
      border-radius: 10rem;
    }
    .table_wrapper{
       display: block;
       overflow-x: auto;
       white-space: nowrap;
   }


   .new-logo{
    background: url({{ asset('img/LOGO_PJE_WARNA.jpg') }}) no-repeat;
    height: 56px; /* Set the height to cover the entire anchor */
    background-size: 100%;
    top: -200px;
  }
  /* .btn-primary {
      background:  #0071BD !important;
      transition: background 0.3s ease; 
  }

  .btn-primary:hover {
    background:  #00BFFF !important;

  } */
.hover-item:hover{
  transform: scale(1.1);
}
  .sidebar-collapse .new-logo {
    background: url({{ asset('img/LOGO_MINI.jpg') }}) no-repeat;
    background-color: #fff;
    background-size: 100%;
    height: 56px; 
  }

  .navbar-customsid{
    background: rgb(2,0,36);
    background: linear-gradient(180deg, rgba(2,0,36,1) 0%, rgba(0,113,189,1) 35%, rgba(31,136,206,1) 51%, rgba(0,113,189,1) 66%, rgba(2,0,36,1) 100%);
  }
.nav-link.nav-link-tab.active{
        background-color: rgb(60, 177, 255);
        color: white; /* Set text color if needed */
    }
  .bg-logistik{
    background: linear-gradient(
          rgba(0, 0, 0, 0.5), 
          rgba(0, 0, 0, 0.5)
        ), url({{ asset('img/logistik.jpg') }});
    background-size: 100%;
  }

  [type=checkbox] {
  width: 1rem;
  height: 1rem;
  color: dodgerblue;
  vertical-align: middle;
  -webkit-appearance: none;
  background: none;
  border: 0;
  outline: 0;
  flex-grow: 0;
  background-color: #FFFFFF;
  transition: background 300ms;
  cursor: pointer;
}

/* Pseudo element for check styling */
[type=checkbox]::before {
  content: "";
  color: transparent;
  display: block;
  width: inherit;
  height: inherit;
  border-radius: inherit;
  border: 0;
  background-color: transparent;
  background-size: contain;
  box-shadow: inset 0 0 0 1px #CCD3D8;
}


/* Checked */

[type=checkbox]:checked {
  background-color: currentcolor;
}

[type=checkbox]:checked::before {
  box-shadow: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E %3Cpath d='M15.88 8.29L10 14.17l-1.88-1.88a.996.996 0 1 0-1.41 1.41l2.59 2.59c.39.39 1.02.39 1.41 0L17.3 9.7a.996.996 0 0 0 0-1.41c-.39-.39-1.03-.39-1.42 0z' fill='%23fff'/%3E %3C/svg%3E");
}


/* Disabled */

[type=checkbox]:disabled {
  background-color: #CCD3D8;
  opacity: 0.84;
  cursor: not-allowed;
}


/* IE */

[type=checkbox]::-ms-check {
  content: "";
  color: transparent;
  display: block;
  width: inherit;
  height: inherit;
  border-radius: inherit;
  border: 0;
  background-color: transparent;
  background-size: contain;
  box-shadow: inset 0 0 0 1px #CCD3D8;
}

[type=checkbox]:checked::-ms-check {
  box-shadow: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E %3Cpath d='M15.88 8.29L10 14.17l-1.88-1.88a.996.996 0 1 0-1.41 1.41l2.59 2.59c.39.39 1.02.39 1.41 0L17.3 9.7a.996.996 0 0 0 0-1.41c-.39-.39-1.03-.39-1.42 0z' fill='%23fff'/%3E %3C/svg%3E");
}
    /* styling dropdown */
    /* .content {
      padding: 7rem 0; }

    h2 {
      font-size: 20px; }

    .custom-dropdown .btn:active, .custom-dropdown .btn:focus {
      -webkit-box-shadow: none !important;
      box-shadow: none !important;
      outline: none; }

    .custom-dropdown .btn.btn-custom {
      border: 1px solid #efefef; }

    .custom-dropdown .dropdown-link {
      color: #888;
      font-size: 15px;
      display: inline-block;
      padding: 8px 15px;
      background: #f8f9fa;
      position: relative; }
      .custom-dropdown .dropdown-link:after {
        content: ""; }

    .custom-dropdown .dropdown-item {
      font-size: 14px;
      color: #888;
      border-bottom: 1px solid #efefef;
      padding-top: 10px;
      padding-left: 15px;
      padding-bottom: 10px;
      position: relative; }
      .custom-dropdown .dropdown-item:before {
        content: "";
        position: absolute;
        width: 0px;
        height: 100%;
        left: 0;
        bottom: 0;
        top: 0;
        opacity: 0;
        visibility: hidden;
        z-index: 2;
        background: #007bff;
        -webkit-transition: .3s all ease;
        -o-transition: .3s all ease;
        transition: .3s all ease; }
      .custom-dropdown .dropdown-item:last-child {
        border-bottom: none; }
      .custom-dropdown .dropdown-item:hover {
        color: #000;
        padding-left: 20px; }
      .custom-dropdown .dropdown-item:hover:before {
          opacity: 1;
          visibility: visible;
          width: 6px; }

    .custom-dropdown .dropdown-menu {
      border: 1px solid transparent;
      -webkit-box-shadow: 0 15px 30px 0 rgba(0, 0, 0, 0.2);
      box-shadow: 0 15px 30px 0 rgba(0, 0, 0, 0.2);
      margin-top: 0px !important;
      padding-top: 0;
      padding-bottom: 0;
      opacity: 0;
      left: 0 !important;
      -webkit-transition: .3s margin-top ease, .3s opacity ease, .3s visibility ease;
      -o-transition: .3s margin-top ease, .3s opacity ease, .3s visibility ease;
      transition: .3s margin-top ease, .3s opacity ease, .3s visibility ease;
      visibility: hidden; }
      .custom-dropdown .dropdown-menu.active {
        opacity: 1;
        visibility: visible;
        margin-top: 20px !important; }

    .custom-dropdown.show .dropdown-link {
      color: #fff;
      background: #007bff; } */
  /* end of it */
</style>

{{-- custom css bootstrap dropdown --}}
<style>
    
</style>

@section('css')
@show