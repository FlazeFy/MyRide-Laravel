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
