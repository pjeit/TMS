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
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent w-50" data-widget="treeview" role="menu" data-accordion="false">
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
        {{-- @php
            // 1 = superadmin
            // 2 = admin
            // 3 = admin nasional
            $user_role = Auth::user()->role_id;
            $role = array(1,2,3,4);
        @endphp --}}

        {{-- @if (in_array($user_role, $role)) --}}
        @php
          $semuaAkses = \Spatie\Permission\Models\Permission::pluck('name')->toArray();
          $userAkses=auth()->user()->getAllPermissions()->pluck('name')->toArray();
          $hasAllPermissions = count(array_diff($semuaAkses, $userAkses)) === 0;
          // dd($userAkses);
        @endphp
            {{-- @php
                  $READ_REVISI_TL = auth()->user()->can('READ_REVISI_TL');
                  $READ_REVISI_UANG_JALAN = auth()->user()->can('READ_REVISI_UANG_JALAN');
                  $READ_REVISI_BIAYA_OPERASIONAL = auth()->user()->can('READ_REVISI_BIAYA_OPERASIONAL');
                  $READ_REVISI_KLAIM_SUPIR = auth()->user()->can('READ_REVISI_KLAIM_SUPIR');
                  $READ_REVISI_INVOICE_TRUCKING = auth()->user()->can('READ_REVISI_INVOICE_TRUCKING');
                  $READ_REVISI_TAGIHAN_REKANAN = auth()->user()->can('READ_REVISI_TAGIHAN_REKANAN');
                  $READ_REVISI_TAGIHAN_PEMBELIAN = auth()->user()->can('READ_REVISI_TAGIHAN_PEMBELIAN');
                  $READ_REVISI_PEMBAYARAN_INVOICE = auth()->user()->can('READ_REVISI_PEMBAYARAN_INVOICE');
              @endphp
              @if(
                $READ_REVISI_TL || $READ_REVISI_UANG_JALAN
                $READ_REVISI_BIAYA_OPERASIONAL || $READ_REVISI_KLAIM_SUPIR
                $READ_REVISI_INVOICE_TRUCKING || $READ_REVISI_TAGIHAN_REKANAN
                $READ_REVISI_TAGIHAN_PEMBELIAN || $READ_REVISI_PEMBAYARAN_INVOICE
              )
              @endif --}}
            {{-- @if (in_array($userAkses, ['READ_GRUP','READ_HEAD','READ_SUPPLIER','READ_KARYAWAN',
                                        'READ_COA', 'READ_KASBANK', 'READ_ROLE', 'READ_USER', 'READ_PENGATURAN_KEUANGAN'
                                      ])) --}}
            @php
                $master = ['READ_GRUP','READ_HEAD','READ_SUPPLIER','READ_KARYAWAN',
                          'READ_COA', 'READ_KASBANK', 'READ_ROLE', 'READ_USER', 'READ_PENGATURAN_KEUANGAN'];
            @endphp
            @if (array_intersect($userAkses, $master) != NULL)
            {{-- MASTER --}}
              {{-- @if ($hasAllPermissions) --}}
              
              <li class="nav-item 
                  {{ 
                      request()->is('marketing*')||
                      request()->is('customer*')||
                      request()->is('head*')||
                      request()->is('chassis*')||
                      request()->is('supplier*')||
                      request()->is('karyawan') ||  request()->is('karyawan/create') || request()->is('karyawan/*/edit') ||
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
                  @can('READ_GRUP')
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
                          @can('READ_GRUP')
                            <li class="nav-item">
                              <a href="{{route('grup.index')}}" class="nav-link {{request()->is('grup') ||  request()->is('grup/create') || request()->is('grup/*/edit') ? ' active' : '' }} " style="font-weight: 500;">
                              <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                                <p>
                                  Grup
                                </p>
                              </a>
                            </li>
                          @endcan
                          @can('READ_MARKETING')
                          <li class="nav-item">
                            <a href="{{route('marketing.index')}}" style="font-weight: 500;" class="nav-link {{ request()->is('marketing*')? ' active' : '' }} ">
                            <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                              <p>
                                Marketing Grup
                              </p>
                            </a>
                          </li>
                          @endcan
                          @can('READ_CUSTOMER')
                          <li class="nav-item">
                            <a href="{{route('customer.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('customer*') ? ' active' : '' }} ">
                            <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                              <p>
                                Customer
                              </p>
                            </a>
                          </li>
                          @endcan
                          @can('READ_GRUP_TUJUAN')
                          <li class="nav-item">
                            <a href="{{route('grup_tujuan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('grup_tujuan') ||   request()->is('grup_tujuan/*/edit') ? ' active' : '' }}">
                            <i class="far fa-dot-circle nav-icon" style=  "font-size: 15px;"></i>
                              <p>
                                Grup Tujuan
                              </p>
                            </a>
                          </li>
                          @endcan

                        </ul>
                    </li>
                  
                  @endcan
                  @can('READ_HEAD')
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
                      @can('READ_HEAD')
                      <li class="nav-item">
                          <a href="{{route('head.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('head*') ? ' active' : '' }} ">
                          <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                            <p>
                              Truck
                            </p>
                          </a>
                        </li>
                      @endcan
                      @can('READ_CHASSIS')
                        <li class="nav-item">
                          <a href="{{route('chassis.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('chassis*') ? ' active' : '' }} ">
                          <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                            <p>
                              Ekor Truck
                            </p>
                          </a>
                        </li> 
                      @endcan
                      @can('READ_PAIR_KENDARAAN')
                        <li class="nav-item">
                          <a href="{{route('pair_kendaraan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('pair_kendaraan*') ? ' active' : '' }}">
                          <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                            <p>
                              Pairing Truck
                            </p>
                          </a>
                        </li>
                      @endcan
                      @can('READ_MUTASI_KENDARAAN')
                        <li class="nav-item">
                          <a href="{{route('mutasi_kendaraan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('mutasi_kendaraan*') ? ' active' : '' }}">
                          <i class="far nav-icon fa fa-undo" style="font-size: 15px;"></i>
                            <p>
                              Mutasi Kendaraan
                            </p>
                          </a>
                        </li>
                      @endcan
                      </ul>
                    </li>
                  @endcan
                  @can('READ_SUPPLIER')
                    <li class="nav-item">
                      <a href="{{route('supplier.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('supplier*') ? ' active' : '' }} ">
                        <i class="far nav-icon fa fa-building" style="font-size: 15px;"></i>
                        <p>
                          Supplier
                        </p>
                      </a>
                    </li>
                  @endcan
                  @can('READ_KARYAWAN')
                    <li class="nav-item">
                      <a href="{{route('karyawan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('karyawan') ||  request()->is('karyawan/create') || request()->is('karyawan/*/edit') ? ' active' : '' }} ">
                      <i class="far nav-icon fa fa-id-card" style="font-size: 15px;"></i>
                        <p>
                          Karyawan
                        </p>
                      </a>
                    </li>
                  @endcan
                  @can('READ_COA')
                    <li class="nav-item">
                      <a href="{{route('coa.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('coa*') ? ' active' : '' }} ">
                      <i class="far nav-icon fa fa-list"></i>
                        <p>
                          COA
                        </p>
                      </a>
                    </li> 
                  @endcan
                  @can('READ_KASBANK')
                    <li class="nav-item">
                      <a href="{{route('kas_bank.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('kas_bank*') ? ' active' : '' }}">
                        <i class="far nav-icon fa fa-university"></i>
                        <p>
                          Kas / Bank
                        </p>
                      </a>
                    </li> 
                  @endcan
                  @can('READ_ROLE')
                    <li class="nav-item">
                      <a href="{{route('role.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('role*') ? ' active' : '' }}">
                        <i class="far nav-icon fa fa-users "></i>
                        <p>
                          Role
                        </p>
                      </a> 
                    </li>
                  @endcan
                  @can('READ_USER')
                    <li class="nav-item">
                      <a href="{{route('users.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('users*') ? ' active' : '' }}">
                        <i class="far nav-icon fa fa-user-circle"></i>
                        <p>
                          User
                        </p>
                      </a> 
                    </li>
                  @endcan
                  @can('READ_PENGATURAN_KEUANGAN')
                    <li class="nav-item">
                      <a href="{{route('pengaturan_keuangan.index')}}" style="font-weight: 500;" class="nav-link {{request()->is('pengaturan_keuangan*') ? ' active' : '' }} ">
                      <i class="far nav-icon fa fa-cog"></i>
                        <p style="font-size: 15px;">
                          Pengaturan
                        </p>
                      </a> 
                    </li> 
                  @endcan

                </ul>
              </li>
            
            @endif
          {{-- @endif --}}
            @php
                $inbound = ['READ_JO','READ_SDT','READ_PENGEMBALIAN_JAMINAN','READ_KARANTINA',];
            @endphp
          {{-- @if (in_array($user_role, [1,2,3])) --}}
            {{-- INBOUND ORDER --}}
            @if (array_intersect($userAkses, $inbound) != NULL)
              <li class="nav-item {{ request()->is('job_order*') ||
                request()->is('storage_demurage*') ||
                request()->is('karantina*') ||
                request()->is('pengembalian_jaminan*') ||
                request()->is('unloading_plan*')
                ? 'menu-is-opening menu-open' : '' }}">
                <a href="#" class="nav-link hover-item" style="font-weight: 700;font-size: 15px;">
                  <i class="nav-icon fas fa-shipping-fast"></i>
                  <p>
                    INBOUND ORDER <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                @can('READ_JO')
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
                @endcan
                @can('READ_SDT')
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
                @endcan
                @can('READ_PENGEMBALIAN_JAMINAN')
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
                @endcan
                @can('READ_KARANTINA')
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{route('karantina.index')}}" class="nav-link {{ request()->is('karantina*') ? ' active' : '' }} " style="font-weight: 500;">
                      <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Input Karantina
                        </p>
                      </a>
                    </li>
                  </ul>
                @endcan
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
            @endif
            @php
                $trucking = ['READ_BOOKING','READ_ORDER','READ_STATUS_KENDARAAN','READ_DALAM_PERJALANAN'];
            @endphp
            @if (array_intersect($userAkses, $trucking) != NULL)
              {{-- TRUCKING ORDER --}}
              <li class="nav-item {{ request()->is('booking*')||
                request()->is('dalam_perjalanan*') ||
                request()->is('status_kendaraan*') ||
                request()->is('truck_order*')? 'menu-is-opening menu-open' : '' }}">
                <a href="#" class="nav-link hover-item" style="font-weight: 700;font-size: 15px;">
                  {{-- <i class="nav-icon fas fa-shipping-fast"></i> --}}
                  <i class="nav-icon fas fa-solid fa-truck"></i>
                  <p>
                    TRUCKING ORDER <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                @can('READ_BOOKING')
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
                @endcan
                @can('READ_ORDER')
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{route('truck_order.index')}}" style="font-weight: 500;" class="nav-link {{ request()->is('truck_order*') ||  request()->is('truck_order/create') || request()->is('truck_order/*/edit') ? ' active' : '' }}">
                    <i class="fa fa-cubes nav-icon" style="font-size: 15px;"></i>
                      <p>
                        Order
                      </p>
                    </a>
                  </li>
                </ul>
                @endcan
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
                @can('READ_STATUS_KENDARAAN')
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{route('status_kendaraan.index')}}" class="nav-link {{request()->url() === route('status_kendaraan.index')? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                      <p>
                        Status Kendaraan
                      </p>
                    </a>
                  </li>
                </ul>
                @endcan
                @can('READ_DALAM_PERJALANAN')
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
                @endcan
              
              </li>
            @endif
            @php
                $finance = [
                    'READ_BIAYA_OPERASIONAL',
                    'READ_KLAIM_SUPIR',
                    'READ_PEMBAYARAN_JO',
                    'READ_PEMBAYARAN_SDT',
                    'READ_PEMBAYARAN_GAJI',
                    'READ_PENCAIRAN_UJ_FTL',
                    'READ_PENCAIRAN_UJ_LTL',
                    'READ_CETAK_UJ',
                    'READ_PENCAIRAN_KOMISI_CUSTOMER',
                    'READ_PENCAIRAN_KOMISI_DRIVER',
                    'READ_TAGIHAN_REKANAN',
                    'READ_TAGIHAN_PEMBELIAN',
                    'READ_TRANSAKSI_NON_OPERASIONAL',
                    'READ_KARYAWAN_HUTANG',
                    'READ_TRANSFER_DANA',
                ];
            @endphp
            @if (array_intersect($userAkses, $finance) != NULL)
              {{-- FINANCE --}}
              <li class="nav-item {{ 
                request()->is('pembayaran_jo*') ||
                request()->is('pencairan_uang_jalan*') ||
                request()->is('pencairan_operasional*') ||
                request()->is('biaya_operasional*') ||
                request()->is('pembayaran_sdt*') ||
                request()->is('pencairan_komisi_driver*')||
                request()->is('klaim_supir*')||
                request()->is('tagihan_rekanan*')||
                request()->is('tagihan_pembayaran*')||
                request()->is('pencairan_komisi_customer*')||
                request()->is('transaksi_lain*')||
                request()->is('tagihan_pembelian*')||
                request()->is('transfer_dana*')||
                request()->is('pembayaran_gaji*')||
                request()->is('cetak_uang_jalan*')||
                request()->is('karyawan_hutang*')
                ? 'menu-is-opening menu-open' : '' }}">
                <a href="#" class="nav-link hover-item" style="font-weight: 700;font-size: 15px;">
                  <i class="nav-icon fas fa-dollar-sign"></i>
                  <p>FINANCE 
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                @can('READ_BIAYA_OPERASIONAL')
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
                @endcan
                @can('READ_KLAIM_SUPIR')
                  <ul class="nav nav-treeview">
                    <li class="nav-item">
                      <a href="{{route('klaim_supir.index')}}" class="nav-link {{request()->is('klaim_supir*')? ' active' : '' }} " style="font-weight: 500;">
                      <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                        <p>
                          Klaim Supir
                        </p>
                      </a>
                    </li>
                  </ul>
                @endcan

                  <ul class="nav nav-treeview">
                    <li class="nav-item   {{ 
                      request()->is('pembayaran_jo*') ||
                      request()->is('pembayaran_sdt*')||
                      request()->is('pembayaran_gaji*')
                      ? 'menu-is-opening menu-open' : '' }}" style="font-size: 15px;">
                      <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p style="font-weight: 500;" >
                          Pembayaran
                          <i class="right fas fa-angle-left" style="font-size: 15px;"></i>
                        </p>
                      </a>
                      @can('READ_PEMBAYARAN_JO')
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
                      @endcan
                      @can('READ_PEMBAYARAN_SDT')
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
                      @endcan
                      @can('READ_PEMBAYARAN_GAJI')
                        <ul class="nav nav-treeview">
                          <li class="nav-item">
                            <a href="{{route('pembayaran_gaji.index')}}" class="nav-link {{request()->url() === route('pembayaran_gaji.index')? ' active' : '' }} " style="font-weight: 500;">
                            <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                              <p>
                                Pembayaran Gaji
                              </p>
                            </a>
                          </li>
                        </ul>
                      @endcan
                      
                    </li>
                  
                  </ul>

                  <ul class="nav nav-treeview">
                    <li class="nav-item   {{ 
                      request()->is('pencairan_uang_jalan*')||
                      request()->is('cetak_uang_jalan*') ||
                      request()->is('pencairan_komisi_driver*')||
                      request()->is('pencairan_komisi_customer*')
                      ? 'menu-is-opening menu-open' : '' }}" style="font-size: 15px;">
                      <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p style="font-weight: 500;" >
                          Pencairan
                          <i class="right fas fa-angle-left" style="font-size: 15px;"></i>
                        </p>
                      </a>
                        <ul class="nav nav-treeview">
                    @php
                        $canReadPencairanUJFTL = auth()->user()->can('READ_PENCAIRAN_UJ_FTL');
                        $canReadPencairanUJLTL = auth()->user()->can('READ_PENCAIRAN_UJ_LTL');
                    @endphp
                    <li class="nav-item   {{ 
                      request()->is('pencairan_uang_jalan*') ||
                      request()->is('pencairan_uang_jalan_ltl*')||
                      request()->is('cetak_uang_jalan*')
                      ? 'menu-is-opening menu-open' : '' }}" style="font-size: 15px;">
                      <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p style="font-weight: 500;" >
                          Pencairan UJ
                          <i class="right fas fa-angle-left" style="font-size: 15px;"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                    
                      @if ($canReadPencairanUJFTL || $canReadPencairanUJLTL)
                        @can('READ_PENCAIRAN_UJ_FTL')
                        <li class="nav-item">
                          <a href="{{route('pencairan_uang_jalan.index')}}" class="nav-link {{ request()->url() === route('pencairan_uang_jalan.index')? ' active' : '' }} " style="font-weight: 500;">
                            <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                            <p>
                              FTL
                            </p>
                          </a>
                        </li>
                        @endcan
                        @can('READ_PENCAIRAN_UJ_LTL')
                        <li class="nav-item">
                          <a href="{{route('pencairan_uang_jalan_ltl.index')}}" class="nav-link {{request()->url() === route('pencairan_uang_jalan_ltl.index')? ' active' : '' }} " style="font-weight: 500;">
                            <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                            <p>
                              LTL
                            </p>
                          </a>
                        </li>
                        @endcan
                      @endif
                      @can('READ_CETAK_UJ')
                        <li class="nav-item">
                          <a href="{{route('cetak_uang_jalan.index')}}" class="nav-link {{request()->url() === route('cetak_uang_jalan.index')? ' active' : '' }} " style="font-weight: 500;">
                          <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                            <p>
                              Cetak UJ
                            </p>
                          </a>
                        </li>
                      @endcan
                      </ul>
                    </li>
                  
                  </ul>
                  @php
                      $canReadPencairanKomisiCustomer = auth()->user()->can('READ_PENCAIRAN_KOMISI_CUSTOMER');
                      $canReadPencairanKomisiDriver = auth()->user()->can('READ_PENCAIRAN_KOMISI_DRIVER');
                  @endphp
                  @if ($canReadPencairanKomisiCustomer || $canReadPencairanKomisiDriver)
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
                            @can('READ_PENCAIRAN_KOMISI_CUSTOMER')
                            <li class="nav-item">
                              <a href="{{route('pencairan_komisi_customer.index')}}" style="font-weight: 500;" class="nav-link {{ request()->is('pencairan_komisi_customer*')? ' active' : '' }} ">
                              <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                                <p>
                                  Customer
                                </p>
                              </a>
                            </li>
                            @endcan
                            @can('READ_PENCAIRAN_KOMISI_DRIVER')
                            <li class="nav-item">
                              <a href="{{route('pencairan_komisi_driver.index')}}" class="nav-link {{request()->is('pencairan_komisi_driver') ||  request()->is('pencairan_komisi_driver/create') || request()->is('pencairan_komisi_driver/*/edit') ? ' active' : '' }} " style="font-weight: 500;">
                              <i class="far fa-dot-circle nav-icon" style="font-size: 15px;"></i>
                                <p>
                                  Driver
                                </p>
                              </a>
                            </li>
                            @endcan

                          </ul>
                        </li>
                      </ul>
                  @endif
                  </li>
                </ul>
                @php
                    $canReadTagihanRekanan = auth()->user()->can('READ_TAGIHAN_REKANAN');
                    $canReadTagihanPembelian = auth()->user()->can('READ_TAGIHAN_PEMBELIAN');
                @endphp
                @if ($canReadTagihanRekanan || $canReadTagihanPembelian)
                  <ul class="nav nav-treeview">
                    <li class="nav-item   {{ 
                    request()->is('tagihan_pembelian*')||
                    request()->is('tagihan_rekanan*')
                    ? 'menu-is-opening menu-open' : '' }}" style="font-size: 15px;">
                      <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p style="font-weight: 500;" >
                          Tagihan
                          <i class="right fas fa-angle-left" style="font-size: 15px;"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        @can('READ_TAGIHAN_REKANAN')
                        <li class="nav-item">
                          <a href="{{route('tagihan_rekanan.index')}}" class="nav-link {{ request()->is('tagihan_rekanan*')? ' active' : '' }} " style="font-weight: 500;">
                          <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                            <p>
                              <span >Tagihan Rekanan</span> {{-- style="font-size: 13.9px" --}}
                            </p>
                          </a>
                        </li>
                        @endcan
                        @can('READ_TAGIHAN_PEMBELIAN')
                        <li class="nav-item">
                          <a href="{{route('tagihan_pembelian.index')}}" class="nav-link {{ request()->is('tagihan_pembelian*')? ' active' : '' }} " style="font-weight: 500;">
                          <i class="far fa-circle nav-icon"></i>
                            <p>
                              Tagihan <span style="font-size: 0.9em;">Pembelian</span> {{-- style="font-size: 13.9px" --}}
                            </p>
                          </a>
                        </li>
                        @endcan
                      </ul>
                    </li>
                  </ul>
                @endif

                @can('READ_TRANSAKSI_NON_OPERASIONAL')
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{route('transaksi_lain.index')}}" class="nav-link {{ request()->is('transaksi_lain*')? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                      <p>
                        <span style="font-size: 0.78em;">Transaksi Non Operasional</span> {{-- style="font-size: 13.9px" --}}
                      </p>
                    </a>
                  </li>
                </ul>
                @endcan
                @can('READ_KARYAWAN_HUTANG')
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{route('karyawan_hutang.index')}}" class="nav-link {{request()->is('karyawan_hutang') || request()->is('karyawan_hutang/*/edit') ? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                      <p>
                        Karyawan Hutang
                      </p>
                    </a>
                  </li>
                </ul>
                @endcan
                @can('READ_TRANSFER_DANA')
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{route('transfer_dana.index')}}" class="nav-link {{ request()->is('transfer_dana*')? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                      <p>
                        <span >Transfer Dana </span> {{-- style="font-size: 13.9px" --}}
                      </p>
                    </a>
                  </li>
                </ul>
                @endcan
              </li>
            @endif
            @php
              $invoice = [
                'READ_BELUM_INVOICE',
                'READ_CETAK_INVOICE',
                'READ_PEMBAYARAN_INVOICE',
                'READ_BUKTI_POTONG',
                'READ_PEMUTIHAN_INVOICE',
                'READ_INVOICE_KARANTINA',
                'READ_PEMBAYARAN_INVOICE_KARANTINA',
            ];
            @endphp
            @if (array_intersect($userAkses, $invoice) != NULL)
              {{-- @if (in_array($user_role, [1,2,3])) --}}
              {{-- INVOICE --}}
              <li class="nav-item {{ request()->is('belum_invoice*') ||
                request()->is('pembayaran_invoice') ||
                request()->is('pembayaran_invoice.*') ||
                request()->is('pembayaran_invoice_karantina*') ||
                request()->is('invoice_karantina*') ||
                request()->is('bukti_potong*') ||
                request()->is('cetak_invoice*') ||
                request()->is('pemutihan_invoice*') 

                  ? 'menu-is-opening menu-open' : '' }}">
                <a href="#" class="nav-link hover-item" style="font-weight: 700;font-size: 15px;">
                  <i class="fas nav-icon fa-solid fa-file-invoice"></i>
                  <p>INVOICE
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @php
                    $READ_BELUM_INVOICE = auth()->user()->can('READ_BELUM_INVOICE');
                    $READ_CETAK_INVOICE = auth()->user()->can('READ_CETAK_INVOICE');
                    $READ_PEMBAYARAN_INVOICE = auth()->user()->can('READ_PEMBAYARAN_INVOICE');
                    $READ_BUKTI_POTONG = auth()->user()->can('READ_BUKTI_POTONG');
                    $READ_PEMUTIHAN_INVOICE = auth()->user()->can('READ_PEMUTIHAN_INVOICE');
                @endphp
                @if($READ_BELUM_INVOICE || $READ_CETAK_INVOICE|| 
                    $READ_PEMBAYARAN_INVOICE|| $READ_BUKTI_POTONG|| $READ_PEMUTIHAN_INVOICE)
                    <li class="nav-item   {{ 
                      request()->is('pembayaran_invoice') ||
                      request()->is('pembayaran_invoice.*') ||
                      request()->is('belum_invoice') ||
                      request()->is('belum_invoice.*') ||
                      request()->is('bukti_potong*')||
                      request()->is('cetak_invoice*') ||
                      request()->is('pemutihan_invoice*') 
                      ? 'menu-is-opening menu-open' : '' }}" style="font-size: 15px;">
                      <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p style="font-weight: 500;" >
                          Invoice Trucking
                          <i class="right fas fa-angle-left" style="font-size: 15px;"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        @can('READ_BELUM_INVOICE')
                        <li class="nav-item">
                          <a href="{{route('belum_invoice.index')}}" class="nav-link {{ request()->is('belum_invoice') || request()->is('belum_invoice.*')? ' active' : '' }} " style="font-weight: 500;">
                          <i class="nav-icon fas fa-pencil-alt " style="font-size: 15px;"></i>
                            <p>
                              Belum Invoice
                            </p>
                          </a>
                        </li>
                        @endcan
                        @can('READ_CETAK_INVOICE')
                        <li class="nav-item">
                          <a href="{{route('cetak_invoice.index')}}" class="nav-link {{request()->url() === route('cetak_invoice.index')? ' active' : '' }} " style="font-weight: 500;">
                          <i class="nav-icon fas fa-print " style="font-size: 15px;"></i>
                            <p>
                              Cetak Invoice
                            </p>
                          </a>
                        </li>
                        @endcan
                        @can('READ_PEMBAYARAN_INVOICE')

                        <li class="nav-item">
                          <a href="{{route('pembayaran_invoice.index')}}" class="nav-link {{ request()->is('pembayaran_invoice.*') || request()->is('pembayaran_invoice')? ' active' : '' }} " style="font-weight: 500;">
                          <i class="nav-icon fas fa-money-bill-wave" style="font-size: 15px;"></i>
                            <p>
                              <span style="font-size: 0.9em;">Pembayaran Invoice</span>
                            </p>
                          </a>
                        </li>
                        @endcan
                        @can('READ_BUKTI_POTONG')
                        <li class="nav-item">
                          <a href="{{route('bukti_potong.index')}}" class="nav-link {{ request()->is('bukti_potong*')? ' active' : '' }} " style="font-weight: 500;">
                          <i class="nav-icon fas fa-file" style="font-size: 15px;"></i>
                            <p>
                              Input Bukti Potong
                            </p>
                          </a>
                        </li>
                        @endcan
                        @can('READ_PEMUTIHAN_INVOICE')
                        <li class="nav-item">
                          <a href="{{route('pemutihan_invoice.index')}}" class="nav-link {{ request()->is('pemutihan_invoice.*') || request()->is('pemutihan_invoice')? ' active' : '' }} " style="font-weight: 500;">
                          <i class="nav-icon fas fa-file-invoice" style="font-size: 15px;"></i>
                            <p>
                              <span >Pemutihan Invoice</span>
                            </p>
                          </a>
                        </li>
                        @endcan
                      </ul>
                    </li>
                @endif
                @php
                    $READ_INVOICE_KARANTINA = auth()->user()->can('READ_INVOICE_KARANTINA');
                    $READ_PEMBAYARAN_INVOICE_KARANTINA = auth()->user()->can('READ_PEMBAYARAN_INVOICE_KARANTINA');
                @endphp
                @if($READ_INVOICE_KARANTINA || $READ_PEMBAYARAN_INVOICE_KARANTINA)
                  <li class="nav-item   {{ 
                    request()->is('invoice_karantina*') ||
                    request()->is('pembayaran_invoice_karantina*') 
                    ? 'menu-is-opening menu-open' : '' }}" style="font-size: 15px;">
                    <a href="#" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p style="font-weight: 500;" >
                        Invoice Karantina
                        <i class="right fas fa-angle-left" style="font-size: 15px;"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      @can('READ_INVOICE_KARANTINA')
                      <li class="nav-item">
                        <a href="{{route('invoice_karantina.index')}}" class="nav-link {{request()->is('invoice_karantina*')? ' active' : '' }} " style="font-weight: 500;">
                        <i class="nav-icon fas fa-pencil-alt " style="font-size: 15px;"></i>
                          <p>
                            Belum Invoice
                          </p>
                        </a>
                      </li>
                      @endcan
                      @can('READ_PEMBAYARAN_INVOICE_KARANTINA')
                      <li class="nav-item">
                        <a href="{{route('pembayaran_invoice_karantina.index')}}" class="nav-link {{ request()->is('pembayaran_invoice_karantina*')? ' active' : '' }} " style="font-weight: 500;">
                        <i class="nav-icon fas fa-money-bill-wave" style="font-size: 15px;"></i>
                          <p>
                            <span style="font-size: 0.9em;">Pembayaran Invoice</span>
                          </p>
                        </a>
                      </li>
                      @endcan
                    </ul>
                  </li>
                @endif
                </ul>
                
              </li>
            @endif
            @php
            $revisi = [
                'READ_REVISI_TL',
                'READ_REVISI_UANG_JALAN',
                'READ_REVISI_BIAYA_OPERASIONAL',
                'READ_REVISI_KLAIM_SUPIR',
                'READ_REVISI_INVOICE_TRUCKING',
                'READ_REVISI_TAGIHAN_REKANAN',
                'READ_REVISI_TAGIHAN_PEMBELIAN',
                'READ_REVISI_PEMBAYARAN_INVOICE',
            ];
            @endphp
            @if (array_intersect($userAkses, $revisi) != NULL)
              {{-- REVISI --}}
              <li class="nav-item {{ request()->is('revisi_uang_jalan*') ||  
                                  request()->is('revisi_tl*')||
                                  request()->is('revisi_tagihan_rekanan*')||
                                  request()->is('revisi_tagihan_pembelian*')||
                                  request()->is('revisi_invoice_trucking*')||
                                  request()->is('revisi_biaya_operasional*')||
                                  request()->is('revisi_klaim_supir*')
                                  ? 'menu-is-opening menu-open' : '' }}">
                <a href="#" class="nav-link hover-item" style="font-weight: 700;font-size: 15px;">
                  <i class="fas nav-icon fa-solid fa fa-undo"></i>
                  <p>Revisi
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>

                <ul class="nav nav-treeview">
                  @can('READ_REVISI_TL')
                  <li class="nav-item">
                    <a href="{{route('revisi_tl.index')}}" class="nav-link {{request()->is('revisi_tl*')? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                      <p>
                        Revisi TL
                      </p>
                    </a>
                  </li>
                  @endcan
                  @can('READ_REVISI_UANG_JALAN')
                  <li class="nav-item">
                    <a href="{{route('revisi_uang_jalan.index')}}" class="nav-link {{request()->url() === route('revisi_uang_jalan.index')? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                      <p>
                        Rev. Uang Jalan
                      </p>
                    </a>
                  </li>
                  @endcan
                  @can('READ_REVISI_BIAYA_OPERASIONAL')
                  <li class="nav-item">
                    <a href="{{route('revisi_biaya_operasional.index')}}" class="nav-link {{ request()->is('revisi_biaya_operasional*')? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                      <p>
                        <span style="font-size: 0.9em;">Rev. Biaya Operasional</span>
                      </p>
                    </a>
                  </li>
                  @endcan
                  @can('READ_REVISI_KLAIM_SUPIR')
                  <li class="nav-item">
                    <a href="{{route('klaim_supir_revisi.index')}}" class="nav-link {{request()->url() === route('klaim_supir_revisi.index')? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                      <p>
                        <span style="font-size: 0.9em;">Rev. Klaim Supir</span>
                      </p>
                    </a>
                  </li>
                  @endcan
                  {{-- <li class="nav-item">
                    <a href="#" class="nav-link {{request()->url() === route('invoice.index')? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                      <p>
                        <span style="font-size: 0.9em;">Rev. Biaya Operasional</span>
                      </p>
                    </a>
                  </li> --}}
                  @can('READ_REVISI_INVOICE_TRUCKING')
                  <li class="nav-item">
                    <a href="{{route('revisi_invoice_trucking.index')}}" class="nav-link {{ request()->is('revisi_invoice_trucking*')? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i> 
                      <p>
                        <span style="font-size: 0.95em;">Rev. Invoice Trucking</span>
                      </p>
                    </a>
                  </li>
                  @endcan
                  @php
                      $READ_REVISI_TAGIHAN_REKANAN = auth()->user()->can('READ_REVISI_TAGIHAN_REKANAN');
                      $READ_REVISI_TAGIHAN_PEMBELIAN = auth()->user()->can('READ_REVISI_TAGIHAN_PEMBELIAN');
                  @endphp
                  @if($canReadPencairanUJFTL || $canReadPencairanUJLTL)
                    <li class="nav-item {{ 
                      request()->is('revisi_tagihan_rekanan*') ||
                      request()->is('revisi_tagihan_pembelian*')
                      ? 'menu-is-opening menu-open' : '' }}" style="font-size: 15px;">
                      <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p style="font-weight: 500;" >
                          Revisi Tagihan
                          <i class="right fas fa-angle-left" style="font-size: 15px;"></i>
                        </p>
                      </a>
                      <ul class="nav nav-treeview">
                        @can('READ_REVISI_TAGIHAN_REKANAN')
                        <li class="nav-item">
                          <a href="{{route('revisi_tagihan_rekanan.index')}}" class="nav-link {{ request()->is('revisi_tagihan_rekanan*')? ' active' : '' }} " style="font-weight: 500;">
                          <i class="far fa-circle nav-icon" style="font-size: 15px;"></i> 
                            <p>
                              <span style="font-size: 0.85em;">Invc. Tagihan Rekanan</span>
                            </p>
                          </a>
                        </li>
                        @endcan
                        @can('READ_REVISI_TAGIHAN_PEMBELIAN')
                        <li class="nav-item">
                          <a href="{{route('revisi_tagihan_pembelian.index')}}" class="nav-link {{ request()->is('revisi_tagihan_pembelian*')? ' active' : '' }} " style="font-weight: 500;">
                          <i class="far fa-circle nav-icon" style="font-size: 15px;"></i> 
                            <p>
                              <span style="font-size: 0.81em;">Invc. Tagihan Pembelian</span>
                            </p>
                          </a>
                        </li>
                        @endcan
                      </ul>
                    </li>
                  @endif
                  {{-- @can('READ_REVISI_PEMBAYARAN_INVOICE') --}}
                  {{-- <li class="nav-item">
                    <a href="#" class="nav-link {{request()->url() === route('invoice.index')? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                      <p>
                        <span style="font-size: 0.80em;">Rev. Pembayaran Invoice</span>
                      </p>
                    </a>
                  </li> --}}
                  {{-- @endcan --}}
                </ul>
              </li>
            @endif

            {{-- <li class="nav-item {{ request()->is('pembayaran_gaji*') ||
                request()->is('karyawan_hutang*') ? 'menu-is-opening menu-open' : '' }}">
              <a href="#" class="nav-link hover-item" style="font-weight: 700;font-size: 15px;">
                <i class="nav-icon fas fa-solid fa-user"></i>
                <p>HRD
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
            </li> --}}
            {{-- @php
                $READ_LAPORAN_KAS = auth()->user()->can('READ_LAPORAN_KAS');
                $READ_LAPORAN_BANK = auth()->user()->can('READ_LAPORAN_BANK');
            @endphp --}}
            @php
            $laporan = [
                'READ_LAPORAN_KAS',
                'READ_LAPORAN_BANK',
            ];
            @endphp
            {{-- @if($READ_LAPORAN_KAS || $READ_LAPORAN_BANK) --}}
            @if (array_intersect($userAkses, $laporan) != NULL)
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
                  @can('READ_LAPORAN_KAS')
                  <li class="nav-item">
                    <a href="{{route('laporan_kas.index')}}" class="nav-link {{request()->url() === route('laporan_kas.index')? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                      <p>
                        Laporan Kas
                      </p>
                    </a>
                  </li>
                  @endcan
                  @can('READ_LAPORAN_BANK')
                  <li class="nav-item">
                    <a href="{{route('laporan_bank.index')}}" class="nav-link {{request()->url() === route('laporan_bank.index')? ' active' : '' }} " style="font-weight: 500;">
                    <i class="far fa-circle nav-icon" style="font-size: 15px;"></i>
                      <p>
                        Laporan Bank
                      </p>
                    </a>
                  </li>
                  @endcan
                </ul>
              </li>
            @endif
            {{-- @endif --}}
          {{-- @endif --}}
        {{-- @endif --}}
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>