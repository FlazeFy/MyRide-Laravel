<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class DriverController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('driver.index')->with('active_menu', 'driver') : redirect('/login');
    }
}
