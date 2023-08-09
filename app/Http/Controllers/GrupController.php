<?php

namespace App\Http\Controllers;

use App\Models\Grup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use Symfony\Component\VarDumper\VarDumper;

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
            ->get();
            
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
                'email.required' => 'Email Harus diisi!',
                'telp1.required' => 'Telp 1 Harus diisi!',
                'total_max_kredit.required' => 'Total Max Kredit Harus diisi!',
            ];
            
            $request->validate([
                'nama_grup' => 'required',
                'nama_pic' => 'required',
                'email' => 'required',
                'telp1' => 'required',
                'total_max_kredit' => 'required',
            ], $pesanKustom);

            // Menghapus karakter koma
            $total_kredit_str = str_replace(',', '', $request['total_kredit']);
            $total_max_kredit_str = str_replace(',', '', $request['total_max_kredit']);

            // Mengubah string menjadi angka desimal
            $total_kredit = floatval($total_kredit_str);
            $total_max_kredit = floatval($total_max_kredit_str);

            $user = 1;
            $grup = new Grup();
            $grup->nama_grup = $request->nama_grup;
            $grup->nama_pic = $request->nama_pic;
            $grup->email = $request->email;
            $grup->telp1 = $request->telp1;
            $grup->telp2 = $request->telp2;
            $grup->total_kredit = $total_kredit;
            $grup->total_max_kredit = $total_max_kredit;

            $grup->created_at = now();
            $grup->created_by = $user; // manual
            $grup->is_aktif = "Y";
            $grup->save();

            return redirect()->route('grup.index')->with('status','Grup berhasil dibuat!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
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
                'email.required' => 'Email Harus diisi!',
                'telp1.required' => 'Telp 1 Harus diisi!',
                'total_max_kredit.required' => 'Total Max Kredit Harus diisi!',
            ];
            
            $request->validate([
                'nama_grup' => 'required',
                'nama_pic' => 'required',
                'email' => 'required',
                'telp1' => 'required',
                'total_max_kredit' => 'required',
            ], $pesanKustom);
    
            $data = $request->collect();
            
            // Menghapus karakter koma
            $total_kredit_str = str_replace(',', '', $data['total_kredit']);
            $total_max_kredit_str = str_replace(',', '', $data['total_max_kredit']);

            // Mengubah string menjadi angka desimal
            $total_kredit = floatval($total_kredit_str);
            $total_max_kredit = floatval($total_max_kredit_str);

            $user = 1;

            DB::table('grup')
                ->where('id', $grup['id'])
                ->update(array(
                    'nama_grup' => $data['nama_grup'],
                    'nama_pic' => $data['nama_pic'],
                    'total_kredit' => $total_kredit,
                    'total_max_kredit' => $total_max_kredit,
                    'email' => $data['email'],
                    'telp1' => $data['telp1'],
                    'telp2' => $data['telp2'],
                    'updated_at'=> date("Y-m-d h:i:s"),
                    'updated_by'=> $user,
                )
            );
            return redirect()->route('grup.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
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
        $user = 1; // masih hardcode nanti diganti cookies
        
        DB::table('grup')
            ->where('id', $grup['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_by'=> $user, 
                'updated_at'=> date("Y-m-d h:i:s"),
            ));

        return redirect()->route('grup.index')->with('status', 'Berhasil menghapus data!');
    }
}
