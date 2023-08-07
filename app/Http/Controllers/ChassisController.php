<?php

namespace App\Http\Controllers;

use App\Models\Chassis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use App\Models\M_Kota;
use App\Models\M_ModelChassis;

class ChassisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = DB::table('chassis as a')
            ->leftJoin('m_model_chassis as b', 'a.model_id', '=', 'b.id')
            ->select('a.*', 'b.nama as nama_model')
            ->where('a.is_hapus', '=', "N")
            ->get();

        // $kompartemens = DB::connection('pgsql2')
        //     ->table('m_unit_kerja as uk')
        //     ->join('m_posisi as p', 'p.orgcode', '=', 'uk.unitkerja')
        //     ->selectRaw('uk.unitkerja, uk.nm_unitkerja')
        //     ->where('p.validto', '>', $end_of_month)
        //     ->where('uk.orglevelname', 'ILIKE', '%Kompartemen%')
        //     ->groupBy('uk.unitkerja')
        //     ->orderBy('nm_unitkerja', 'ASC')
        //     ->get();

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
        $model_chassis = M_ModelChassis::orderBy('id', 'ASC')->get();

        return view('pages.master.chassis.create',[
            'judul' => "Chassis",
            'model_chassis' => $model_chassis,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $pesanKustom = [
                'kode.required' => 'Kode Harus diisi!',
            ];
            
            $request->validate([
                'kode' => 'required',
            ], $pesanKustom);
    
            $supplier = new Chassis();
            $supplier->kode = $request->kode;
            $supplier->karoseri = $request->karoseri;
            $supplier->model_id = $request->model_id;
            $supplier->created_at = date("Y-m-d h:i:s");
            $supplier->created_by = 1; // manual
            $supplier->updated_at = date("Y-m-d h:i:s");
            $supplier->updated_by = 1; // manual
            $supplier->is_hapus = "N";
            $supplier->save();

            return redirect()->route('chassis.index')->with('status','Chassis berhasil dibuat!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
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
    public function edit($id)
    {
        $data = Chassis::where('is_hapus', 'N')->findOrFail($id);

        return view('pages.master.chassis.edit',[
            'judul' => "Chassis",
            'data' => $data,
        ]);
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
        try {
            $pesanKustom = [
                'kode.required' => 'Kode Harus diisi!',
            ];
            
            $request->validate([
                'kode' => 'required',
            ], $pesanKustom);
    
            $supplier = new Chassis();
            $supplier->kode = $request->kode;
            $supplier->karoseri = $request->karoseri;
            $supplier->model_id = $request->model_id;
            $supplier->created_at = date("Y-m-d h:i:s");
            $supplier->created_by = 1; // manual
            $supplier->updated_at = date("Y-m-d h:i:s");
            $supplier->updated_by = 1; // manual
            $supplier->is_hapus = "N";
            $supplier->save();

            return redirect()->route('chassis.index')->with('status','Chassis berhasil dibuat!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chassis  $chassis
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chassis $chassis)
    {
        DB::table('chassis')
        ->where('id', $chassis['id'])
        ->update(array(
            'is_hapus' => "Y",
            'updated_at'=> date("Y-m-d h:i:s"),
            'updated_by'=> 1, // masih hardcode nanti diganti cookies
          )
        );
        return redirect()->route('chassis.index')->with('status','Berhasil menghapus data!');
    }
}
