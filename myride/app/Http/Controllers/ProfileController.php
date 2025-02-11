<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Helpers
use App\Helpers\Generator;

// Models
use App\Models\CleanModel;
use App\Models\TripModel;

class ProfileController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($user_id != null){    
            return view('profile.index');
        } else {
            return redirect("/login");
        }
    }
}
