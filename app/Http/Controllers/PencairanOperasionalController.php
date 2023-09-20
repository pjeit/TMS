<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helper\VariableHelper;
use App\Models\Grup;
use App\Models\Jaminan;
use App\Models\JobOrder;
use App\Models\JobOrderDetail;
use App\Models\Sewa;
use App\Models\SewaOperasional;

class PencairanOperasionalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Sewa::select('sewa.*','c.id AS id_cust','c.nama AS nama_cust','gt.nama_tujuan','k.nama_panggilan as supir','k.telp1 as telpSupir')
                ->leftJoin('customer AS c', 'c.id', '=', 'sewa.id_customer')
                ->leftJoin('grup_tujuan AS gt', 'sewa.id_grup_tujuan', '=', 'gt.id')
                ->leftJoin('karyawan AS k', 'sewa.id_karyawan', '=', 'k.id')
                ->where('sewa.is_aktif', '=', 'Y')
                ->where('sewa.jenis_tujuan', 'like', '%FTL%')
                ->whereNull('sewa.id_supplier')
                ->where('sewa.status', 'like', "%MENUNGGU UANG JALAN%")
                ->orderBy('c.id','ASC')
                ->get();
        
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        return view('pages.finance.pencairan_operasional.index',[
            'judul' => "Biaya Operasional",
            'data' => $data,
        ]);
    }

    public function pencairan($id)
    {
        $grup = Grup::where('is_aktif', 'Y')->findOrFail($id);
        $data = SewaOperasional::select('sewa_operasional.*', 's.id_customer as id_custoemr', 's.id_jo as id_jo')
            ->leftJoin('sewa as s', function($join) use($id){
                $join->on('sewa_operasional.id_sewa', '=', "s.id_sewa")
                    ->where('s.is_aktif', "Y");
            })
            ->leftJoin('customer as c', function($join) use($id){
                $join->on('c.id', '=', "s.id_customer")
                    ->where('c.is_aktif', "Y");
            })
            ->where('c.grup_id', $id)
            ->where('sewa_operasional.is_aktif', "Y")
            ->get();
        $dataPengaturanKeuangan = DB::table('pengaturan_keuangan')
            ->select('*')
            ->where('pengaturan_keuangan.is_aktif', '=', "Y")
            ->get();
       
        $dataKas = DB::table('kas_bank')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();

        return view('pages.finance.pencairan_operasional.pencairan',[
            'judul'=>"Pencairan Operasional || " . $grup->nama_grup,
            'dataKas' => $dataKas,
            'data' => $data,
            'dataPengaturanKeuangan' =>$dataPengaturanKeuangan,

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
        //
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
        //
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
}
