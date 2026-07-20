<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class ServiceController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('service.index')->with('active_menu', 'service') : redirect('/login');
    }
}
