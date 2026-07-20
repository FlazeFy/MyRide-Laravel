<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class GarageController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('garage.index')->with('active_menu', 'garage') : redirect('/login');
    }
}
