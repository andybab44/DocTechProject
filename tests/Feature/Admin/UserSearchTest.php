<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\User;
use App\Models\WorkJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_users_by_name(): void
    {
        $admin  = User::factory()->admin()->create();
        $doctor = User::factory()->doctor()->create(['name' => 'Alice Smith']);
        User::factory()->technician()->create(['name' => 'Bob Jones']);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['search' => 'Alice']))
            ->assertOk()
            ->assertSee('Alice Smith')
            ->assertDontSee('Bob Jones');
    }

    public function test_admin_can_filter_users_by_email(): void
    {
        $admin  = User::factory()->admin()->create();
        User::factory()->doctor()->create(['email' => 'unique@clinic.com', 'name' => 'Target User']);
        User::factory()->technician()->create(['email' => 'other@clinic.com', 'name' => 'Other User']);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['search' => 'unique@clinic.com']))
            ->assertOk()
            ->assertSee('Target User')
            ->assertDontSee('Other User');
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        $admin      = User::factory()->admin()->create();
        $doctor     = User::factory()->doctor()->create(['name' => 'Doctor Dana']);
        $technician = User::factory()->technician()->create(['name' => 'Tech Tom']);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['role' => Role::Doctor->value]))
            ->assertOk()
            ->assertSee('Doctor Dana')
            ->assertDontSee('Tech Tom');
    }

    public function test_filter_returns_all_users_when_no_params_given(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->doctor()->create(['name' => 'Doctor Dana']);
        User::factory()->technician()->create(['name' => 'Tech Tom']);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Doctor Dana')
            ->assertSee('Tech Tom');
    }

    public function test_admin_index_shows_job_counts(): void
    {
        $admin  = User::factory()->admin()->create();
        $doctor = User::factory()->doctor()->create();

        WorkJob::factory()->count(3)->create(['doctor_id' => $doctor->id]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertViewHas('users');
    }

    public function test_pagination_preserves_filter_params(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['search' => 'test', 'role' => Role::Doctor->value]))
            ->assertOk()
            ->assertSee('search');
    }
}
