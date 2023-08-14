<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
         $dataRole = DB::table('role')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();

            return view('pages.master.role.index',[
            'judul'=>"Role",
            'dataRole' => $dataRole,
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
         //
        return view('pages.master.role.create',[
            'judul'=>"Role",
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
        //
         //
        $user = 1; // masih hardcode nanti diganti cookies atau auth masih gatau

        try {

            $pesanKustom = [
                'nama.required' => 'Nama Role Harus diisi!',
            ];
            
            $request->validate([
                'nama' => 'required',
            ], $pesanKustom);

            $data = $request->collect();
          
            DB::table('role')
                ->insert(array(
                    'nama' => $data['nama'],
                    'created_at'=>VariableHelper::TanggalFormat(), 
                    'created_by'=> $user,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                    'is_aktif' => "Y",

                )
            ); 
            return redirect()->route('role.index')->with('status','Sukses menambahkan role baru!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        //
          return view('pages.master.role.edit',[
            'role'=>$role,
            'judul'=>"Role",

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        //
         //
         //
        $user = 1; // masih hardcode nanti diganti cookies atau auth masih gatau

        try {

            $pesanKustom = [
             
                'nama.required' => 'Nama Role Harus diisi!',
      
            ];
            
            $request->validate([
                'nama' => 'required',
            ], $pesanKustom);

            $data = $request->collect();
          
            DB::table('role')
            ->where('id', $role['id'])
            ->update(array(
                    'nama' => $data['nama'],
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                    'is_aktif' => "Y",

                )
            ); 
            return redirect()->route('role.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        //
         //
        $user = 1; // masih hardcode nanti diganti cookies atau auth masih gatau

        try{
            DB::table('role')
            ->where('id', $role['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> VariableHelper::TanggalFormat(),
                'updated_by'=> $user, // masih hardcode nanti diganti cookies
              )
            );
             return redirect()->route('role.index')->with('status','Sukses Menghapus Data Kas!');

        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }
}