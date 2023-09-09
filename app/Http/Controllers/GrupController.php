<?php

namespace App\Http\Controllers;

use App\Models\Grup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use App\Models\GrupTujuan;
use App\Models\GrupTujuanBiaya;
use Symfony\Component\VarDumper\VarDumper;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class GrupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('grup')
            ->where('is_aktif', '=', "Y")
            ->orderBy('nama_grup', 'ASC')
            ->orderBy('nama_pic', 'ASC')
            ->get();

        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
    
            
        return view('pages.master.grup.index',[
                'judul' => "Grup",
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
        return view('pages.master.grup.create',[
            'judul' => "Grup",
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
                'nama_grup.required' => 'Nama Grup Harus diisi!',
                'nama_pic.required' => 'Nama PIC Harus diisi!',
                'telp1.required' => 'Telp 1 Harus diisi!',
                'total_max_kredit.required' => 'Total Max Kredit Harus diisi!',
            ];
            
            $request->validate([
                'nama_grup' => 'required',
                'nama_pic' => 'required',
                'telp1' => 'required',
                'total_max_kredit' => 'required',
            ], $pesanKustom);

            $total_kredit = 0;
            $total_max_kredit = floatval(str_replace(',', '', $request['total_max_kredit']));

            $user = Auth::user()->id;
            $grup = new Grup();
            $grup->nama_grup = $request->nama_grup;
            $grup->nama_pic = $request->nama_pic;
            $grup->email = $request->email;
            if(isset($request['telp1'])){
                if (substr($request['telp1'], 0, 2) === "08") {
                    $telp1 = "8" . substr($request['telp1'], 2);
                }else{
                    $telp1 = $request['telp1'];
                }
            }else{
                $telp1 = '';
            }
            if(isset($request['telp2'])){
                if (substr($request['telp2'], 0, 2) === "08") {
                    $telp2 = "8" . substr($request['telp2'], 2);
                }else{
                    $telp2 = $request['telp2'];
                }
            }else{
                $telp2 = '';
            }
            $grup->telp1 = $telp1;
            $grup->telp2 = $telp2;
            $grup->total_kredit = $total_kredit;
            $grup->total_max_kredit = $total_max_kredit;
            $grup->created_at = now();
            $grup->created_by = $user; 
            $grup->is_aktif = "Y";
            $grup->save();

            return redirect()->route('grup.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->route('grup.index')->with('status','Error!!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Grup  $grup
     * @return \Illuminate\Http\Response
     */
    public function show(Grup $grup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Grup  $grup
     * @return \Illuminate\Http\Response
     */
    public function edit(Grup $grup)
    {
        $data = Grup::where('is_aktif', 'Y')->findOrFail($grup->id);
        
        return view('pages.master.grup.edit',[
            'judul' => "Grup",
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Grup  $grup
     * @return \Illuminate\Http\Response
     */
        public function update(Request $request, Grup $grup)
        {
            try {
                $pesanKustom = [
                    'nama_grup.required' => 'Nama Grup Harus diisi!',
                    'nama_pic.required' => 'Nama PIC Harus diisi!',
                    'telp1.required' => 'Telp 1 Harus diisi!',
                ];
                
                $request->validate([
                    'nama_grup' => 'required',
                    'nama_pic' => 'required',
                    'telp1' => 'required',
                ], $pesanKustom);
        
                $data = $request->collect();
                $user = Auth::user()->id;

                $edit_grup = Grup::where('is_aktif', 'Y')->findOrFail($grup->id);
                $edit_grup->nama_grup = $data['nama_grup'];
                $edit_grup->nama_pic = $data['nama_pic'];
                $edit_grup->email = $data['email'];
                if(isset($data['telp1'])){
                    if (substr($data['telp1'], 0, 2) === "08") {
                        $telp1 = "8" . substr($data['telp1'], 2);
                    }else{
                        $telp1 = $data['telp1'];
                    }
                }else{
                    $telp1 = '';
                }
                if(isset($data['telp2'])){
                    if (substr($data['telp2'], 0, 2) === "08") {
                        $telp2 = "8" . substr($data['telp2'], 2);
                    }else{
                        $telp2 = $data['telp2'];
                    }
                }else{
                    $telp2 = '';
                }
                $edit_grup->telp1 = $telp1;
                $edit_grup->telp2 = $telp2;
                $edit_grup->updated_at = now();
                $edit_grup->updated_by = $user;
                $edit_grup->save();
                return redirect()->route('grup.index')->with('status','Success!!');
                
            } catch (ValidationException $e) {
                return redirect()->route('grup.index')->with('status','Error!!');
                // return redirect()->back()->withErrors($e->errors())->withInput();
            }
        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Grup  $grup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Grup $grup)
    {
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies
        // var_dump($grup); die;
        $del_grup = DB::table('grup')
                    ->where('id', $grup['id'])
                    ->update(array(
                        'is_aktif' => "N",
                        'updated_by'=> $user, 
                        'updated_at'=> now(),
                    ));
        if($del_grup){

            $del_grup_tuj = DB::table('grup_tujuan')->where('grup_id', $grup['id'])
                            ->update(array(
                                'is_aktif' => "N",
                                'updated_by'=> $user, 
                                'updated_at'=> now(),
                            ));

                if($del_grup_tuj){
                    $del_grup_tuj_biy = DB::table('grup_tujuan_biaya')->where('grup_id', $grup['id'])
                                        ->update(array(
                                            'is_aktif' => "N",
                                            'updated_by'=> $user, 
                                            'updated_at'=> now(),
                                        ));
                }
        }
       
        return redirect()->route('grup.index')->with('status', 'Berhasil menghapus data!');
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Berhasil menghapus data!'
        // ]);
    }
}
