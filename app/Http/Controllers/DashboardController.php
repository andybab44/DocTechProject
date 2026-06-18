<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly AnalyticsService $analytics) {}

    public function admin(): View
    {
        return view('dashboard.admin', [
            'quickStats' => $this->analytics->adminQuickStats(),
            'workJobStats' => $this->analytics->workJobStats(),
        ]);
    }

    public function doctor(): View
    {
        $user = auth()->user();

        return view('dashboard.doctor', [
            'workJobStats'    => $this->analytics->doctorWorkJobStats($user),
            'nextAppointment' => $this->analytics->doctorNextAppointment($user),
        ]);
    }

    public function technician(): View
    {
        $user = auth()->user();

        return view('dashboard.technician', [
            'workJobStats' => $this->analytics->technicianWorkJobStats($user),
        ]);
    }
}
