<?php

namespace App\Http\Controllers;

use App\Models\Grup;
use App\Models\GrupMember;
use App\Models\M_Kota;
use App\Models\Marketing;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MarketingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_MARKETING', ['only' => ['index']]);
		$this->middleware('permission:CREATE_MARKETING', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_MARKETING', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_MARKETING', ['only' => ['destroy']]);  
    }
    
    public function index()
    {

        $data = DB::table('grup_member as gm')
                        ->leftJoin('grup as g', 'gm.grup_id', '=', 'g.id')
                        ->leftJoin('roles as r', 'gm.role_id', '=', 'r.id')
                        ->select('gm.*', 'g.nama_grup as nama_grup', 'r.name as nama_role')
                        ->where('gm.is_aktif', '=', "Y")
                        ->get();
        
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        return view('pages.master.marketing.index',[
            'judul' => "Marketing Grup",
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
        $kota = M_Kota::orderBy('nama', 'ASC')->get();

        return view('pages.master.marketing.create',[
            'judul' => "Marketing Grup",
            'grup' => $grup,
            'role' => $role,
            'kota' => $kota,
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
                // 'role_id.required' => 'Role Harus diisi!',
            ];
            
            $request->validate([
                'nama' => 'required',
                'grup_id' => 'required',
                // 'role_id' => 'required',
            ], $pesanKustom);

            $user = Auth::user()->id;
        
            // hardcode langsung id marketing
            $role = 6;
            $roleMarketing = Role::where('is_aktif', 'Y')->where('name', 'Marketing')->first();
            if(isset($roleMarketing)){
                $role = $roleMarketing->id;
            }

            $new_customer = new Marketing();
            $new_customer->grup_id = $request->grup_id;

            $new_customer->role_id = $role; // marketing
            $new_customer->nama = $request->nama;
            $new_customer->no_rek = $request->no_rek;
            $new_customer->atas_nama = $request->atas_nama;
            $new_customer->bank = $request->bank;
            $new_customer->cabang = $request->cabang;
            $telp1 = isset($request->telp1) ? (substr($request->telp1, 0, 2) === "08" ? "8" . substr($request->telp1, 2) : $request->telp1) : '';
            $new_customer->telp1 = $telp1;
            $new_customer->kota_id = $request->kota_id;
            $new_customer->created_by = $user;
            $new_customer->created_at = now();
            $new_customer->is_aktif = 'Y';
            $new_customer->save();

                return redirect()->route('marketing.index')->with(['status' => 'Success', 'msg' => 'Data berhasil dibuat!']);
        } catch (ValidationException $e) {
            // return redirect()->back()->withErrors($e->errors())->withInput();
            return redirect()->route('marketing.index')->with(['status' => 'Error', 'msg' => $e->getMessage()]);

        }
        catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('marketing.index')->with(['status' => 'Error', 'msg' => $th->getMessage()]);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GrupMember  $grupMember
     * @return \Illuminate\Http\Response
     */
    public function show(Marketing $marketing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GrupMember  $grupMember
     * @return \Illuminate\Http\Response
     */
    public function edit(Marketing $marketing)
    {
        $grup = Grup::where('is_aktif', 'Y')->get();
        $role = Role::where('is_aktif', 'Y')->get();
        $data = $marketing;
        $kota = M_Kota::orderBy('nama', 'ASC')->get();

        return view('pages.master.marketing.edit',[
            'judul' => "Marketing Grup",
            'data' => $data,
            'grup' => $grup,
            'role' => $role,            
            'kota' => $kota,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GrupMember  $grupMember
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Marketing $marketing)
    {
        try {
            $pesanKustom = [
                'nama.required' => 'Nama Harus diisi!',
                'grup_id.required' => 'Grup Harus diisi!',
                // 'role_id.required' => 'Role Harus diisi!',
            ];
            
            $request->validate([
                'nama' => 'required',
                'grup_id' => 'required',
                // 'role_id' => 'required',
            ], $pesanKustom);

            $user = Auth::user()->id;
            $role = 6;
            $roleMarketing = Role::where('is_aktif', 'Y')->where('nama', 'Marketing')->first();
            if(isset($roleMarketing)){
                $role = $roleMarketing->id;
            }

            $marketing->grup_id = $request->grup_id;
            $marketing->role_id = $role;
            $marketing->nama = $request->nama;
            $marketing->no_rek = $request->no_rek;
            $marketing->atas_nama = $request->atas_nama;
            $marketing->bank = $request->bank;
            $marketing->cabang = $request->cabang;
            $telp1 = isset($request->telp1) ? (substr($request->telp1, 0, 2) === "08" ? "8" . substr($request->telp1, 2) : $request->telp1) : '';
            $marketing->telp1 = $telp1;
            $marketing->kota_id = $request->kota_id;
            $marketing->updated_by = $user;
            $marketing->updated_at = now();
            $marketing->is_aktif = 'Y';
            $marketing->save();

            // return redirect()->route('marketing.index')->with('status','Success!!');
            return redirect()->route('marketing.index')->with(['status' => 'Success', 'msg' =>'Sukses edit data marketing']);

        } catch (ValidationException $e) {
            // return redirect()->back()->withErrors($e->errors())->withInput();
            return redirect()->route('marketing.index')->with(['status' => 'Error', 'msg' => $e->errors()]);

        }
        catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('marketing.index')->with(['status' => 'Error', 'msg' => $th->getMessage()]);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GrupMember  $grupMember
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marketing $marketing)
    {
        try {
            //code...
            $user = Auth::user()->id;
            $marketing->updated_by = $user;
            $marketing->updated_at = now();
            $marketing->is_aktif = "N";
            $marketing->save();
            return redirect()->route('marketing.index')->with(['status' => 'Success', 'msg' => 'Berhasil hapus data marketing!']);

        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('marketing.index')->with(['status' => 'Error', 'msg' => $th->getMessage()]);

        }
       

        // return redirect()->route('marketing.index')->with('status','Success!!');
    }
}
