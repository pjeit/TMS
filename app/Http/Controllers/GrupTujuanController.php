<?php

namespace App\Http\Controllers;

use App\Models\Grup;
use Illuminate\Http\Request;
use App\Models\GrupMember;
use App\Models\GrupTujuan;
use App\Models\GrupTujuanBiaya;
use App\Models\Marketing;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\PDF; // use PDF;
use Illuminate\Http\RedirectResponse;
use Mockery\Undefined;

class GrupTujuanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_GRUP_TUJUAN', ['only' => ['index']]);
		$this->middleware('permission:CREATE_GRUP_TUJUAN', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_GRUP_TUJUAN', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_GRUP_TUJUAN', ['only' => ['destroy']]);  
    }

    public function index()
    {
        $data = Grup::select('grup.*', DB::raw('SUM(CASE WHEN grup_tujuan.is_aktif = "Y" THEN 1 ELSE 0 END) AS total_tujuan'))
            ->leftJoin('grup_tujuan', 'grup_tujuan.grup_id', '=', 'grup.id')
            ->where('grup.is_aktif', 'Y')
            ->groupBy('grup.id', 'grup.nama_grup' /* other columns you need */)
            ->orderBy('nama_grup', 'ASC')
            ->with('customers')
            ->get();
        // dd($data[0]->customers[0]->nama);
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";

        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        return view('pages.master.grup_tujuan.index',[
                'judul' => "Grup Tujuan",
                'data' => $data,
        ]);
    }

    public function getMarketing($groupId)
    {
        $role = 6;
        $roleMarketing = Role::where('is_aktif', 'Y')->where('name', 'Marketing')->first();
        if(isset($roleMarketing)){
            $role = $roleMarketing->id;
        }
        $marketingList = Marketing::where('grup_id', $groupId)->where('role_id', $role)->get();
        return response()->json($marketingList);
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
        $data['grup'] = Grup::where('is_aktif', 'Y')
        ->with('customers')
        ->findOrFail($id);
        $data['grup_all'] = Grup::where('is_aktif', 'Y')->get();
        $tujuan = GrupTujuan::where('grup_id', $id)->where('is_aktif', 'Y')->get();
        foreach ($tujuan as $key => $value) {
            $biaya = GrupTujuanBiaya::/*where('grup_id', $id)
                                ->*/where('is_aktif', 'Y')
                                ->where('grup_tujuan_id', $value->id)
                                ->get();

            $data['tujuan'][$key]=(object)array(
                'id'=>$value->id,
                'grup_id'=>$value->grup_id,
                'marketing_id'=>$value->marketing_id,
                'nama_tujuan'=>$value->nama_tujuan,
                'pic'=>$value->pic,
                'alamat'=>$value->alamat,
                'jenis_tujuan'=>$value->jenis_tujuan,
                'harga_per_kg'=>$value->harga_per_kg,
                'min_muatan'=>$value->min_muatan,
                'uang_jalan'=>$value->uang_jalan,
                'tarif'=>$value->tarif,
                'komisi'=>$value->komisi,
                'komisi_driver'=>$value->komisi_driver,
                'catatan'=>$value->catatan,
                'seal_pelayaran'=>$value->seal_pelayaran,
                'seal_pje'=>$value->seal_pje,
                'tally'=>$value->tally,
                'plastik'=>$value->plastik,
                'kargo'=>$value->kargo,
                'detail_uang_jalan'=>json_encode($biaya),
            );
        }

        $dataPengaturanKeuangan = DB::table('pengaturan_keuangan')
            ->select('*')
            ->where('pengaturan_keuangan.is_aktif', '=', "Y")
            ->get();
        // dd($data['tujuan']);

        return view('pages.master.grup_tujuan.edit',[
            'judul' => "Grup",
            'data' => $data,
            'id' => $id,
            'dataPengaturanKeuangan' => $dataPengaturanKeuangan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateOld(Request $request, $id)
    {
        try {
            $data = $request->post();
            $user = Auth::user()->id;
            // dd($data); 

            // delete data dulu
            if($data['data']['deleted_tujuan'] != null){
                $del = explode(',', $data['data']['deleted_tujuan']);
                $delete_grup = GrupTujuan::whereIn('id', ($del))
                ->update([
                    'is_aktif' => 'N',
                    'updated_by' => $user,
                ]);

               $delete_biaya = GrupTujuanBiaya::whereIn('grup_id', $del)
               ->update([
                   'is_aktif' => 'N',
                   'updated_by' => $user,
               ]);
            }
            if($data['data']['deleted_biaya'] != null){
                $del = explode(',', $data['data']['deleted_biaya']);
                // Update the records
                $delete_biaya = GrupTujuanBiaya::whereIn('id', $del)
                    ->update([
                        'is_aktif' => 'N',
                        'updated_by' => $user,
                    ]);
            }
            
            // dd($data['data']['tujuan'][0]['komisi_driver_hidden'] != '');
            foreach ($data['data']['tujuan'] as $key => $value) {
                if(isset($value['id_tujuan']) && $value['id_tujuan'] != 'undefined' ){
                    $tarif = ($value['tarif'] != '')? floatval(str_replace(',', '', $value['tarif'])):0;
                    $komisi = ($value['komisi'] != '')? floatval(str_replace(',', '', $value['komisi'])):0;
                    $komisi_driver_hidden = isset($value['komisi_driver_hidden'])? $value['komisi_driver_hidden'] != ''? floatval(str_replace(',', '', $value['komisi_driver_hidden'])):0 : 0;

                    $uang_jalan = ($value['uang_jalan'] != '')? floatval(str_replace(',', '', $value['uang_jalan'])):0;
                    $harga_per_kg = isset($value['harga_per_kg_hidden'])? $value['harga_per_kg_hidden'] != ''? floatval(str_replace(',', '', $value['harga_per_kg_hidden'])):0 : 0;

                    $edit_tujuan = GrupTujuan::where('is_aktif', 'Y')->findOrFail($value['id_tujuan']);
                    if($edit_tujuan){
                        $edit_tujuan->marketing_id = isset($value['marketing_hidden'])? $value['marketing_hidden']:null;
                        $edit_tujuan->nama_tujuan = $value['nama_tujuan'];
                        $edit_tujuan->pic = $value['pic'];
                        $edit_tujuan->alamat = $value['alamat_hidden'];
                        $edit_tujuan->jenis_tujuan = $value['jenis_tujuan'];
                        $edit_tujuan->harga_per_kg = $harga_per_kg;
                        $edit_tujuan->min_muatan = isset($value['min_muatan_hidden'])? $value['min_muatan_hidden']:null;
                        $edit_tujuan->uang_jalan = $uang_jalan;
                        $edit_tujuan->tarif = $tarif;
                        $edit_tujuan->komisi = $komisi;
                        $edit_tujuan->komisi_driver = $komisi_driver_hidden;
                        $edit_tujuan->catatan = $value['catatan'];
                        $edit_tujuan->seal_pje = isset($value['seal_pje_hidden'])? ($value['seal_pje_hidden'] != '')? floatval(str_replace(',', '', $value['seal_pje_hidden'])):null : null;
                        $edit_tujuan->seal_pelayaran = isset($value['seal_pelayaran_hidden'])? ($value['seal_pelayaran_hidden'] != '')? floatval(str_replace(',', '', $value['seal_pelayaran_hidden'])):null : null;
                        $edit_tujuan->plastik = isset($value['plastik_hidden'])? ($value['plastik_hidden'] != '')? floatval(str_replace(',', '', $value['plastik_hidden'])):null : null;
                        $edit_tujuan->tally = isset($value['tally_hidden'])? ($value['tally_hidden'] != '')? floatval(str_replace(',', '', $value['tally_hidden'])):null : null;
                        $edit_tujuan->kargo = isset($value['kargo_hidden'])? $value['kargo_hidden']:null;
                        $edit_tujuan->updated_by = $user;
                        $edit_tujuan->updated_at = now();
                        if($edit_tujuan->save()){
                            if($value['jenis_tujuan']=="FTL"){
                                if(isset($value['obj_biaya'])){
                                    $data_biaya = json_decode($value['obj_biaya'], true);
                                    foreach ($data_biaya as $key => $item) {
                                        $biaya = ($item['biaya'] != '')? floatval(str_replace(',', '', $item['biaya'])):0;
            
                                        $new_biaya = GrupTujuanBiaya::where('is_aktif', 'Y')->find($item['id']);
                                        if($new_biaya){
                                            $new_biaya->biaya = $biaya;
                                            $new_biaya->deskripsi = $item['deskripsi'];
                                            $new_biaya->catatan = $item['catatan'];
                                            $new_biaya->updated_by = $user;
                                            $new_biaya->updated_at = now();
                                            $new_biaya->save();
                                        }else{
                                            $new_biaya = new GrupTujuanBiaya();
                                            $new_biaya->grup_id = $value['grup_hidden'];
                                            // $new_biaya->grup_tujuan_id = $edit_tujuan->id;
                                            $new_biaya->biaya = $biaya;
                                            $new_biaya->deskripsi = $item['deskripsi'];
                                            $new_biaya->catatan = $item['catatan'];
                                            $new_biaya->created_by = $user;
                                            $new_biaya->created_at = now();
                                            $new_biaya->save();
                                        }
                                    }    
                                }
                            }
                        }
                    }
                }else{
                     // ini create baru
                    $tarif = ($value['tarif'] != '')? floatval(str_replace(',', '', $value['tarif'])):0;
                    $komisi = ($value['komisi'] != '')? floatval(str_replace(',', '', $value['komisi'])):0;
                    $uang_jalan = ($value['uang_jalan'] != '')? floatval(str_replace(',', '', $value['uang_jalan'])):0;
                    $harga_per_kg = ($value['harga_per_kg_hidden'] != '')? floatval(str_replace(',', '', $value['harga_per_kg_hidden'])):0;
                    $komisi_driver_hidden = ($value['komisi_driver_hidden'] != '')? floatval(str_replace(',', '', $value['komisi_driver_hidden'])):0;

                    $new_tuj = new GrupTujuan();
                    $new_tuj->grup_id = $value['grup_hidden'];
                    $new_tuj->marketing_id = $value['marketing_hidden'];
                    $new_tuj->nama_tujuan = $value['nama_tujuan'];
                    $new_tuj->pic = $value['pic'];
                    $new_tuj->alamat = $value['alamat_hidden'];
                    $new_tuj->jenis_tujuan = $value['jenis_tujuan'];
                    $new_tuj->harga_per_kg = $harga_per_kg;
                    $new_tuj->min_muatan = $value['min_muatan_hidden'];
                    $new_tuj->uang_jalan = $uang_jalan;
                    $new_tuj->tarif = $tarif;
                    $new_tuj->komisi = $komisi;
                    $new_tuj->komisi_driver = $komisi_driver_hidden;
                    $new_tuj->catatan = $value['catatan'];
                    $new_tuj->seal_pje = ($value['seal_pje_hidden'] != '')? floatval(str_replace(',', '', $value['seal_pje_hidden'])):null;
                    $new_tuj->seal_pelayaran = ($value['seal_pelayaran_hidden'] != '')? floatval(str_replace(',', '', $value['seal_pelayaran_hidden'])):null;
                    $new_tuj->tally = ($value['tally_hidden'] != '')? floatval(str_replace(',', '', $value['tally_hidden'])):null;
                    $new_tuj->plastik = ($value['plastik_hidden'] != '')? floatval(str_replace(',', '', $value['plastik_hidden'])):null;
                    $new_tuj->kargo = $value['kargo_hidden'];
                    $new_tuj->created_by = $user;
                    $new_tuj->created_at = now();

                    if($new_tuj->save()){
                        if($value['jenis_tujuan']=="FTL")
                        {
                            $data_biaya = json_decode($value['obj_biaya'], true);
                            foreach ($data_biaya as $key => $item) {
                                $biaya = ($item['biaya'] != '')? floatval(str_replace(',', '', $item['biaya'])):0;
    
                                $new_biaya = new GrupTujuanBiaya();
                                // $new_biaya->grup_id = $value['grup_hidden'];
                                $new_biaya->grup_tujuan_id = $new_tuj->id;
                                $new_biaya->biaya = $biaya;
                                $new_biaya->deskripsi = $item['deskripsi'];
                                $new_biaya->catatan = $item['catatan'];
                                $new_biaya->created_by = $user;
                                $new_biaya->created_at = now();
                                $new_biaya->save();
                            }
                        }
                    }
                }
            }
            // return redirect()->route('grup_tujuan.index')->with('status','Success!!');
            return redirect('grup_tujuan')->with(['status' => 'Success', 'msg' => 'Data berhasil disimpan!']);
        } catch (ValidationException $e) {
            return redirect('grup_tujuan')->with('status', 'Error');
        }
    }

    public function update_tujuan(Request $request){
        $data = $request->post();
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($data);

        try {
            if($data['delete_biaya'] != null){
                $del = explode(',', $data['delete_biaya']);
                // Update the records
                $delete_biaya = GrupTujuanBiaya::whereIn('id', $del)
                    ->update([
                        'updated_by' => $user,
                        'updated_at' => now(),
                        'is_aktif' => 'N',
                    ]);
            }
            $grup_tujuan = GrupTujuan::where('is_aktif', 'Y')->find($data['tujuan_id']);
            
            if($grup_tujuan){
                $grup_tujuan->grup_id = $data['grup'];
                $grup_tujuan->marketing_id = isset($data['marketing']) ? $data['marketing'] : null;
                $grup_tujuan->nama_tujuan = $data['nama_tujuan'];
                $grup_tujuan->pic = $data['pic'];
                $grup_tujuan->alamat = $data['alamat'];
                $grup_tujuan->jenis_tujuan = $data['select_jenis_tujuan'];
                $grup_tujuan->harga_per_kg = $data['harga_per_kg'];
                $grup_tujuan->min_muatan = $data['min_muatan'];
                $grup_tujuan->uang_jalan = floatval(str_replace(',', '', $data['uang_jalan']));
                $grup_tujuan->tarif = floatval(str_replace(',', '', $data['tarif']));
                $grup_tujuan->komisi = floatval(str_replace(',', '', $data['komisi']));
                $grup_tujuan->komisi_driver = floatval(str_replace(',', '', $data['komisi_driver']));
                $grup_tujuan->catatan = $data['catatan'];
                $grup_tujuan->seal_pje = floatval(str_replace(',', '', $data['seal_pje']));
                $grup_tujuan->seal_pelayaran = floatval(str_replace(',', '', $data['seal_pelayaran']));
                $grup_tujuan->plastik = floatval(str_replace(',', '', $data['plastik_pje']));
                $grup_tujuan->tally = floatval(str_replace(',', '', $data['tally_pje']));
                $grup_tujuan->kargo = $data['kargo_pje'];
                $grup_tujuan->updated_by = $user;
                $grup_tujuan->updated_at = now();
                $grup_tujuan->save();

                if(isset($data['biaya'])){
                    foreach ($data['biaya'] as $key => $biaya) {
                        if(substr($key, 0, 1) != 'x'){ 
                            // update
                            $tujuan_biaya = GrupTujuanBiaya::where('is_aktif', 'Y')->find($key);
                            if($tujuan_biaya){
                                $tujuan_biaya->deskripsi = $biaya['deskripsi'];
                                $tujuan_biaya->biaya = floatval(str_replace(',', '', $biaya['biaya']));
                                $tujuan_biaya->catatan = $biaya['catatan'];
                                $tujuan_biaya->updated_by = $user;
                                $tujuan_biaya->updated_at = now();
                                $tujuan_biaya->save();
                            }
                        }else{
                            $new = new GrupTujuanBiaya();
                            // $new->grup_id = $data['grup_id'];
                            $new->grup_tujuan_id = $data['tujuan_id'];
                            $new->deskripsi = $biaya['deskripsi'];
                            $new->biaya = floatval(str_replace(',', '', $biaya['biaya']));
                            $new->catatan = $biaya['catatan'];
                            $new->created_by = $user;
                            $new->created_at = now();
                            $new->save();
                        }
                    }
                }

            }else{
                if($data['tujuan_id'] == null){
                    $new_grup_tujuan = new GrupTujuan();
                    $new_grup_tujuan->grup_id = $data['grup_id'];
                    $new_grup_tujuan->marketing_id = $data['marketing'][0];
                    $new_grup_tujuan->nama_tujuan = $data['nama_tujuan'];
                    $new_grup_tujuan->pic = $data['pic'];
                    $new_grup_tujuan->alamat = $data['alamat'];
                    $new_grup_tujuan->jenis_tujuan = $data['select_jenis_tujuan'];
                    $new_grup_tujuan->harga_per_kg = $data['harga_per_kg'];
                    $new_grup_tujuan->min_muatan = $data['min_muatan'];
                    $new_grup_tujuan->uang_jalan = floatval(str_replace(',', '', $data['uang_jalan']));
                    $new_grup_tujuan->tarif = floatval(str_replace(',', '', $data['tarif']));
                    $new_grup_tujuan->komisi = floatval(str_replace(',', '', $data['komisi']));
                    $new_grup_tujuan->komisi_driver = floatval(str_replace(',', '', $data['komisi_driver']));
                    $new_grup_tujuan->catatan = $data['catatan'];
                    $new_grup_tujuan->seal_pje = floatval(str_replace(',', '', $data['seal_pje']));
                    $new_grup_tujuan->seal_pelayaran = floatval(str_replace(',', '', $data['seal_pelayaran']));
                    $new_grup_tujuan->plastik = floatval(str_replace(',', '', $data['plastik_pje']));
                    $new_grup_tujuan->tally = floatval(str_replace(',', '', $data['tally_pje']));
                    $new_grup_tujuan->kargo = $data['kargo_pje'];
                    $new_grup_tujuan->created_by = $user;
                    $new_grup_tujuan->created_at = now();
                    $new_grup_tujuan->save();
    
                    if(isset($data['biaya'])){
                        foreach ($data['biaya'] as $key => $biaya) {
                            $new = new GrupTujuanBiaya();
                            // $new->grup_id = $data['grup_id'];
                            $new->grup_tujuan_id = $new_grup_tujuan->id;
                            $new->deskripsi = $biaya['deskripsi'];
                            $new->biaya = floatval(str_replace(',', '', $biaya['biaya']));
                            $new->catatan = $biaya['catatan'];
                            $new->created_by = $user;
                            $new->created_at = now();
                            $new->save();
                        }
                    }
        
                }
            }


            DB::commit();
            return redirect()->back()->with(['status' => 'Success', 'msg' => 'Data berhasil disimpan!']);
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->with(['status' => 'Error', 'msg' => 'Terjadi kesalahan.']);

        }
    }

    public function delete_tujuan(Request $request){
        $data = $request->post();
        $user = Auth::user()->id;
        DB::beginTransaction(); 

        try {
            $tujuan = GrupTujuan::where('is_aktif', 'Y')->find($data['delete_tujuan']);

            if($tujuan){
                $tujuan->updated_by = $user;
                $tujuan->updated_at = now();
                $tujuan->is_aktif = "N";
                $tujuan->save();
            }

            DB::commit();
            return redirect()->back()->with(['status' => 'Success', 'msg' => 'Data berhasil dihapus!']);
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->with(['status' => 'Error', 'msg' => 'Terjadi kesalahan.']);

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
       // ga ada yg di hapus
    }

    // public function printDetail($grup)
    // {
    //     //
    //     die($grup);
    //     $dataSupplier = DB::table('grup_tujuan')
    //         ->select('*')
    //         ->where('supplier.is_aktif', '=', "Y")
    //         ->where('supplier.id', '=', $JobOrder->id_supplier)
    //         ->get();
    //     // dd($dataJoDetail);   
    //     $pdf = PDF::loadView('pages.order.job_order.print',[
    //         'judul'=>"Job Order",
    //         'JobOrder'=>$JobOrder,
    //         'dataSupplier'=>$dataSupplier,
    //         'dataCustomer'=>$dataCustomer,
    //         'dataJoDetail'=>$dataJoDetail,
    //         'dataJaminan'=>$dataJaminan,
    //     ]); 
    //     // dd($JobOrder);
    //     $pdf->setPaper('A5', 'portrait');
    //     // Customize the PDF generation process if needed
    //     $pdf->setOptions([
    //         'isHtml5ParserEnabled' => true, // Enable HTML5 parser
    //         'isPhpEnabled' => true, // Enable inline PHP execution
    //         'defaultFont' => 'sans-serif'
    //     ]);
    //     // langsung download
    //     // return $pdf->download('fileCoba.pdf'); 
    //     // preview dulu
    //     return $pdf->stream('fileCoba.pdf'); 

    //     //  return view('pages.order.job_order.print',[
    //     //     'judul'=>"Job Order",
    //     //     'JobOrder'=>$JobOrder,
    //     //     'dataSupplier'=>$dataSupplier,
    //     //     'dataCustomer'=>$dataCustomer,
    //     //     'dataJoDetail'=>$dataJoDetail

    //     // ]);
    // }
}
