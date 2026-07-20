<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class AddTripController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('trip.add.index')->with('active_menu', 'trip') : redirect('/login');
    }
}
