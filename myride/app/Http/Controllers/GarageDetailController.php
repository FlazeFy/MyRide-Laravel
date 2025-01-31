<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Helpers
use App\Helpers\Generator;
use App\Helpers\Query;

// Models
use App\Models\VehicleModel;

class GarageDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($user_id != null){
            return view('garage.detail.index')
                ->with('id', $id);
        } else {
            return redirect("/login");
        }
    }
}
