<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

// Helpers
use App\Helpers\Generator;

class AddInventoryController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($user_id != null){
            return view('inventory.add.index')->with('active_menu','inventory');
        } else {
            return redirect("/login");
        }
    }
}
