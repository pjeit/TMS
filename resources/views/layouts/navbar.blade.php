<nav class="main-header navbar navbar-expand navbar-white navbar-light bg-primary ">

  <!-- Left navbar links -->
  <ul class="navbar-nav ">
    <li class="nav-item">
      <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a  class="nav-link text-white">{{strtoupper($judul) }}</a>
    </li>
    <!--<li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>-->
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
   
    <li class="nav-item dropdown user-menu">
      <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
        <img src="{{ asset('img/user-1.png') }}" class="user-image img-circle elevation-2 bg-white" alt="User Image">
        {{-- <span class="d-none d-md-inline text-white">Alexander Pierce</span> --}}
      </a>
      @php
          $username = Auth::user()->username;
          $role = Auth::user()->getRole();
      @endphp
      <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <!-- User image -->
        <li class="user-header bg-primary">
          <img src="{{ asset('img/user-1.png') }}" class="img-circle elevation-2 bg-white" alt="User Image">
          <p>
            {{$username}} - {{$role}}
            {{-- <small>Member since Nov. 2012</small> --}}
          </p>
        </li>
        <!-- Menu Body -->
      
        <!-- Menu Footer-->
        <li class="user-footer">
          {{-- <a href="#" class="btn btn-default btn-flat">Profile</a> --}}
          <a href="{{route('signout')}}" class="btn btn-default btn-flat float-right border">Sign out</a>
        </li>
      </ul>
    </li>

    {{-- <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li> --}}

    {{--   
      <li class="nav-item">
        <a class="btn btn-block btn-outline-danger btn-sm" href=""
          onClick="event.preventDefault(); document.getElementById('logout-form').submit();">
          Logout
        </a>
        <form id="logout-form" action="" method="POST" class="d-none">
          @csrf
        </form>
      </li> 
    --}}
  </ul>
</nav>