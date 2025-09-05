<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Helpers
use App\Helpers\Generator;

use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($user_id != null){
            return view('dashboard.index')
                ->with('active_menu','dashboard');
        } else {
            return redirect("/login");
        }
    }

    public function sign_out()
    {
        Session::flush();

        return redirect('/login')->with('success_message', 'Successfully sign out'); 
    }

    public function toogle_view_stats_fuel(Request $request){
        $request->session()->put('toogle_total_stats_fuel', $request->toogle_view_stats_fuel);

        return redirect()->back();
    }
}
