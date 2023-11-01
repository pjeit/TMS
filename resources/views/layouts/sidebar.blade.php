<style>

</style>

<aside class="main-sidebar sidebar-dark-primary elevation-4 ">
{{-- <aside class="main-sidebar elevation-4 sidebar-light-primary"> --}}

  <!-- Brand Logo -->

  <a href="/home" class="brand-link d-flex align-items-center " style="background: linear-gradient(to bottom, #0071BD, #00BFFF); ">
    <img src="{{ asset('img/LOGO_PJE_CLEAR.png') }}" alt="PJE Logo" class="brand-image img-circle ">
    <span class="brand-text mx-2 text-bold font-italic" ><b>PRIMATRANS</b></span>
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
          <a href="#" class="nav-link hover-item" style="font-weight: 700;">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              DASHBOARD
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{route('dashboard.reset')}}" class="nav-link">
                <i class="far fa-check-circle nav-icon"></i>
                <p>Reset Data <small class="text-warning">(Dev only)</small></p>
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
            // 1 = superadmin
            // 2 = admin
            // 3 = admin nasional
            $user_role = Auth::user()->role_id;
            $role = array(1,2,3,4);
        @endphp

        @if (in_array($user_role, $role))
        
          @if (in_array($user_role, [1,2,3]))
            {{-- MASTER --}}
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
                <a href="#" class="nav-link hover-item" style="font-weight: 700; font-size: 15px;">
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
                  <a href="#" class="nav-link hover-item">
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
                      <a href="{{route('customer.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('customer*') ? ' active' : '' }} ">
                      <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Customer
                        </p>
                      </a>
                    </li>
                   
                    <li class="nav-item">
                      <a href="{{route('grup_tujuan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('grup_tujuan') ||   request()->is('grup_tujuan/*/edit') ? ' active' : '' }}">
                      <i class="far fa-dot-circle nav-icon" style=  "font-size: 15px;"></i>
                        <p>
                          Grup Tujuan
                        </p>
                      </a>
                    </li>

                  </ul>
                </li>
                
                <li class="nav-item {{ request()->is('head*')||
                              request()->is('pair_kendaraan*')||
                              request()->is('mutasi_kendaraan*')||
                              request()->is('chassis*')
                              ? 'menu-is-opening menu-open' : '' }}">
                  <a href="#" class="nav-link hover-item">
                    <i class="far nav-icon fa fa-truck"></i>
                    <p style="font-weight: 500;" >
                      Master Truck
                      <i class="right fas fa-angle-left"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{route('head.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('head*') ? ' active' : '' }} ">
                      <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Truck
                        </p>
                      </a>
                    </li>
                     <li class="nav-item">
                      <a href="{{route('chassis.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('chassis*') ? ' active' : '' }} ">
                      <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Ekor Truck
                        </p>
                      </a>
                    </li> 
                    <li class="nav-item">
                      <a href="{{route('pair_kendaraan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('pair_kendaraan*') ? ' active' : '' }}">
                      <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
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
              </ul>
            </li>
          @endif
            
          @if (in_array($user_role, [1,2,3]))
            {{-- BOUND ORDER --}}
            <li class="nav-item {{ request()->is('job_order*')||
              request()->is('storage_demurage*')||
              request()->is('unloading_plan*')
              ? 'menu-is-opening menu-open' : '' }}">
              <a href="#" class="nav-link hover-item" style="font-weight: 700;font-size: 15px;">
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
                      Input S/D/T
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
          
            {{-- TRUCKING ORDER --}}
            <li class="nav-item {{ request()->is('booking*')||
              request()->is('dalam_perjalanan*') ||
              request()->is('truck_order*')? 'menu-is-opening menu-open' : '' }}">
              <a href="#" class="nav-link hover-item" style="font-weight: 700;font-size: 15px;">
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
              {{-- <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('truck_order_rekanan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('truck_order_rekanan') ||  request()->is('truck_order_rekanan/create') || request()->is('truck_order_rekanan/*/edit') ? ' active' : '' }}">
                  <i class="fa fa-cubes nav-icon" style="font-size: 15px;"></i>
                    <p>
                       Order Rekanan
                    </p>
                  </a>
                </li>
              </ul>  --}}
  
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('dalam_perjalanan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('dalam_perjalanan*') ? 'active' : ''  }}">
                  <i class="fa fa-cubes nav-icon" style="font-size: 15px;"></i>
                    <p>
                       Dalam Perjalanan
                    </p>
                  </a>
                </li>
              </ul> 
              
            </li>
           
            {{-- FINANCE --}}
            <li class="nav-item {{ 
              request()->is('pembayaran_jo*') ||
              request()->is('pencairan_uang_jalan*') ||
              request()->is('pencairan_operasional*') ||
              request()->is('biaya_operasional*') ||
              request()->is('pembayaran_sdt*') ||
              request()->is('pengembalian_jaminan*') ||
              request()->is('pencairan_komisi_driver*')||
              request()->is('klaim_supir*')||
              request()->is('tagihan_rekanan*')||
              request()->is('pencairan_komisi_customer*')||
              request()->is('cetak_uang_jalan*')
              ? 'menu-is-opening menu-open' : '' }}">
              <a href="#" class="nav-link hover-item" style="font-weight: 700;font-size: 15px;">
                <i class="nav-icon fas fa-dollar-sign"></i>
                <p>FINANCE 
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>

              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('pembayaran_jo.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('pembayaran_jo*') ? 'active' : ''  }}">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                    <p>
                      Pembayaran JO
                    </p>
                  </a>
                </li>
              </ul>
            
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('pembayaran_sdt.index')}}" class="nav-link {{ route('pembayaran_sdt.index') === request()->url() ? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                    <p>
                      Pembayaran S/D/T
                    </p>
                  </a>
                </li>
              </ul>

              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('biaya_operasional.index')}}" class="nav-link {{request()->url() === route('biaya_operasional.index')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                    <p>
                      Biaya Operasional
                    </p>
                  </a>
                </li>
              </ul>

              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('klaim_supir.index')}}" class="nav-link {{request()->url() === route('klaim_supir.index')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                    <p>
                      Klaim Supir
                    </p>
                  </a>
                </li>
              </ul>
            
              <ul class="nav nav-treeview">
                <li class="nav-item   {{ 
                request()->is('pencairan_uang_jalan*') ||
                request()->is('pencairan_uang_jalan_ltl*')
                ? 'menu-is-opening menu-open' : '' }}" style="font-size: 15px;">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p style="font-weight: 500;" >
                      Pencairan UJ
                      <i class="right fas fa-angle-left" style="font-size: 15px;"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{route('pencairan_uang_jalan.index')}}" class="nav-link {{ request()->url() === route('pencairan_uang_jalan.index')? ' active' : '' }} " style="font-weight: 500;">
                        <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          FTL
                        </p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{route('pencairan_uang_jalan_ltl.index')}}" class="nav-link {{request()->url() === route('pencairan_uang_jalan_ltl.index')? ' active' : '' }} " style="font-weight: 500;">
                        <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          LTL
                        </p>
                      </a>
                    </li>
                  </ul>
                </li>
              
              </ul>

              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('cetak_uang_jalan.index')}}" class="nav-link {{request()->url() === route('cetak_uang_jalan.index')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                    <p>
                      Cetak Uang Jalan
                    </p>
                  </a>
                </li>
              </ul>
              
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('pengembalian_jaminan.index')}}" class="nav-link {{request()->url() === route('pengembalian_jaminan.index')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                    <p>
                      <span style="font-size: 13.9px;">Pengembalian Jaminan</span>
                    </p>
                  </a>
                </li>
              </ul>

              <ul class="nav nav-treeview">
                <li class="nav-item   {{ 
                request()->is('pencairan_komisi_driver*')||
                request()->is('pencairan_komisi_customer*')
                ? 'menu-is-opening menu-open' : '' }}" style="font-size: 15px;">
                  <a href="#" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p style="font-weight: 500;" >
                      Pencairan Komisi
                      <i class="right fas fa-angle-left" style="font-size: 15px;"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{route('pencairan_komisi_customer.index')}}" style="font-weight: 500;" class="nav-link {{ request()->is('pencairan_komisi_customer*')? ' active' : '' }} ">
                      <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Customer
                        </p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="{{route('pencairan_komisi_driver.index')}}" class="nav-link {{request()->is('pencairan_komisi_driver') ||  request()->is('pencairan_komisi_driver/create') || request()->is('pencairan_komisi_driver/*/edit') ? ' active' : '' }} " style="font-weight: 500;">
                      <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Driver
                        </p>
                      </a>
                    </li>
                  </ul>
                </li>
              
              </ul>

              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('tagihan_rekanan.index')}}" class="nav-link {{ request()->is('tagihan_rekanan*')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                    <p>
                      <span >Tagihan Rekanan</span> {{-- style="font-size: 13.9px" --}}
                    </p>
                  </a>
                </li>
              </ul>


            </li>
          @endif

          @if (in_array($user_role, [1,2,3]))
            {{-- INVOICE --}}
            <li class="nav-item {{ request()->is('belum_invoice*') ||
              request()->is('pembayaran_invoice*') ||
              request()->is('bukti_potong*') ||
              request()->is('cetak_invoice*') 
                ? 'menu-is-opening menu-open' : '' }}">
              <a href="#" class="nav-link hover-item" style="font-weight: 700;font-size: 15px;">
                <i class="fas nav-icon fa-solid fa-file-invoice"></i>
                <p>INVOICE
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>

              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('belum_invoice.index')}}" class="nav-link {{request()->is('belum_invoice*')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="nav-icon fas fa-pencil-alt " style="font-size: 15px;"></i>
                    <p>
                      Belum Invoice
                    </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('cetak_invoice.index')}}" class="nav-link {{request()->url() === route('cetak_invoice.index')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="nav-icon fas fa-print " style="font-size: 15px;"></i>
                    <p>
                      Cetak Invoice
                    </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('pembayaran_invoice.index')}}" class="nav-link {{ request()->is('pembayaran_invoice*')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="nav-icon fas fa-money-bill-wave" style="font-size: 15px;"></i>
                    <p>
                      Pembayaran Invoice
                    </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('bukti_potong.index')}}" class="nav-link {{ request()->is('bukti_potong*')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="nav-icon fas fa-file" style="font-size: 15px;"></i>
                    <p>
                      Input Bukti Potong
                    </p>
                  </a>
                </li>
            

              </ul>
            </li>

            {{-- Rollback --}}
            <li class="nav-item {{ request()->is('revisi_uang_jalan*') ||  
                                request()->is('revisi_tl*')
                                ? 'menu-is-opening menu-open' : '' }}">
              <a href="#" class="nav-link hover-item" style="font-weight: 700;font-size: 15px;">
                <i class="fas nav-icon fa-solid fa fa-undo"></i>
                <p>Revisi
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>

              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('revisi_tl.index')}}" class="nav-link {{request()->is('revisi_tl*')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                    <p>
                      Revisi TL
                    </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{route('revisi_uang_jalan.index')}}" class="nav-link {{request()->url() === route('revisi_uang_jalan.index')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>

                    <p>
                      Rev. Uang Jalan
                    </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link {{request()->url() === route('invoice.index')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                    <p>
                      <span style="font-size: 0.9em;">Rev. Biaya Operasional</span>
                    </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link {{request()->url() === route('invoice.index')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i> 
                    <p>
                      Rev. Belum Invoice
                    </p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link {{request()->url() === route('invoice.index')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                    <p>
                      <span style="font-size: 0.84em;">Rev. Pembayaran Invoice</span>
                    </p>
                  </a>
                </li>
              </ul>
            </li>

            {{-- LAPORAN FINANCE --}}
            <li class="nav-item {{ request()->is('laporan_kas*') ||
                request()->is('laporan_bank*') ? 'menu-is-opening menu-open' : '' }}">
              <a href="#" class="nav-link hover-item" style="font-weight: 700;font-size: 15px;">
                <i class="nav-icon fas fa-dollar-sign"></i>
                <p>LAPORAN FINANCE
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>

              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{route('laporan_kas.index')}}" class="nav-link {{request()->url() === route('laporan_kas.index')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>

                    <p>
                      Laporan Kas
                    </p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="{{route('laporan_bank.index')}}" class="nav-link {{request()->url() === route('laporan_bank.index')? ' active' : '' }} " style="font-weight: 500;">
                  <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>

                    <p>
                      Laporan Bank
                    </p>
                  </a>
                </li>
            
              </ul>
            </li>
          @endif
            
        @endif

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>