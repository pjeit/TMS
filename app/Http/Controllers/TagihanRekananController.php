<?php

namespace App\Http\Controllers;

use App\Models\KasBankTransaction;
use App\Models\Sewa;
use App\Models\Supplier;
use App\Models\TagihanRekanan;
use App\Models\TagihanRekananDetail;
use App\Models\TagihanRekananPembayaran;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helper\CoaHelper;
use App\Models\KasBank;
class TagihanRekananController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_TAGIHAN_REKANAN', ['only' => ['index']]);
		$this->middleware('permission:CREATE_TAGIHAN_REKANAN', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_TAGIHAN_REKANAN', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_TAGIHAN_REKANAN', ['only' => ['destroy']]);  
    }

    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        $data = TagihanRekanan::from('tagihan_rekanan as tr')
                    ->with('getSupplier')
                    ->where('tr.is_aktif', 'Y')
                    ->get();
        // dd($data);
        return view('pages.finance.tagihan_rekanan.index',[
            'judul' => "Tagihan Rekanan",
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
                    ->with('getCustomer')
                    ->leftJoin('grup_tujuan as gt', 'gt.id', 's.id_grup_tujuan')
                    ->where(['is_tagihan' => 'N', 's.is_aktif' => 'Y'])
                    ->groupBy('s.id_supplier')
                    ->get();

        return view('pages.finance.tagihan_rekanan.create',[
            'judul' => "Tagihan Rekanan",
            'dataKas' => $dataKas,
            'supplier' => $supplier,
        ]);
    }

    public function bayar(Request $request)
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

        $data = $request->collect();
        if( !isset($data['idTagihan']) ){
            return redirect()->route('tagihan_rekanan.index')->with(['status' => 'error', 'msg' => 'Harap pilih data dahulu!']);
        }
        $data_tagihan = TagihanRekanan::with('getDetails')->where('is_aktif', 'Y')->whereIn('id', $data['idTagihan'])->get();
        // dd($data_tagihan);
        
        if($data_tagihan){
            return view('pages.finance.tagihan_rekanan.bayar',[
                'judul' => "Tagihan Rekanan",
                'dataKas' => $dataKas,
                'supplier' => $supplier,
                'data_tagihan' => $data_tagihan,
            ]);
        }else{
            return redirect()->route('tagihan_rekanan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap ulangi lagi!']);
        }
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
        // dd($data);
        try {
            $tagihan = new TagihanRekanan();
            $tagihan->id_supplier = $data['supplier'];
            $tagihan->no_nota = $data['no_nota'];
            $tagihan->tgl_nota = date_create_from_format('d-M-Y', $data['tgl_nota']);
            $tagihan->jatuh_tempo = date_create_from_format('d-M-Y', $data['jatuh_tempo']);
            $tagihan->catatan = $data['catatan'];
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

    public function bayar_save(Request $request)
    {
        $data = $request->collect();
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($data);
        
        try {
            $keterangan = 'TAGIHAN REKANAN: '. $data['nama_supplier'] . ' - ';
            $biaya_admin = floatval(str_replace(',', '', $data['biaya_admin']));
            $i = 0;

            $pembayaran = new TagihanRekananPembayaran();
            $pembayaran->id_supplier = $data['id_supplier'];
            $pembayaran->id_kas = $data['id_kas'];
            $pembayaran->catatan = $data['catatan'];
            $pembayaran->tgl_bayar = date_create_from_format('d-M-Y', $data['tgl_bayar']);
            $pembayaran->total_bayar = floatval(str_replace(',', '', $data['total_bayar']));
            $pembayaran->save();

            foreach ($data['data'] as $key => $value) {
                $tagihan = TagihanRekanan::where('is_aktif', 'Y')->find($key);
                if($tagihan){
                    $tagihan->id_pembayaran = $pembayaran->id;
                    $tagihan->pph = $value['pph'];
                    $tagihan->sisa_tagihan -= ($value['total_bayar'] + $value['pph']);
                    if($i == 0){
                        $tagihan->tagihan_dibayarkan += $value['total_bayar'] - $biaya_admin;
                        $tagihan->biaya_admin = $biaya_admin;
                    }else{
                        $tagihan->tagihan_dibayarkan += $value['total_bayar'];
                    }
                    if($tagihan->sisa_tagihan == 0){
                        $tagihan->status = 'LUNAS';
                    }
                    $tagihan->updated_by = $user;
                    $tagihan->updated_at = now();
                    $tagihan->save();     

                    $keterangan .= ' #NOTA: '. $value['no_nota'] . ' #TOTAL BAYAR: ' . $tagihan->tagihan_dibayarkan;
                    if($value['pph'] != 0){
                        $keterangan .= ' #PPh23: '. $value['pph'];
                    }
                    if($i == 0 && $biaya_admin != 0){
                        $keterangan .= ' #BIAYA ADMIN: '. $biaya_admin;
                    }
                    $i++;
                }                
            }


            $history = new KasBankTransaction();
            $history->id_kas_bank = $data['id_kas'];
            $history->tanggal = date_create_from_format('d-M-Y', $data['tgl_bayar']);
            $history->id_kas_bank = $data['id_kas'];
            $history->debit = 0;
            $history->kredit = floatval(str_replace(',', '', $data['total_bayar']));
            $history->kode_coa = CoaHelper::DataCoa(5005);  // hardcode
            $history->jenis = 'TAGIHAN_REKANAN';
            $history->keterangan_transaksi = $keterangan;
            $history->keterangan_kode_transaksi = $pembayaran->id;
            $history->created_by = $user;
            $history->created_at = now();
            if($history->save()){
                $kas_bank= KasBank::where('is_aktif', 'Y')
                                ->where('id', $data['id_kas'])
                                ->first();
                $kas_bank->saldo_sekarang -=  floatval(str_replace(',', '',  $data['total_bayar']));
                $kas_bank->updated_at = now();
                $kas_bank->updated_by = $user;
                $kas_bank->save();
                DB::commit();
            }

            return redirect()->route('tagihan_rekanan.index')->with(['status' => 'Success', 'msg' => 'Tagihan berhasil dibayar']);

        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('tagihan_rekanan.index')->with(['status' => 'error', 'msg' => 'Tagihan gagal dibayar!']);
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
                                if($ditagihkan != 0){
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
                                        $sewa = Sewa::where('is_aktif', 'Y')->find($value['id_sewa']);
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
                        else{
                            if($ditagihkan != 0){

                                // kalau tidak ada data yg ditemukan, tp ada nilai yg ditagihkan, buat data baru
                                $newDetail = new TagihanRekananDetail();
                                $newDetail->id_tagihan_rekanan = $tagihan->id;
                                $newDetail->id_sewa = $value['id_sewa'];
                                $newDetail->catatan = $value['catatan'];
                                $newDetail->total_tagihan = floatval(str_replace(',', '', $value['ditagihkan']));
                                $newDetail->created_by = $user;
                                $newDetail->created_at = now();
                                if($newDetail->save()){
                                    $sewa = Sewa::where('is_aktif', 'Y')->find($value['id_sewa']);
                                    if($sewa){
                                        // flag is_tagihan di sewa rekanan di aktifkan = "Y"
                                        $sewa->is_tagihan = 'Y';
                                        $sewa->updated_by = $user;
                                        $sewa->updated_at = now();
                                        $sewa->save();
                                    }
                                }
                            }
                        }
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
    
        $sewa = Sewa::from('sewa as s')
            ->with('getCustomer')
            ->leftJoin('grup_tujuan as gt', 'gt.id', 's.id_grup_tujuan')
            ->where(['is_tagihan' => 'N', 's.is_aktif' => 'Y'])
            ->where('s.id_supplier', $id)
            ->get();
        
        return $sewa;
    }

    public function filtered_data($id_tagihan, $id_supplier)
    {
        $sewa = DB::select("SELECT s.harga_jual,trd.id, s.no_sewa, c.nama, s.tanggal_berangkat, s.total_tarif, trd.total_tagihan,
                                trd.catatan as catatan, s.id_sewa, trd.id_tagihan_rekanan, s.id_supplier, trd.is_aktif AS trd_is_aktif, 
                                s.is_tagihan, gt.nama_tujuan as nama_tujuan, c.kode
                            FROM tagihan_rekanan_detail as trd 
                            LEFT JOIN sewa as s on s.id_sewa = trd.id_sewa
                            LEFT JOIN customer as c on c.id = s.id_customer
                            LEFT JOIN grup_tujuan as gt on gt.id = s.id_grup_tujuan
                            WHERE trd.id_tagihan_rekanan = $id_tagihan AND trd.is_aktif = 'Y' AND s.is_tagihan = 'Y'
                            UNION ALL
                            SELECT s.harga_jual,trd.id, s.no_sewa, c.nama, s.tanggal_berangkat, s.total_tarif, trd.total_tagihan, 
                                trd.catatan as catatan, s.id_sewa, trd.id_tagihan_rekanan, s.id_supplier, trd.is_aktif AS trd_is_aktif, 
                                s.is_tagihan, gt.nama_tujuan as nama_tujuan, c.kode
                            FROM sewa as s
                            LEFT JOIN tagihan_rekanan_detail as trd on trd.id_sewa = s.id_sewa and trd.is_aktif is null
                            LEFT JOIN customer as c on c.id = s.id_customer
                            LEFT JOIN grup_tujuan as gt on gt.id = s.id_grup_tujuan
                            WHERE s.id_supplier = $id_supplier AND s.is_tagihan <> 'Y' 
                            -- AND s.jenis_tujuan = 'LTL' 
                            ");

        return $sewa;
    }
}