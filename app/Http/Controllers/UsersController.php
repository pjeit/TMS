<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Helper\VariableHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dataUser = DB::select(DB::raw("SELECT user.id,user.username as 'username',role.nama as 'role',karyawan.nama_panggilan,m_kota.nama as 'kota' 
                                FROM user
                                left join karyawan on user.karyawan_id = karyawan.id
                                left JOIN m_kota on karyawan.m_kota_id = m_kota.id
                                LEFT JOIN role on user.role_id = role.id
                                where user.is_aktif = 'Y'  or  user.is_aktif = 'Y' and user.karyawan_id is null"));
        //   $dataUser = DB::table('user')
        //     ->select('user.id','role.nama as role','karyawan.nama_panggilan')
        //     // ->select('user.id','user.username','role.nama as role','karyawan.nama_panggilan','m_kota.nama as kota')
        //     ->leftJoin('role', 'user.role_id', '=', 'role.id')
        //     ->leftJoin('karyawan', 'user.karyawan_id', '=', 'karyawan.id')
        //     // ->leftJoin('m_kota','karyawan.m_kota_id','=','m_kota.id')
        //     ->where('karyawan.is_aktif', 'Y')
        //     ->where('karyawan.is_keluar', '=', 'N')
        //     ->where('role.is_aktif', '=', 'Y')
        //     ->where('user.is_aktif', '=', 'Y')
        //     // ->orWhereNull('user.karyawan_id')
        //     ->get();
            // var_dump($dataUser); die;

        //    $dataUser = DB::table('users')
        //     ->select('users.*')
        //     ->where('users.is_aktif', '=', "Y")
        //     ->get();

        // bee pakek kasih ae
        // dd($dataUser);
          $dataKaryawan = DB::table('karyawan')
            ->select('karyawan.*')
            ->where('karyawan.is_aktif', '=', "Y")
            ->where('karyawan.is_keluar', '=', "N")
            ->get();

          $dataRole = DB::table('role')
            ->select('role.*')
            ->where('role.is_aktif', '=', "Y")
            ->get();

            return view('pages.master.users.index',[
            'judul' => "User",
            'dataUser' => $dataUser,
            'dataKaryawan' => $dataKaryawan,
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
        
          $dataUser = DB::table('user')
            ->select('user.*')
            ->where('user.is_aktif', '=', "Y")
            ->get();

          $dataKaryawan = DB::table('karyawan')
            ->select('karyawan.*')
            ->where('karyawan.is_aktif', '=', "Y")
            ->where('karyawan.is_keluar', '=', "N")
            ->get();

          $dataRole = DB::table('role')
            ->select('role.*')
            ->where('role.is_aktif', '=', "Y")
            ->get();
          $dataCustomer = DB::table('customer')
            ->select('customer.*')
            ->where('customer.is_aktif', '=', "Y")
            ->get();
           return view('pages.master.users.create',[
            'judul' => "User",
            'dataUser' => $dataUser,
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
        //
        $user = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

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

            $data = $request->collect();
            DB::table('user')
                ->insert(array(
                    'username' => $data['username'],
                    // 'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                    'password' => Hash::make($data['password']),
                    'role_id' => $data['role'],
                    'karyawan_id' => ($data['karyawan']==null)?null:$data['karyawan'],
                    'customer_id' => ($data['customer']==null)?null:$data['customer'],
                    'created_at'=>VariableHelper::TanggalFormat(), 
                    'created_by'=> $user,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $user,
                    'is_aktif' => "Y",

                )
            ); 
            return redirect()->route('users.index')->with('status','Sukses menambahkan user baru!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function show(Users $users)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function edit(Users $user)
    {
        //
         $dataKaryawan = DB::table('karyawan')
            ->select('karyawan.*')
            ->where('karyawan.is_aktif', '=', "Y")
            ->where('karyawan.is_keluar', '=', "N")
            ->get();

          $dataRole = DB::table('role')
            ->select('role.*')
            ->where('role.is_aktif', '=', "Y")
            ->get();
             $dataCustomer = DB::table('customer')
            ->select('customer.*')
            ->where('customer.is_aktif', '=', "Y")
            ->get();
            // dd($user);
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
    public function update(Request $request, Users $user)
    {
        //
          //
        $usersCrt = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau

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

            $data = $request->collect();
            // dd($user);
            DB::table('user')
                 ->where('id', $user['id'])
                ->update(array(
                    'username' => $data['username'],
                    'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                    'role_id' => $data['role'],
                    'karyawan_id' => ($data['karyawan']==null)?null:$data['karyawan'],
                    'customer_id' => ($data['customer']==null)?null:$data['customer'],
                    'created_at'=>VariableHelper::TanggalFormat(), 
                    'created_by'=> $usersCrt,
                    'updated_at'=> VariableHelper::TanggalFormat(),
                    'updated_by'=> $usersCrt,
                    'is_aktif' => "Y",

                )
            ); 
            return redirect()->route('users.index')->with('status','Sukses merubah data user!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function destroy(Users $user)
    {
        //
        $useras = Auth::user()->id; // masih hardcode nanti diganti cookies atau auth masih gatau
        try{
            DB::table('role')
            ->where('id', $user['id'])
            ->update(array(
                'is_aktif' => "N",
                'updated_at'=> VariableHelper::TanggalFormat(),
                'updated_by'=> $useras, // masih hardcode nanti diganti cookies
              )
            );
             return redirect()->route('users.index')->with('status','Sukses Menghapus Data User!');

        }
        catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }
}
