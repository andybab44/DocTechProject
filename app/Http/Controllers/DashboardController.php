<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): View
    {
        return view('dashboard.admin');
    }

    public function doctor(): View
    {
        return view('dashboard.doctor');
    }

    public function technician(): View
    {
        return view('dashboard.technician');
    }
}
