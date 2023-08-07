<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('supplier')
            ->select('*')
            ->where('is_hapus', '=', "N")
            ->get();

            return view('pages.master.supplier.index',[
            'judul' => "Supplier",
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
        return view('pages.master.supplier.create',[
            'judul' => "Supplier",
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
                'nama.required' => 'Nama Harus diisi!',
            ];
            
            $request->validate([
                'nama' => 'required',
            ], $pesanKustom);
    
            $supplier = new Supplier();
            $supplier->jenis_supplier_id = $request->jenis_supplier_id;
            $supplier->nama = $request->nama;
            $supplier->alamat = $request->alamat;
            $supplier->kota_id = $request->kota_id;
            $supplier->telp = $request->telp;
            $supplier->email = $request->email;
            $supplier->npwp = $request->npwp;
            $supplier->no_rek = $request->no_rek;
            $supplier->rek_nama = $request->rek_nama;
            $supplier->bank = $request->bank;
            $supplier->cabang = $request->cabang;
            $supplier->created_at = date("Y-m-d h:i:s");
            $supplier->created_by = 1; // manual
            $supplier->updated_at = date("Y-m-d h:i:s");
            $supplier->updated_by = 1; // manual
            $supplier->is_hapus = "N";
            $supplier->save();

            return redirect()->route('supplier.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
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
        $data = Supplier::where('is_hapus', 'N')->findOrFail($id);

        return view('pages.master.supplier.edit',[
            'data' => $data,
            'judul' => "Supplier",
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        try {
            $pesanKustom = [
                'nama.required' => 'Nama Harus diisi!',
            ];
            $request->validate([
                'nama' => 'required',
            ], $pesanKustom);
    
            $data = $request->collect();
            DB::table('supplier')
                ->where('id', $supplier['id'])
                ->update(array(
                    'jenis_supplier_id' => $data['jenis_supplier_id'],
                    'nama' => $data['nama'],
                    'alamat' => $data['alamat'],
                    'kota_id' => $data['kota_id'],
                    'telp' => $data['telp'],
                    'email' => $data['email'],
                    'npwp' => $data['npwp'],
                    'no_rek' => $data['no_rek'],
                    'rek_nama' => $data['rek_nama'],
                    'bank' => $data['bank'],
                    'cabang' => $data['cabang'],
                    'updated_at'=> date("Y-m-d h:i:s"),
                    'updated_by'=> 1,// masih hardcode nanti diganti cookies
                    'is_hapus' => "N",
                )
            );
            return redirect()->route('supplier.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        DB::table('supplier')
        ->where('id', $supplier['id'])
        ->update(array(
            'is_hapus' => "Y",
            'updated_at'=> date("Y-m-d h:i:s"),
            'updated_by'=> 1, // masih hardcode nanti diganti cookies
          )
        );
        return redirect()->route('supplier.index')->with('status','Success!!');
    }
}
