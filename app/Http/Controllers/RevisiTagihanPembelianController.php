<?php

namespace App\Http\Controllers;

use App\Models\KasBank;
use App\Models\KasBankTransaction;
use App\Models\Supplier;
use App\Models\TagihanPembelian;
use App\Models\TagihanPembelianDetail;
use App\Models\TagihanPembelianPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;
use App\Helper\CoaHelper;
use App\Models\TagihanPembelianPembayaranDetail;
use Exception;

class RevisiTagihanPembelianController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_REVISI_TAGIHAN_PEMBELIAN', ['only' => ['index']]);
		$this->middleware('permission:CREATE_REVISI_TAGIHAN_PEMBELIAN', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_REVISI_TAGIHAN_PEMBELIAN', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_REVISI_TAGIHAN_PEMBELIAN', ['only' => ['destroy']]);  
    }

    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        $data = TagihanPembelianPembayaran::where('is_aktif', 'Y')->get();
        // dd($data);
        return view('pages.revisi.revisi_tagihan_pembelian.index',[
            'judul' => "Revisi Tagihan Nota",
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
        $dataKas = DB::table('kas_bank')
                ->select('*')
                ->where('is_aktif', '=', "Y")
                ->get();

        $supplier = Supplier::where('is_aktif', 'Y')->get();

        $data = TagihanPembelianPembayaran::where('is_aktif', 'Y')->with('get_nota_pembayaran_detail')->find($id);
        // dd(  $data );


        $data_tagihan_from_supplier = TagihanPembelian::with('getDetails')
        ->where('is_aktif', 'Y')
        ->whereDoesntHave('get_tagihan_pembayaran_detail', function ($query) {
            $query->where('is_aktif', 'Y');
        })
        ->where('id_supplier', $data->get_nota_pembayaran_detail[0]->get_nota_value->id_supplier)->get();



        $data_tagihan_udah_bayar = TagihanPembelianPembayaranDetail::with('get_nota_value')
        ->where('is_aktif', 'Y')
        ->where('id_tagihan_pembayaran', $id)->get();
    
        return view('pages.revisi.revisi_tagihan_pembelian.edit',[
            'judul' => "Revisi Tagihan Nota",
            'dataKas' => $dataKas,
            'supplier' => $supplier,
            'data' => $data,
            'data_tagihan_from_supplier' => $data_tagihan_from_supplier,
            'data_tagihan_udah_bayar' => $data_tagihan_udah_bayar,
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
        $keterangan = 'Pembayaran Nota Ke: '. $data['nama_supplier'] . ' -';
        $i = 0;
        // dd($data);
        $isErr = false;

        // try {
            // history kas bank di nonaktifkan
            $pembayaran_lama = TagihanPembelianPembayaran::where('is_aktif', 'Y')->find($id);
            $biaya_admin = isset($data['biaya_admin'])? floatval(str_replace(',', '', $data['biaya_admin'])):0;
            $total_pph = isset($data['pph'])? floatval(str_replace(',', '', $data['pph'])):0;

            if($pembayaran_lama){
              
                // dana dikembalikan
                $returnKas = KasBank::where('is_aktif','Y')->find($pembayaran_lama->id_kas);
                $returnKas->saldo_sekarang += $pembayaran_lama->total_bayar;
                $returnKas->updated_by = $user;
                $returnKas->updated_at = now();

                if( $returnKas->save())
                {
                    if(isset($data['data'])){
                        foreach ($data['data'] as $key => $value) {
                            $tagihan_dibayarkan = isset($value['tagihan_dibayarkan'])? floatval(str_replace(',', '', $value['tagihan_dibayarkan'])):0;
                            $pph = isset($value['pph'])?$value['pph']:0;
                            $biaya_admin_detail = isset($value['biaya_admin'])? floatval(str_replace(',', '', $value['biaya_admin'])):0;

                            if(isset($value['id_pembayaran_detail']))
                            {
                                $tagihan_pembayaran_detail = TagihanPembelianPembayaranDetail::where('is_aktif','Y')->find($value['id_pembayaran_detail']);
                                if( $tagihan_pembayaran_detail)
                                {
                                    $tagihan = TagihanPembelian::where('is_aktif', 'Y')->find($tagihan_pembayaran_detail->id_tagihan);
                                    // $tagihan->no_nota = $value['no_nota'];
                                    if($tagihan)
                                    {
                                        $tagihan->sisa_tagihan += $tagihan_pembayaran_detail->total_dibayar; 
                                        $tagihan->tagihan_dibayarkan -= $tagihan_pembayaran_detail->total_dibayar;

                                    //contoh :
                                    //     8 => array:9 [▼
                                    //     "id_pembayaran_detail" => "4"
                                    //     "id_nota" => "8"
                                    //     "no_nota" => "CC XX 212"
                                    //     "bukti_potong" => null
                                    //     "pph" => "100000"
                                    //     "biaya_admin" => "10000"
                                    //     "total_tagihan" => "5200000"
                                    //     "sisa_tagihan" => "4600000"
                                    //     "tagihan_dibayarkan" => "490000"
                                    //   ]
                                    //   9 => array:8 [▼
                                    //     "id_nota" => "9"
                                    //     "no_nota" => "TEST"
                                    //     "bukti_potong" => null
                                    //     "total_tagihan" => "100000"
                                    //     "sisa_tagihan" => "100000"
                                    //     "pph" => "0"
                                    //     "biaya_admin" => null
                                    //     "tagihan_dibayarkan" => "100000"
                                    //   ]
                                        $tagihan_pembayaran_detail->dibayar = $tagihan_dibayarkan;
                                        //biaya admin di cantolin di index pertama
                                        $tagihan_pembayaran_detail->pph_23 =  $pph;
                                        $tagihan_pembayaran_detail->biaya_admin = $biaya_admin_detail;
                                        $tagihan_pembayaran_detail->total_dibayar = $tagihan_dibayarkan+ $pph + $biaya_admin_detail;
                                        $tagihan_pembayaran_detail->bukti_potong = $value['bukti_potong'];
                                        $tagihan_pembayaran_detail->updated_by = $user;
                                        $tagihan_pembayaran_detail->updated_at = now();
                                        if($tagihan_pembayaran_detail->save())
                                        {
                                            $sum_all = $tagihan_dibayarkan + $pph +$biaya_admin_detail;
                                            $tagihan->sisa_tagihan -= $sum_all;
                                            $tagihan->tagihan_dibayarkan += $sum_all;
                                            if($tagihan->sisa_tagihan == 0){
                                                $tagihan->status = 'LUNAS';
                                            }
                                            if($tagihan->sisa_tagihan < 0){
                                                $isErr = true; // ini error karna minus
                                            }
                                            $tagihan->updated_by = $user;
                                            $tagihan->updated_at = now();
                                            $tagihan->save();
                                            
                                            $keterangan .= ' >> NOTA: '. $value['no_nota'] . ' >> TOTAL BAYAR: ' . $tagihan_pembayaran_detail->dibayar;
                                            if($pph != 0){
                                                $keterangan .= ' >> PPh23: '. $pph;
                                            }
                                            if($i == 0 && $biaya_admin_detail != 0){
                                                $keterangan .= ' >> BIAYA ADMIN: '. $biaya_admin_detail;
                                            }
                                            $i++;

                                        }
                                    }
                                }
                            }
                            else
                            {
                                $tagihan = TagihanPembelian::where('is_aktif', 'Y')->find($value['id_nota']);
                                // $tagihan->no_nota = $value['no_nota'];
                                if($tagihan)
                                {
                                    $tagihan_pembayaran_detail_new = new TagihanPembelianPembayaranDetail();

                                    $tagihan_pembayaran_detail_new->id_tagihan_pembayaran = $id;
                                    $tagihan_pembayaran_detail_new->id_tagihan = $tagihan->id;
                                    $tagihan_pembayaran_detail_new->dibayar = $tagihan_dibayarkan;
                                    //biaya admin di cantolin di index pertama
                                    $tagihan_pembayaran_detail_new->pph_23 =  $pph;
                                    $tagihan_pembayaran_detail_new->biaya_admin = $biaya_admin_detail;
                                    $tagihan_pembayaran_detail_new->total_dibayar = $tagihan_dibayarkan+ $pph + $biaya_admin_detail;
                                    $tagihan_pembayaran_detail_new->bukti_potong = $value['bukti_potong'];
                                    $tagihan_pembayaran_detail_new->created_by = $user;
                                    $tagihan_pembayaran_detail_new->created_at = now();
                                    $tagihan_pembayaran_detail_new->is_aktif = 'Y';
                                    if($tagihan_pembayaran_detail_new->save())
                                    {
                                        $sum_all = $tagihan_dibayarkan + $pph +$biaya_admin_detail;
                                        $tagihan->sisa_tagihan -= $sum_all;
                                        $tagihan->tagihan_dibayarkan += $sum_all;
                                        if($tagihan->sisa_tagihan == 0){
                                            $tagihan->status = 'LUNAS';
                                        }
                                        if($tagihan->sisa_tagihan < 0){
                                            $isErr = true; // ini error karna minus
                                        }
                                        $tagihan->updated_by = $user;
                                        $tagihan->updated_at = now();
                                        $tagihan->save();
                                        $keterangan .= ' >> NOTA: '. $value['no_nota'] . ' >> TOTAL BAYAR: ' . $tagihan_pembayaran_detail_new->dibayar;
                                        if($pph != 0){
                                            $keterangan .= ' >> PPh23: '. $pph;
                                        }
                                        if($i == 0 && $biaya_admin_detail != 0){
                                            $keterangan .= ' >> BIAYA ADMIN: '. $biaya_admin_detail;
                                        }
                                        $i++;

                                    }
                                }
                            }
                        }
                    }
                    // hapus data
                    if($isErr === true){
                        db::rollBack();
                        return redirect()->route('revisi_tagihan_pembelian.index')->with(["status" => "error", "msg" => 'Terjadi kesalahan (Pembayaran melebihi sisa nota)!']);
                    }
                    $is_ketemu_detail = true;
                    if($data['data_deleted'] != null){
                        $array = explode(",", $data['data_deleted']);

                        foreach ($array as $key => $value) {
                            $pembayaran_detail = TagihanPembelianPembayaranDetail::where('is_aktif', 'Y')->where('id',$value)->first();
                            if($pembayaran_detail)
                            {
                                $pembayaran_detail->is_aktif='N';
                                $pembayaran_detail->updated_by = $user;
                                $pembayaran_detail->updated_at = now();
                                if($pembayaran_detail->save())
                                {
                                    $pembelian = TagihanPembelian::where('is_aktif', 'Y')->where('id', $pembayaran_detail->id_tagihan)->first();
                                    $pembelian->sisa_tagihan +=  $pembayaran_detail->total_dibayar;
                                    $pembelian->tagihan_dibayarkan -= $pembayaran_detail->total_dibayar;
                                    $pembelian->status = 'MENUNGGU PEMBAYARAN';
                                    $pembelian->updated_by = $user;
                                    $pembelian->updated_at = now();
                                    $pembelian->save();
                                }
                            }
                            else
                            {
                                $is_ketemu_detail = false;
                                break;
                            }
                            // $del_pembelian = TagihanPembelian::where('is_aktif', 'Y')->find($value);
                            // $del_pembelian->sisa_tagihan =  $del_pembelian->total_tagihan;
                            // $del_pembelian->status = 'MENUNGGU PEMBAYARAN';
                            // $del_pembelian->tagihan_dibayarkan = 0;
                            // $del_pembelian->updated_by = $user;
                            // $del_pembelian->updated_at = now();
                            // $del_pembelian->save();
                        }
                    }
                    if(!$is_ketemu_detail)
                    {
                        db::rollBack();
                        return redirect()->route('revisi_tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Data Detail Pembayaran Tidak ditemukan']);
                    }
                    $pembayaran = TagihanPembelianPembayaran::where('is_aktif', 'Y')->find($id);
                    $pembayaran->catatan = 'REVISI - CATATAN: ' . $data['catatan'];
                    $pembayaran->total_dibayar = floatval(str_replace(',', '', $data['total_bayar']));
                    $pembayaran->total_pph23 = $total_pph;
                    $pembayaran->total_biaya_admin = $biaya_admin;
                    $pembayaran->updated_by = $user;
                    $pembayaran->updated_at = now();
                    $pembayaran->save();
                    if($pembayaran->save())
                    {
                        $history = KasBankTransaction::where('is_aktif','Y')
                        ->where('keterangan_kode_transaksi', $id)
                        ->where('tanggal', $pembayaran->tgl_bayar)
                        ->where('jenis', 'tagihan_supplier')
                        ->first();
                        // dd($history);
                        if($history)
                        {
                            $history->keterangan_transaksi = 'REVISI:'. $keterangan ;
                            $history->kredit = floatval(str_replace(',', '', $data['total_bayar'])) ;
                            // $history->is_aktif = 'N';
                            $history->updated_by = $user;
                            $history->updated_at = now();
                            // $history->save();
                            if( $history->save())
                            {
                                // kurangi kasbank sekarang
                                $kurangiKas = KasBank::where('is_aktif','Y')->find($data['id_kas']);
                                $kurangiKas->saldo_sekarang -= floatval(str_replace(',', '', $data['total_bayar']));
                                $kurangiKas->updated_by = $user;
                                $kurangiKas->updated_at = now();
                                if($kurangiKas->save()){
                                    DB::commit();
                                }
                            }
                        }
                        else
                        {
                            db::rollBack();
                            return redirect()->route('revisi_tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Data Riwayat Pembayaran Tidak ditemukan']);
                        }
                       
                    }
                }
    
            }
            return redirect()->route('revisi_tagihan_pembelian.index')->with(['status' => 'Success', 'msg'  => 'Berhasil revisi nota pembelian!']);
        // } catch (ValidationException $e) {
        //     db::rollBack();
        //     return redirect()->route('revisi_tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Revisi gagal!']);
        // }
        // catch (\Throwable $th) {
        //     db::rollBack();
        //     return redirect()->route('revisi_tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        // }
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
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        try {
            $pembayaran = TagihanPembelianPembayaran::where('is_aktif', 'Y')->find($id);
            if($pembayaran){
                $pembayaran->is_aktif = 'N';
                $pembayaran->updated_by = $user;
                $pembayaran->updated_at = now();
                if($pembayaran->save()){
                    // nonaktifin semua data dan relasinya
                    $pembayaran_detail = TagihanPembelianPembayaranDetail::where('is_aktif', 'Y')
                    ->where('id_tagihan_pembayaran', $id)
                    ->get();
                    foreach ($pembayaran_detail as $key => $value) {
                        $value->is_aktif='N';
                        $value->updated_by = $user;
                        $value->updated_at = now();
                        if($value->save())
                        {
                            $pembelian = TagihanPembelian::where('is_aktif', 'Y')->where('id', $value->id_tagihan)->first();
                            $pembelian->sisa_tagihan +=  $value->total_dibayar;
                            $pembelian->tagihan_dibayarkan -= $value->total_dibayar;
                            $pembelian->status = 'MENUNGGU PEMBAYARAN';
                            $pembelian->updated_by = $user;
                            $pembelian->updated_at = now();
                            $pembelian->save();
                        }
                    }
                }
                $history = KasBankTransaction::where('is_aktif','Y')
                            ->where('keterangan_kode_transaksi', $id)
                            ->where('tanggal', $pembayaran->tgl_bayar)
                            ->where('jenis', 'tagihan_supplier')
                            ->first();
                if($history)
                {
                    $history->is_aktif = 'N';
                    $history->updated_by = $user;
                    $history->updated_at = now();
                    if($history->save()){
                        // kembalikan kasbank sekarang
                        $returnKas = KasBank::where('is_aktif','Y')->find($history['id_kas_bank']);
                        $returnKas->saldo_sekarang += floatval(str_replace(',', '', $history['kredit']));
                        $returnKas->updated_by = $user;
                        $returnKas->updated_at = now();
                        if($returnKas->save()){
                            DB::commit();
                            // return response()->json(['status' => 'success']);
                            return redirect()->route('revisi_tagihan_pembelian.index')->with(['status' => 'Success', 'msg' => 'Pembayaran nota pembelian berhasil dihapus!']);
                        }
                    }
                }
                else
                {
                    db::rollBack();
                    return redirect()->route('revisi_tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Data Riwayat Pembayaran Tidak ditemukan']);
                }
            }    
            return response()->json(['status' => 'error']);
        } 
        catch (\Throwable $th) {
            db::rollBack();
            return redirect()->route('revisi_tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        }
    }
    public function load_data(Request $request)
    {
        if ($request->ajax()) {
            // $data =  TagihanPembelianPembayaran::latest()->with('getPembelian.getSupplier', 'getPembelianDetail')->where('is_aktif', 'Y')->get();
            $data =  TagihanPembelianPembayaran::latest()->with('get_nota_pembayaran_detail.get_nota_value.getSupplier', 'getPembelianDetail')->where('is_aktif', 'Y')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('id_supplier', function($item){ // edit supplier
                    return $item->getSupplier->nama;
                }) 
                ->addColumn('no_nota', function($row){ // tambah kolom baru
                    $no_nota = '';
                    foreach ($row->get_nota_pembayaran_detail as $key => $value) {
                        $no_nota .=  " #" .$value->get_nota_value->no_nota . '<br>';
                    } 
                    return substr($no_nota, 1);
                })
                ->addColumn('tgl_nota', function($row){ // tambah kolom baru
                    $tgl_nota = '';
                    foreach ($row->get_nota_pembayaran_detail as $key => $value) {
                        $tgl_nota .= " #".date("d-M-Y", strtotime($value->get_nota_value->tgl_nota)) . '<br>';
                    } 
                    return substr($tgl_nota, 1);
                })
                ->addColumn('jatuh_tempo', function($row){ // tambah kolom baru
                    $jatuh_tempo = '';
                    foreach ($row->get_nota_pembayaran_detail as $key => $value) {
                        $jatuh_tempo .= " #".date("d-M-Y", strtotime($value->get_nota_value->jatuh_tempo)) . '<br>';
                    } 
                    return substr($jatuh_tempo, 1);
                })
                ->editColumn('total_bayar', function($item){ // edit format uang
                    return number_format($item->total_dibayar);
                }) 
                ->addColumn('action', function($row){
                     // $actionBtn = '
                    //             <div class="btn-group dropleft">
                    //                 <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    //                     <i class="fa fa-list"></i>
                    //                 </button>
                    //                 <div class="dropdown-menu" >
                    //                     <a href="/revisi_tagihan_pembelian/'.$row->id.'/edit" class="dropdown-item edit">
                    //                         <span class="fas fa-pen-alt mr-3"></span> Edit 
                    //                     </a>
                    //                     <button type="button" class="dropdown-item delete" value="'.$row->id.'">
                    //                         <span class="fas fa-trash mr-3"></span> Delete
                    //                     </button>
                    //                 </div>
                    //             </div>';
                    //                 // <a href="#" class="edit btn btn-primary btn-sm"><span class="fas fa-pen-alt"></span> Edit</a> 
                    //                 // <a href="#" class="delete btn btn-danger btn-sm"><span class="fas fa-trash-alt"></span> Delete</a>';
                    // return $actionBtn;
                    $edit=auth()->user()->can('EDIT_REVISI_TAGIHAN_PEMBELIAN')?'<a href="/revisi_tagihan_pembelian/'.$row->id.'/edit" class="dropdown-item edit">
                                            <span class="fas fa-pen-alt mr-3"></span> Edit 
                                        </a>':'';
                    // $delete = auth()->user()->can('DELETE_REVISI_TAGIHAN_PEMBELIAN')?'<button type="button" class="dropdown-item delete" value="/revisi_tagihan_pembelian/'.$row->id.'" data-confirm-delete="true">
                    //                         <span class="fas fa-trash mr-3"></span> Delete
                    //                     </button>':'';
                    $delete=auth()->user()->can('DELETE_REVISI_TAGIHAN_PEMBELIAN')?'<a href="/revisi_tagihan_pembelian/'.$row->id.'" class="dropdown-item edit" data-confirm-delete="true">
                    <span class="fas fa-trash mr-3"></span> Delete
                    </a>':'';
                    $actionBtn = '
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                    '.$edit. $delete.'
                                    </div>
                                </div>';
                    return $actionBtn;
                                    // <a href="#" class="edit btn btn-primary btn-sm"><span class="fas fa-pen-alt"></span> Edit</a> 
                                    // <a href="#" class="delete btn btn-danger btn-sm"><span class="fas fa-trash-alt"></span> Delete</a>';
                })
                ->rawColumns(['action', 'no_nota', 'tgl_nota', 'jatuh_tempo']) // ini buat render raw html, kalo ga pake nanti jadi text biasa
                ->make(true);
        }
    }
    public function delete($id)
    {
        // $user = Auth::user()->id;
        // DB::beginTransaction(); 
        
        // try {
        //     $pembayaran = TagihanPembelianPembayaran::where('is_aktif', 'Y')->find($id);
        //     if($pembayaran){
        //         $pembayaran->catatan = 'HAPUS - ' . isset($pembayaran->catatan)? $pembayaran->catatan:'';
        //         $pembayaran->is_aktif = 'N';
        //         $pembayaran->updated_by = $user;
        //         $pembayaran->updated_at = now();
        //         if($pembayaran->save()){
        //             // nonaktifin semua data dan relasinya
        //             $pembelian = TagihanPembelian::where('is_aktif', 'Y')->where('id_pembayaran', $id)->get();
        //             foreach ($pembelian as $key => $value) {
        //                 $value->catatan = 'HAPUS - ' . isset($value->catatan)? $value->catatan:'';
        //                 $value->is_aktif = 'N';
        //                 $value->updated_by = $user;
        //                 $value->updated_at = now();
        //                 if($value->save()){
        //                     $details = TagihanPembelianDetail::where('is_aktif', 'Y')->where('id_tagihan_pembelian', $value->id)->get();
        //                     foreach ($details as $key => $detail) {
        //                         $detail->is_aktif = 'N';
        //                         $detail->updated_by = $user;
        //                         $detail->updated_at = now();
        //                         $detail->save();
        //                     }
        //                 }
        //             }
        //         }
    
        //         $history = KasBankTransaction::where('is_aktif','Y')
        //                     ->where('keterangan_kode_transaksi', $id)
        //                     ->where('jenis', 'tagihan_pembelian')
        //                     ->first();
        //         $history->keterangan_transaksi = 'HAPUS - ' . isset($history->keterangan_transaksi)? $history->keterangan_transaksi:'';
        //         $history->is_aktif = 'N';
        //         $history->updated_by = $user;
        //         $history->updated_at = now();
        //         if($history->save()){
        //             // kembalikan kasbank sekarang
        //             $returnKas = KasBank::where('is_aktif','Y')->find($history['id_kas_bank']);
        //             $returnKas->saldo_sekarang += floatval(str_replace(',', '', $history['kredit']));
        //             $returnKas->updated_by = $user;
        //             $returnKas->updated_at = now();
        //             if($returnKas->save()){
        //                 DB::commit();
        //                 return response()->json(['status' => 'success']);
        //             }
        //         }

        //     }    

        //     db::rollBack();
        //     return response()->json(['status' => 'error']);
        // } catch (ValidationException $e) {
        //     db::rollBack();
        //     return response()->json(['status' => 'error']);
        // }
        // catch (\Throwable $th) {
        //     db::rollBack();
        //     return redirect()->route('revisi_tagihan_pembelian.index')->with(['status' => 'error', 'msg' => 'Terjadi kesalahan, harap hubungi IT :'.$th->getMessage()]);
        // }
    }
}
