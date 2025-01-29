<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Helpers
use App\Helpers\Generator;
use App\Helpers\Query;

// Models
use App\Models\VehicleModel;

class GarageEditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($user_id != null){
            return view('garage.edit.index')
                ->with('id', $id);
        } else {
            return redirect("/login");
        }
    }

    public function edit_vehicle(Request $request,$id)
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($request->vehicle_transmission_code == "MT"){
            $vehicle_transmission = "Manual";
        } else if($request->vehicle_transmission_code == "AT"){
            $vehicle_transmission = "Automatic";
        } else if($request->vehicle_transmission_code == "CVT"){
            $vehicle_transmission = "CVT";
        }

        VehicleModel::where('created_by',$user_id)
            ->where('id',$id)
            ->update([
                'vehicle_name' => $request->vehicle_name.' '.$request->vehicle_transmission_code, 
                'vehicle_merk' => $request->vehicle_merk, 
                'vehicle_type' => $request->vehicle_type, 
                'vehicle_price' => $request->vehicle_price, 
                'vehicle_desc' => $request->vehicle_desc, 
                'vehicle_distance' => $request->vehicle_distance, 
                'vehicle_category' => $request->vehicle_category, 
                'vehicle_status' => $request->vehicle_status, 
                'vehicle_year_made' => $request->vehicle_year_made, 
                'vehicle_plate_number' => $request->vehicle_plate_number, 
                'vehicle_fuel_status' => $request->vehicle_fuel_status, 
                'vehicle_fuel_capacity' => $request->vehicle_fuel_capacity, 
                'vehicle_default_fuel' => $request->vehicle_default_fuel, 
                'vehicle_color' => $request->vehicle_color, 
                'vehicle_transmission' => $vehicle_transmission,
                'vehicle_capacity' => $request->vehicle_capacity, 
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        return redirect()->back()->with('success_mini_message', 'Vehicle updated!');
    }

    public function edit_vehicle_doc(Request $request,$id)
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        VehicleModel::where('created_by',$user_id)
            ->where('id',$id)
            ->update([
                'vehicle_document' => $request->vehicle_document,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        return redirect()->back()->with('success_mini_message', 'Vehicle document updated!');
    }
}
