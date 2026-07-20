<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class PlaceController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('place.index')->with('active_menu', 'place') : redirect('/login');
    }
}
