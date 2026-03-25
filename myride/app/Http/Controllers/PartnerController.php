<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class PartnerController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('partner.index')->with('active_menu', 'partner') : redirect('/login');
    }
}
