<?php

namespace App\Http\Controllers;

use App\Models\Head;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:READ_DASHBOARD', ['only' => ['index']]);
		// $this->middleware('permission:CREATE_DASHBOARD', ['only' => ['create','store']]);
		// $this->middleware('permission:EDIT_DASHBOARD', ['only' => ['edit','update']]);
		// $this->middleware('permission:DELETE_DASHBOARD', ['only' => ['destroy']]);  
    }

   
    function get_master_jadwal($minggu){
        $year = 0;
        if ($minggu < 1) {
            $year = -1;
            /*untuk ngecek jika pindah tahun.. diupdate RMA 7 Januari 2022*/
            $last_year = date('Y')-1;
            $lastweek = strtotime('12/31/'.$last_year);
            $maxweek = date('W',$lastweek);
            $minggu = $maxweek + $minggu;
        }
        $sql="  select  k.kendaraan_id, k.no_polisi, kw.panggilan, 
                        hdm.hari_ke, hdm.seqno,
                        case
                        when hdm.hari_ke = 1 then
                            'Senin'
                        when hdm.hari_ke = 2 then
                            'Selasa'
                        when hdm.hari_ke = 3 then
                            'Rabu'
                        when hdm.hari_ke = 4 then
                            'Kamis'
                        when hdm.hari_ke = 5 then
                            'Jumat'
                        when hdm.hari_ke = 6 then
                            'Sabtu'
                        when hdm.hari_ke = 0 then
                            'Minggu'
                        end nama_hari,
                        concat(s.nama_tujuan,' - ',c.kode) as nama_tujuan, s.status, s.dibuat_tanggal, s.total_uang_jalan
                from    kendaraan k
                        left join karyawan kw
                            on k.driver_id = kw.karyawan_id
                                and kw.is_aktif = 'Y'
                        join hari_dalam_minggu hdm
                            on 1=1
                        left join sewa s
                            on s.is_aktif = 'Y'
                                and k.kendaraan_id = s.kendaraan_id
                                and date_format(s.tanggal_berangkat,'%w') = hdm.hari_ke
                                and s.tanggal_berangkat 
                                    between str_to_date(concat(date_format(now(),'%Y') + $year,lpad($minggu,2,'0'),'1'),'%Y%u%w')
                                            and date_add(str_to_date(concat(date_format(now(),'%Y') + $year,lpad($minggu,2,'0'),'1'),'%Y%u%w'),interval 6 day)    
                        left join customer c
                            on s.customer_id = c.customer_id
                where   k.is_aktif = 'Y'
                        and k.supplier_id is null
                union all
                select  p.kendaraan_id, k.no_polisi, kw.panggilan, 
                        hdm.hari_ke, hdm.seqno,
                        case
                        when hdm.hari_ke = 1 then
                            'Senin'
                        when hdm.hari_ke = 2 then
                            'Selasa'
                        when hdm.hari_ke = 3 then
                            'Rabu'
                        when hdm.hari_ke = 4 then
                            'Kamis'
                        when hdm.hari_ke = 5 then
                            'Jumat'
                        when hdm.hari_ke = 6 then
                            'Sabtu'
                        when hdm.hari_ke = 0 then
                            'Minggu'
                        end nama_hari,
                        'Maintenance', 'Maintenance', p.dibuat_tanggal, 0
                from    perawatan p
                        join hari_dalam_minggu hdm
                            on 1=1
                        join
                        (select adddate(str_to_date('01011970','%d%m%Y'), t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date 
                            from (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
                                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
                                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
                                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
                                (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
                            ) v
                            on selected_date between p.tanggal_mulai and ifnull(p.tanggal_selesai,now())
                                and date_format(selected_date,'%w') = hdm.hari_ke
                            join kendaraan k
                            on p.kendaraan_id = k.kendaraan_id
                            left join karyawan kw
                            on k.driver_id = kw.karyawan_id
                                and kw.is_aktif = 'Y'
                where   p.is_aktif = 'Y'
                        and selected_date 
                        between str_to_date(concat(date_format(now(),'%Y') + $year,lpad($minggu,2,'0'),'1'),'%Y%u%w')
                                and date_add(str_to_date(concat(date_format(now(),'%Y') + $year,lpad($minggu,2,'0'),'1'),'%Y%u%w'),interval 6 day)
                order by 2,5,9";
        $result=DB::raw($sql);
        return
        array(
                'data'  =>$result['data'],
                'status'=>$result['status']
            );
    }
    
    function get_count($type){
        $icon="fas fa-chart-pie";
        if($type=='dokumen'){
            $icon='fas fa-bell';
            $text1='Pengingat';
            $text2='Dokumen';
            $sql="  select  count(*) as info
                    from    (
                                select  cd.id
                                from    chassis_dokumen cd
                                        join chassis c
                                            on cd.chassis_id = c.id
                                            and c.is_aktif = 'Y'
                                where   cd.is_aktif = 'Y'
                                        and cd.is_reminder = 'Y'
                                        and date_add(cd.berlaku_hingga,interval (-1 * cd.reminder_hari) day) <= now()
                                union all
                                select  kd.id
                                from    kendaraan_dokumen kd
                                        join kendaraan k
                                            on kd.kendaraan_id = k.id
                                            and k.is_aktif = 'Y'
                                where   kd.is_aktif = 'Y'
                                        and kd.is_reminder = 'Y'
                                        and date_add(kd.berlaku_hingga,interval (-1 * kd.reminder_hari) day) <= now()
                            ) a";
                            
        }elseif($type=='dalam_perjalanan'){
            $icon='fas fa-truck';
            $text1='Belum';
            $text2='dalam_perjalanan';
            $sql="  select  count(s.id_sewa) as info
                    from    sewa s
                            join customer c
                                on s.id_customer = c.id
                                join karyawan kw
                                on s.id_karyawan = kw.id
                        where   s.is_aktif = 'Y'
                                and s.is_kembali = 'N'
                                and s.tanggal_berangkat < CURRENT_DATE()
                                and (s.status = 'PROSES DOORING' or (s.status = 'MENUNGGU UANG JALAN' and s.total_uang_jalan = 0))";
                            
        }elseif($type=='status_kendaraan'){
            $icon='fas fa-wrench';
            $text1='status_kendaraan';
            $text2='status_kendaraan';
            $sql="  select  count(id) as info
                    from    (
                                select  k.id, k.no_polisi
                                from    status_kendaraan sk
                                        join kendaraan k
                                            on sk.kendaraan_id = k.id
                                            and k.is_aktif = 'Y'
                                where   sk.is_aktif = 'Y'
                                        and sk.is_selesai = 'N'
                                group by k.id, k.no_polisi
                            ) p";
        }elseif($type=='booking'){
            $icon='fas fa-book';
            $text1='booking';
            $text2='';
            $sql="  select  count(b.id) as info
                    from    booking b
                            left join sewa s
                                on b.id = s.id_booking
                                    and s.is_aktif = 'Y'
                                join customer c
                                on b.id_customer = c.id
                                    and c.is_aktif = 'Y'
                                join grup_tujuan gt
                                on b.id_grup_tujuan = gt.id
                                    and gt.is_aktif = 'Y'
                    where   b.is_aktif = 'Y' 
                            AND b.id_jo_detail is null
                            AND s.id_sewa is null
                            AND is_sewa = 'N'";
        }elseif($type=='belum_invoice'){
            $icon='fas fa-exclamation-circle';
            $text1='Belum';
            $text2='Invoice';
            $sql="  select  count(s.id_sewa) as info
                    from    sewa s
                            join customer c
                                on s.id_customer = c.id
                                left join invoice_detail ids
                                on s.id_sewa = ids.id_sewa
                                    and ids.is_aktif = 'Y'
                        where   s.is_aktif = 'Y'
                                and s.is_kembali = 'Y'
                                and ids.id is null
                                and s.total_tarif > 0";
                            
        }elseif($type=='pembayaran_invoice_jatuh_tempo'){
            $icon='fas fa-calendar';
            $text1='Cust.';
            $text2='Jatuh Tempo';
            $sql="  select  concat(ifnull(a.invoice_count,0),' (Rp ',FORMAT(ifnull(a.invoice_total,0), 0),')') info 
                    from    (   select  count(i.id) as invoice_count, 
                                        sum(i.total_tagihan - ifnull(i.total_dibayar,0)) as invoice_total
                                from    invoice i
                                        join customer c
                                        on i.billing_to = c.id
                                where   i.is_aktif = 'Y'
                                    -- and i.total_tagihan > i.total_dibayar
                                     and i.total_sisa > 0
                                        and i.jatuh_tempo <= now()
                            )a";
                            
        }elseif($type=='tagihan_pembelian'){
            $icon='fa fa-shopping-cart';
            $text1='Supp.';
            $text2='Jatuh tempo';
            $sql="  select  concat(ifnull(a.beli_count,0),' (Rp ',FORMAT(ifnull(a.beli_total,0), 0),')') info 
                    from    (   select  count(tp.id) as beli_count, 
                                        sum(tp.total_tagihan) as beli_total
                                from    tagihan_pembelian tp
                                        join supplier s
                                            on tp.id_supplier = s.id
                                    where   tp.is_aktif = 'Y'
                                        -- and tp.total_tagihan > (ifnull(tp.tagihan_dibayarkan,0)) 
                                        and tp.sisa_tagihan >0
                                            and date_add(tp.jatuh_tempo, interval -7 day) <= now()
                                )a";
                            
        }elseif($type=='menunggu_uang_jalan'){
            $icon='fas fa-money-bill-wave';
            $text1='Uang';
            $text2='Jalan';
            $sql="  select  concat(ifnull(a.jalan_count,0),' (Rp ',FORMAT(ifnull(a.total_jalan,0), 0),')') info 
                    from    (   select  count(s.id_sewa) as jalan_count, sum(s.total_uang_jalan) as total_jalan
                                from    sewa s
                                        join customer c
                                          on s.id_customer = c.id
                                        join grup_tujuan gt
                                          on s.id_grup_tujuan = gt.id
                                        join karyawan kw
                                          on s.id_karyawan = kw.id
                                where   s.is_aktif = 'Y'
                                        and s.total_uang_jalan > 0
                                        and s.status = 'MENUNGGU UANG JALAN'
                            )a";
                            
        }
        
        
        // $result=DB::select($sql) ;
        return 
        // DB::raw($sql)->first()
        array(
                'data'  =>DB::select($sql)[0],
                'text1'=>$text1,
                'text2'=>$text2,
                'icon'=>$icon,
                'link'=>$type
            );
    }
    
    function get_dasboard_data($tgl_minggu_awal,$tgl_minggu_akhir,$tambah_minggu=0,$kurang_minggu=0){
        try {
            if($tambah_minggu>0)
            {
                $tgl_minggu_awal = Carbon::parse($tgl_minggu_awal)->addWeeks($tambah_minggu);
                $tgl_minggu_akhir = Carbon::parse($tgl_minggu_akhir)->addWeeks($tambah_minggu);
            }
            else if($kurang_minggu>0)
            {
                $tgl_minggu_awal = Carbon::parse($tgl_minggu_awal)->subWeeks($kurang_minggu);
                $tgl_minggu_akhir = Carbon::parse($tgl_minggu_akhir)->subWeeks($kurang_minggu);
            }
            else
            {
                $tgl_minggu_awal = $tgl_minggu_awal;
                $tgl_minggu_akhir = $tgl_minggu_akhir;
            }

            $period = CarbonPeriod::create($tgl_minggu_awal, $tgl_minggu_akhir);
            $tanggal_semua = [];
            foreach ($period as $date) {
                // $tanggal_semua[] = $date->format('d-M-y');
                $tanggal_semua[] = $date->format('Y-m-d');
            }

            $data = Head::where('is_aktif','Y')
            // ->whereHas('get_sewa_dashboard', function ($query){
            //     $query->where('is_aktif', 'Y');
            // })
            ->with([
                'get_sewa_dashboard' => function ($query) use($tgl_minggu_awal,$tgl_minggu_akhir){
                    $query ->whereBetween('tanggal_berangkat', [
                                date('Y-m-d', strtotime($tgl_minggu_awal)),
                                date('Y-m-d', strtotime($tgl_minggu_akhir))
                            ])
                          ->where('is_aktif', 'Y')
                          ->whereNull('id_supplier')
                          ->with([
                              'getCustomer',
                              'getTujuan',
                          ]);
                }
            ])
            ->with([
                'get_maintenance_dashboard' => function ($query) use($tgl_minggu_awal,$tgl_minggu_akhir){
                    $query ->where('is_aktif', 'Y')
                          ->where('is_selesai', 'N');
                }
            ])
            ->with('get_driver_dashboard')
            ->with('get_driver_dashboard')
            ->get();
            return response()->json(['data' =>$data,
                                           'status' => 'success',
                                           'error' => null,
                                           'tgl_minggu_awal_convert' =>date('d-M-y', strtotime($tgl_minggu_awal)),
                                           'tgl_minggu_akhir_convert' =>date('d-M-y', strtotime($tgl_minggu_akhir)),
                                            'semua_tanggal'=>$tanggal_semua
                                        ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['data' =>null,'status' => 'error','error' => $th->getMessage()]);
        }
   
    }
    public function index()
    {
        // dd($this->get_count('kembali')['data']->info);
        // dd($this->get_count('dokumen'));
        $currentDate = Carbon::now();
        // Mendapatkan hari ini
        $currentDate->translatedFormat('l');
        // dd(Carbon::now()->translatedFormat('l'));
        $tanggal_senin_ini = Carbon::now()->startOfWeek();
        $tanggal_minggu_ini = Carbon::now()->endOfWeek();
        // $currentDate->subDay()->day;
        return view('pages.dashboard.index',[
            'judul'=>"DASHBOARD",
            'dokumen'=>$this->get_count('dokumen'),
            'dalam_perjalanan'=>$this->get_count('dalam_perjalanan'),
            'status_kendaraan'=>$this->get_count('status_kendaraan'),
            'booking'=>$this->get_count('booking'),
            'belum_invoice'=>$this->get_count('belum_invoice'),
            'pembayaran_invoice_jatuh_tempo'=>$this->get_count('pembayaran_invoice_jatuh_tempo'),
            'tagihan_pembelian'=>$this->get_count('tagihan_pembelian'),
            'menunggu_uang_jalan'=>$this->get_count('menunggu_uang_jalan'),
            // 'week_start'=> date("d-M-y", strtotime($tanggal_senin_ini->subDay(7))),
            // 'week_end'=>date("d-M-y", strtotime($tanggal_minggu_ini->subDay(7))),
            'week_start'=> date("Y-m-d", strtotime($tanggal_senin_ini)),
            'week_end'=>date("Y-m-d", strtotime($tanggal_minggu_ini)),
            'week_start_tampil'=> date("d-M-y", strtotime($tanggal_senin_ini)),
            'week_end_tampil'=>date("d-M-y", strtotime($tanggal_minggu_ini)),
            // 'week_start'=> $tanggal_senin_ini,
            // 'week_end'=>$tanggal_minggu_ini,
            'weeknumber'=>6,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    function load_data_setting($load){
        if($load=='setting'){
            $sql="  select  kbp.kode, kbp.kas_bank_id, kb.nama as nama_kas_bank, kbp.nominal
                    from    kas_bank_pengaturan kbp
                            left join kas_bank kb
                            on kbp.kas_bank_id = kb.kas_bank_id
                    where   kbp.is_aktif = 'Y'
                    order by seqno";
                    
        }elseif($load=='bank'){
            $sql="  select  kas_bank_id, nama
                    from    kas_bank
                    where   is_aktif = 'Y'
                    order by tipe, nama";
                    
        }
        return $sql;
    }
    
    function box_list($type,$limit=-1,$offset=-1,$order='',$dir_order=''){
        $limit_offset=$order_string='';
        
        if($limit!=-1 && $offset!=-1){
            $limit_offset="LIMIT $limit OFFSET $offset";
        }
        
        if($order!='' && $dir_order!=''){
            $order_string="order by ".($order+1)." $dir_order";
        }else{
            if($type=='dokumen'){
                $order_string="order by 2, 7 desc";
            }elseif($type=='kembali'){
                $order_string="order by 2, s.tanggal_berangkat, c.nama";                
            }elseif($type=='perawatan'){
                $order_string="order by 2, k.no_polisi";
            }elseif($type=='book'){
                $order_string="order by 2, b.tanggal_berangkat, b.no_booking";
            }elseif($type=='sewa'){
                $order_string="order by s.tanggal_kembali, c.nama, s.no_sewa";                
            }elseif($type=='jatuh'){
                $order_string="order by ai.jatuh_tempo, c.nama, ai.no_invoice";                
            }elseif($type=='tagihan'){
                $order_string="order by ab.jatuh_tempo, s.nama, ab.no_nota";                
            }elseif($type=='uang'){
                $order_string="order by 2, s.tanggal_berangkat, c.nama, s.no_sewa";
            }
        }
        
        if($type=='dokumen'){
            $sql="  select  ed.ekor_id id, ed.jenis, 
                            case
                            when k.ekor_id is null then
                                e.kode
                            else
                                concat(k.no_polisi,' (',e.kode,')')
                            end deskripsi, 
                            'Chassis' as tipe, ed.nomor,
                            date_format(ed.berlaku_hingga,'%d-%b-%Y') as berlaku_hingga,
                            datediff(now(), ed.berlaku_hingga) as hari
                    from    ekor_dokumen ed
                            join ekor e
                              on ed.ekor_id = e.ekor_id
                                 and e.is_aktif = 'Y'
                            left join kendaraan k
                              on e.ekor_id = k.ekor_id
                    where   ed.is_aktif = 'Y'
                            and ed.is_reminder = 'Y'
                            and date_add(ed.berlaku_hingga,interval (-1 * ed.reminder_hari) day) <= now()
                    union
                    select  kd.kendaraan_id id, kd.jenis,
                            k.no_polisi as deskripsi, 
                            'Head' as tipe, kd.nomor, 
                            date_format(kd.berlaku_hingga,'%d-%b-%Y') as berlaku_hingga,
                            datediff(now(), kd.berlaku_hingga) as hari
                    from    kendaraan_dokumen kd
                            join kendaraan k
                              on kd.kendaraan_id = k.kendaraan_id
                                 and k.is_aktif = 'Y'
                    where   kd.is_aktif = 'Y'
                            and kd.is_reminder = 'Y'
                            and date_add(kd.berlaku_hingga,interval (-1 * kd.reminder_hari) day) <= now()";
                            
        }elseif($type=='kembali'){
            $sql="  select  s.sewa_id id, concat(kw.panggilan,' (',kw.telp,')') as driver,
                            s.no_sewa, date_format(s.tanggal_berangkat,'%d-%b-%Y') as tanggal_berangkat, 
                            c.nama as nama_customer, s.nama_tujuan
                    from    sewa s
                            join customer c
                              on s.customer_id = c.customer_id
                            join karyawan kw
                              on s.driver_id = kw.karyawan_id
                    where   s.is_aktif = 'Y'
                            and s.is_kembali = 'N'
                            and s.tanggal_berangkat < CURRENT_DATE()
                            and (s.status = 'Released' or (s.status = 'Approved' and s.total_uang_jalan = 0))";  
                            
        }elseif($type=='perawatan'){
            $sql="  select  p.rawat_id id, date_format(p.tanggal_mulai,'%d-%b-%Y') tanggal_mulai, k.no_polisi,
                            if(length(p.detail_perawatan) > 30, concat(substr(p.detail_perawatan,1,30),'...'), p.detail_perawatan) as detail_perawatan
                    from    perawatan p
                            join kendaraan k
                              on p.kendaraan_id = k.kendaraan_id
                                 and k.is_aktif = 'Y'
                    where   p.is_aktif = 'Y'
                            and p.is_selesai = 'N'";
                    
        }elseif($type=='book'){
            $sql="  select  b.booking_id id, c.nama as nama_customer, 
                            b.no_booking, date_format(b.tanggal_berangkat,'%d-%b-%Y') as tanggal_berangkat,
                            ct.nama as nama_tujuan, b.catatan
                    from    booking b
                            left join sewa s
                              on b.booking_id = s.booking_id
                                 and s.is_aktif = 'Y'
                            join customer c
                              on b.customer_id = c.customer_id
                                 and c.is_aktif = 'Y'
                            join customer_tujuan ct
                              on b.tujuan_id = ct.tujuan_id
                                 and ct.is_aktif = 'Y'
                    where   b.is_aktif = 'Y'
                            and s.sewa_id is null";
                        
        }elseif($type=='sewa'){
            $sql="  select  s.sewa_id id, concat(s.no_sewa, '<br>', date_format(s.tanggal_berangkat,'%d-%b-%Y')) as sewa, 
                            date_format(s.tanggal_kembali,'%d-%b-%Y') as tanggal_kembali, 
                            concat(c.nama, '<br>', s.nama_tujuan) as customer,
                            format(s.total_tarif,'0') as total_tarif
                    from    sewa s
                            join customer c
                              on s.customer_id = c.customer_id
                            left join ar_invoice_detail aid
                              on s.sewa_id = aid.sewa_id
                                 and aid.is_aktif = 'Y'
                    where   s.is_aktif = 'Y'
                            and s.is_kembali = 'Y'
                            and aid.invoice_detail_id is null";  
                            
        }elseif($type=='jatuh'){
            $sql="  select  ai.invoice_id id, ai.no_invoice, date_format(ai.jatuh_tempo,'%d-%b-%Y') jatuh_tempo,
                            c.nama as nama_customer,  format(ai.total_tagihan,0) as total_tagihan,
                            format(ai.total_tagihan - ifnull(ai.total_bayar,0),0) as total_sisa
                    from    ar_invoice ai
                            join customer c
                              on ai.customer_id = c.customer_id
                    where   ai.is_aktif = 'Y'
                            and ai.total_tagihan > ai.total_bayar
                            and ai.jatuh_tempo <= now()";  
                            
        }elseif($type=='tagihan'){
            $sql="  select  ab.beli_id id, ab.no_nota, date_format(ab.jatuh_tempo,'%d-%b-%Y') jatuh_tempo, 
                            s.nama as nama_supplier, format(ab.total_nota,0) as total_nota,
                            concat(s.no_rekening,' (',s.nama_bank,')','<br>',s.atas_nama) as rekening
                    from    ap_beli ab
                            join supplier s
                              on ab.supplier_id = s.supplier_id
                            left join ap_bayar_nota abn
                              on ab.beli_id = abn.beli_id
                                 and abn.is_aktif = 'Y'
                    where   ab.is_aktif = 'Y'
                            and abn.bayar_nota_id is null
                            and ab.jatuh_tempo <= now()";   
                            
        }elseif($type=='uang'){
            $sql="  select  s.sewa_id id, concat(kw.panggilan, ' (', kw.telp,')') as driver, 
                            s.no_sewa, date_format(s.tanggal_berangkat,'%d-%b-%Y') as tanggal_berangkat,
                            c.nama as nama_customer, ct.nama as nama_tujuan,
                            format(s.total_uang_jalan,0) as total_uang_jalan
                    from    sewa s
                            join customer c
                              on s.customer_id = c.customer_id
                            join customer_tujuan ct
                              on s.tujuan_id = ct.tujuan_id
                            join karyawan kw
                              on s.driver_id = kw.karyawan_id
                    where   s.is_aktif = 'Y'
                            and s.total_uang_jalan > 0
                            and s.status = 'Approved'";
        }     
        
        $sql=$sql." ".$order_string." ".$limit_offset;
        $result=$sql;
        return
        array(
                'data'  =>$result['data'],
                'status'=>$result['status']
            );
    }
    public function Reset()
    {
        try {
            DB::transaction(function () {
                // Add your DELETE statements here
                // DB::statement('DELETE FROM booking');
                // DB::statement('DELETE FROM tagihan_pembelian');
                // DB::statement('DELETE FROM tagihan_pembelian_detail');
                // DB::statement('DELETE FROM tagihan_pembelian_pembayaran');
                // DB::statement('DELETE FROM tagihan_rekanan');
                // DB::statement('DELETE FROM tagihan_rekanan_detail');
                // DB::statement('DELETE FROM tagihan_rekanan_pembayaran');
                // DB::statement('DELETE FROM pencairan_komisi_detail');
                // DB::statement('DELETE FROM pencairan_komisi');
                // DB::statement('DELETE FROM trip_supir');
                // DB::statement('DELETE FROM sewa_operasional_pembayaran');
                // DB::statement('DELETE FROM sewa_biaya');
                // DB::statement('DELETE FROM sewa_operasional');
                // DB::statement('DELETE FROM sewa_biaya');
                // DB::statement('DELETE FROM sewa');
                // DB::statement('DELETE FROM job_order_detail_biaya');
                // DB::statement('DELETE FROM job_order_detail');
                // DB::statement('DELETE FROM job_order');
                // DB::statement('DELETE FROM jaminan');
                // DB::statement('DELETE FROM invoice');
                // DB::statement('DELETE FROM invoice_detail');
                // DB::statement('DELETE FROM invoice_detail_addcost');
                // DB::statement('DELETE FROM invoice_pembayaran');
                // DB::statement('DELETE FROM karyawan_hutang_transaction');
                // DB::statement('DELETE FROM uang_jalan_riwayat');
                // DB::statement('DELETE FROM sewa_batal_cancel');
                // DB::statement('DELETE FROM tagihan_rekanan');
                // DB::statement('DELETE FROM tagihan_rekanan_detail');
                // DB::statement('DELETE FROM tagihan_rekanan_pembayaran');
                // DB::statement('DELETE FROM karantina');
                // DB::statement('DELETE FROM karantina_detail');
                // DB::statement('DELETE FROM invoice_karantina');
                // DB::statement('DELETE FROM invoice_karantina_detail');
                // DB::statement('DELETE FROM invoice_karantina_pembayaran');
                // DB::statement('DELETE FROM klaim_supir');
                // DB::statement('DELETE FROM klaim_supir_riwayat');
                // DB::statement('DELETE FROM pemutihan_invoice');

            });
        } catch (\Exception $e) {
            // Handle or log the exception
            Log::error($e->getMessage());
        }
        
        return view('home', [
            'judul'=>'Home'
        ])->with(['status' => 'Success', 'msg' => 'Berhasil reset data']);

    }
}
