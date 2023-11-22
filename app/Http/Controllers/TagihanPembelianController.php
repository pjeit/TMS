<?php

namespace App\Http\Controllers;

use App\Models\KasBankTransaction;
use App\Models\Sewa;
use App\Models\Supplier;
use App\Models\TagihanPembelian;
use App\Models\TagihanPembelianDetail;
use App\Models\TagihanPembelianPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\KasBank;
use App\Helper\CoaHelper;
class TagihanPembelianController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_TAGIHAN_PEMBELIAN', ['only' => ['index']]);
		$this->middleware('permission:CREATE_TAGIHAN_PEMBELIAN', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_TAGIHAN_PEMBELIAN', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_TAGIHAN_PEMBELIAN', ['only' => ['destroy']]);  
    }
    
    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        $data = TagihanPembelian::from('tagihan_pembelian as tp')
                    ->where('tp.is_aktif', 'Y')
                    ->get();
        // dd($data);
        return view('pages.finance.tagihan_pembelian.index',[
            'judul' => "Tagihan Pembelian",
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

        $supplier = Supplier::where(['is_aktif' => 'Y'])->get();

        return view('pages.finance.tagihan_pembelian.create',[
        'judul' => "Tagihan Pembelian",
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

        // dd($data); 
        try {
            $tagihan = new TagihanPembelian();
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
                    $detail = new TagihanPembelianDetail();
                    $detail->id_tagihan_pembelian = $tagihan->id;
                    $detail->jumlah = $value['jumlah'];
                    $detail->satuan = $value['satuan'];
                    $detail->deskripsi = $value['deskripsi'];
                    $detail->total_tagihan = floatval(str_replace(',', '', $value['subtotal']));
                    $detail->created_by = $user;
                    $detail->created_at = now();
                    $detail->save();
                }

                DB::commit();
            }

            return redirect()->route('tagihan_pembelian.index')->with(['status' => 'Success', 'msg' => 'Tagihan berhasil dibuat']);

        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Tagihan gagal dibuat!']);
        }
    }
    
    public function bayar(Request $request)
    {
        $data = $request->collect();
        if( !isset($data['idTagihan']) ){
            return redirect()->route('tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Harap pilih data dahulu!']);
        }
        $dataKas = DB::table('kas_bank')
                    ->select('*')
                    ->where('is_aktif', '=', "Y")
                    ->get();

        $supplier = Supplier::where('is_aktif', 'Y')->get();


        $data_tagihan = TagihanPembelian::with('getDetails')->where('is_aktif', 'Y')->whereIn('id', $data['idTagihan'])->get();

        if($data_tagihan){
            return view('pages.finance.tagihan_pembelian.bayar',[
                'judul' => "Tagihan Rekanan",
                'dataKas' => $dataKas,
                'supplier' => $supplier,
                'data_tagihan' => $data_tagihan,
            ]);
        }else{
            return redirect()->route('tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap ulangi lagi!']);
        }
    }

    public function bayar_save(Request $request){
        $data = $request->collect();
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($data);
    
        try {
            $keterangan = 'TAGIHAN PEMBELIAN: '. $data['nama_supplier'] . ' - ';
            $biaya_admin = floatval(str_replace(',', '', $data['biaya_admin']));
            $i = 0;

            $pembayaran = new TagihanPembelianPembayaran();
            $pembayaran->id_supplier = $data['id_supplier'];
            $pembayaran->id_kas = $data['id_kas'];
            $pembayaran->catatan = $data['catatan'];
            $pembayaran->tgl_bayar = date_create_from_format('d-M-Y', $data['tgl_bayar']);
            $pembayaran->total_bayar = floatval(str_replace(',', '', $data['total_bayar']));
            $pembayaran->created_by = $user;
            $pembayaran->created_at = now();
            $pembayaran->save();

            foreach ($data['data'] as $key => $value) {
                $tagihan = TagihanPembelian::where('is_aktif', 'Y')->find($key);
                if($tagihan){
                    $tagihan->id_pembayaran = $pembayaran->id;
                    $tagihan->bukti_potong = $value['bukti_potong'];
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
            $history->kode_coa = CoaHelper::DataCoa(2010); // hardcode
            $history->jenis = 'TAGIHAN_PEMBELIAN';
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
            return redirect()->route('tagihan_pembelian.index')->with(['status' => 'Success', 'msg' => 'Tagihan berhasil dibayar']);

        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Tagihan gagal dibayar!']);
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

        $supplier = Supplier::where('is_aktif', 'Y')->get();

        $tagihan = TagihanPembelian::where('is_aktif', 'Y')->with('getDetails')->find($id);

        return view('pages.finance.tagihan_pembelian.edit',[
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
        $user = Auth::user()->id;
        $data = $request->collect();
        DB::beginTransaction(); 
        // dd($data);

        try {
            $tagihan = TagihanPembelian::where('is_aktif', 'Y')->find($data['id_tagihan']);
            $tagihan->no_nota = $data['no_nota'];
            $tagihan->tgl_nota = date("Y-m-d", strtotime($data['tgl_nota']));
            $tagihan->jatuh_tempo = date("Y-m-d", strtotime($data['jatuh_tempo']));
            $tagihan->total_tagihan = floatval(str_replace(',', '', $data['tagihan']));
            $tagihan->sisa_tagihan = floatval(str_replace(',', '', $data['tagihan']));
            $tagihan->catatan = $data['catatan']; 
            $tagihan->updated_by = $user;
            $tagihan->updated_at = now();
            if($tagihan->save()){
                // update data 
                if(isset($data['data'])){
                    foreach ($data['data'] as $key => $value) {
                        $detail = TagihanPembelianDetail::where('is_aktif', 'Y')->find($key);
                        $detail->deskripsi = $value['deskripsi'];
                        $detail->jumlah = $value['jumlah'];
                        $detail->satuan = $value['satuan'];
                        $detail->total_tagihan = floatval(str_replace(',', '', $value['total_tagihan']));
                        $detail->updated_by = $user;
                        $detail->updated_at = now();
                        $detail->save();
                    }
                }

                // buat data baru
                if(isset($data['data_baru'])){
                    foreach ($data['data_baru'] as $key => $value) {
                        $detail = new TagihanPembelianDetail();
                        $detail->id_tagihan_pembelian = $data['id_tagihan'];
                        $detail->deskripsi = $value['deskripsi'];
                        $detail->jumlah = $value['jumlah'];
                        $detail->satuan = $value['satuan'];
                        $detail->total_tagihan = floatval(str_replace(',', '', $value['subtotal']));
                        $detail->created_by = $user;
                        $detail->created_at = now();
                        $detail->save();
                    }
                }

                // hapus data
                if($data['data_deleted'] != null){
                    $array = explode(",", $data['data_deleted']);
                    foreach ($array as $key => $value) {
                        $detail = TagihanPembelianDetail::where('is_aktif', 'Y')->find($value);
                        $detail->updated_by = $user;
                        $detail->updated_at = now();
                        $detail->is_aktif = 'N';
                        $detail->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('tagihan_pembelian.index')->with(['status' => 'Success', 'msg'  => 'Pembayaran berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
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

    // public function filtered_data($id_tagihan, $id_supplier)
    // {
    //     $sewa = DB::select("SELECT s.harga_jual,trd.id, s.no_sewa, c.nama, s.tanggal_berangkat, s.total_tarif, trd.total_tagihan, trd.catatan as catatan, s.id_sewa, trd.id_tagihan_pembelian, s.id_supplier, trd.is_aktif AS trd_is_aktif, s.is_tagihan
    //                         FROM tagihan_pembelian_detail as tpd 
    //                         LEFT JOIN customer as c on c.id = s.id_customer
    //                         WHERE trd.id_tagihan_pembelian = $id_tagihan AND trd.is_aktif = 'Y' AND s.is_tagihan = 'Y'
    //                         UNION ALL
    //                         SELECT s.harga_jual,trd.id, s.no_sewa, c.nama, s.tanggal_berangkat, s.total_tarif, trd.total_tagihan, trd.catatan as catatan, s.id_sewa, trd.id_tagihan_pembelian, s.id_supplier, trd.is_aktif AS trd_is_aktif, s.is_tagihan
    //                         FROM sewa as s
    //                         LEFT JOIN tagihan_pembelian_detail as trd on trd.id_sewa = s.id_sewa and trd.is_aktif is null
    //                         LEFT JOIN customer as c on c.id = s.id_customer
    //                         WHERE s.id_supplier = $id_supplier AND s.is_tagihan <> 'Y' 
    //                         ");

    //     return $sewa;
    // }
}
