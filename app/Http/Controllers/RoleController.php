<?php

namespace App\Http\Controllers;

use App\Helper\ClearCache;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_ROLE', ['only' => ['index']]);
		$this->middleware('permission:CREATE_ROLE', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_ROLE', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_ROLE', ['only' => ['destroy']]);  
    }

    public function index()
    {
        ClearCache::Clear();

         $dataRole = DB::table('roles')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            // ->paginate(10);
            ->get();
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

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
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

        try {

            $pesanKustom = [
                'nama.required' => 'Nama Role Harus diisi!',
            ];
            
            $request->validate([
                'nama' => 'required',
            ], $pesanKustom);

            $data = $request->collect();

            $dataRolemaxID = DB::table('roles')
            ->where('is_aktif', '=', 'Y')
            ->max('id');
            // dd($dataRolemaxID+1);
            DB::table('roles')
                ->insert(array(
                    'id'=>$dataRolemaxID+1,
                    'name' => strtoupper($data['nama']),
                    'guard_name' => 'web',
                    'created_by'=> $user,
                    'created_at'=>VariableHelper::TanggalFormat(), 
                    'updated_by'=> $user,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'is_aktif' => "Y",

                )
            ); 
            // return redirect()->route('role.index')->with('status','Sukses Menambahkan Role Baru!!');
            return redirect()->route('role.index')->with(['status' => 'Success', 'msg' => 'Berhasil menambah data role!']);

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
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

        try {

            $pesanKustom = [
                'nama.required' => 'Nama Role Harus diisi!',
            ];
            
            $request->validate([
                'nama' => 'required',
            ], $pesanKustom);

            $data = $request->collect();
            DB::table('roles')
            ->where('id', $role['id'])
            ->update(array(
                    'name' => strtoupper($data['nama']),
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                    'is_aktif' => "Y",

                )
            ); 
            // return redirect()->route('role.index')->with('status','Sukses Mengubah Data role!!');
            return redirect()->route('role.index')->with(['status' => 'Success', 'msg' => 'Berhasil mengubah data role!']);

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
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

        try{
            DB::table('roles')
            ->where('id', $role['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> VariableHelper::TanggalFormat(),
                'updated_by'=> $user, // masih hardcode nanti diganti cookies
            )
            );
            // return redirect()->route('role.index')->with('status','Sukses Menghapus Data Role!!');
            return redirect()->route('role.index')->with(['status' => 'Success', 'msg' => 'Berhasil menghapus data role!']);


        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }
}
