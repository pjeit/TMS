<?php

namespace App\Http\Controllers;

use App\Helper\ClearCache;
use App\Models\Access;
use App\Models\Permissions;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class AccessController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_ACCESS', ['only' => ['index']]);
		$this->middleware('permission:CREATE_ACCESS', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_ACCESS', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_ACCESS', ['only' => ['destroy']]);  
    }
    
    public function index()
    {
        ClearCache::Clear();

        $data = Role::where('is_aktif', 'Y')->get();

        return view('pages.master.access.index',[
            'judul' => 'Hak Akses',
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
        $role = Role::where('is_aktif', 'Y')->find($id);
        $permissions = Permissions::where('is_aktif', 'Y')->orderBy('menu', 'ASC')->groupBy('menu')->get();
        
        return view('pages.master.access.edit',[
            'judul' => 'Permission',
            'role' => $role,
            'permissions' => $permissions,
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
            if($data['data'] != null){
                $access = Access::where('is_aktif', 'Y')->where('role_id', $id)->delete();
    
                foreach ($data['data'] as $menus) {
                    foreach ($menus as $key => $permission_id) {
                        Access::create([
                            'permission_id' => $permission_id,
                            'role_id' => $id,
                            'created_by' => $user,
                            'created_at' => now(),
                            'updated_by' => $user,
                            'updated_at' => now(),
                            'is_aktif' => 'Y',
                        ]);
                    }
                }
            }
            
            DB::commit();
            return redirect()->route('access.index')->with(['status' => 'Success', 'msg'  => 'Hak Akses berhasil dirubah!']);
        } catch (ValidationException $e) {
            db::rollBack();
            return redirect()->route('access.index')->with(['status' => 'error', 'msg' => 'Hak Akses gagal dirubah!']);
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
}
