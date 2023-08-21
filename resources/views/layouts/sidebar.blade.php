<aside class="main-sidebar sidebar-dark-primary elevation-4">
{{-- <aside class="main-sidebar elevation-4 sidebar-light-primary"> --}}

  <!-- Brand Logo -->
  <a href="/home" class="brand-link d-flex align-items-center bg-primary" >
    <img src="{{ asset('img/pje.jpg') }}" alt="PJE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text small mx-2 text-bold" >Primatrans Jaya Express</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    {{-- <div class="user-panel mt-3 pb-3 mb-3 d-flex justify-content-center align-items-center">
     
      <div class="info">
        <a class="d-block text-white " > <span class="text-bold ">{{$username}}</span> ( {{$rolex}} )</a>
      </div>
    </div> --}}

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
          <a href="#" class="nav-link" style="font-weight: 700;">
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
       
        {{-- menu admin --}}
        @php
            $user_role = Auth::user()->role_id;
            $role = array(1,2,3,4);
        @endphp

        @if (in_array($user_role, $role))
          @if ($user_role == 1 || $user_role == 2)
            <li class="nav-item 
                {{ request()->is('grup*')||
                request()->is('grup_member*')||
                request()->is('customer*')||
                request()->is('head*')||
                request()->is('chassis*')||
                request()->is('supplier*')||
                request()->is('karyawan*')||
                request()->is('coa*')||
                request()->is('kas_bank*')||
                request()->is('role*')||
                request()->is('users*')||
                request()->is('pengaturan_sistem*')
                ? 'menu-is-opening menu-open' : '' }}">
                <a href="#" class="nav-link" style="font-weight: 700;">
                  <i class="nav-icon fas fa-key"></i>
                  <p>MASTER 
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>
                      Master Grup
                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{route('grup.index')}}" class="nav-link {{request()->url() === route('grup.index')? ' active' : '' }} " style="font-weight: 500;">
                      <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Grup
                        </p>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a href="{{route('grup_member.index')}}" style="font-weight: 500;" class="nav-link {{ request()->url() === route('grup_member.index')? ' active' : '' }} ">
                      <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Marketing Grup
                        </p>
                      </a>
                    </li>
                   
                    <li class="nav-item">
                      <a href="{{route('grup_tujuan.index')}}" style="font-weight: 500;" class="nav-link {{ request()->url() === route('grup_tujuan.index')? ' active' : '' }} ">
                      <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Grup Tujuan
                        </p>
                      </a>
                    </li>
    
                    <li class="nav-item">
                      <a href="{{route('customer.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('customer*') ? ' active' : '' }} ">
                      <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Customer
                        </p>
                      </a>
                    </li>
                  </ul>
                </li>
                
                  {{-- {{request()->url() === route('karyawan.index') ? ' active' : '' }}  --}}
                

                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>
                      Master Truck
                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{route('head.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('head*') ? ' active' : '' }} ">
                      <i class="far nav-icon fa fa-truck" style="font-size: 15px;"></i>
                        <p>
                          Master Truck All
                        </p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{route('head.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('head*') ? ' active' : '' }} ">
                      <i class="far nav-icon fa fa-truck" style="font-size: 15px;"></i>
                        <p>
                          Master Truck PJE
                        </p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{route('pair_kendaraan.index')}}" style="font-weight: 500;" class="nav-link ">
                      <i class="far nav-icon fa fa-truck" style="font-size: 15px;"></i>
                        <p>
                          Master Pair kendaraan
                        </p>
                      </a>
                    </li>
                  </ul>
                </li>
                
                

                <li class="nav-item">
                  <a href="{{route('chassis.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('chassis*') ? ' active' : '' }} ">
                  <i class="far nav-icon fa fa-square" style="font-size: 15px;"></i>
                    <p>
                      Ekor Truck
                    </p>
                  </a>
                </li> 
        
                <li class="nav-item">
                  <a href="{{route('supplier.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('supplier*') ? ' active' : '' }} ">
                  <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                    <p>
                      Supplier
                    </p>
                  </a>
                </li>
            
                <li class="nav-item">
                  <a href="{{route('karyawan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('karyawan*') ? ' active' : '' }} ">
                  <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                    <p>
                      Karyawan
                    </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('coa.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('coa*') ? ' active' : '' }} ">
                  <i class="far nav-icon"></i>
                    <p>
                      COA
                    </p>
                  </a>
                </li> 
                
                <li class="nav-item">
                  <a href="{{route('kas_bank.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('kas_bank*') ? ' active' : '' }}">
                    <i class="far nav-icon"></i>
                    <p>
                      Kas / Bank
                    </p>
                  </a>
                </li> 
                <li class="nav-item">
                  <a href="{{route('role.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('role*') ? ' active' : '' }}">
                    <i class="far nav-icon"></i>
                    <p>
                      Role
                    </p>
                  </a> 
                </li>
                <li class="nav-item">
                  <a href="{{route('users.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('users*') ? ' active' : '' }}">
                    <i class="far nav-icon"></i>
                    <p>
                      User
                    </p>
                  </a> 
                </li>
                <li class="nav-item">
                  <a href="{{route('pengaturan_sistem.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('pengaturan_sistem*') ? ' active' : '' }} ">
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
          @endif

          @if ($user_role == 1 || $user_role == 3)
            {{-- menu marketing --}}
            <li class="nav-item">
              <a href="#" class="nav-link" style="font-weight: 700;">
                <i class="nav-icon fas fa-key"></i>
                <p>Menu Marketing
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{url('marketing1')}}" style="font-weight: 500;" class="nav-link ">
                  <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                    <p>
                      Menu Marketing 1
                    </p>
                  </a>
                <li class="nav-item">
                  <a href="{{url('marketing2')}}" style="font-weight: 500;" class="nav-link ">
                  <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                    <p>
                      Menu Marketing 2
                    </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{url('marketing3')}}" style="font-weight: 500;" class="nav-link ">
                  <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                    <p>
                      Menu Marketing 3
                    </p>
                  </a>
                </li>
              </ul>
            </li>
          @endif

          @if ($user_role == 1 || $user_role == 4)
            {{-- menu finnance --}}
            <li class="nav-item">
              <a href="#" class="nav-link" style="font-weight: 700;">
                <i class="nav-icon fas fa-key"></i>
                <p>Menu Finnance
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{url('finnance1')}}" style="font-weight: 500;" class="nav-link ">
                  <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                    <p>
                      Menu Finnance 1
                    </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{url('finnance2')}}" style="font-weight: 500;" class="nav-link ">
                  <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                    <p>
                      Menu Finnance 2
                    </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{url('finnance3')}}" style="font-weight: 500;" class="nav-link ">
                  <i class="far nav-icon fa fa-circle" style="font-size: 15px;"></i>
                    <p>
                      Menu Finnance 3
                    </p>
                  </a>
                </li>
              </ul>
            </li>
          @endif

        @endif


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