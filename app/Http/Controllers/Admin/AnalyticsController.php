<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Module;
use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsService $analytics) {}

    public function index(): View
    {
        $user    = auth()->user();
        $license = $user->license;

        $hasInventory    = $license && $license->isValid() && $license->hasModule(Module::Inventory);
        $hasAppointments = $license && $license->isValid() && $license->hasModule(Module::Appointments);

        return view('admin.analytics.index', [
            'workJobStats'       => $this->analytics->workJobStats(),
            'workJobsByDoctor'   => $this->analytics->workJobsByDoctor(),
            'workJobsByTechnician' => $this->analytics->workJobsByTechnician(),
            'userStats'          => $this->analytics->userStats(),
            'appointmentStats'   => $hasAppointments ? $this->analytics->appointmentStats() : null,
            'patientCount'       => $hasAppointments ? $this->analytics->patientCount() : null,
            'inventoryStats'     => $hasInventory    ? $this->analytics->inventoryStats() : null,
            'hasInventory'       => $hasInventory,
            'hasAppointments'    => $hasAppointments,
        ]);
    }
}
