<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class EmbedController extends Controller
{
    public function app_summary()
    {
        return view('embed.app_summary');
    }
}
