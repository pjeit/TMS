<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PencairanKomisiDriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $kasBank = DB::table('kas_bank')
        ->where('is_aktif', 'Y')
        ->orderBy('nama', 'asc')
        ->get();

        $dataDriver = DB::table('karyawan')
        ->where('is_aktif', 'Y')
        ->where('role_id', 5)//5 itu driver
        ->orderBy('nama_panggilan', 'asc')
        ->get();

        return view('pages.finance.pencairan_komisi_driver.index',[
            'judul' => "PENCAIRAN KOMISI DRIVER",
            'kasBank'=>$kasBank,
            'dataDriver'=>$dataDriver
        ]);
    }

    public function load_data(Request $request)
    {
        $tanggal_awal   = $request->input('tanggal_awal');
        $tanggal_akhir  = $request->input('tanggal_akhir');
        $karyawan  = $request->input('karyawan');

        $tanggal_awal_convert = date_create_from_format('d-M-Y', $tanggal_awal);
        $tanggal_akhir_convert = date_create_from_format('d-M-Y', $tanggal_akhir);

        try {
            $data = DB::table('sewa as s')
            ->select('s.*')
            ->whereNull('id_supplier') 
            ->where('s.is_aktif', '=', "Y")
            ->where('s.status_pencairan_driver', 'BELUM DICAIRKAN')
            ->where('s.id_karyawan', $karyawan)
            ->where('s.total_komisi_driver', '!=', 0)
            ->whereBetween('s.tanggal_berangkat', [date_format($tanggal_awal_convert, 'Y-m-d'), date_format($tanggal_akhir_convert, 'Y-m-d')])
            // ->whereRaw("CAST(s.tanggal_berangkat AS DATE) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'")
            // ->orderBy('s.tanggal_berangkat', 'DESC')
            ->get();
            return response()->json(['status'=>'success','data'=>$data]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status'=>'error','error'=>$th->getMessage()]);

        }


        


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
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function show(Sewa $sewa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function edit(Sewa $sewa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sewa $sewa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sewa  $sewa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sewa $sewa)
    {
        //
    }
}
