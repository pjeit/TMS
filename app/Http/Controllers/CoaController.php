<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper\VariableHelper;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class CoaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_COA', ['only' => ['index']]);
		$this->middleware('permission:CREATE_COA', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_COA', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_COA', ['only' => ['destroy']]);  
    }

    public function index()
    {
        $dataCOA = DB::table('coa')
            // ->paginate(10);
            ->select('coa.*')
            ->where('coa.is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();


        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        return view('pages.master.Coa.index',[
            'judul'=>"COA",
            'dataCOA' => $dataCOA,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    
        return view('pages.master.Coa.create',[
            'judul'=>"COA",

            // 'dataCOAHead' => $dataCOAHead,
            // 'dataCOADetail' => $dataCOADetail,
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
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

        try {
            $pesanKustom = [
                'no_akun.required' => 'Nomor akun Harus diisi!',
                'nama_jenis.required' => 'Jenis COA harus diisi!',
                'tipe.required' => 'Tipe COA harap dipilih salah satu!',
                'is_show.required' => 'kategori transaksi operasional harap dipilih salah satu!',

                // 'catatan.required' => 'The Catatan field is required.',
            ];
            
            $request->validate([
                'no_akun' => 'required',
                'nama_jenis' => 'required',
                'tipe' => 'required|in:1,2',
                'is_show' => 'required',

                // 'catatan' => 'required',
            ], $pesanKustom);
    
            $data = $request->collect();
            // dd($data);
            
            DB::table('coa')
                ->insert(array(
                    'no_akun' => strtoupper($data['no_akun']),
                    'alias' => strtoupper($data['alias']),
                    'nama_jenis' => strtoupper($data['nama_jenis']),
                    'is_show' => $data['is_show'],
                    'tipe' => $data['tipe']==1?'pengeluaran':'penerimaan',
                    // 'jenis_laporan_keuangan' => $data['jenis_laporan_keuangan'] == null?null:$data['jenis_laporan_keuangan'],
                    'catatan' => strtoupper($data['catatan']),
                    'created_at'=> VariableHelper::TanggalFormat(), 
                    'created_by'=>$user,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=>$user,
                    'is_aktif' => "Y",

                )
            ); 
            
            return redirect()->route('coa.index')->with(['status' => 'Success', 'msg' => 'Berhasil menambah data coa!']);

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Coa  $coa
     * @return \Illuminate\Http\Response
     */
    public function show(Coa $coa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Coa  $coa
     * @return \Illuminate\Http\Response
     */
    public function edit(Coa $coa)
    {
        //
        $dataCOA = DB::table('coa')
        // ->paginate(10);
        ->select('coa.*')
        ->where('coa.is_aktif', '=', "Y")
        ->get();
        // dd( $dataCOA);
        return view('pages.master.Coa.edit',[
            'coa'=>$coa,
            'dataCOA' => $dataCOA,
            'judul'=>"COA",

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Coa  $coa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coa $coa)
    {
        //
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

        try {
            $pesanKustom = [
                'no_akun.required' => 'Nomor akun Harus diisi!',
                'nama_jenis.required' => 'Jenis COA harus diisi!',
                'tipe.required' => 'Tipe COA harap dipilih salah satu!',
                // 'catatan.required' => 'The Catatan field is required.',
                    'is_show.required' => 'kategori transaksi operasional harap dipilih salah satu!',

            ];
            
            $request->validate([
                'no_akun' => 'required',
                'nama_jenis' => 'required',
                'tipe' => 'required|in:1,2',
                // 'catatan' => 'required',
                'is_show' => 'required',

            ], $pesanKustom);

            $data = $request->collect();
            DB::table('COA')
                ->where('id', $coa['id'])
                ->update(array(
                    'no_akun' => strtoupper($data['no_akun']),
                    'alias' => strtoupper($data['alias']),
                    'nama_jenis' => strtoupper($data['nama_jenis']),
                    'tipe' => $data['tipe']==1?'pengeluaran':'penerimaan',
                    'is_show' => $data['is_show'],
                    // 'jenis_laporan_keuangan' => $data['jenis_laporan_keuangan'] == null?null:$data['jenis_laporan_keuangan'],
                    'catatan' => strtoupper($data['catatan']),
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=>  $user,
                    'is_aktif' => "Y",
                )
            );
            // return redirect()->route('coa.index')->with('status','Sukses Mengubah Data Coa!!');
            return redirect()->route('coa.index')->with(['status' => 'Success', 'msg' => 'Berhasil mengubah data coa!']);

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Coa  $coa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coa $coa)
    {
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

        DB::table('COA')
        ->where('id', $coa['id'])
        ->update(array(
            'is_aktif' => "N",
            'updated_at'=>VariableHelper::TanggalFormat(),
            'updated_by'=> $user, // masih hardcode nanti diganti cookies
            )
        );
        // return redirect()->route('coa.index')->with('status','Sukses menghapus Data Coa!!');
            return redirect()->route('coa.index')->with(['status' => 'Success', 'msg' => 'Berhasil menghapus data coa!']);

    }
}
