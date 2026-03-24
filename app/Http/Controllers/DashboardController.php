<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $title = trans('lang.dashboard');
        return view('dashboard', compact('title'));
    }
}
