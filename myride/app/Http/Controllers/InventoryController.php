<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class InventoryController extends Controller
{
    public function index()
    {
        $user_id = Generator::getUserId(session()->get('role_key'));

        return $user_id ? view('inventory.index')->with('active_menu', 'inventory') : redirect('/login');
    }
}
