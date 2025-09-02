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
                ->with('dt_trip_category', $dt_trip_category)
                ->with('active_menu','garage');
        } else {
            return redirect("/login");
        }
    }
}
