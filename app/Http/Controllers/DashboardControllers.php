<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardControllers extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'type_menu' => "Dashboard"
        ];

        return view('pages.dashboard.index', $data);
    }
}
