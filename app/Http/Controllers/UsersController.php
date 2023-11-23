<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use App\Models\Karyawan;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_USER', ['only' => ['index']]);
		$this->middleware('permission:CREATE_USER', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_USER', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_USER', ['only' => ['destroy']]);  
    }

    public function index()
    {
        $dataUser = User::where('is_aktif', 'Y')->with('karyawan')->orderBy('created_by', 'ASC')->get();
            
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        return view('pages.master.users.index',[
            'judul' => "User",
            'dataUser' => $dataUser,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
          $dataKaryawan = Karyawan::where([
                                        'is_aktif' => 'Y',
                                        'is_keluar' => 'N',
                                    ])
                                    ->orderBy('nama_panggilan', 'ASC')
                                    ->get();

          $dataRole = Role::where('is_aktif', '=', "Y")->orderBy('id', 'ASC')->get();
          $dataCustomer = DB::table('customer')
            ->select('customer.*')
            ->where('customer.is_aktif', '=', "Y")
            ->get();

           return view('pages.master.users.create',[
            'judul' => "User",
            'dataKaryawan' => $dataKaryawan,
            'dataRole' => $dataRole,
            'dataCustomer'=>$dataCustomer
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
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        $data = $request->collect();
        DB::beginTransaction();

        try {
            $pesanKustom = [
                'username.required' => 'Username Harus diisi!',
                'password.required' => 'Password Harus diisi!',
                'role.required' => 'Posisi Harus diisi!',
            ];
            
            $request->validate([
                'username' => 'required',
                'password' => 'required',
                'role' => 'required',
            ], $pesanKustom);


            $newUser = User::create([
                'username' => $data['username'],
                // 'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'password' => Hash::make($data['password']),
                'role_id' => $data['role'],
                'karyawan_id' => ($data['karyawan']==null)?null:$data['karyawan'],
                'customer_id' => ($data['customer']==null)?null:$data['customer'],
                'created_at' => now(), 
                'created_by' => $user,
                'updated_at' => NULL,
            ]);

            $roles = Role::where('is_aktif', 'Y')->find($data['role']);
            $newUser->assignRole($roles['name']);

            DB::commit();
            return redirect()->route('users.index')->with('status','Sukses Menambahkan User Baru!!');
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function show(User $users)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $dataKaryawan = Karyawan::where([
                            'is_aktif' => 'Y',
                            'is_keluar' => 'N',
                        ])
                        ->orderBy('nama_panggilan', 'ASC')
                        ->get();

        $dataRole = Role::where('is_aktif', '=', "Y")->orderBy('id', 'ASC')->get();
        $dataCustomer = DB::table('customer')
                        ->select('customer.*')
                        ->where('customer.is_aktif', '=', "Y")
                        ->get();

        return view('pages.master.users.edit',[
            'judul' => "User",
            'user' => $user,
            'dataKaryawan' => $dataKaryawan,
            'dataRole' => $dataRole,
            'dataCustomer'=>$dataCustomer
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $updated_by = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        $data = $request->collect();
        DB::beginTransaction();
        // dd($data);

        try {
            $pesanKustom = [
                'username.required' => 'Username Harus diisi!',
                // 'password.required' => 'Password Harus diisi!',
                'role.required' => 'Posisi Harus diisi!',
            ];
            
            $request->validate([
                'username' => 'required',
                // 'password' => 'required',
                'role' => 'required',
            ], $pesanKustom);

            DB::table('user')
                ->where('id', $user['id'])
                ->update(array(
                    'username' => $data['username'],
                    'password' => ($data['password'])?password_hash($data['password'], PASSWORD_DEFAULT):$user['password'],
                    'role_id' => $data['role'],
                    'karyawan_id' => isset($data['karyawan']) ? $data['karyawan'] : null,
                    'customer_id' => isset($data['customer']) ? $data['customer'] : null,
                    'updated_by'=> $updated_by,
                    'updated_at'=> now(),
                )
            ); 

            $roles = Role::where('is_aktif', 'Y')->find($data['role']);
            $user->syncRoles($roles->name);
        //  $user->assignRole('Super Admin');

            DB::commit();
            return redirect()->route('users.index')->with(['status' => 'Success', 'msg' => 'Update Data Berhasil!']);
        } catch (ValidationException $e) {
            DB::rollBack();
            // return redirect()->back()->withErrors($e->errors())->withInput();
            return redirect()->back()->withErrors($e->errors())->with(['status' => 'Success', 'msg' => 'Update Data Gagal!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
        $useras = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        try{
            DB::table('user')
            ->where('id', $user['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> VariableHelper::TanggalFormat(),
                'updated_by'=> $useras, // masih hardcode nanti diganti cookies
              )
            );
             return redirect()->route('users.index')->with('status','Sukses Menghapus Data User!!');

        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }
}
