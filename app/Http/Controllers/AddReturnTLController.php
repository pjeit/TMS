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
        // dd($checkTL);

        return view('pages.finance.add_return_TL.refund',[
            'judul' => "Pengembalian TL",
            'sewa' => $sewa,
            'jumlah' => $pengaturan[$sewa['stack_tl']],
            'dataKas' => $dataKas,
            'id_sewa_defaulth' => $id_sewa_default,
            'checkTL'=>$checkTL,
            'id'=>$id
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
        $user = Auth::user()->id;
        $data = $request->post();
        try {
            //code...
            DB::table('sewa_biaya')
                ->insert(array(
                'id_sewa' =>  $data['id_sewa_defaulth'],
                'deskripsi' => 'TL',
                'biaya' => (float)str_replace(',', '', $data['jumlah']),
                'catatan' => $data['stack_tl_hidden_value'],//value combobox
                'created_at' => now(),
                'created_by' => $user,
                'is_aktif' => "Y",
                )
            ); 
            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
            array(
                $data['pembayaran'],// id kas_bank dr form
                date_create_from_format('d-M-Y', $data['tanggal_pencairan']),//tanggal
                0,// debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                (float)str_replace(',', '', $data['jumlah']), //uang keluar (kredit)
                1016, //kode coa
                'teluk_lamong',
                'UANG KELUAR # PENAMBAHAN TELUK LAMONG'.'#'.$data['no_sewa'].'#'.$data['kendaraan'].'('.$data['driver'].')'.'#'.$data['customer'].'#'.$data['tujuan'].'#'.$data['catatan'], //keterangan_transaksi
                $data['id_sewa_defaulth'],//keterangan_kode_transaksi
                $user,//created_by
                now(),//created_at
                $user,//updated_by
                now(),//updated_at
                'Y'
            ));

            
            $saldo = DB::table('kas_bank')
                ->select('*')
                ->where('is_aktif', '=', "Y")
                ->where('kas_bank.id', '=', $data['pembayaran'])
                ->first();

            $saldo_baru = $saldo->saldo_sekarang - (float)str_replace(',', '', $data['jumlah']);
            
            DB::table('kas_bank')
                ->where('id', $data['pembayaran'])
                ->update(array(
                    'saldo_sekarang' => $saldo_baru,
                    'updated_at'=> now(),
                    'updated_by'=> $user,
                )
            );
            return redirect()->route('add_return_tl.index')->with(['status' => 'Success', 'msg' => 'Sukses Menambah Biaya TL!!']);
                    
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->back()->withErrors($th->getMessage())->withInput();
            db::rollBack();

        }
          
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
        $user = Auth::user()->id;
        // dd($id);
        // dd($data);

        try {
            //code...
             DB::table('sewa_biaya')
                ->where('id_biaya', $data['id_sewa_biaya'])
                ->update(array(
                    'id_sewa' =>  $data['id_sewa_defaulth'],
                    'updated_at' => now(),
                    'updated_by' => $user,
                    'is_aktif' => "N",
                )
            );
            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
            array(
                $data['pembayaran'],// id kas_bank dr form
                date_create_from_format('d-M-Y', $data['tanggal_pengembalian']),//tanggal
                (float)str_replace(',', '', $data['jumlah']),// debit uang masuk
                0, //uang keluar (kredit) 0 soalnya kan ini uang MASUK, ga ada uang KELUAR
                1016, //kode coa
                'teluk_lamong',
                'UANG kembali # PENGEMBALIAN TELUK LAMONG'.'#'.$data['no_sewa'].'#'.$data['kendaraan'].'('.$data['driver'].')'.'#'.$data['customer'].'#'.$data['tujuan'].'#'.$data['catatan'], //keterangan_transaksi
                $data['id_sewa_defaulth'],//keterangan_kode_transaksi
                $user,//created_by
                now(),//created_at
                $user,//updated_by
                now(),//updated_at
                'Y'
            ));
             $saldo = DB::table('kas_bank')
                ->select('*')
                ->where('is_aktif', '=', "Y")
                ->where('kas_bank.id', '=', $data['pembayaran'])
                ->first();

            $saldo_baru = $saldo->saldo_sekarang + (float)str_replace(',', '', $data['jumlah']);
            
            DB::table('kas_bank')
                ->where('id', $data['pembayaran'])
                ->update(array(
                    'saldo_sekarang' => $saldo_baru,
                    'updated_at'=> now(),
                    'updated_by'=> $user,
                )
            );
            return redirect()->route('add_return_tl.index')->with(['status' => 'Success', 'msg' => 'Sukses Mengembalikan Biaya TL!!']);

        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->back()->withErrors($th->getMessage())->withInput();
            db::rollBack();

        }
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
                ->whereNotIn('s.stack_tl',['tl_teluk_lamong']);

            })
            ->whereNotIn('s.stack_tl',['tl_teluk_lamong'])
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
                ->where('s.stack_tl','like','%tl_teluk_lamong%');

            })
            ->where('s.stack_tl','like','%tl_teluk_lamong%')
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
