<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="/home" class="brand-link d-flex align-items-center">
    <img src="{{ asset('img/pje.jpg') }}" alt="PJE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light small mx-2 text-bold">Primatrans Jaya Express</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <!-- <div class="image">
        <img src="{{asset('assets/dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
      </div> -->
      <div class="info">
        <a class="d-block text-white"> <span class="text-bold">Username</span> (Super User)</a>

      </div>
    </div>

    <!-- SidebarSearch Form -->
    <!-- <div class="form-inline">
      <div class="input-group" data-widget="sidebar-search">
        <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-sidebar">
            <i class="fas fa-search fa-fw"></i>
          </button>
        </div>
      </div>
    </div> -->

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <!-- =================================================== -->

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="../../index.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Dashboard v1</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../../index2.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Dashboard v2</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../../index3.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Dashboard v3</p>
              </a>
            </li>
          </ul>
        </li>
        <!-- =================================================== -->

        <!-- =================================================== -->
        <!-- =================================================== -->

        <!-- <li class="nav-item">
          <a href="../widgets.html" class="nav-link">
            <i class="nav-icon fas fa-th"></i>
            <p>
              Widgets
              <span class="right badge badge-danger">New</span>
            </p>
          </a>
        </li> -->
        <!-- =================================================== -->

        <!-- =================================================== -->

        <!-- <li class="nav-item menu-open">
          <a href="#" class="nav-link active">
            <i class="nav-icon fas fa-copy"></i>
            <p>
              Layout Options
              <i class="fas fa-angle-left right"></i>
              <span class="badge badge-info right">6</span>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="../layout/top-nav.html" class="nav-link active">
                <i class="far fa-circle nav-icon"></i>
                <p>Top Navigation</p>
              </a>
            </li>
          </ul>
        </li> -->
        <!-- =================================================== -->



        <!-- <li class="nav-header">MASTER</li> -->
       

          <li class="nav-item {{ request()->url() === route('coa.index') ||request()->url() === route('pengaturan_sistem.index') ? 'menu-is-opening menu-open' : '' }}">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-key"></i>
            <p>MASTER
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">

            <li class="nav-item">
              <a href="{{route('grup.index')}}" class="nav-link {{request()->url() === route('grup.index') ? ' active' : '' }} ">
              <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                <p>
                  Grup
                </p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{route('grup_member.index')}}" class="nav-link {{request()->url() === route('grup_member.index') ? ' active' : '' }} ">
              <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                <p>
                  Grup Member
                </p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{route('customer.index')}}" class="nav-link {{request()->url() === route('customer.index') ? ' active' : '' }} ">
              <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                <p>
                  Customer
                </p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{route('head.index')}}" class="nav-link {{request()->url() === route('head.index') ? ' active' : '' }} ">
              <i class="far nav-icon fa fa-truck" style="font-size: 15px;"></i>
                <p>
                  Head
                </p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{route('chassis.index')}}" class="nav-link {{request()->url() === route('chassis.index') ? ' active' : '' }} ">
              <i class="far nav-icon fa fa-square" style="font-size: 15px;"></i>
                <p>
                  Chassis
                </p>
              </a>
            </li>
     
            <li class="nav-item">
              <a href="{{route('supplier.index')}}" class="nav-link {{request()->url() === route('supplier.index') ? ' active' : '' }} ">
              <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                <p>
                  Supplier
                </p>
              </a>
            </li>
         
            <li class="nav-item">
              <a href="{{route('karyawan.index')}}" class="nav-link {{request()->url() === route('karyawan.index') ? ' active' : '' }} ">
              <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                <p>
                  Karyawan
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{route('coa.index')}}" class="nav-link {{request()->url() === route('coa.index') ? ' active' : '' }} ">
              <i class="far nav-icon"></i>
                <p>
                  COA
                </p>
              </a>
            </li>
            
            <li class="nav-item">
              <a href="{{route('kas_bank.index')}}" class="nav-link">
                <i class="far nav-icon"></i>
                <p>
                  Kas / Bank
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{route('role.index')}}" class="nav-link">
                <i class="far nav-icon"></i>
                <p>
                  Role
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{route('users.index')}}" class="nav-link">
                <i class="far nav-icon"></i>
                <p>
                  User
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{route('pengaturan_sistem.index')}}" class="nav-link {{request()->url() === route('pengaturan_sistem.index') ? ' active' : '' }} ">
              <i class="far nav-icon"></i>
                <p>
                  Pengaturan Sistem
                </p>
              </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
                    Level 2
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-dot-circle nav-icon"></i>
                      <p>Level 3</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-dot-circle nav-icon"></i>
                      <p>Level 3</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-dot-circle nav-icon"></i>
                      <p>Level 3</p>
                    </a>
                  </li>
                </ul>
              </li>
          </ul>
        </li>

        <!-- <li class="nav-item">
          <a href="" class="nav-link">
            <i class="nav-icon fas fa-columns"></i>
            <p>
              User
            </p>
          </a>
        </li> -->
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>