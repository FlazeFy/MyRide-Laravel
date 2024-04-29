<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Helpers
use App\Helpers\Generator;

// Models
use App\Models\CleanModel;
use App\Models\TripModel;
use App\Models\VehicleModel;

// Export
use App\Exports\DataExport;

use Maatwebsite\Excel\Facades\Excel;

class StatsController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        $total_trip_by_category = TripModel::getTotalTripByCategory($user_id);
        $total_vehicle_by_category = VehicleModel::getTotalVehicleByCategory($user_id);
        $total_trip_by_destination = TripModel::getTotalTripByDestinationOrigion($user_id, 'destination');
        $total_trip_by_origin = TripModel::getTotalTripByDestinationOrigion($user_id, 'origin');

        if($user_id != null){    
            return view('stats.index')
                ->with('total_trip_by_category',$total_trip_by_category)
                ->with('total_vehicle_by_category',$total_vehicle_by_category)
                ->with('total_trip_by_destination',$total_trip_by_destination)
                ->with('total_trip_by_origin',$total_trip_by_origin);
        } else {
            return redirect("/login");
        }
    }

    public function convert_csv(Request $request)
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($request->module == 'Vehicle'){
            $data = VehicleModel::select('*')
                ->where('created_by', $user_id)
                ->orderBy('created_at', 'DESC')
                ->get();
        } else if($request->module == 'Trip'){
            $data = TripModel::select('*')
                ->where('created_by', $user_id)
                ->orderBy('created_at', 'DESC')
                ->get();
        } else if($request->module == 'Clean'){
            $data = CleanModel::select('*')
                ->where('created_by', $user_id)
                ->orderBy('created_at', 'DESC')
                ->get();
        }

        $file_name = date('l, j F Y \a\t H:i:s');

        return Excel::download(new DataExport($data), "$file_name-$request->module Data.xlsx");
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
