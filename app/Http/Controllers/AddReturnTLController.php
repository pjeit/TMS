<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceDetailAddcost;
use App\Models\Sewa;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\PDF; // use PDF;
use Exception;
use App\Models\PengaturanKeuangan;
use App\Helper\SewaDataHelper;
use App\Models\SewaBiaya;

class AddReturnTLController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        $data = DB::table('sewa as s')
            ->select('s.*', 'gt.nama_tujuan as nama_tujuan', 'k.nama_lengkap as nama_lengkap', 'c.nama as nama_customer')
            ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 's.id_grup_tujuan')
            ->leftJoin('karyawan as k', 'k.id', '=', 's.id_karyawan')
            ->leftJoin('customer as c', 'c.id', '=', 's.id_customer')
            ->where('gt.is_aktif', '=', "Y")
            ->where('s.is_aktif', '=', "Y")
            ->where('s.status', 'PROSES DOORING')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('pages.finance.add_return_TL.index',[
            'judul' => "ADD RETURN TL",
            'data' => $data,
        ]);
    }

    public function cair(Request $request, $id)
    {
        $id_sewa_default = $id;
        $pengaturan = PengaturanKeuangan::first();

        $sewa = Sewa::from('sewa AS s')
                    ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.jenis_tujuan', 'like', '%FTL%')
                    ->where('s.status', "PROSES DOORING")
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.id_sewa', $id)
                    ->groupBy('c.id')
                    ->first();
         $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();
        // dd($sewa);
        return view('pages.finance.add_return_TL.cair',[
            'judul' => "Pencairan TL",
            'sewa' => $sewa,
            'jumlah' => $pengaturan[$sewa['stack_tl']],
            'dataKas' => $dataKas,
            'id_sewa_defaulth' => $id_sewa_default,
        ]);
    }

    public function refund(Request $request, $id)
    {
        $id_sewa_default = $id;
        $pengaturan = PengaturanKeuangan::first();

        $sewa = Sewa::from('sewa AS s')
                    ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                    ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                    ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                    ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.jenis_tujuan', 'like', '%FTL%')
                    ->where('s.status', "PROSES DOORING")
                    ->where('s.is_aktif', '=', 'Y')
                    ->where('s.id_sewa', $id)
                    ->groupBy('c.id')
                    ->first();
         $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();
  $checkTL = SewaBiaya::where('is_aktif', 'Y')
                            ->where('deskripsi', 'TL')
                            ->where('id_sewa', $id)
                            ->first();
        // dd($pengaturan);

        return view('pages.finance.add_return_TL.refund',[
            'judul' => "Pengembalian TL",
            'sewa' => $sewa,
            'jumlah' => $pengaturan[$sewa['stack_tl']],
            'dataKas' => $dataKas,
            'id_sewa_defaulth' => $id_sewa_default,
            'checkTL'=>$checkTL
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
        $data_sewa = Sewa::where('is_aktif', 'Y')->findOrFail($id);
        $checkTL = SewaBiaya::where('is_aktif', 'Y')
                            ->where('deskripsi', 'TL')
                            ->where('id_sewa', $id)
                            ->first();

        return view('pages.finance.add_return_TL.edit',[
            'judul' => "Edit Trucking Order",
            'data' => $data_sewa,
            'checkTL' => $checkTL,
            'datajO' => SewaDataHelper::DataJO(),
            'dataCustomer' => SewaDataHelper::DataCustomer(),
            'dataDriver' => SewaDataHelper::DataDriver(),
            'dataKendaraan' => SewaDataHelper::DataKendaraan(),
            'dataChassis' => SewaDataHelper::DataChassis()
        ]);
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
        $data = $request->post();
        dd($data);
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

    public function getData($status)
     {
        // some logic to determine if the publisher is main
        
        if($status == 'Return TL'){
            return DB::table('sewa as s')
            ->select('s.*', 'gt.nama_tujuan as nama_tujuan', 'k.nama_lengkap as nama_lengkap', 'c.nama as nama_customer'
                    ,'s.stack_tl','sb.id_biaya', 'sb.deskripsi as isTL', 'sb.catatan as jenisTL', 'sb.is_aktif as TLAktif')
            ->leftJoin('sewa_biaya as sb', function($join){
                $join->on('sb.id_sewa', '=', 's.id_sewa')
                ->where('sb.deskripsi', 'TL')
                ->where('sb.is_aktif', 'Y')
                ->whereNull('s.stack_tl');
            })
            ->whereNull('s.stack_tl')
            ->whereNotNull('sb.id_biaya')
            ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 's.id_grup_tujuan')
            ->leftJoin('karyawan as k', 'k.id', '=', 's.id_karyawan')
            ->leftJoin('customer as c', 'c.id', '=', 's.id_customer')
            ->where('gt.is_aktif', '=', "Y")
            ->where('s.is_aktif', '=', "Y")
            ->where('s.status', 'PROSES DOORING')
            ->orderBy('created_at', 'DESC')
            ->get();
        }else if($status == 'Add TL'){
            return DB::table('sewa as s')
            ->select('s.*', 'gt.nama_tujuan as nama_tujuan', 'k.nama_lengkap as nama_lengkap', 'c.nama as nama_customer'
                    ,'s.stack_tl','sb.id_biaya', 'sb.deskripsi as isTL', 'sb.catatan as jenisTL', 'sb.is_aktif as TLAktif')
            ->leftJoin('sewa_biaya as sb', function($join){
                $join->on('sb.id_sewa', '=', 's.id_sewa')
                ->where('sb.deskripsi', 'TL')
                ->where('sb.is_aktif', 'Y')
                ->whereNotNull('s.stack_tl');
            })
            ->whereNotNull('s.stack_tl')
            ->whereNull('sb.id_biaya')
            ->leftJoin('grup_tujuan as gt', 'gt.id', '=', 's.id_grup_tujuan')
            ->leftJoin('karyawan as k', 'k.id', '=', 's.id_karyawan')
            ->leftJoin('customer as c', 'c.id', '=', 's.id_customer')
            ->where('gt.is_aktif', '=', "Y")
            ->where('s.is_aktif', '=', "Y")
            ->where('s.status', 'PROSES DOORING')
            ->orderBy('created_at', 'DESC')
            ->get();
        }else{
            return null;
        }

     }
}
