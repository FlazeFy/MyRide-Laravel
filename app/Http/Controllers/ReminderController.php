<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class ReminderController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('reminder.index')->with('active_menu', 'reminder') : redirect('/login');
    }
}
