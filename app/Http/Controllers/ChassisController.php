<?php

namespace App\Http\Controllers;

use App\Models\Chassis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;

class ChassisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('chassis')
            ->select('*')
            ->where('is_hapus', '=', "N")
            ->get();
<<<<<<< Updated upstream
            // tes git branch edwin git
=======
        // tes git
        // $kompartemens = DB::connection('pgsql2')
        //     ->table('m_unit_kerja as uk')
        //     ->join('m_posisi as p', 'p.orgcode', '=', 'uk.unitkerja')
        //     ->selectRaw('uk.unitkerja, uk.nm_unitkerja')
        //     ->where('p.validto', '>', $end_of_month)
        //     ->where('uk.orglevelname', 'ILIKE', '%Kompartemen%')
        //     ->groupBy('uk.unitkerja')
        //     ->orderBy('nm_unitkerja', 'ASC')
        //     ->get();

>>>>>>> Stashed changes
            return view('pages.master.chassis.index',[
            'judul' => "Chassis",
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
     * @param  \App\Models\Chassis  $chassis
     * @return \Illuminate\Http\Response
     */
    public function show(Chassis $chassis)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chassis  $chassis
     * @return \Illuminate\Http\Response
     */
    public function edit(Chassis $chassis)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chassis  $chassis
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chassis $chassis)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chassis  $chassis
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chassis $chassis)
    {
        //
    }
}
