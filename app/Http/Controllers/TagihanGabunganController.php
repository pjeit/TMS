<?php

namespace App\Http\Controllers;

use App\Models\TagihanPembelian;
use Illuminate\Http\Request;
use App\Models\Sewa;
use App\Models\Supplier;
use App\Models\TagihanPembelianDetail;
use App\Models\TagihanPembelianPembayaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\KasBank;
use App\Helper\CoaHelper;
use App\Models\KasBankTransaction;
use App\Models\TagihanPembelianPembayaranDetail;
use App\Models\TagihanRekanan;

class TagihanGabunganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        $data = TagihanPembelian::from('tagihan_pembelian as tp')
                    ->where('tp.is_aktif', 'Y')
                    ->where('tp.is_sewa', 'Y')
                    ->get();
        // dd($data);
        return view('pages.finance.tagihan_gabungan.index',[
            'judul' => "Tagihan Gabungan",
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
        //
        $dataKas = DB::table('kas_bank')
                    ->select('*')
                    ->where('is_aktif', '=', "Y")
                    ->get();

        $supplier = Sewa::from('sewa as s')
                    ->with('getCustomer')
                    ->leftJoin('grup_tujuan as gt', 'gt.id', 's.id_grup_tujuan')
                    ->where(
                        ['is_tagihan' => 'N',
                         's.is_aktif' => 'Y',
                        ])
                    ->groupBy('s.id_supplier')
                    ->get();

        return view('pages.finance.tagihan_gabungan.create',[
            'judul' => "Tagihan Gabungan",
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
        //
        $data = $request->collect();
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($data);
        // try {
            $tagihan = new TagihanPembelian();
            $tagihan->is_sewa = 'Y';
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
                        $sewa = Sewa::where('is_aktif', 'Y')->find($key);

                        $detail = new TagihanPembelianDetail();
                        $detail->id_tagihan_pembelian= $tagihan->id;
                        $detail->id_sewa = $key;
                        // $detail->catatan = $value['catatan'];
                        $detail->jumlah = 1;
                        $detail->satuan = 'NOTA';
                        $detail->deskripsi = $sewa->no_sewa .">>". $sewa->nama_tujuan."(".date('d-M-Y',strtotime($sewa->tanggal_berangkat)) .")";
                        $detail->total_tagihan = floatval(str_replace(',', '', $value['ditagihkan']));
                        $detail->created_by = $user;
                        $detail->created_at = now();
                        if($detail->save()){
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

            return redirect()->route('tagihan_gabungan.index')->with(['status' => 'Success', 'msg' => 'Tagihan Gabungan berhasil dibuat']);

        // } 
        // catch (\Throwable $th) {
        //     db::rollBack();
        //     return redirect()->route('tagihan_gabungan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TagihanPembelian  $tagihanPembelian
     * @return \Illuminate\Http\Response
     */
    public function show(TagihanPembelian $tagihanPembelian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TagihanPembelian  $tagihanPembelian
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $dataKas = DB::table('kas_bank')
        ->select('*')
        ->where('is_aktif', '=', "Y")
        ->get();

        $supplier = Supplier::where('is_aktif', 'Y')->get();
        $tagihan = TagihanPembelian::where('is_aktif', 'Y')->where('is_sewa', 'Y')->with('getDetailsGabungan')->with('getSupplier')->find($id);
        // dd($tagihan);

        return view('pages.finance.tagihan_gabungan.edit',[
            'judul' => "Tagihan Gabungan",
            'dataKas' => $dataKas,
            'supplier' => $supplier,
            'tagihan' => $tagihan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TagihanPembelian  $tagihanPembelian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TagihanPembelian $tagihanPembelian)
    {
        //
        $data = $request->collect();
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($data); 
        
        try {
            $tagihan = TagihanPembelian::where('is_aktif', 'Y')->find($data['id_tagihan']);
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
                            $detail = TagihanPembelianDetail::where('is_aktif', 'Y')->find($value['id_detail']);
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
                                $sewa = Sewa::where('is_aktif', 'Y')->find($value['id_sewa']);
                                // kalau tidak ada data yg ditemukan, tp ada nilai yg ditagihkan, buat data baru
                                $newDetail = new TagihanPembelianDetail();
                                $newDetail->id_tagihan_pembelian = $tagihan->id;
                                $newDetail->id_sewa = $value['id_sewa'];
                                $newDetail->deskripsi = $sewa->no_sewa;
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
            return redirect()->route('tagihan_gabungan.index')->with(['status' => 'Success', 'msg' => 'Tagihan gabungan berhasil dibuat']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('tagihan_gabungan.index')->with(['status' => 'error', 'msg' => 'Tagihan gabungan gagal dibuat!']);
        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('tagihan_gabungan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TagihanPembelian  $tagihanPembelian
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = Auth::user()->id;
        DB::beginTransaction(); 

        try {
            $tagihan = TagihanPembelian::where('is_aktif', 'Y')->find($id);
            $tagihan->updated_by = $user;
            $tagihan->updated_at = now();
            $tagihan->is_aktif = 'N';
            if($tagihan->save()){
                $details = TagihanPembelianDetail::where('is_aktif', 'Y')->where('id_tagihan_pembelian',$id)->get();
                foreach ($details as $detail) {
                    $detail->updated_by = $user;
                    $detail->updated_at = now();
                    $detail->is_aktif = 'N';
                    // $detail->save();
                    if($detail->save())
                    {
                        $sewa = Sewa::where('is_aktif','Y')->where('id_sewa',$detail->id_sewa)->first();
                        $sewa->updated_by = $user;
                        $sewa->updated_at = now();
                        $sewa->is_tagihan = 'N';
                        $sewa->save();
                    }
                }
            }
            DB::commit();
            return redirect()->route('tagihan_gabungan.index')->with(['status' => 'Success', 'msg' => 'Hapus data nota gabungan berhasil!']);

        } catch (\Throwable $th) {
            //throw $th;
            db::rollBack();
            return redirect()->route('tagihan_gabungan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
            
        }
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
            return redirect()->route('tagihan_gabungan.index')->with(['status' => 'error', 'msg' => 'Harap pilih data dahulu!']);
        }
        $data_tagihan = TagihanPembelian::with('getDetailsGabungan')->with('getSupplier')->where('is_aktif', 'Y')->whereIn('id', $data['idTagihan'])->get();
        $data_tagihan_from_supplier = TagihanPembelian::with('getDetailsGabungan')->with('getDetailsGabungan')->with('getSupplier')
        ->where('is_aktif', 'Y')
        ->whereNull('id_pembayaran')
        ->where('id_supplier', $data_tagihan[0]->id_supplier)->get();
        // dd($data_tagihan);
        $cek_supplier = $data_tagihan[0]->id_supplier;
        $flag_salah = false;
        foreach ($data_tagihan as $value) {
            if($value->id_supplier!=$cek_supplier)
            {
                $flag_salah = true;
                break;
            }
        }
        if( $flag_salah)
        {
            return redirect()->route('tagihan_gabungan.index')->with(['status' => 'error', 'msg' => 'Supplier harus berbeda dalam pembayaran nota']);
        }
        if($data_tagihan){
            return view('pages.finance.tagihan_gabungan.bayar',[
                'judul' => "Tagihan Gabungan",
                'dataKas' => $dataKas,
                'supplier' => $supplier,
                'data_tagihan' => $data_tagihan,
                'data_tagihan_from_supplier' => $data_tagihan_from_supplier,

            ]);
        }else{
            return redirect()->route('tagihan_gabungan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap ulangi lagi!']);
        }
    }

    public function bayar_save(Request $request)
    {
        $data = $request->collect();
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($data);
        try {
            $keterangan = 'TAGIHAN GABUNGAN: '. $data['nama_supplier'] . ' - ';
            $biaya_admin = isset($data['biaya_admin'])? floatval(str_replace(',', '', $data['biaya_admin'])):0;
            $total_pph = isset($data['pph'])? floatval(str_replace(',', '', $data['pph'])):0;
            $isErr = false;
                $i = 0;

            if(floatval(str_replace(',', '', $data['total_bayar']))==0 || !isset($data['total_bayar']))
            {
                 return redirect()->route('tagihan_gabungan.index')->with(['status' => 'error', 'msg' => 'Total Bayar harus diisi!']);
            }
            else
            {
                $pembayaran = new TagihanPembelianPembayaran();
                $pembayaran->id_supplier = $data['id_supplier'];
                $pembayaran->id_kas = $data['id_kas'];
                $pembayaran->catatan = $data['catatan'];
                $pembayaran->tgl_bayar = date_create_from_format('d-M-Y', $data['tgl_bayar']);
                $pembayaran->total_dibayar = floatval(str_replace(',', '', $data['total_bayar']));
                $pembayaran->total_pph23 = $total_pph;
                $pembayaran->total_biaya_admin = $biaya_admin;
                $pembayaran->created_by = $user;
                $pembayaran->created_at = now();
                $pembayaran->is_aktif = 'Y';
                // $pembayaran->save();
                if($pembayaran->save())
                {
                    foreach ($data['data'] as $key => $value) {
                        $tagihan = TagihanPembelian::where('is_aktif', 'Y')->find($key);
                            if($tagihan){
                                $pph = isset($value['pph'])?$value['pph']:0;
                                // "total_tagihan" => "5,200,000"
                                // "sisa_tagihan" => "5,200,000"
                                // "pph" => "10,000"
                                // "total_bayar" => "500,000"  ini dikurangin sama pph, biaya admin di data bukan looping
                                $pembayaran_detail = new TagihanPembelianPembayaranDetail();
                                $pembayaran_detail->id_tagihan_pembayaran = $pembayaran->id;
                                $pembayaran_detail->id_tagihan = $tagihan->id;
                                if($i == 0){
                                    $pembayaran_detail->dibayar = $value['total_bayar'] - $biaya_admin;
                                    $pembayaran_detail->pph_23 =  $pph;
                                    $pembayaran_detail->biaya_admin = $biaya_admin;
                                }else{
                                    $pembayaran_detail->dibayar = $value['total_bayar'];
                                    $pembayaran_detail->pph_23 =  $pph;
                                    $pembayaran_detail->biaya_admin = 0;
                                }
                                $pembayaran_detail->total_dibayar = $value['total_bayar'] + $pph;
                                $pembayaran_detail->bukti_potong = $value['bukti_potong'];
                                $pembayaran_detail->created_by = $user;
                                $pembayaran_detail->created_at = now();
                                $pembayaran_detail->is_aktif = 'Y';
                                if($pembayaran_detail->save()){
                                    $dibayar = $value['total_bayar'] + $pph;
    
                                    $tagihan->sisa_tagihan -= $dibayar ;
                                    $tagihan->tagihan_dibayarkan += $dibayar ;
                                    if($tagihan->sisa_tagihan == 0){
                                        $tagihan->status = 'LUNAS';
                                    }
                                    if($tagihan->sisa_tagihan < 0){
                                        $isErr = true; // ini error karna minus
                                    }
                                    $tagihan->updated_by = $user;
                                    $tagihan->updated_at = now();
                                    $tagihan->save();     
    
                                    $keterangan .= ' #NOTA: '. $value['no_nota'] . ' #TOTAL BAYAR: ' . number_format($pembayaran_detail->dibayar);
                                    if($value['pph'] != 0){
                                        $keterangan .= ' #PPh23: '.  number_format($pph);
                                    }
                                    if($i == 0 && $biaya_admin != 0){
                                        $keterangan .= ' #BIAYA ADMIN: '. number_format($biaya_admin);
                                    }
                                    $i++;
                                }
                            }   
                    }
                }
                if($isErr === true){
                    db::rollBack();
                    return redirect()->route('tagihan_pembelian.index')->with(["status" => "error", "msg" => 'Terjadi kesalahan (Pembayaran melebihi sisa tagihan)!']);
                }
                else
                {
                    if(floatval(str_replace(',', '', $data['total_bayar']))!=0 || isset($data['total_bayar']) )
                    {
                        $history = new KasBankTransaction();
                        $history->tanggal = date_create_from_format('d-M-Y', $data['tgl_bayar']);
                        $history->id_kas_bank = $data['id_kas'];
                        $history->debit = 0;
                        $history->kredit = floatval(str_replace(',', '', $data['total_bayar']));
                        $history->kode_coa = CoaHelper::DataCoa(5005);  // hardcode
                        $history->jenis = 'tagihan_supplier';
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
                    }
                    return redirect()->route('tagihan_gabungan.index')->with(['status' => 'Success', 'msg' => 'Tagihan berhasil dibayar']);
                }
            }
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('tagihan_gabungan.index')->with(['status' => 'error', 'msg' => 'Tagihan gagal dibayar!']);
        }
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('tagihan_gabungan.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
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
        // tpd.catatan as catatan,
        $sewa = DB::select("SELECT s.harga_jual,tpd.id, s.no_sewa, c.nama, s.tanggal_berangkat, s.total_tarif, tpd.total_tagihan,
                                            s.id_sewa, tpd.id_tagihan_pembelian, s.id_supplier, tpd.is_aktif AS tpd_is_aktif, 
                                        s.is_tagihan, gt.nama_tujuan as nama_tujuan, c.kode
                                    FROM tagihan_pembelian_detail as tpd 
                                    LEFT JOIN sewa as s on s.id_sewa = tpd.id_sewa
                                    LEFT JOIN customer as c on c.id = s.id_customer
                                    LEFT JOIN grup_tujuan as gt on gt.id = s.id_grup_tujuan
                                    WHERE tpd.id_tagihan_pembelian = $id_tagihan AND tpd.is_aktif = 'Y' AND s.is_tagihan = 'Y' 
                                    UNION ALL
                                    SELECT s.harga_jual,tpd.id, s.no_sewa, c.nama, s.tanggal_berangkat, s.total_tarif, tpd.total_tagihan, 
                                        s.id_sewa, tpd.id_tagihan_pembelian, s.id_supplier, tpd.is_aktif AS tpd_is_aktif, 
                                        s.is_tagihan, gt.nama_tujuan as nama_tujuan, c.kode
                                    FROM sewa as s
                                    LEFT JOIN tagihan_pembelian_detail as tpd on tpd.id_sewa = s.id_sewa and tpd.is_aktif = 'Y'
                                    LEFT JOIN customer as c on c.id = s.id_customer
                                    LEFT JOIN grup_tujuan as gt on gt.id = s.id_grup_tujuan
                                    WHERE s.id_supplier = $id_supplier AND s.is_tagihan <> 'Y' and s.is_aktif = 'Y'
                            -- AND s.jenis_tujuan = 'LTL' 
                            ");

        return $sewa;
    }
}
