<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// Helpers
use App\Helpers\Generator;

class DashboardController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($user_id != null){
            if(!session()->get('toogle_select_year')){
                session()->put('toogle_select_year', date('Y'));
            }
            
            return view('dashboard.index')->with('active_menu','dashboard');
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

    public function toogle_year(Request $request)
    {
        $request->session()->put('toogle_select_year', $request->toogle_year);

        return redirect()->back();
    }
}
