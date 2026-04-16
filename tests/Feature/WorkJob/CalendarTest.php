<?php

namespace Tests\Feature\WorkJob;

use App\Models\User;
use App\Models\WorkJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_calendar(): void
    {
        $this->get('/calendar')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_calendar(): void
    {
        $this->actingAs(User::factory()->doctor()->create())
            ->get('/calendar')
            ->assertOk()
            ->assertViewIs('work-jobs.calendar');
    }

    public function test_admin_sees_all_jobs_in_calendar(): void
    {
        $admin  = User::factory()->admin()->create();
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();

        WorkJob::factory()->count(3)->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'scheduled_at' => Carbon::now()->startOfMonth()->addDays(5),
        ]);

        $response = $this->actingAs($admin)
            ->get('/calendar?year=' . now()->year . '&month=' . now()->month);

        $response->assertOk();
        $this->assertCount(3, $response->viewData('jobsByDay')->flatten());
    }

    public function test_doctor_only_sees_own_jobs_in_calendar(): void
    {
        $doctor1 = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();
        $tech    = User::factory()->technician()->create();

        WorkJob::factory()->create([
            'doctor_id'    => $doctor1->id,
            'technician_id' => $tech->id,
            'scheduled_at' => Carbon::now()->startOfMonth()->addDays(5),
        ]);
        WorkJob::factory()->create([
            'doctor_id'    => $doctor2->id,
            'technician_id' => $tech->id,
            'scheduled_at' => Carbon::now()->startOfMonth()->addDays(5),
        ]);

        $response = $this->actingAs($doctor1)
            ->get('/calendar?year=' . now()->year . '&month=' . now()->month);

        $response->assertOk();
        $this->assertCount(1, $response->viewData('jobsByDay')->flatten());
    }

    public function test_technician_only_sees_assigned_jobs_in_calendar(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech1  = User::factory()->technician()->create();
        $tech2  = User::factory()->technician()->create();

        WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech1->id,
            'scheduled_at' => Carbon::now()->startOfMonth()->addDays(5),
        ]);
        WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech2->id,
            'scheduled_at' => Carbon::now()->startOfMonth()->addDays(5),
        ]);

        $response = $this->actingAs($tech1)
            ->get('/calendar?year=' . now()->year . '&month=' . now()->month);

        $response->assertOk();
        $this->assertCount(1, $response->viewData('jobsByDay')->flatten());
    }

    public function test_calendar_accepts_year_and_month_query_params(): void
    {
        $doctor = User::factory()->doctor()->create();
        $tech   = User::factory()->technician()->create();

        WorkJob::factory()->create([
            'doctor_id'    => $doctor->id,
            'technician_id' => $tech->id,
            'scheduled_at' => Carbon::create(2025, 6, 15),
        ]);

        $this->actingAs($doctor)
            ->get('/calendar?year=2025&month=6')
            ->assertOk()
            ->assertViewHas('currentMonth', fn ($m) => $m->month === 6 && $m->year === 2025);
    }
}
