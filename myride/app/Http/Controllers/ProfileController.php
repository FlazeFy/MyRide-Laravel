<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

// Helpers
use App\Helpers\Generator;

class ProfileController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($user_id != null){    
            return view('profile.index')->with('active_menu','profile');
        } else {
            return redirect("/login");
        }
    }
}
