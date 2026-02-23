<?php

namespace App\Http\Controllers;

class JourneyController extends Controller
{
    public function index()
    {
        return view('journey.index')->with('active_menu','journey');
    }
}
