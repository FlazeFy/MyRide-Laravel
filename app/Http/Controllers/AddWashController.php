<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class AddWashController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('wash.add.index')->with('active_menu', 'wash') : redirect('/login');
    }
}
