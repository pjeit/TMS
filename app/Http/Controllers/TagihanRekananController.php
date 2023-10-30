<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Supplier;
use App\Models\TagihanRekanan;
use App\Models\TagihanRekananDetail;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TagihanRekananController extends Controller
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

        $data = TagihanRekanan::from('tagihan_rekanan as tr')->with('getSupplier')->where('tr.is_aktif', 'Y')
                    ->get();

        return view('pages.finance.tagihan_rekanan.index',[
            'judul' => "Biaya Operasional",
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
        $dataKas = DB::table('kas_bank')
                    ->select('*')
                    ->where('is_aktif', '=', "Y")
                    ->get();

        $supplier = Sewa::from('sewa as s')
                    ->leftJoin('supplier as sup', 'sup.id', '=', 's.id_supplier')
                    ->where('s.is_aktif', 'Y')
                    ->whereNotNull('s.id_supplier')
                    ->groupBy('s.id_supplier')
                    ->get();

        return view('pages.finance.tagihan_rekanan.create',[
            'judul' => "Tagihan Rekanan",
            'dataKas' => $dataKas,
            'supplier' => $supplier,
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
        $data = $request->collect();
        $user = Auth::user()->id;
        DB::beginTransaction(); 

        try {
            $tagihan = new TagihanRekanan();
            $tagihan->id_supplier = $data['supplier'];
            $tagihan->no_nota = $data['no_nota'];
            $tagihan->tgl_nota = date_create_from_format('d-M-Y', $data['tgl_nota']);
            $tagihan->jatuh_tempo = date_create_from_format('d-M-Y', $data['jatuh_tempo']);
            $tagihan->catatan = $data['catatan'];
            $tagihan->diskon = floatval(str_replace(',', '', $data['diskon']));
            $tagihan->ppn = floatval(str_replace(',', '', $data['ppn']));
            $tagihan->total_tagihan = floatval(str_replace(',', '', $data['tagihan']));
            $tagihan->sisa_tagihan = floatval(str_replace(',', '', $data['tagihan']));
            $tagihan->created_by = $user;
            $tagihan->created_at = now();
            if($tagihan->save()){
                foreach ($data['data'] as $key => $value) {
                    if($value['ditagihkan'] != NULL){
                        $detail = new TagihanRekananDetail();
                        $detail->id_tagihan_rekanan = $tagihan->id;
                        $detail->id_sewa = $key;
                        $detail->catatan = $value['catatan'];
                        $detail->total_tagihan = floatval(str_replace(',', '', $value['ditagihkan']));
                        $detail->created_by = $user;
                        $detail->created_at = now();
                        if($detail->save()){
                            $sewa = Sewa::where('is_aktif', 'Y')->find($key);
                            if($sewa){
                                $sewa->is_tagihan = 'Y';
                                $sewa->updated_by = $user;
                                $sewa->updated_at = now();
                                $sewa->save();
                            }
                        }
                    }
                }

                DB::commit();
            }

            return redirect()->route('tagihan_rekanan.index')->with(['status' => 'Success', 'msg' => 'Tagihan berhasil dibuat']);

        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('tagihan_rekanan.index')->with(['status' => 'error', 'msg' => 'Tagihan gagal dibuat!']);
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
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();

        $supplier = Sewa::from('sewa as s')
                    ->leftJoin('supplier as sup', 'sup.id', '=', 's.id_supplier')
                    ->where('s.is_aktif', 'Y')
                    ->whereNotNull('s.id_supplier')
                    ->groupBy('s.id_supplier')
                    ->get();

        $tagihan = TagihanRekanan::where('is_aktif', 'Y')->find($id);

        return view('pages.finance.tagihan_rekanan.edit',[
            'judul' => "Tagihan Rekanan",
            'dataKas' => $dataKas,
            'supplier' => $supplier,
            'tagihan' => $tagihan,
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
        $data = $request->collect();
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($data); 

        try {
            $tagihan = TagihanRekanan::where('is_aktif', 'Y')->find($data['id_tagihan']);
            if($tagihan){
                $tagihan->tgl_nota = date_create_from_format('d-M-Y', $data['tgl_nota']);
                $tagihan->jatuh_tempo = date_create_from_format('d-M-Y', $data['jatuh_tempo']);
                $tagihan->catatan = $data['catatan'];
                $tagihan->diskon = floatval(str_replace(',', '', $data['diskon']));
                $tagihan->ppn = floatval(str_replace(',', '', $data['ppn']));
                $tagihan->total_tagihan = floatval(str_replace(',', '', $data['tagihan']));
                $tagihan->sisa_tagihan = floatval(str_replace(',', '', $data['tagihan']));
                $tagihan->updated_by = $user;
                $tagihan->updated_at = now();
                if($tagihan->save()){
                    foreach ($data['data'] as $key => $value) {
                        $ditagihkan = floatval(str_replace(',', '', $value['ditagihkan']));

                        if($value['id_detail'] != "null"){
                            $detail = TagihanRekananDetail::where('is_aktif', 'Y')->find($value['id_detail']);
                            // cek apakah ada data detail
                            if($detail){
                                if($ditagihkan != null){
                                    // jika ada datanya dan ada nilai yg ditagihkan, maka di update
                                    $detail->total_tagihan = $ditagihkan;
                                    $detail->catatan = $value['catatan'];
                                    $detail->updated_by = $user;
                                    $detail->updated_at = now();
                                    $detail->save();
                                }else{
                                    // jika ada datanya dan tidak ada nilai yg ditagihkan, maka di non-aktifkan
                                    $detail->updated_by = $user;
                                    $detail->updated_at = now();
                                    $detail->is_aktif = "N";
                                    if($detail->save()){
                                        $sewa = Sewa::where('is_aktif', 'Y')->find($key);
                                        if($sewa){
                                            // flag is_tagihan di sewa rekanan di non-aktifkan = "N"
                                            $sewa->is_tagihan = 'N';
                                            $sewa->updated_by = $user;
                                            $sewa->updated_at = now();
                                            $sewa->save();
                                        }
                                    }
                                }
                            }
                        }
                        // else{
                        //     if($ditagihkan != null){
                        //         // kalau tidak ada data yg ditemukan, tp ada nilai yg ditagihkan, buat data baru
                        //         $detail = new TagihanRekananDetail();
                        //         $detail->id_tagihan_rekanan = $tagihan->id;
                        //         $detail->id_sewa = $value['id_sewa'];
                        //         $detail->catatan = $value['catatan'];
                        //         $detail->total_tagihan = floatval(str_replace(',', '', $value['ditagihkan']));
                        //         $detail->created_by = $user;
                        //         $detail->created_at = now();
                        //         if($detail->save()){
                        //             $sewa = Sewa::where('is_aktif', 'Y')->find($key);
                        //             if($sewa){
                        //                 // flag is_tagihan di sewa rekanan di aktifkan = "Y"
                        //                 $sewa->is_tagihan = 'Y';
                        //                 $sewa->updated_by = $user;
                        //                 $sewa->updated_at = now();
                        //                 $sewa->save();
                        //             }
                        //         }
                        //     }
                        // }
                    }
                    DB::commit();
                }
            }

            return redirect()->route('tagihan_rekanan.index')->with(['status' => 'Success', 'msg' => 'Tagihan berhasil dibuat']);

        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('tagihan_rekanan.index')->with(['status' => 'error', 'msg' => 'Tagihan gagal dibuat!']);
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

    public function load_data($id)
    {
        $sewa = Sewa::from('sewa as s')->with('getCustomer')->where('s.is_aktif', 'Y')
                    ->where('s.id_supplier', $id)
                    ->get();
        
        return $sewa;
    }

    public function filtered_data($id_tagihan, $id_supplier)
    {
        // $sewa = DB::table('tagihan_rekanan_detail as trd')
        //             ->leftJoin('sewa as s', 's.id_sewa', '=', 'trd.id_sewa')
        //             ->where('id_tagihan_rekanan', $id_tagihan)
        //             ->get();
        $sewa = DB::select("SELECT trd.id, s.no_sewa, c.nama, s.tanggal_berangkat, s.total_tarif, trd.total_tagihan, trd.catatan as catatan, s.id_sewa
                            FROM tagihan_rekanan_detail as trd 
                            LEFT JOIN sewa as s on s.id_sewa = trd.id_sewa
                            LEFT JOIN customer as c on c.id = s.id_customer
                            WHERE trd.id_tagihan_rekanan = $id_tagihan
                            UNION ALL
                            SELECT trd.id, s.no_sewa, c.nama, s.tanggal_berangkat, s.total_tarif, trd.total_tagihan, trd.catatan as catatan, s.id_sewa
                            FROM sewa as s
                            LEFT JOIN tagihan_rekanan_detail as trd on trd.id_sewa = s.id_sewa
                            LEFT JOIN customer as c on c.id = s.id_customer
                            WHERE id_supplier = $id_supplier AND is_tagihan = 'N' 
                            GROUP BY s.id_sewa
                            ");

        return $sewa;
    }
}