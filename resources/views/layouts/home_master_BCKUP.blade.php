<!DOCTYPE html>
<html lang="en">
<head>
  @include('components.meta')

  @include('components.style') 


</head>

<body class="hold-transition sidebar-mini layout-fixed">  {{--  --}}
  <div class="wrapper">

    <!-- Preloader -->
    {{-- <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake img-circle" src="{{asset('img/pje.jpg')}}" alt="AdminLTELogo" height="60" width="60">
    </div> --}}

    <!-- Navbar -->
    @include('layouts.navbar')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @include('layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
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
  <!-- ./wrapper -->


  @include('components.js')

</body>

</html>