<?php

namespace App\Http\Controllers;

use App\Models\JenisSupplier;
use App\Models\M_Kota;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:READ_SUPPLIER', ['only' => ['index']]);
		$this->middleware('permission:CREATE_SUPPLIER', ['only' => ['create','store']]);
		$this->middleware('permission:EDIT_SUPPLIER', ['only' => ['edit','update']]);
		$this->middleware('permission:DELETE_SUPPLIER', ['only' => ['destroy']]);  
    }

    public function index()
    {
         \Artisan::call('cache:clear');
        \Artisan::call('route:clear');
        \Artisan::call('optimize:clear');

        $data = DB::table('supplier')
            ->select('supplier.id','supplier.nama','supplier.alamat','supplier.telp','supplier.catatan','jenis_supplier.nama as jenis','m_kota.nama as kota')
            ->join('jenis_supplier', 'supplier.jenis_supplier_id', '=', 'jenis_supplier.id')
            ->join('m_kota', 'supplier.kota_id', '=', 'm_kota.id')
            ->where('supplier.is_aktif', '=', "Y")
            ->groupBy('supplier.id','supplier.nama','supplier.alamat','supplier.telp','supplier.catatan','jenis_supplier.nama','m_kota.nama')
            // ->paginate(10);
            ->get();

        $dataJenisFilter = DB::table('jenis_supplier')
            ->select('*')
            ->where('jenis_supplier.is_aktif', '=', "Y")
            ->orderBy('nama')
            ->get();
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);


            // dd($dataJenisFilter);

            return view('pages.master.supplier.index',[
            'judul' => "Supplier",
            'data' => $data,
            'dataJenisFilter' => $dataJenisFilter
        ]);
    }
    public function filterSupplier(Request $request)
    {
        $jenisFilter = $request->input('jenisFilter');
        // dd($jenisFilter);
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);
        if($jenisFilter==null)
        {
              $data = DB::table('supplier')
            ->select('supplier.id','supplier.nama','supplier.alamat','supplier.telp','supplier.catatan','jenis_supplier.nama as jenis','m_kota.nama as kota')
            ->join('jenis_supplier', 'supplier.jenis_supplier_id', '=', 'jenis_supplier.id')
            ->join('m_kota', 'supplier.kota_id', '=', 'm_kota.id')
            ->where('supplier.is_aktif', '=', "Y")
            ->groupBy('supplier.id','supplier.nama','supplier.alamat','supplier.telp','supplier.catatan','jenis_supplier.nama','m_kota.nama')
            // ->paginate(10);
            ->get();


        }
        else

        {
            $data = DB::table('supplier')
            ->select('supplier.id','supplier.nama','supplier.alamat','supplier.telp','supplier.catatan','jenis_supplier.nama as jenis','m_kota.nama as kota')
            ->join('jenis_supplier', 'supplier.jenis_supplier_id', '=', 'jenis_supplier.id')
            ->join('m_kota', 'supplier.kota_id', '=', 'm_kota.id')
            ->where('supplier.is_aktif', '=', "Y")
            ->where('jenis_supplier.id', '=',  $jenisFilter )
            ->groupBy('supplier.id','supplier.nama','supplier.alamat','supplier.telp','supplier.catatan','jenis_supplier.nama','m_kota.nama')
            // ->paginate(10);
            ->get();

        }

       
        $dataJenisFilter = DB::table('jenis_supplier')
            ->select('*')
            ->where('jenis_supplier.is_aktif', '=', "Y")
            ->get();

            // dd($dataJenisFilter);
        // return response()->json(['datas' => $data]);

        return view('pages.master.supplier.index',[
            'judul' => "Supplier",
            'data' => $data,
            'dataJenisFilter' => $dataJenisFilter
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kota = M_Kota::orderBy('nama', 'ASC')->get();
        $jenis_supplier = JenisSupplier::orderBy('nama', 'ASC')->get();

        return view('pages.master.supplier.create',[
            'judul' => "Supplier",
            'kota' => $kota,
            'jenis_supplier' => $jenis_supplier,
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
        try {
            $pesanKustom = [
                'nama.required' => 'Nama Harus diisi!',
            ];
            
            $request->validate([
                'nama' => 'required',
            ], $pesanKustom);
            // dd($request->collect());
    
            $supplier = new Supplier();
            $supplier->jenis_supplier_id = $request->jenis_supplier_id;
            $supplier->nama = $request->nama;
            $supplier->alamat = $request->alamat;
            $supplier->kota_id = $request->kota_id;
             $telp= $request->telp;

            // misal 085102935062, jadi yang diambil cuman index 0
            if (substr($telp, 0, 1) == "0"&& $telp!=null) {
                //terus di ubah jadi +62 . 85102935062 = +6285102935062 
                $telp = (string) "+62" . substr($telp, 1);
            } else if (substr($telp, 0, 2) == "62"&& $telp!=null) {
                $telp = (string) "+" . $telp;
            }
            $supplier->telp = $telp;
            $supplier->email = $request->email;
            $supplier->npwp = $request->npwp;
            $supplier->no_rek = $request->no_rek;
            $supplier->is_virtual_acc = $request->hiddenVirtual;
            $supplier->rek_nama = $request->rek_nama;
            $supplier->bank = $request->bank;
            $supplier->cabang = $request->cabang;
            $supplier->catatan = $request->catatan;
            $supplier->pph = $request->pph; 
            $supplier->created_at = date("Y-m-d h:i:s");
            $supplier->created_by = $user; // manual
            $supplier->updated_at = date("Y-m-d h:i:s");
            $supplier->updated_by = $user; // manual
            $supplier->is_aktif = "Y";
            $supplier->save();

            return redirect()->route('supplier.index')->with('status','Sukses Menambahkan Supplier Baru!!');
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
        $data = Supplier::where('is_aktif', 'Y')->findOrFail($id);
        $kota = M_Kota::orderBy('nama', 'asc')->get();
        $jenis_supplier = JenisSupplier::orderBy('nama', 'asc')->get();

        return view('pages.master.supplier.edit',[
            'judul' => "Supplier",
            'data' => $data,
            'kota' => $kota,
            'jenis_supplier' => $jenis_supplier,
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
        $user = Auth::user()->id;
        try {
            $pesanKustom = [
                'nama.required' => 'Nama Harus diisi!',
            ];
            $request->validate([
                'nama' => 'required',
            ], $pesanKustom);
    
            $data = $request->collect();
                $telp= $data['telp'];

            // misal 085102935062, jadi yang diambil cuman index 0
            if (substr($telp, 0, 1) == "0"&& $telp!=null) {
                //terus di ubah jadi +62 . 85102935062 = +6285102935062 
                $telp = (string) "+62" . substr($telp, 1);
            } else if (substr($telp, 0, 2) == "62"&& $telp!=null) {
                $telp = (string) "+" . $telp;
            }
            $supplier->telp = $telp;
            DB::table('supplier')
                ->where('id', $supplier['id'])
                ->update(array(
                    'jenis_supplier_id' => $data['jenis_supplier_id'],
                    'nama' => $data['nama'],
                    'alamat' => $data['alamat'],
                    'kota_id' => $data['kota_id'],
                    'telp' => $telp,
                    'email' => $data['email'],
                    'npwp' => $data['npwp'],
                    'no_rek' => $data['no_rek'],
                    'is_virtual_acc' => $data['hiddenVirtual'],
                    'rek_nama' => $data['rek_nama'],
                    'bank' => $data['bank'],
                    'cabang' => $data['cabang'],
                    'catatan' => $data['catatan'],
                      'pph' => $data['pph'],
                    'updated_at'=> date("Y-m-d h:i:s"),
                    'updated_by'=> $user,// masih hardcode nanti diganti cookies
                    'is_aktif' => "Y",
                )
            );
            return redirect()->route('supplier.index')->with('status','Sukses Merubah Data Supplier!!');
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
        $user = Auth::user()->id;
        DB::table('supplier')
        ->where('id', $supplier['id'])
        ->update(array(
            'is_aktif' => "N",
            'updated_at'=> date("Y-m-d h:i:s"),
            'updated_by'=> $user, // masih hardcode nanti diganti cookies
          )
        );
        return redirect()->route('supplier.index')->with('status','Sukses Menghapus Data Supplier!');
    }
}
