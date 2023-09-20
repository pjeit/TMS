<aside class="main-sidebar sidebar-dark-primary elevation-4 ">
{{-- <aside class="main-sidebar elevation-4 sidebar-light-primary"> --}}

  <!-- Brand Logo -->
  <a href="/home" class="brand-link d-flex align-items-center bg-primary" >
    <img src="{{ asset('img/pje.jpg') }}" alt="PJE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text small mx-2 text-bold" >Primatrans Jaya Express</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar ">
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
    <nav class="mt-2 ">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <!-- =================================================== -->

        <li class="nav-item">
          <a href="#" class="nav-link" style="font-weight: 700;">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              DASHBOARD
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
                {{ 
                    request()->is('marketing*')||
                    request()->is('customer*')||
                    request()->is('head*')||
                    request()->is('chassis*')||
                    request()->is('supplier*')||
                    request()->is('karyawan*')||
                    request()->is('coa*')||
                    request()->is('kas_bank*')||
                    request()->is('role*')||
                    request()->is('users*')||
                    request()->is('pengaturan_keuangan*')||
                    request()->is('head*')||
                    request()->is('pair_kendaraan*')||
                    request()->is('chassis*')||
                    request()->is('mutasi_kendaraan*')||
                    request()->is('grup*')
                ? 'menu-is-opening menu-open' : '' }}">
                <a href="#" class="nav-link" style="font-weight: 700; font-size: 15px;">
                  <i class="nav-icon fas fa-key"></i>
                  <p>MASTER 
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
              <ul class="nav nav-treeview">
                <li class="nav-item   {{ 
                request()->is('grup*')||
                request()->is('marketing*')||
                request()->is('customer*')||
                request()->is('grup_tujuan*')
                ? 'menu-is-opening menu-open' : '' }}">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p style="font-weight: 500;" >
                      Master Grup
                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{route('grup.index')}}" class="nav-link {{request()->is('grup') ||  request()->is('grup/create') || request()->is('grup/*/edit') ? ' active' : '' }} " style="font-weight: 500;">
                      <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Grup
                        </p>
                      </a>
                    </li>

                    <li class="nav-item">
                      <a href="{{route('marketing.index')}}" style="font-weight: 500;" class="nav-link {{ request()->is('marketing*')? ' active' : '' }} ">
                      <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Marketing Grup
                        </p>
                      </a>
                    </li>
                   
                    <li class="nav-item">
                      <a href="{{route('grup_tujuan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('grup_tujuan') ||   request()->is('grup_tujuan/*/edit') ? ' active' : '' }}">
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
                

                <li class="nav-item {{ request()->is('head*')||
                              request()->is('pair_kendaraan*')||
                              request()->is('mutasi_kendaraan*')||
                              request()->is('chassis*')
                              ? 'menu-is-opening menu-open' : '' }}">
                  <a href="#" class="nav-link">
                    <i class="far nav-icon fa fa-truck"></i>
                    <p style="font-weight: 500;" >
                      Master Truck
                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{route('head.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('head*') ? ' active' : '' }} ">
                      <i class="far nav-icon fa fa-truck" style="font-size: 15px;"></i>
                        <p>
                          Truck
                        </p>
                      </a>
                    </li>
                     <li class="nav-item">
                      <a href="{{route('chassis.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('chassis*') ? ' active' : '' }} ">
                      <i class="far nav-icon fa fa-truck" style="font-size: 15px;"></i>
                        <p>
                          Ekor Truck
                        </p>
                      </a>
                    </li> 
                    <li class="nav-item">
                      <a href="{{route('pair_kendaraan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('pair_kendaraan*') ? ' active' : '' }}">
                      <i class="far nav-icon fa fa-truck" style="font-size: 15px;"></i>
                        <p>
                          Pairing Truck
                        </p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{route('mutasi_kendaraan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('mutasi_kendaraan*') ? ' active' : '' }}">
                      <i class="far nav-icon fa fa-undo" style="font-size: 15px;"></i>
                        <p>
                          Mutasi Kendaraan
                        </p>
                      </a>
                    </li>
                  </ul>
                </li>
        
                <li class="nav-item">
                  <a href="{{route('supplier.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('supplier*') ? ' active' : '' }} ">
                    <i class="far nav-icon fa fa-building" style="font-size: 15px;"></i>
                    <p>
                      Supplier
                    </p>
                  </a>
                </li>
            
                <li class="nav-item">
                  <a href="{{route('karyawan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('karyawan*') ? ' active' : '' }} ">
                  <i class="far nav-icon fa fa-id-card" style="font-size: 15px;"></i>
                    <p>
                      Karyawan
                    </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('coa.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('coa*') ? ' active' : '' }} ">
                  <i class="far nav-icon fa fa-list"></i>
                    <p>
                      COA
                    </p>
                  </a>
                </li> 
                
                <li class="nav-item">
                  <a href="{{route('kas_bank.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('kas_bank*') ? ' active' : '' }}">
                    <i class="far nav-icon fa fa-university"></i>
                    <p>
                      Kas / Bank
                    </p>
                  </a>
                </li> 
                <li class="nav-item">
                  <a href="{{route('role.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('role*') ? ' active' : '' }}">
                    <i class="far nav-icon fa fa-users "></i>
                    <p>
                      Role
                    </p>
                  </a> 
                </li>
                <li class="nav-item">
                  <a href="{{route('users.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('users*') ? ' active' : '' }}">
                    <i class="far nav-icon fa fa-user-circle"></i>
                    <p>
                      User
                    </p>
                  </a> 
                </li>
                <li class="nav-item">
                  <a href="{{route('pengaturan_keuangan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('pengaturan_keuangan*') ? ' active' : '' }} ">
                  <i class="far nav-icon fa fa-cog"></i>
                    <p style="font-size: 15px;">
                      Pengaturan
                    </p>
                  </a> 
                </li> 
                {{-- <li class="nav-item">
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
                  </li> --}}
              </ul>
            </li>
          @endif

          <li class="nav-item {{ request()->is('job_order*')||
            request()->is('storage_demurage*')||
            request()->is('unloading_plan*')
            ? 'menu-is-opening menu-open' : '' }}">
            <a href="#" class="nav-link" style="font-weight: 700;font-size: 15px;">
              <i class="nav-icon fas fa-shipping-fast"></i>
              <p>
                INBOUND ORDER <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('job_order.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('job_order*') ? 'active' : ''  }}">
                <i class="fa fa-cubes nav-icon" style="font-size: 15px;"></i>
                  <p>
                    Job Order
                  </p>
                </a>
              </li>
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('storage_demurage.index')}}" class="nav-link {{request()->url() === route('storage_demurage.index')? ' active' : '' }} " style="font-weight: 500;">
                <i class="fa fa-cubes nav-icon" style="font-size: 15px;"></i>
                  <p>
                    Storage Demurage
                  </p>
                </a>
              </li>
            </ul>
            {{-- <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('job_order.unloading_plan')}}" class="nav-link {{request()->url() === route('job_order.unloading_plan')? ' active' : '' }} " style="font-weight: 500;">
                <i class="fa fa-cubes nav-icon" style="font-size: 15px;"></i>
                  <p>
                    Unloading Plan
                  </p>
                </a>
              </li>
            </ul> --}}
         
          </li>
           <li class="nav-item {{ request()->is('booking*')||
            request()->is('biaya_operasional*') ||
            request()->is('truck_order*')? 'menu-is-opening menu-open' : '' }}">
            <a href="#" class="nav-link" style="font-weight: 700;font-size: 15px;">
              {{-- <i class="nav-icon fas fa-shipping-fast"></i> --}}
              <i class="nav-icon fas fa-solid fa-truck"></i>
              <p>
                TRUCKING ORDER <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('booking.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('booking*') ? 'active' : ''  }}">
                <i class="far fa-bookmark nav-icon" style="font-size: 15px;"></i>
                  <p>
                    Booking
                  </p>
                </a>
              </li>
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('truck_order.index')}}" style="font-weight: 500;" class="nav-link {{ request()->is('truck_order') ||  request()->is('truck_order/create') || request()->is('truck_order/*/edit') ? ' active' : '' }}">
                <i class="fa fa-cubes nav-icon" style="font-size: 15px;"></i>
                  <p>
                     Order
                  </p>
                </a>
              </li>
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('truck_order_rekanan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('truck_order_rekanan') ||  request()->is('truck_order_rekanan/create') || request()->is('truck_order_rekanan/*/edit') ? ' active' : '' }}">
                <i class="fa fa-cubes nav-icon" style="font-size: 15px;"></i>
                  <p>
                     Order Rekanan
                  </p>
                </a>
              </li>
            </ul> 
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('biaya_operasional.index')}}" class="nav-link {{request()->url() === route('biaya_operasional.index')? ' active' : '' }} " style="font-weight: 500;">
                <i class="fas fa-dollar-sign nav-icon" style="font-size: 15px;"></i>
                  <p>
                    Biaya Operasional
                  </p>
                </a>
              </li>
            </ul>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('perjalanan_kembali.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('sewa_kembali') ||  request()->is('sewa_kembali/create') || request()->is('sewa_kembali/*/edit') ? ' active' : '' }}">
                <i class="fa fa-cubes nav-icon" style="font-size: 15px;"></i>
                  <p>
                     Perjalanan Kembali 
                  </p>
                </a>
              </li>
            </ul> 
            
          </li>
         
          <li class="nav-item {{ 
            request()->is('pembayaran_jo*') ||
            request()->is('pencairan_uang_jalan_ftl*') ||
            request()->is('pencairan_operasional*') ||
            request()->is('pembayaran_sdt*') 
            ? 'menu-is-opening menu-open' : '' }}">
            <a href="#" class="nav-link" style="font-weight: 700;font-size: 15px;">
              <i class="nav-icon fas fa-dollar-sign"></i>
              <p>FINANCE 
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
          
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('pembayaran_jo.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('pembayaran_jo*') ? 'active' : ''  }}">
                <i class="fas fa-dollar-sign nav-icon" style="font-size: 15px;"></i>
                  <p>
                    Pembayaran JO
                  </p>
                </a>
              </li>
            </ul>
          
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('pembayaran_sdt.index')}}" class="nav-link {{request()->url() === route('pembayaran_sdt.index')? ' active' : '' }} " style="font-weight: 500;">
                <i class="fas fa-dollar-sign nav-icon" style="font-size: 15px;"></i>
                  <p>
                    Pembayaran S/D/T
                  </p>
                </a>
              </li>
            </ul>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('pencairan_uang_jalan_ftl.index')}}" class="nav-link {{request()->url() === route('pencairan_uang_jalan_ftl.index')? ' active' : '' }} " style="font-weight: 500;">
                <i class="fas fa-dollar-sign nav-icon" style="font-size: 15px;"></i>
                  <p>
                    Pencairan UJ
                  </p>
                </a>
              </li>
            </ul>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('pencairan_operasional.index')}}" class="nav-link {{request()->url() === route('pencairan_operasional.index')? ' active' : '' }} " style="font-weight: 500;">
                <i class="fas fa-dollar-sign nav-icon" style="font-size: 15px;"></i>
                  <p>
                    Pencairan <small><strong>Operasional</strong></small>
                  </p>
                </a>
              </li>
            </ul>
           
            
          </li>

          <li class="nav-item {{ request()->is('laporan_kas*') ||
              request()->is('laporan_bank*') ? 'menu-is-opening menu-open' : '' }}">
            <a href="#" class="nav-link" style="font-weight: 700;font-size: 15px;">
              <i class="nav-icon fas fa-dollar-sign"></i>
              <p>LAPORAN FINANCE
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
          
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('laporan_kas.index')}}" class="nav-link {{request()->url() === route('laporan_kas.index')? ' active' : '' }} " style="font-weight: 500;">
                <i class="fas fa-dollar-sign nav-icon" style="font-size: 15px;"></i>
                  <p>
                    Laporan Kas
                  </p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{route('laporan_bank.index')}}" class="nav-link {{request()->url() === route('laporan_bank.index')? ' active' : '' }} " style="font-weight: 500;">
                <i class="fas fa-dollar-sign nav-icon" style="font-size: 15px;"></i>
                  <p>
                    Laporan Bank
                  </p>
                </a>
              </li>
           
            </ul>
          </li>

          @if ($user_role == 1 || $user_role == 3)
            {{-- menu marketing --}}
            <li class="nav-item">
              <a href="#" class="nav-link" style="font-weight: 700;font-size: 15px;">
                <i class="nav-icon fas fa-key"></i>
                <p>MARKETING
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
            {{-- <li class="nav-item">
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
            </li> --}}
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