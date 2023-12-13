<?php

namespace App\Http\Controllers;

use App\Models\KasBank;
use App\Models\KasBankTransaction;
use App\Models\Sewa;
use App\Models\Supplier;
use App\Models\TagihanPembelian;
use App\Models\TagihanPembelianDetail;
use App\Models\TagihanPembelianPembayaran;
use App\Models\TagihanRekanan;
use App\Models\TagihanRekananDetail;
use App\Models\TagihanRekananPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;
use App\Helper\CoaHelper;
class RevisiTagihanRekananController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_REVISI_TAGIHAN_REKANAN', ['only' => ['index']]);
		$this->middleware('permission:CREATE_REVISI_TAGIHAN_REKANAN', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_REVISI_TAGIHAN_REKANAN', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_REVISI_TAGIHAN_REKANAN', ['only' => ['destroy']]);  
    }

    public function index()
    {
        $data =  TagihanRekananPembayaran::where('is_aktif', 'Y')->get();
        // dd($data);
        return view('pages.revisi.revisi_tagihan_rekanan.index',[
            'judul' => "Revisi Tagihan Rekanan",
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

        $data = TagihanRekananPembayaran::where('is_aktif', 'Y')->find($id);

        // $data_tagihan = TagihanPembelian::with('getDetails')->where('is_aktif', 'Y')->whereIn('id', $data['idTagihan'])->get();

        // dd($data_tagihan);
        
        return view('pages.revisi.revisi_tagihan_rekanan.edit',[
            'judul' => "Revisi Tagihan Rekanan",
            'dataKas' => $dataKas,
            'supplier' => $supplier,
            'data' => $data,
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
        $keterangan = 'TAGIHAN REKANAN: '. $data['nama_supplier'] . ' -';
        $i = 0;
        // dd($data);

        // history kas bank di nonaktifkan
        $history = KasBankTransaction::where('is_aktif','Y')
                                    ->where('keterangan_kode_transaksi', $id)
                                    ->where('jenis', 'TAGIHAN_REKANAN')
                                    ->first();
        $history->keterangan_transaksi = 'REVISI OFF - CATATAN: '. $data['catatan'] . ' || ' .$history->keterangan_transaksi;
        $history->is_aktif = 'N';
        $history->updated_by = $user;
        $history->updated_at = now();
        $history->save();

        // dana dikembalikan
        $returnKas = KasBank::where('is_aktif','Y')->find($history->id_kas_bank);
        $returnKas->saldo_sekarang += $history->kredit;
        $returnKas->updated_by = $user;
        $returnKas->updated_at = now();
        $returnKas->save();

        try {
            if(isset($data['data'])){
                foreach ($data['data'] as $key => $value) {
                    $tagihan = TagihanRekanan::where('is_aktif', 'Y')->find($key);
                    $tagihan->no_nota = $value['no_nota'];
                    $tagihan->pph = $value['pph'];
                    $tagihan->bukti_potong = $value['bukti_potong'];
                    $tagihan->biaya_admin = $value['biaya_admin'];
                    $tagihan->total_tagihan = $value['total_tagihan'];
                    $tagihan->tagihan_dibayarkan = $value['tagihan_dibayarkan'];
                    $tagihan->updated_by = $user;
                    $tagihan->updated_at = now();
                    $tagihan->save();

                    $keterangan .= ' #NOTA: '. $value['no_nota'] . ' #TOTAL BAYAR: ' . $tagihan->tagihan_dibayarkan;
                    if($value['pph'] != 0){
                        $keterangan .= ' #PPh23: '. $value['pph'];
                    }
                    if($i == 0 && $value['biaya_admin'] != 0){
                        $keterangan .= ' #BIAYA ADMIN: '. $value['biaya_admin'];
                    }
                    $i++;
                }
            }

            // hapus data
            if($data['data_deleted'] != null){
                $array = explode(",", $data['data_deleted']);
                foreach ($array as $key => $value) {
                    $del_rekanan = TagihanRekanan::where('is_aktif', 'Y')->find($value);
                    $del_rekanan->updated_by = $user;
                    $del_rekanan->updated_at = now();
                    $del_rekanan->is_aktif = 'N';
                    if($del_rekanan->save()){
                        $del_details = TagihanRekananDetail::where('is_aktif', 'Y')->where('id_tagihan_rekanan', $value)->get();
                        foreach ($del_details as $key => $item) {
                            $item->updated_by = $user;
                            $item->updated_at = now();
                            $item->is_aktif = 'N';
                            $item->save();
                        }
                    }
                }
            }

            $pembayaran = TagihanRekananPembayaran::where('is_aktif', 'Y')->find($id);
            $pembayaran->catatan = 'REVISI - CATATAN: ' . $data['catatan'] . ' || ' . $pembayaran->catatan;
            $pembayaran->total_bayar = floatval(str_replace(',', '', $data['total_bayar']));
            $pembayaran->updated_by = $user;
            $pembayaran->updated_at = now();
            $pembayaran->save();

            // insert history transaksi baru 
            $new_history = new KasBankTransaction();
            $new_history->tanggal = $history->tanggal;
            $new_history->id_kas_bank = $data['id_kas'];
            $new_history->debit = 0;
            $new_history->kredit = floatval(str_replace(',', '', $data['total_bayar']));
            $new_history->kode_coa = CoaHelper::DataCoa(5005); // coa rekanan
            $new_history->jenis = 'TAGIHAN_REKANAN';
            $new_history->keterangan_transaksi = $keterangan . '(REVISI) - ' . $data['catatan'];
            $new_history->keterangan_kode_transaksi = $pembayaran->id;
            $new_history->created_by = $user;
            $new_history->created_at = now();
            if($new_history->save()){
                // kurangi kasbank sekarang
                $kurangiKas = KasBank::where('is_aktif','Y')->find($data['id_kas']);
                $kurangiKas->saldo_sekarang -= floatval(str_replace(',', '', $data['total_bayar']));
                $kurangiKas->updated_by = $user;
                $kurangiKas->updated_at = now();
                if($kurangiKas->save()){
                    DB::commit();
                }

            }

            return redirect()->route('revisi_tagihan_rekanan.index')->with(['status' => 'Success', 'msg'  => 'Revisi berhasil!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('revisi_tagihan_rekanan.index')->with(['status' => 'error', 'msg' => 'Revisi gagal!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $user = Auth::user()->id;
        DB::beginTransaction(); 
        // dd($id);
        try {
            $pembayaran = TagihanRekananPembayaran::where('is_aktif', 'Y')->find($id);
            if($pembayaran){
                $pembayaran->catatan = 'HAPUS - ' . isset($pembayaran->catatan)? $pembayaran->catatan:'';
                $pembayaran->is_aktif = 'N';
                $pembayaran->updated_by = $user;
                $pembayaran->updated_at = now();
                if($pembayaran->save()){
                    // nonaktifin semua data dan relasinya
                    $rekanan = TagihanRekanan::where('is_aktif', 'Y')->where('id_pembayaran', $id)->get();
                    foreach ($rekanan as $key => $value) {
                        $value->catatan = 'HAPUS - ' . isset($value->catatan)? $value->catatan:'';
                        $value->is_aktif = 'N';
                        $value->updated_by = $user;
                        $value->updated_at = now();
                        if($value->save()){
                            $details = TagihanRekananDetail::where('is_aktif', 'Y')->where('id_tagihan_rekanan', $value->id)->get();
                            foreach ($details as $key => $detail) {
                                $detail->catatan = 'HAPUS - ' . $detail->catatan;
                                $detail->is_aktif = 'N';
                                $detail->updated_by = $user;
                                $detail->updated_at = now();
                                $detail->save();
                            }
                        }
                    }
                }
    
                $history = KasBankTransaction::where('is_aktif','Y')
                            ->where('keterangan_kode_transaksi', $id)
                            ->where('jenis', 'TAGIHAN_REKANAN')
                            ->first();
                $history->keterangan_transaksi = 'HAPUS - ' . isset($history->keterangan_transaksi)? $history->keterangan_transaksi:'';
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
                        return response()->json(['status' => 'success']);
                    }
                }

            }    

            db::rollBack();
            return response()->json(['status' => 'error']);
        } catch (ValidationException $e) {
            db::rollBack();
            return response()->json(['status' => 'error']);
        }
    }

    public function load_data(Request $request)
    {
        // return DataTables::of(User::query())->toJson();
        if ($request->ajax()) {
            // $data = TagihanRekanan::latest()->with('getSupplier')->WHERE('is_aktif', 'Y')->where('status', 'LUNAS')->get();
            $data =  TagihanRekananPembayaran::with('getRekanan.getSupplier', 'getRekananDetail')->where('is_aktif', 'Y')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('id_supplier', function($item){ // edit supplier
                    return $item->getSupplier->nama;
                }) 
                ->addColumn('no_nota', function($row){ // tambah kolom baru
                    $no_nota = '';
                    foreach ($row->getRekanan as $key => $value) {
                        $no_nota .=  " #" .$value->no_nota . '<br>';
                    } 
                    return substr($no_nota, 1);
                })
                ->addColumn('tgl_nota', function($row){ // tambah kolom baru
                    $tgl_nota = '';
                    foreach ($row->getRekanan as $key => $value) {
                        $tgl_nota .= " #".date("d-M-Y", strtotime($value->tgl_nota)) . '<br>';
                    } 
                    return substr($tgl_nota, 1);
                })
                ->addColumn('jatuh_tempo', function($row){ // tambah kolom baru
                    $jatuh_tempo = '';
                    foreach ($row->getRekanan as $key => $value) {
                        $jatuh_tempo .= " #".date("d-M-Y", strtotime($value->jatuh_tempo)) . '<br>';
                    } 
                    return substr($jatuh_tempo, 1);
                })
                ->editColumn('total_bayar', function($item){ // edit format uang
                    return number_format($item->total_bayar);
                }) 
                ->addColumn('action', function($row){
                    // $actionBtn = '
                    //             <div class="btn-group dropleft">
                    //                 <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    //                     <i class="fa fa-list"></i>
                    //                 </button>
                    //                 <div class="dropdown-menu" >
                    //                     <a href="/revisi_tagihan_rekanan/'.$row->id.'/edit" class="dropdown-item edit">
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
                    $edit=auth()->user()->can('EDIT_REVISI_TAGIHAN_REKANAN')?'<a href="/revisi_tagihan_rekanan/'.$row->id.'/edit" class="dropdown-item edit">
                                            <span class="fas fa-pen-alt mr-3"></span> Edit 
                                        </a>':'';
                    $delete = auth()->user()->can('DELETE_REVISI_TAGIHAN_REKANAN')?'<button type="button" class="dropdown-item delete" value="'.$row->id.'">
                                            <span class="fas fa-trash mr-3"></span> Delete
                                        </button>':'';
                    $actionBtn = '
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                        '.$edit.$delete.'
                                    </div>
                                </div>';
                                    // <a href="#" class="edit btn btn-primary btn-sm"><span class="fas fa-pen-alt"></span> Edit</a> 
                                    // <a href="#" class="delete btn btn-danger btn-sm"><span class="fas fa-trash-alt"></span> Delete</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'no_nota', 'tgl_nota', 'jatuh_tempo']) // ini buat render raw html, kalo ga pake nanti jadi text biasa
                ->make(true);
        }
    }
}
