<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Enums\WorkJobStatus;
use App\Models\Appointment;
use App\Models\InventoryItem;
use App\Models\Patient;
use App\Models\User;
use App\Models\WorkJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Work job totals grouped by status (all jobs in system).
     *
     * @return array{total: int, by_status: Collection}
     */
    public function workJobStats(): array
    {
        $byStatus = WorkJob::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'total'     => $byStatus->sum('total'),
            'by_status' => $byStatus,
        ];
    }

    /**
     * Top doctors by number of work jobs created.
     *
     * @return Collection
     */
    public function workJobsByDoctor(int $limit = 5): Collection
    {
        return WorkJob::select('doctor_id', DB::raw('count(*) as total'))
            ->with('doctor:id,name')
            ->whereNotNull('doctor_id')
            ->groupBy('doctor_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * Top technicians by number of work jobs assigned.
     *
     * @return Collection
     */
    public function workJobsByTechnician(int $limit = 5): Collection
    {
        return WorkJob::select('technician_id', DB::raw('count(*) as total'))
            ->with('technician:id,name')
            ->whereNotNull('technician_id')
            ->groupBy('technician_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * Appointment totals grouped by status.
     *
     * @return array{total: int, by_status: Collection}
     */
    public function appointmentStats(): array
    {
        $byStatus = Appointment::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'total'     => $byStatus->sum('total'),
            'by_status' => $byStatus,
        ];
    }

    /**
     * Total registered patients.
     */
    public function patientCount(): int
    {
        return Patient::count();
    }

    /**
     * Inventory summary: total items and low-stock count.
     *
     * @return array{total: int, low_stock: int}
     */
    public function inventoryStats(): array
    {
        return [
            'total'     => InventoryItem::count(),
            'low_stock' => InventoryItem::whereColumn('quantity', '<=', 'low_stock_threshold')->count(),
        ];
    }

    /**
     * User totals grouped by role.
     *
     * @return array{total: int, by_role: Collection}
     */
    public function userStats(): array
    {
        $byRole = User::selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->get()
            ->keyBy('role');

        return [
            'total'   => $byRole->sum('total'),
            'by_role' => $byRole,
        ];
    }

    /**
     * Work job stats scoped to a specific doctor.
     *
     * @return array{total: int, by_status: Collection}
     */
    public function doctorWorkJobStats(User $user): array
    {
        $byStatus = WorkJob::where('doctor_id', $user->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'total'     => $byStatus->sum('total'),
            'by_status' => $byStatus,
        ];
    }

    /**
     * Work job stats scoped to a specific technician.
     *
     * @return array{total: int, by_status: Collection, completed_this_month: int}
     */
    public function technicianWorkJobStats(User $user): array
    {
        $byStatus = WorkJob::where('technician_id', $user->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $completedThisMonth = WorkJob::where('technician_id', $user->id)
            ->where('status', WorkJobStatus::Delivered->value)
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        return [
            'total'                => $byStatus->sum('total'),
            'by_status'            => $byStatus,
            'completed_this_month' => $completedThisMonth,
        ];
    }

    /**
     * Next upcoming appointment for a specific doctor.
     */
    public function doctorNextAppointment(User $user): ?Appointment
    {
        return Appointment::where('doctor_id', $user->id)
            ->where('status', AppointmentStatus::Scheduled->value)
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->with('patient')
            ->first();
    }

    /**
     * Admin dashboard quick-stats summary.
     *
     * @return array{users: int, work_jobs: int, active_jobs: int, patients: int}
     */
    public function adminQuickStats(): array
    {
        $activeStatuses = [
            WorkJobStatus::AwaitingAcceptance->value,
            WorkJobStatus::InProgress->value,
            WorkJobStatus::InReview->value,
            WorkJobStatus::NeedsRevision->value,
            WorkJobStatus::ReadyForDelivery->value,
        ];

        return [
            'users'       => User::count(),
            'work_jobs'   => WorkJob::count(),
            'active_jobs' => WorkJob::whereIn('status', $activeStatuses)->count(),
            'patients'    => Patient::count(),
        ];
    }
}
