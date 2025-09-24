<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Helpers
use App\Helpers\Generator;

class AddCleanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        if($user_id != null){
            return view('clean.add.index')
                ->with('active_menu','clean');
        } else {
            return redirect("/login");
        }
    }
}
