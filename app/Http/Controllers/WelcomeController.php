<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Display the welcome/landing page.
     */
    public function index()
    {
        return view('welcome');
    }
}

