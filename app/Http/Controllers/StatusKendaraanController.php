<?php

namespace App\Http\Controllers;

use App\Models\StatusKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Auth;

class StatusKendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = 'Data akan dihapus!';
        $text = "Apakah Anda yakin?";
        $confirmButtonText = 'Ya';
        $cancelButtonText = "Batal";
        confirmDelete($title, $text, $confirmButtonText, $cancelButtonText);

         $dataStatusKendaraan = StatusKendaraan::where('is_aktif','Y')
            ->select('*')
            ->where('is_aktif', '=', "Y")
            ->get();
    
        return view('pages.asset.status_kendaraan.index',[
            'judul'=>"Trucking Order",
            'dataStatusKendaraan' =>  $dataStatusKendaraan ,
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StatusKendaraan  $statusKendaraan
     * @return \Illuminate\Http\Response
     */
    public function show(StatusKendaraan $statusKendaraan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StatusKendaraan  $statusKendaraan
     * @return \Illuminate\Http\Response
     */
    public function edit(StatusKendaraan $statusKendaraan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StatusKendaraan  $statusKendaraan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StatusKendaraan $statusKendaraan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StatusKendaraan  $statusKendaraan
     * @return \Illuminate\Http\Response
     */
    public function destroy(StatusKendaraan $statusKendaraan)
    {
        //
    }
}
