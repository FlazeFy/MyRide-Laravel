<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class AddServiceController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('service.add.index')->with('active_menu', 'service') : redirect('/login');
    }
}
