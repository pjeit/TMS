<?php

namespace App\Http\Controllers;

use App\Models\Karantina;
use App\Models\KasBankTransaction;
use App\Models\SewaOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RevisiBiayaOperasionalController extends Controller
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

        $dataKas = DB::table('kas_bank')
        ->select('*')
        ->where('is_aktif', '=', "Y")
        ->get();

        return view('pages.revisi.revisi_biaya_operasional.index',[
            'judul' => "Revisi Biaya Operasional",
            'dataKas' => $dataKas,
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
        $user = Auth::user()->id;
        $data = $request->collect();
        DB::beginTransaction(); 

        try {
            foreach ($data['data'] as $key => $value) {
                if($value['dicairkan'] != $value['dicairkan_old']){
                    $oprs = SewaOperasional::where('is_aktif', 'Y')->find($key);
                    if($oprs){
                        $oprs->total_dicairkan = $value['dicairkan_old'];
                        $oprs->updated_by = $user;
                        $oprs->updated_at = now(); 
                        if($oprs->save()){
                            $history = KasBankTransaction::where('is_aktif', 'Y')
                                                            ->where('jenis', 'pencairan_operasional')
                                                            ->where('keterangan_kode_transaksi', $oprs->id)
                                                            ->first();
                            if($history){
                                $history->keterangan_transaksi = 'REVISI OFF - ' + $history->keterangan_transaksi;
                                $history->updated_by = $user;
                                $history->updated_at = now();
                                $history->is_aktif = 'N';
                                if($history->save()){
                                    
                                }
                            }
                        }
                    }


                }
            }

            DB::commit();
            return redirect()->route('controller.method')->with(['status' => 'Success', 'msg'  => 'Pembayaran berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('controller.method')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
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

    public function load_data($item){
        try {
            if($item == 'KARANTINA'){
                $data = Karantina::where('is_aktif', 'Y')->where('total_dicairkan', NULL)->with('details', 'getJO', 'getCustomer.getGrup')->get();
            }else{
                $data = SewaOperasional::where('is_aktif', 'Y')
                                            ->with('getSewa.getTujuan.getGrup')
                                            ->with('getSewa.getCustomer')
                                            ->with('getSewa.getSupplier')
                                            ->whereHas('getSewa', function ($query) {
                                                $query->where('status', 'MENUNGGU INVOICE');
                                            })
                                            ->where('deskripsi', $item)
                                            ->get();

            }
            
            return response()->json(["result" => "success",'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(["result" => "error", 'message' => $th->getMessage()], 500);
        }
    }
}
