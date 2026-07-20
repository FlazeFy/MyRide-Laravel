<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class JourneyController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('journey.index')->with('active_menu', 'journey') : redirect('/login');
    }
}
