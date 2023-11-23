<?php

namespace App\Http\Controllers;

use App\Models\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Permissions::where('is_aktif', 'Y')->get();

        return view('pages.master.permission.index',[
            'judul' => 'Permission',
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
        return view('pages.master.permission.create',[
            'judul' => 'Create Permission',
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
        $user = Auth::user()->id;
        $data = $request->collect();
        DB::beginTransaction(); 
        
        try {
            $permission = new Permissions();
            $permission->menu = $data['menu'];
            $permission->name = $data['action'];
            $permission->guard_name = $data['guard_name'];
            $permission->created_by = $user;
            $permission->created_at = now();
            if($permission->save()){
                DB::commit();
                return redirect()->route('permission.index')->with(['status' => 'Success', 'msg'  => 'Tambah data berhasil!']);
            }
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('permission.index')->with(['status' => 'error', 'msg' => 'Tambah data gagal!']);
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
        $data = Permissions::where('is_aktif', 'Y')->find($id);

        return view('pages.master.permission.edit',[
            'judul' => 'Edit Permission',
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
        
        try {
            $permission = Permissions::where('is_aktif', 'Y')->find($id);
            $permission->menu = $data['menu'];
            $permission->name = $data['action'];
            $permission->guard_name = $data['guard_name'];
            $permission->updated_by = $user;
            $permission->updated_at = now();
            if($permission->save()){
                DB::commit();
                return redirect()->route('permission.index')->with(['status' => 'Success', 'msg'  => 'Edit data berhasil!']);
            }
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('permission.index')->with(['status' => 'error', 'msg' => 'Edit data gagal!']);
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
        
        try {
            $permission = Permissions::where('is_aktif', 'Y')->find($id);
            $permission->updated_by = $user;
            $permission->updated_at = now();
            $permission->is_aktif = 'N';
            if($permission->save()){
                DB::commit();
                return redirect()->route('permission.index')->with(['status' => 'Success', 'msg'  => 'Hapus data berhasil!']);
            }
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('permission.index')->with(['status' => 'error', 'msg' => 'Hapus data gagal!']);
        }
    }
}
