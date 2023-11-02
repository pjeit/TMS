<nav class="main-header navbar navbar-expand navbar-white navbar-light " style="background: linear-gradient(to bottom, #0071BD, #00BFFF);">

  <!-- Left navbar links -->
  <ul class="navbar-nav ">
    <li class="nav-item">
      <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a  class="nav-link brand-text text-white text-bold font-italic"><b>{{strtoupper($judul) }}</b></a>
    {{-- <span class="brand-text mx-2 text-bold" ><b>{{strtoupper($judul) }}</b></span> --}}

    </li>
    <!--<li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>-->
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
   
    <li class="nav-item dropdown user-menu">
      <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
        <img src="{{ asset('img/user-1.png') }}" class="user-image img-circle elevation-3 bg-white" alt="User Image">
        {{-- <span class="d-none d-md-inline text-white">Alexander Pierce</span> --}}
      </a>
      @php
          $username = Auth::user()->username;
          $role = Auth::user()->getRole();
      @endphp
      <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="margin-top: 8px; border-top: none;">
        <!-- User image -->
        <li class="user-header " style="background: linear-gradient(to bottom, #00BFFF, #0071BD);">
          <img src="{{ asset('img/user-1.png') }}" class="img-circle elevation-5 bg-white" alt="User Image">
          <p class="text-white mt-4 ">
            <strong><b>{{ strtoupper($username) }}</b></strong>
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