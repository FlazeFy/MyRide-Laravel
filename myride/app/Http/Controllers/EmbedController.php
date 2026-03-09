<?php

namespace App\Http\Controllers;

class EmbedController extends Controller
{
    public function app_summary()
    {
        return view('embed.app_summary');
    }

    public function trip_discovered()
    {
        return view('embed.trip_discovered');
    }
}
