<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuspensionController extends Controller
{
    /**
     * Display the suspension page
     */
    public function show()
    {
        return view('auth.suspended');
    }
}