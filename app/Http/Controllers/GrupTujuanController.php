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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Grup::select('grup.*', DB::raw('SUM(CASE WHEN grup_tujuan.is_aktif = "Y" THEN 1 ELSE 0 END) AS total_tujuan'))
            ->leftJoin('grup_tujuan', 'grup_tujuan.grup_id', '=', 'grup.id')
            ->where('grup.is_aktif', 'Y')
            ->groupBy('grup.id', 'grup.nama_grup', /* other columns you need */)
            ->orderBy('nama_grup', 'ASC')
            ->get();
    
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
        $roleMarketing = Role::where('is_aktif', 'Y')->where('nama', 'Marketing')->first();
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
        $data['grup'] = Grup::where('is_aktif', 'Y')->findOrFail($id);
        $tujuan = GrupTujuan::where('grup_id', $id)->where('is_aktif', 'Y')->get();
        foreach ($tujuan as $key => $value) {
            $biaya = GrupTujuanBiaya::where('grup_id', $id)
                                ->where('is_aktif', 'Y')
                                ->where('grup_tujuan_id', $value->id)
                                ->get();
            
            $data['tujuan'][$key]=(object)array(
                'id'=>$value->id,
                'grup_id'=>$value->grup_id,
                'marketing_id'=>$value->marketing_id,
                'nama_tujuan'=>$value->nama_tujuan,
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
        // dd($dataPengaturanKeuangan);

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
    public function update(Request $request, $id)
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

            foreach ($data['data']['tujuan'] as $key => $value) {
                if($value['id_tujuan'] != 'undefined'){
                    // ini edit 
                    $tarif = ($value['tarif'] != '')? floatval(str_replace(',', '', $value['tarif'])):0;
                    $komisi = ($value['komisi'] != '')? floatval(str_replace(',', '', $value['komisi'])):0;
                    $komisi_driver_hidden = ($value['komisi_driver_hidden'] != '')? floatval(str_replace(',', '', $value['komisi_driver_hidden'])):0;

                    $uang_jalan = ($value['uang_jalan'] != '')? floatval(str_replace(',', '', $value['uang_jalan'])):0;
                    $harga_per_kg = ($value['harga_per_kg_hidden'] != '')? floatval(str_replace(',', '', $value['harga_per_kg_hidden'])):0;

                    $edit_tujuan = GrupTujuan::where('is_aktif', 'Y')->findOrFail($value['id_tujuan']);
                    if($edit_tujuan){
                        // $edit_tujuan->grup_id = $value['grup_hidden'];
                        $edit_tujuan->marketing_id = $value['marketing_hidden'];
                        $edit_tujuan->nama_tujuan = $value['nama_tujuan'];
                        $edit_tujuan->alamat = $value['alamat_hidden'];
                        $edit_tujuan->jenis_tujuan = $value['jenis_tujuan'];
                        $edit_tujuan->harga_per_kg = $harga_per_kg;
                        $edit_tujuan->min_muatan = 0;
                        $edit_tujuan->uang_jalan = $uang_jalan;
                        $edit_tujuan->tarif = $tarif;
                        $edit_tujuan->komisi = $komisi;
                        $edit_tujuan->komisi_driver = $komisi_driver_hidden;
                        $edit_tujuan->catatan = $value['catatan'];
                        $edit_tujuan->seal_pje = ($value['seal_pje_hidden'] != '')? floatval(str_replace(',', '', $value['seal_pje_hidden'])):null;
                        $edit_tujuan->seal_pelayaran = ($value['seal_pelayaran_hidden'] != '')? floatval(str_replace(',', '', $value['seal_pelayaran_hidden'])):null;
                        $edit_tujuan->plastik = ($value['plastik_hidden'] != '')? floatval(str_replace(',', '', $value['plastik_hidden'])):null;
                        $edit_tujuan->tally = ($value['tally_hidden'] != '')? floatval(str_replace(',', '', $value['tally_hidden'])):null;
                        $edit_tujuan->kargo = $value['kargo_hidden'];
                        $edit_tujuan->updated_by = $user;
                        $edit_tujuan->updated_at = now();
                        // var_dump($edit_tujuan);
                        $edit_tujuan->save();
                        // if(){
                        //     if($value['obj_biaya'] != null){
                        //         $data_biaya = json_decode($value['obj_biaya'], true);
                                
                        //         foreach ($data_biaya as $key => $item) {
                        //             $biaya_clean = ($item['biaya'] != '')? floatval(str_replace(',', '', $item['biaya'])):0;
                        //             // var_dump(  $item ); 
                        //             if (!empty($item['id'])) {
                        //                 $biaya = GrupTujuanBiaya::where('is_aktif', 'Y')->findOrFail($item['id']);
                        //                 if($biaya){
                        //                     $biaya->updated_by = $user;
                        //                     $biaya->updated_at = now();
                        //                     $biaya->biaya = $biaya_clean;
                        //                     $biaya->deskripsi = $item['deskripsi'];
                        //                     $biaya->catatan = $item['catatan'];
                        //                     $biaya->save();
                        //                 }
                        //             }else{
                        //                 $biaya = new GrupTujuanBiaya();
                        //                 $biaya->grup_id = $value['grup_hidden'];
                        //                 $biaya->grup_tujuan_id = $value['id_tujuan'];
                        //                 $biaya->created_by = $user;
                        //                 $biaya->created_at = now();
                        //                 $biaya->biaya = $biaya_clean;
                        //                 $biaya->deskripsi = $item['deskripsi'];
                        //                 $biaya->catatan = $item['catatan'];
                        //                 $biaya->save();
                        //             }
                        //         }
                        //     }
                        // }
                    }
                    // die;
                }else{
                     // ini create baru

                    $tarif = ($value['tarif'] != '')? floatval(str_replace(',', '', $value['tarif'])):0;
                    $komisi = ($value['komisi'] != '')? floatval(str_replace(',', '', $value['komisi'])):0;
                    $uang_jalan = ($value['uang_jalan'] != '')? floatval(str_replace(',', '', $value['uang_jalan'])):0;
                    $harga_per_kg = ($value['harga_per_kg_hidden'] != '')? floatval(str_replace(',', '', $value['harga_per_kg_hidden'])):0;

                    $new_tuj = new GrupTujuan();
                    $new_tuj->grup_id = $value['grup_hidden'];
                    $new_tuj->marketing_id = $value['marketing_hidden'];
                    $new_tuj->nama_tujuan = $value['nama_tujuan'];
                    $new_tuj->alamat = $value['alamat_hidden'];
                    $new_tuj->jenis_tujuan = $value['jenis_tujuan'];
                    $new_tuj->harga_per_kg = $harga_per_kg;
                    $new_tuj->min_muatan = $value['min_muatan_hidden'];
                    $new_tuj->uang_jalan = $uang_jalan;
                    $new_tuj->tarif = $tarif;
                    $new_tuj->komisi = $komisi;
                    $new_tuj->catatan = $value['catatan'];
                    $new_tuj->seal_pje = ($value['seal_pje_hidden'] != '')? floatval(str_replace(',', '', $value['seal_pje_hidden'])):null;
                    $new_tuj->seal_pelayaran = ($value['seal_pelayaran_hidden'] != '')? floatval(str_replace(',', '', $value['seal_pelayaran_hidden'])):null;
                    $new_tuj->tally = ($value['tally_hidden'] != '')? floatval(str_replace(',', '', $value['tally_hidden'])):null;
                    $new_tuj->plastik = ($value['plastik_hidden'] != '')? floatval(str_replace(',', '', $value['plastik_hidden'])):null;
                    $new_tuj->kargo = $value['kargo_hidden'];
                    $new_tuj->created_by = $user;
                    $new_tuj->created_at = now();

                    if($new_tuj->save()){
                        $data_biaya = json_decode($value['obj_biaya'], true);

                        foreach ($data_biaya as $key => $item) {
                            $biaya = ($item['biaya'] != '')? floatval(str_replace(',', '', $item['biaya'])):0;

                            $new_biaya = new GrupTujuanBiaya();
                            $new_biaya->grup_id = $value['grup_hidden'];
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

            // return redirect()->route('grup_tujuan.index')->with('status','Success!!');
            return redirect('grup_tujuan')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect('grup_tujuan')->with('status','Error!!');
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
