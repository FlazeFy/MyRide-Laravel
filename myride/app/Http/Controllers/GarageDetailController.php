<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

// Helpers
use App\Helpers\Generator;

class GarageDetailController extends Controller
{
    public function index($id)
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($user_id != null){
            return view('garage.detail.index')
                ->with('id', $id)
                ->with('active_menu','garage');
        } else {
            return redirect("/login");
        }
    }
}
