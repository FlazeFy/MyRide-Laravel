<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class TripController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('trip.index')->with('active_menu', 'trip') : redirect('/login');
    }
}
