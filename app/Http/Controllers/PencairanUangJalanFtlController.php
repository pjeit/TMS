<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use Illuminate\Http\Request;
  use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helper\VariableHelper;
class PencairanUangJalanFtlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
      
        $sewa = DB::table('sewa AS s')
                        ->select('s.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                        ->leftJoin('customer AS c', 'c.id', '=', 's.id_customer')
                        ->leftJoin('grup_tujuan AS gt', 's.id_grup_tujuan', '=', 'gt.id')
                        ->leftJoin('karyawan AS k', 's.id_karyawan', '=', 'k.id')
                        ->where('s.is_aktif', '=', 'Y')
                        ->where('s.status', 'like', "%MENUNGGU UANG JALAN%")
                        ->groupBy('c.id')
                        ->get();
            //   dd($sewa);
                return view('pages.finance.pembayaran_uang_jalan.index',[
                    'judul' => "Pencairan Uang Jalan",
                    'sewa'=>$sewa,
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
    public function form(Request $request)
    {
        //form
         $id = $request->input('id');

        // Set the session variable
        session(['your_session_variable' => $id]);
    }
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
