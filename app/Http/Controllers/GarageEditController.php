<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;
// Models
use App\Models\VehicleModel;

class GarageEditController extends Controller
{
    public function index($id)
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('garage.edit.index')->with('id', $id)->with('active_menu', 'garage') : redirect('/login');
    }
}
