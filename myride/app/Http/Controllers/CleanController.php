<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\CleanModel;

class CleanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dt_clean = CleanModel::selectRaw("
                clean.id, vehicle.vehicle_merk || ' - ' || vehicle.vehicle_name as vehicle_name, clean_desc, clean_by, clean_tools, 
                is_clean_body, is_clean_window, is_clean_dashboard, is_clean_tires, is_clean_trash, is_clean_engine, is_clean_seat, is_clean_carpet, 
                is_clean_pillows, clean_address, clean_start_time, clean_end_time, is_fill_window_cleaning_water, is_fill_fuel, is_clean_hollow, 
                clean.created_at, clean.updated_at
            ")
            ->join('vehicle','vehicle.id','=','clean.vehicle_id')
            ->orderBy('clean.created_at')
            ->get();

        return view('clean.index')
            ->with('dt_all_clean',$dt_clean);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
