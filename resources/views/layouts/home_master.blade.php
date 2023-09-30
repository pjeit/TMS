<!DOCTYPE html>
<html lang="en">

<head>
  @include('components.meta')
  @include('components.style')

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
</head>


<style>
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
<body class="hold-transition sidebar-mini layout-fixed">

  <div class="wrapper">

    @include('layouts.navbar')

    <!-- Main Sidebar Container -->
    @include('layouts.sidebar')
    <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <br>
      
        <!-- Main content -->
        <section class="content">

          @if(session("message"))
          <div class="alert alert-warning">
            {{session('message')}}
          </div>
          @endif

          @if(session("status"))

          {{-- sweetalert --}}
          @include('sweetalert::alert')

            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{session('status')}}

              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

          @endif

          @yield('content')
          @include('sweetalert::alert')
          
        </section>
        <!-- /.content -->
      </div>
    <!-- /.content-wrapper -->
    @include('layouts.footer')

    <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
      </aside>
    <!-- /.control-sidebar -->
  </div>

@include('components.js')

  


</body>

<script>
 
</script>

</html>