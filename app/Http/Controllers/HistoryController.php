<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class HistoryController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('history.index')->with('active_menu', 'history') : redirect('/login');
    }
}
