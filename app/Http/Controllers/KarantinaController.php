<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use App\Models\JobOrderDetail;
use App\Models\Karantina;
use App\Models\KarantinaDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KarantinaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

        // $customer = JobOrder::where('is_aktif', 'Y')
        //                     ->with('getCustomer', 'getDetails.getTujuan')
        //                     ->groupBy('id_customer')
        //                     ->get();

        $customer = DB::table('job_order as jo')
                        ->leftJoin('job_order_detail as jod', 'jod.id_jo', '=', 'jo.id')
                        ->leftJoin('customer as c', 'c.id', '=', 'jo.id_customer')
                        ->selectRaw('c.id, c.nama, COUNT(jod.is_karantina) as karantina_count')
                        ->where('jod.is_karantina', 'N')
                        ->groupBy('jo.id_customer')
                        ->get();
                        
        // dd($customer[0]->getCustomer);
        return view('pages.finance.karantina.index',[
            'judul' => "Karantina",
            'customer' => $customer,
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
        // dd($data);

        try {
            foreach ($data['data'] as $key => $value) {
                // $JO = JobOrder::where('is_aktif', 'Y')->find($key);
                // $JO->is_karantina = 'Y';
                // $JO->created_by = $user;
                // $JO->created_at = now();
                // $JO->save();

                if($value['nominal'] != null && isset($value['detail'])){
                    $karantina = new Karantina();
                    $karantina->id_jo = $key;
                    $karantina->id_customer = $data['customer'];
                    $karantina->total_operasional = floatval(str_replace(',', '', $value['nominal']));
                    $karantina->created_by = $user;
                    $karantina->created_at = now();
                    if($karantina->save()){
                        foreach ($value['detail'] as $i => $item) {
                            $JOD = JobOrderDetail::where('is_aktif', 'Y')->find($i);
                            $JOD->is_karantina = 'Y';
                            $JOD->created_by = $user;
                            $JOD->created_at = now();
                            $JOD->save();

                            $kontainer = new KarantinaDetail();
                            $kontainer->id_karantina = $karantina->id; 
                            $kontainer->id_jo_detail = $i; 
                            $kontainer->created_by = $user;
                            $kontainer->created_at = now();
                            $kontainer->save();
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('karantina.index')->with(['status' => 'Success', 'msg'  => 'Pembayaran berhasil!']);
        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->route('karantina.index')->with(['status' => 'error', 'msg' => 'Pembayaran gagal!']);
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
        //
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
        //
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

    public function load_data($id)
    {
        $data = JobOrder::where('is_aktif', 'Y')->with('getCustomer', 'getKarantina.getTujuan')
                            ->where('id_customer', $id)
                            // ->selectRaw('c.id, c.nama, COUNT(jod.is_karantina) as karantina_count')
                            ->get();

        // $customer = DB::table('job_order as jo')
        //                 ->leftJoin('job_order_detail as jod', 'jod.id_jo', '=', 'jo.id')
        //                 ->leftJoin('customer as c', 'c.id', '=', 'jo.id_customer')
        //                 ->where('jod.is_karantina', 'N')
        //                 ->get();
        return $data;
    }
}
