<?php

namespace App\Http\Controllers;

// Helpers
use App\Helpers\Generator;

class GarageDetailController extends Controller
{
    public function index($id)
    {
        $user_id = Generator::getUserId(session()->get('role_key'));
        
        return $user_id ? view('garage.detail.index')->with('id', $id)->with('active_menu', 'garage') : redirect('/login');
    }
}
