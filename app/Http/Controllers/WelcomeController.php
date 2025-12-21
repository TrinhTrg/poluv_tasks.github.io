<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    /**
     * Display the welcome/landing page.
     * Redirect authenticated users to home page.
     */
    public function index()
    {
        // If user is already authenticated, redirect to home
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('welcome');
    }
}

