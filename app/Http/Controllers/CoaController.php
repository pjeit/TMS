<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helper\VariableHelper;
use Illuminate\Validation\ValidationException;

class CoaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $dataCOA = DB::table('coa')
            // ->paginate(10);
            ->select('coa.*')
            ->where('coa.is_hapus', '=', "N")
            ->get();
            // dd( $dataCOA);
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
        try {
            $pesanKustom = [
                'no_akun.required' => 'Nomor akun Harus diisi!',
                'nama_jenis.required' => 'Jenis COA harus diisi!',
                'tipe.required' => 'Tipe COA harap dipilih salah satu!',
                // 'catatan.required' => 'The Catatan field is required.',
            ];
            
            $request->validate([
                'no_akun' => 'required',
                'nama_jenis' => 'required',
                'tipe' => 'required|in:1,2',
                // 'catatan' => 'required',
            ], $pesanKustom);
    
            $data = $request->collect();
            // dd($data);
            
            DB::table('coa')
                ->insert(array(
                    'no_akun' => $data['no_akun'],
                    'nama_jenis' => $data['nama_jenis'],
                    'tipe' => $data['tipe']==1?'pengeluaran':'penerimaan',
                    // 'jenis_laporan_keuangan' => $data['jenis_laporan_keuangan'] == null?null:$data['jenis_laporan_keuangan'],
                    'catatan' => $data['catatan'],
                    'created_at'=> /*VariableHelper::TanggalFormat()*/date("Y-m-d h:i:s"), 
                    'created_by'=> 1,// masih hardcode nanti diganti cookies
                    'updated_at'=> date("Y-m-d h:i:s"),
                    'updated_by'=> 1,// masih hardcode nanti diganti cookies
                    'is_hapus' => "N",

                )
            ); 
            return redirect()->route('coa.index')->with('status','Success!!');
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
        ->where('coa.is_hapus', '=', "N")
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
        try {
            $pesanKustom = [
                'no_akun.required' => 'Nomor akun Harus diisi!',
                'nama_jenis.required' => 'Jenis COA harus diisi!',
                'tipe.required' => 'Tipe COA harap dipilih salah satu!',
                // 'catatan.required' => 'The Catatan field is required.',
            ];
            
            $request->validate([
                'no_akun' => 'required',
                'nama_jenis' => 'required',
                'tipe' => 'required|in:1,2',
                // 'catatan' => 'required',
            ], $pesanKustom);
    
            $data = $request->collect();
            DB::table('COA')
                ->where('id', $coa['id'])
                ->update(array(
                    'no_akun' => $data['no_akun'],
                    'nama_jenis' => $data['nama_jenis'],
                    'tipe' => $data['tipe']==1?'pengeluaran':'penerimaan',
                    // 'jenis_laporan_keuangan' => $data['jenis_laporan_keuangan'],
                    'catatan' => $data['catatan'],
                    'updated_at'=> date("Y-m-d h:i:s"),
                    'updated_by'=> 1,// masih hardcode nanti diganti cookies
                    'is_hapus' => "N",

                )
            );
            return redirect()->route('coa.index')->with('status','Success!!');
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
        DB::table('COA')
        ->where('id', $coa['id'])
        ->update(array(
            'is_hapus' => "Y",
            'updated_at'=> date("Y-m-d h:i:s"),
            'updated_by'=> 1, // masih hardcode nanti diganti cookies
            )
        );
        return redirect()->route('coa.index')->with('status','Success!!');
    }
}
