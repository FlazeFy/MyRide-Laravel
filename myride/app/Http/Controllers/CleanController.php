<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Helpers
use App\Helpers\Generator;
use App\Helpers\Audit;

// Models
use App\Models\CleanModel;

class CleanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($user_id != null){
            $dt_clean = CleanModel::selectRaw("
                clean.id, CONCAT(vehicle.vehicle_merk, ' - ', vehicle.vehicle_name)  as vehicle_name, vehicle_plate_number, clean_desc, clean_by, clean_tools, 
                is_clean_body, is_clean_window, is_clean_dashboard, is_clean_tires, is_clean_trash, is_clean_engine, is_clean_seat, is_clean_carpet, 
                is_clean_pillows, clean_address, clean_start_time, clean_end_time, is_fill_window_cleaning_water, is_fill_fuel, is_clean_hollow, 
                clean.created_at, clean.updated_at
            ")
            ->join('vehicle','vehicle.id','=','clean.vehicle_id')
            ->orderBy('clean.created_at')
            ->get();

            return view('clean.index')
                ->with('dt_all_clean',$dt_clean);
        } else {
            return redirect("/login");
        }
    }

    public function hard_del_clean($id)
    {
        CleanModel::destroy($id);

        Audit::createHistory('Permentally Delete', 'Clean history');

        return redirect()->back()->with('success_message', 'Clean history deleted!');        
    }
}
