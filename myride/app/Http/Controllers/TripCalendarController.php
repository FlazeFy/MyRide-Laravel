<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class TripCalendarController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('trip.calendar.index')->with('active_menu', 'calendar') : redirect('/login');
    }
}
