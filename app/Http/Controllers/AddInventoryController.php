<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class AddInventoryController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('inventory.add.index')->with('active_menu', 'inventory') : redirect('/login');
    }
}
