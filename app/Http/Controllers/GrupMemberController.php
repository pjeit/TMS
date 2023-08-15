<?php

namespace App\Http\Controllers;

use App\Models\Grup;
use App\Models\GrupMember;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GrupMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = DB::table('grup_member as gm')
                        ->leftJoin('grup as g', 'gm.grup_id', '=', 'g.id')
                        ->leftJoin('role as r', 'gm.role_id', '=', 'r.id')
                        ->select('gm.*', 'g.nama_grup as nama_grup', 'r.nama as nama_role')
                        ->where('gm.is_aktif', '=', "Y")
                        ->get();
        
        return view('pages.master.grup_member.index',[
            'judul' => "Grup Member",
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
        $grup = Grup::where('is_aktif', 'Y')->get();
        $role = Role::where('is_aktif', 'Y')->get();

        return view('pages.master.grup_member.create',[
            'judul' => "Grup Member",
            'grup' => $grup,
            'role' => $role,
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
                'grup_id.required' => 'Grup Harus diisi!',
                'role_id.required' => 'Role Harus diisi!',
            ];
            
            $request->validate([
                'nama' => 'required',
                'grup_id' => 'required',
                'role_id' => 'required',
            ], $pesanKustom);

            $user = Auth::user()->id;

            $new_customer = new GrupMember();
            $new_customer->grup_id = $request->grup_id;
            $new_customer->role_id = $request->role_id;
            $new_customer->nama = $request->nama;
            $new_customer->no_rek = $request->no_rek;
            $new_customer->telp1 = $request->telp1;
            $new_customer->telp2 = $request->telp2;
            $new_customer->created_by = $user;
            $new_customer->created_at = now();
            $new_customer->is_aktif = 'Y';
            $new_customer->save();

            return redirect()->route('grup_member.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GrupMember  $grupMember
     * @return \Illuminate\Http\Response
     */
    public function show(GrupMember $grupMember)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GrupMember  $grupMember
     * @return \Illuminate\Http\Response
     */
    public function edit(GrupMember $grupMember)
    {
        $grup = Grup::where('is_aktif', 'Y')->get();
        $role = Role::where('is_aktif', 'Y')->get();
        $data = $grupMember;

        return view('pages.master.grup_member.edit',[
            'judul' => "Grup Member",
            'data' => $data,
            'grup' => $grup,
            'role' => $role,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GrupMember  $grupMember
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GrupMember $grupMember)
    {
        try {
            $pesanKustom = [
                'nama.required' => 'Nama Harus diisi!',
                'grup_id.required' => 'Grup Harus diisi!',
                'role_id.required' => 'Role Harus diisi!',
            ];
            
            $request->validate([
                'nama' => 'required',
                'grup_id' => 'required',
                'role_id' => 'required',
            ], $pesanKustom);

            $user = Auth::user()->id;

            $grupMember->grup_id = $request->grup_id;
            $grupMember->role_id = $request->role_id;
            $grupMember->nama = $request->nama;
            $grupMember->no_rek = $request->no_rek;
            $grupMember->telp1 = $request->telp1;
            $grupMember->telp2 = $request->telp2;
            $grupMember->updated_by = $user;
            $grupMember->updated_at = now();
            $grupMember->is_aktif = 'Y';
            $grupMember->save();

            return redirect()->route('grup_member.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GrupMember  $grupMember
     * @return \Illuminate\Http\Response
     */
    public function destroy(GrupMember $grupMember)
    {
        $user = Auth::user()->id;
        $grupMember->updated_by = $user;
        $grupMember->updated_at = now();
        $grupMember->is_aktif = "N";
        $grupMember->save();

        return redirect()->route('grup_member.index')->with('status','Success!!');
    }
}
