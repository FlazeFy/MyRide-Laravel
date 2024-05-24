<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Helpers
use App\Helpers\Generator;
use App\Helpers\Audit;

// Models
use App\Models\TripModel;
use App\Models\VehicleModel;
use App\Models\DictionaryModel;

class AddTripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        $dt_all_vehicle = VehicleModel::selectRaw("id, CONCAT(vehicle_merk,' - ',vehicle_name) as vehicle_name")
            ->orderBy('vehicle_name', 'ASC')
            ->get();

        $dt_trip_category = DictionaryModel::getDictionaryByType('trip_category');

        if($user_id != null){
            return view('trip.add.index')
                ->with('dt_all_vehicle', $dt_all_vehicle)
                ->with('dt_trip_category', $dt_trip_category);
        } else {
            return redirect("/login");
        }
    }

    public function post_trip(Request $request)
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        TripModel::create([
            'id' => Generator::getUUID(), 
            'vehicle_id' => $request->vehicle_id, 
            'trip_desc' => $request->trip_desc, 
            'trip_category' => $request->trip_category,  
            'trip_person' => $request->trip_person, 
            'trip_origin_name' => $request->trip_origin_name, 
            'trip_origin_coordinate' => $request->trip_origin_coordinate,  
            'trip_destination_name'  => $request->trip_destination_name,
            'trip_destination_coordinate' => $request->trip_destination_coordinate,  
            'created_at' => date('Y-m-d H:i:s'), 
            'created_by' => $user_id, 
            'updated_at' => null, 
            'deleted_at' => null
        ]);

        Audit::createHistory('Add Trip', 'History');

        return redirect("/trip")->with('success_message', 'Trip created!');;
    }
}
