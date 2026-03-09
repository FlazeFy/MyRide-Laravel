<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class ProfileController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('profile.index')->with('active_menu', 'profile') : redirect('/login');
    }
}
