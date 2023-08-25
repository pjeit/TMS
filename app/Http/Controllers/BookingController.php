<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\GrupTujuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Booking::where('is_aktif', 'Y')->paginate(5);

        return view('pages.order.booking.index',[
            'judul' => "Booking",
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
        $customers = Customer::where('is_aktif', 'Y')->get();

        return view('pages.order.booking.create',[
            'judul' => "Booking",
            'customers' => $customers,
        ]);
    }

    public function getTujuan($id)
    {
        $cust = Customer::where('id', $id)->first();
        $Tujuan = GrupTujuan::where('grup_id', $cust->grup_id)->where('is_aktif', 'Y')->get();
        return response()->json($Tujuan);
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
            var_dump($request->post()); die; 
            
            $booking = new Booking();
            $booking->catatan = $request->catatan;
            $booking->pph = $request->pph; 
            $booking->created_at = date("Y-m-d h:i:s");
            $booking->created_by = $user; // manual
            $booking->updated_at = date("Y-m-d h:i:s");
            $booking->updated_by = $user; // manual
            $booking->is_aktif = "Y";
            $booking->save();

            return redirect()->route('booking.index')->with('status','Sukses Menambahkan Supplier Baru!!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
