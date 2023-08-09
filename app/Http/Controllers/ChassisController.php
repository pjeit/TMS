<?php

namespace App\Http\Controllers;

use App\Models\Chassis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use App\Models\ChassisDokumen;
use App\Models\M_Kota;
use App\Models\M_ModelChassis;
use Mockery\Undefined;
use Symfony\Component\VarDumper\VarDumper;

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
            ->where('a.is_aktif', '=', "Y")
            ->get();
            
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
            
            // var_dump($request->post()['dokumen'] ); die;
            $chassis = new Chassis();
            $chassis->kode = $request->kode;
            $chassis->karoseri = $request->karoseri;
            $chassis->model_id = $request->model_id;
            $chassis->kepemilikan = $request->kepemilikan;
            $chassis->created_at = date("Y-m-d h:i:s");
            $chassis->created_by = 1; // manual
            $chassis->updated_at = date("Y-m-d h:i:s");
            $chassis->updated_by = 1; // manual
            $chassis->is_aktif = "Y";
            $chassis->save();

            if($request->post()['dokumen'] != null){
                $arrayDokumen = json_decode($request->post()['dokumen'], true);

                foreach ($arrayDokumen as $key => $item) {
                    $dokumen = new ChassisDokumen();
                    $dokumen->chassis_id = $chassis->id;
                    $dokumen->jenis_chassis = $item['jenis'];
                    $dokumen->nomor = $item['nomor'];
                    $dokumen->berlaku_hingga = $item['berlaku_hingga'];
                    $dokumen->is_reminder = $item['is_reminder'];
                    $dokumen->reminder_hari = ($item['reminder_hari'] == '')? NULL:$item['reminder_hari'] ;
                    $dokumen->is_aktif = 'Y';
                    $dokumen->created_by = 1; // nanti di edit
                    $dokumen->created_at = date("Y-m-d h:i:s");
                    $dokumen->save();
                }
            }

            return response()->json(['message' => 'Chassis berhasil dibuat', 'id' => $chassis->id]);
            // return redirect()->route('chassis.index')->with('status','Chassis berhasil dibuat!');
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
        $data = Chassis::where('is_aktif', 'Y')->findOrFail($id);
        $dokumen = ChassisDokumen::where('chassis_id', $id)->where('is_aktif', 'Y')->get();
        $model_chassis = M_ModelChassis::orderBy('id', 'ASC')->get();

        return view('pages.master.chassis.edit',[
            'judul' => "Chassis",
            'data' => $data,
            'dokumen' => $dokumen,
            'model_chassis' => $model_chassis,
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
            
            $chassis = Chassis::where('is_aktif', 'Y')->findOrFail($chassis->id);
            $chassis->kode = $request->kode;
            $chassis->karoseri = $request->karoseri;
            $chassis->model_id = $request->model_id;
            $chassis->kepemilikan = $request->kepemilikan;
            $chassis->updated_at = now(); // Menggunakan now() untuk waktu saat ini
            $chassis->updated_by = 1; // manual
            $chassis->save();
            if ($request->post()['dokumen'] != null) {
                $arrayDokumen = json_decode($request->post()['dokumen'], true);
                $delete_selain_ini = array_column($arrayDokumen, 'dokumen_id');
                
                // Perform actions if $arrayDokumen->dokumen_id is set and not undefined
                ChassisDokumen::where('chassis_id', $chassis->id)
                                ->whereNotIn('id', $delete_selain_ini)
                                ->update(['is_aktif' => 'N']);

                foreach ($arrayDokumen as $key => $item) {
          
                    if ( $item['dokumen_id'] != 'undefined' ) {
                        $dokumen = ChassisDokumen::where('chassis_id', $chassis->id)
                                                    ->where('id', $item['dokumen_id'])
                                                    ->where('is_aktif', 'Y')
                                                    ->first(); // Menggunakan first() untuk objek tunggal
                        if($dokumen){
                            $date = date_create_from_format('d-M-Y', $item['berlaku_hingga']);
    
                            $dokumen->jenis_chassis = $item['jenis'];
                            $dokumen->nomor = $item['nomor'];
                            $dokumen->berlaku_hingga = date_format($date, 'Y-m-d');
                            $dokumen->is_reminder = $item['is_reminder'];
                            $dokumen->reminder_hari = ($item['reminder_hari'] == '') ? null : $item['reminder_hari'];
                            $dokumen->is_aktif = 'Y';
                            $dokumen->updated_by = 1; // nanti di edit
                            $dokumen->updated_at = now(); // Menggunakan now() untuk waktu saat ini
                            $dokumen->save();
                        }
                    }else{
                        $dokumen = new ChassisDokumen();
                        $date = date_create_from_format('d-M-Y', $item['berlaku_hingga']);

                        $dokumen->chassis_id = $chassis->id;
                        $dokumen->jenis_chassis = $item['jenis'];
                        $dokumen->nomor = $item['nomor'];
                        $dokumen->berlaku_hingga = date_format($date, 'Y-m-d');
                        $dokumen->is_reminder = $item['is_reminder'];
                        $dokumen->reminder_hari = ($item['reminder_hari'] == '')? NULL:$item['reminder_hari'] ;
                        $dokumen->is_aktif = 'Y';
                        $dokumen->created_by = 1; // nanti di edit
                        $dokumen->created_at = now(); // date("Y-m-d h:i:s");
                        $dokumen->save();
                    }
                }
            }

            return response()->json(['message' => 'Chassis berhasil dirubah', 'id' => $chassis->id]);
            // return redirect()->route('chassis.index')->with('status','Chassis berhasil dibuat!');
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
        $user = 1; // masih hardcode nanti diganti cookies

        $off_chassis = DB::table('chassis')
            ->where('id', $chassis['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> now(), // date("Y-m-d h:i:s"),
                'updated_by'=> $user, 
            )
        );

        // sementara id comment, untuk keperluan history, jadi ketika di cek lagi akan terlihat
        // dokumen mana saja yg aktif pada waktu chassis di offkan
        // if($off_chassis){
        //     $off_dokumen = DB::table('chassis_dokumen')
        //         ->where('id', $chassis['id'])
        //         ->update(array(
        //             'is_aktif' => "N",
        //             'updated_at'=> now(), // date("Y-m-d h:i:s"),
        //             'updated_by'=> $user, 
        //         )
        //     );
        // }
        return redirect()->route('chassis.index')->with('status','Berhasil menghapus data!');
    }
}
