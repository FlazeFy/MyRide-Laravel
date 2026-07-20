<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class AddFuelController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('fuel.add.index')->with('active_menu', 'fuel') : redirect('/login');
    }
}
