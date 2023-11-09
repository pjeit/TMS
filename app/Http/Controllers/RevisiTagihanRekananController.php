<?php

namespace App\Http\Controllers;

use App\Models\Sewa;
use App\Models\Supplier;
use App\Models\TagihanPembelian;
use App\Models\TagihanPembelianDetail;
use App\Models\TagihanPembelianPembayaran;
use App\Models\TagihanRekanan;
use App\Models\TagihanRekananPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class RevisiTagihanRekananController extends Controller
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

    public function edit2($id)
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

        $tagihan = TagihanRekananPembayaran::where('is_aktif', 'Y')->find($id);

        return view('pages.revisi.revisi_tagihan_rekanan.edit',[
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
        dd($data);

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
    public function destroy($id)
    {
        //
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
                    $actionBtn = '
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-rounded btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu" >
                                        <a href="/revisi_tagihan_rekanan/'.$row->id.'/edit" class="dropdown-item edit">
                                            <span class="fas fa-pen-alt"></span> Edit
                                        </a>
                                        <a href="{{ route(`tagihan_rekanan.destroy`, ['.$row->id.']) }}" class="dropdown-item update_resi">
                                            <span class="fas fa-trash-alt"></span> Delete
                                        </a>
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
