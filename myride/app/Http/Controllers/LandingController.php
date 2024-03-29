<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Helpers
use App\Helpers\Generator;

use Illuminate\Support\Facades\Session;

class LandingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($user_id != null){
            return view('landing.index');
        } else {
            return redirect("/login");
        }
    }

    public function sign_out()
    {
        Session::flush();

        return redirect('/login')->with('success_message', 'Successfully sign out'); 
    }
}
