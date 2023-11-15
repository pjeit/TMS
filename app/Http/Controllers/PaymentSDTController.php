<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Helper\VariableHelper;
use App\Models\JobOrder;
use App\Models\JobOrderDetail;
use App\Helper\CoaHelper;
class PaymentSDTController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplier = DB::table('supplier')
            ->select('*')
            ->where('supplier.is_aktif', '=', "Y")
            ->where('jenis_supplier_id', 6) // jenis pelayaran
            ->orderBy('nama')
            ->get();

        // $customer = DB::table('customer')
        //     ->select('*')
        //     ->where('customer.is_aktif', "Y")
        //     ->orderBy('nama')
        //     ->get();

        $customer = DB::table('job_order_detail_biaya as jodb')
                    ->leftJoin('job_order as jo', 'jodb.id_jo', '=', 'jo.id')
                    ->leftJoin('customer as c', 'c.id', '=', 'jo.id_customer')
                    ->where('jodb.is_aktif', "Y")
                    ->where('jodb.status_bayar', "MENUNGGU PEMBAYARAN")
                    ->where('c.is_aktif', "Y")
                    ->select('c.id as id', DB::raw("CONCAT('[',c.kode,'] ', c.nama) as nama"))
                    ->distinct()
                    ->get();

        // dd($customer);  

        return view('pages.finance.pembayaran_SDT.index',[
            'judul' => "Pembayaran Storage / Demurage / Detention",
            'supplier' => $supplier,
            'customer' => $customer,
            'dataJO' => null,
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
      
        $biaya = DB::table('job_order_detail_biaya as jodb')
            ->select('jodb.*', 'jod.no_kontainer')
            ->leftJoin('job_order_detail as jod', 'jod.id', '=', 'jodb.id_jo_detail')
            ->where('jodb.is_aktif', '=', "Y")
            ->where('jodb.status_bayar', 'like', "%MENUNGGU PEMBAYARAN%") //TIMOTHY EDIT INI
            ->where('jod.is_aktif', '=', "Y")
            ->where('jod.id_jo', "$id")
            ->get();

        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();

        $TotalBiayaRev = 0;
        $pembayaran_jo = 0;

        $JO = JobOrder::where('is_aktif', 'Y')->find($id);
        $data['JO'] = $JO;
        $data['biaya'] = $biaya;

        return view('pages.finance.pembayaran_SDT.edit',[
            'judul'=>"Pembayaran Storage / Demurage / Detention",
            'data'=> $data,
            'pembayaran_jo' =>$pembayaran_jo,
            'dataKas'=>$dataKas,
            'TotalBiayaRev'=>$TotalBiayaRev
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
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        // dd(implode('-', $data['no_kontainer']));
        // dd($data);
        try {
            $saldo = DB::table('kas_bank')
                ->select('*')
                ->where('is_aktif', '=', "Y")
                ->where('kas_bank.id', '=', $data['pembayaran'])
                ->get();
         
            DB::table('job_order_detail_biaya')
                ->whereIn('id', $data['array_id'])
                ->update(array(
                    'status_bayar' => 'SELESAI PEMBAYARAN',
                    'catatan'=>$data['catatan'],
                    'updated_at'=> now(),
                    'updated_by'=> $user,
                    'is_aktif' => "Y",
                )
            );

            $saldo_baru = $saldo[0]->saldo_sekarang - ($data['total']);

            DB::table('kas_bank')
                ->where('id', $data['pembayaran'])
                ->update(array(
                    'saldo_sekarang' => $saldo_baru,
                    'updated_at'=> now(),
                    'updated_by'=> $user,
                )
            );
           
            DB::select('CALL InsertTransaction(?,?,?,?,?,?,?,?,?,?,?,?,?)',
                array(
                    $data['pembayaran'], // id kas_bank dr form
                    now(), // tanggal
                    0, // debit 0 soalnya kan ini uang keluar, ga ada uang masuk
                    $data['total'], // uang keluar (kredit)
                    1013, //kode coa
                    'uang_SDT',
                    'UANG KELUAR # BAYAR S/D/T'.'# BL:'.$data['no_bl'].'# PENGIRIM:'.$data['pengiriman'].'# PELAYARAN:'.$data['pelayaran'].'# NO.KONTAINER:'.'('.implode('-', $data['no_kontainer']).')'.'# CATATAN :'.$data['catatan'], // keterangan_transaksi
                    $data['no_bl'],//keterangan_kode_transaksi
                    $user, // created_by
                    now(), // created_at
                    $user, // updated_by
                    now(), // updated_at
                    'Y'
                ) 
            );
            return redirect()->route('pembayaran_sdt.index')->with(['status' => 'Success', 'msg' => "Berhasil membayar SDT"]);
            
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
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

    public function load_data(Request $request){
        try {
            $data = $request->collect();
            $pengirim  = $data['pengirim'];
            $pelayaran   =  $data['pelayaran'];

            $dataJO = DB::table('job_order AS jo')
                    ->select('jo.*','jod.*','jo.status as statusJO','jod.status as statusDetail',
                             'c.kode AS kode', 'c.nama AS nama_cust', 's.nama AS nama_supp')
                    ->leftJoin('customer AS c', 'c.id', '=', 'jo.id_customer')
                    ->leftJoin('supplier AS s', 's.id', '=', 'jo.id_supplier')
                    ->join('job_order_detail AS jod', function($join) {
                            $join->on('jo.id', '=', 'jod.id_jo')
                            ->leftJoin('job_order_detail_biaya as jodb', 'jodb.id_jo_detail', '=', 'jod.id')
                            ->where('jodb.is_aktif',"Y")
                            ->where('jodb.status_bayar', "MENUNGGU PEMBAYARAN")
                            ->where('jod.is_aktif',"Y")
                            ->selectRaw('SUM(jodb.storage) AS tot_storage') // need to use selectRaw for aggregate values like this.
                            ->select('jod.*');
                    })
                    ->leftJoin('grup_tujuan AS gt', 'jod.id_grup_tujuan', '=', 'gt.id')
                    ->where('jo.is_aktif', '=', 'Y')
                    ->where('jo.status', "PROSES DOORING")
                    ->when(isset($pengirim), function($query) use ($pengirim){
                        return $query->where('id_customer', $pengirim);
                    })
                    ->when(isset($pelayaran), function($query) use ($pelayaran){
                        return $query->where('id_supplier', $pelayaran);
                    })
                    ->groupBy('jod.id_jo','jod.id')
                    ->get();

            return response()->json(["result" => "success",'data' => $dataJO], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error",'message' => $th->getMessage()], 500);
        }
       
    }
}
