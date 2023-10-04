<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Grup;
use App\Models\M_Kota;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Customer::where('is_aktif', 'Y')->get();
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        
        return view('pages.master.customer.index',[
            'judul' => "Customer",
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
        $grups = Grup::where('is_aktif', 'Y')->get();
        $kota = M_Kota::get();

        return view('pages.master.customer.create',[
            'judul' => "Customer",
            'grups' => $grups,
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
                'kode.required' => 'Kode Harus diisi!',
                'nama.required' => 'Nama Harus diisi!',
            ];
            
            $request->validate([
                'kode' => 'required',
                'nama' => 'required',
            ], $pesanKustom);
            $user = Auth::user()->id;
            $max_kredit = ($request['maks_kredit'] != '-')? floatval(str_replace(',', '', $request['maks_kredit'])):0;

            $new_customer = new Customer();
            $new_customer->kode = $request->kode;
            $new_customer->nama = $request->nama;
            $new_customer->grup_id = $request->grup_id;
            $new_customer->npwp = $request->npwp;
            $new_customer->alamat = $request->alamat;
            $new_customer->kota_id = $request->kota_id;
            // $new_customer->telp1 = $request->telp1;
            // $new_customer->telp2 = $request->telp2;
            // $new_customer->email = $request->email;
            $new_customer->alamat = $request->alamat;
            $new_customer->catatan = $request->catatan;
            $new_customer->kredit_sekarang = 0/*($request->kredit_sekarang == 0)? NULL:$request->kredit_sekarang*/;
            // $new_customer->max_kredit = $max_kredit;
            $new_customer->ketentuan_bayar = $request->ketentuan_bayar;
            $new_customer->created_by = $user;
            $new_customer->created_at = now();
            $new_customer->save();
            // if($new_customer->save()){
            //     if($max_kredit != null || $max_kredit != 0){
            //         $grup = Grup::where('id', $request->grup_id)->where('is_aktif', 'Y')->findOrFail($request->grup_id);
            //         $grup->total_max_kredit += $max_kredit;
            //         $grup->updated_by = $user;
            //         $grup->updated_at = now();
            //         $grup->save();
            //     }
            // }

            return redirect()->route('customer.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        $grups = Grup::where('is_aktif', 'Y')->get();
        $kota = M_Kota::get();

        return view('pages.master.customer.edit',[
            'judul' => "Customer",
            'data' => $customer,
            'grups' => $grups,
            'kota' => $kota,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        try {
            $pesanKustom = [
                'kode.required' => 'Kode Harus diisi!',
                'nama.required' => 'Nama Harus diisi!',
            ];
            
            $request->validate([
                'kode' => 'required',
                'nama' => 'required',
            ], $pesanKustom);
            $user = Auth::user()->id;
            // $max_kredit = ($request['max_kredit'] != '-')? floatval(str_replace(',', '', $request['max_kredit'])):0;
            // $temp_max_kredit = $customer->max_kredit;
            
            $customer->grup_id = $request->grup_id;
            $customer->kode = $request->kode;
            $customer->nama = $request->nama;
            $customer->npwp = $request->npwp;
            $customer->alamat = $request->alamat;
            $customer->kota_id = $request->kota_id;
            // $customer->telp1 = $request->telp1;
            // $customer->telp2 = $request->telp2;
            // $customer->email = $request->email;
            $customer->catatan = $request->catatan;
            $customer->kredit_sekarang = ($request->kredit_sekarang == 0)? NULL:floatval(str_replace(',', '', $request->kredit_sekarang));
            // $customer->max_kredit = $max_kredit;
            $customer->ketentuan_bayar = $request->ketentuan_bayar;
            $customer->updated_by = $user;
            $customer->updated_at = now();
            $customer->save();
            // if($customer->save()){
            //     if($max_kredit != null || $max_kredit != 0){
            //         $grup = Grup::where('id', $request->grup_id)->where('is_aktif', 'Y')->findOrFail($request->grup_id);
            //         if($grup){
            //             if($max_kredit > $temp_max_kredit){
            //                 // kalo berubahnya lebih gede, max di grup ditambah
    
            //                 $max_kredit = $max_kredit-$temp_max_kredit;
            //                 $grup->total_max_kredit += $max_kredit;
            //             }elseif($max_kredit < $temp_max_kredit){
            //                 // kalo berubahnya lebih kecil, max di grup dikurangi
    
            //                 $max_kredit = $temp_max_kredit-$max_kredit;
            //                 $grup->total_max_kredit -= $max_kredit;
            //             }
            //             $grup->updated_by = $user;
            //             $grup->updated_at = now();
            //             $grup->save();
            //         }
            //     }
            // }

            return redirect()->route('customer.index')->with('status','Success!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $user = Auth::user()->id;
        $customer->updated_by = $user;
        $customer->updated_at = now();
        $customer->is_aktif = "N";
        $customer->save();

        return redirect()->route('customer.index')->with('status','Success!!');
    }
}
