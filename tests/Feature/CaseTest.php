<?php

namespace Tests\Feature;

use App\Models\DentalCase;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaseTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // index (GET /cases)
    // =========================================================================

    public function test_guest_cannot_access_cases(): void
    {
        $this->get(route('cases.index'))->assertRedirect(route('login'));
    }

    public function test_technician_cannot_access_cases(): void
    {
        $this->actingAs(User::factory()->technician()->create())
            ->get(route('cases.index'))
            ->assertForbidden();
    }

    public function test_doctor_can_view_own_cases(): void
    {
        $doctor = User::factory()->doctor()->create();
        DentalCase::factory()->count(2)->create(['doctor_id' => $doctor->id]);

        $this->actingAs($doctor)
            ->get(route('cases.index'))
            ->assertOk()
            ->assertViewIs('cases.index')
            ->assertViewHas('cases');
    }

    public function test_doctor_cannot_see_other_doctors_cases(): void
    {
        $doctor1 = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();
        $patient = Patient::factory()->create();

        DentalCase::factory()->create(['doctor_id' => $doctor1->id, 'patient_id' => $patient->id]);
        DentalCase::factory()->create(['doctor_id' => $doctor2->id, 'patient_id' => $patient->id]);

        $cases = $this->actingAs($doctor1)
            ->get(route('cases.index'))
            ->viewData('cases');

        $this->assertCount(1, $cases);
    }

    public function test_admin_can_see_all_cases(): void
    {
        $admin   = User::factory()->admin()->create();
        $doctor1 = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();
        $patient = Patient::factory()->create();

        DentalCase::factory()->create(['doctor_id' => $doctor1->id, 'patient_id' => $patient->id]);
        DentalCase::factory()->create(['doctor_id' => $doctor2->id, 'patient_id' => $patient->id]);

        $cases = $this->actingAs($admin)
            ->get(route('cases.index'))
            ->viewData('cases');

        $this->assertCount(2, $cases);
    }

    // =========================================================================
    // store (POST /cases)
    // =========================================================================

    public function test_doctor_can_create_a_case(): void
    {
        $doctor  = User::factory()->doctor()->create();
        $patient = Patient::factory()->create();

        $this->actingAs($doctor)
            ->post(route('cases.store'), [
                'patient_id'  => $patient->id,
                'title'       => 'Crown replacement',
                'description' => 'Upper left molar',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('dental_cases', [
            'doctor_id'  => $doctor->id,
            'patient_id' => $patient->id,
            'title'      => 'Crown replacement',
        ]);
    }

    public function test_technician_cannot_create_a_case(): void
    {
        $tech    = User::factory()->technician()->create();
        $patient = Patient::factory()->create();

        $this->actingAs($tech)
            ->post(route('cases.store'), [
                'patient_id' => $patient->id,
                'title'      => 'Some case',
            ])
            ->assertForbidden();
    }

    public function test_case_creation_requires_title(): void
    {
        $doctor  = User::factory()->doctor()->create();
        $patient = Patient::factory()->create();

        $this->actingAs($doctor)
            ->post(route('cases.store'), [
                'patient_id' => $patient->id,
            ])
            ->assertSessionHasErrors('title');
    }

    // =========================================================================
    // show (GET /cases/{case})
    // =========================================================================

    public function test_doctor_can_view_own_case(): void
    {
        $doctor     = User::factory()->doctor()->create();
        $dentalCase = DentalCase::factory()->create(['doctor_id' => $doctor->id]);

        $this->actingAs($doctor)
            ->get(route('cases.show', $dentalCase))
            ->assertOk()
            ->assertViewIs('cases.show');
    }

    public function test_doctor_cannot_view_another_doctors_case(): void
    {
        $doctor1 = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();
        $patient = Patient::factory()->create();

        $dentalCase = DentalCase::factory()->create(['doctor_id' => $doctor2->id, 'patient_id' => $patient->id]);

        $this->actingAs($doctor1)
            ->get(route('cases.show', $dentalCase))
            ->assertForbidden();
    }

    // =========================================================================
    // update (PUT /cases/{case})
    // =========================================================================

    public function test_doctor_can_update_own_case(): void
    {
        $doctor     = User::factory()->doctor()->create();
        $dentalCase = DentalCase::factory()->create(['doctor_id' => $doctor->id]);

        $this->actingAs($doctor)
            ->put(route('cases.update', $dentalCase), ['title' => 'Updated Title'])
            ->assertRedirect(route('cases.show', $dentalCase));

        $this->assertDatabaseHas('dental_cases', ['id' => $dentalCase->id, 'title' => 'Updated Title']);
    }

    // =========================================================================
    // destroy (DELETE /cases/{case})
    // =========================================================================

    public function test_doctor_can_delete_own_case(): void
    {
        $doctor     = User::factory()->doctor()->create();
        $dentalCase = DentalCase::factory()->create(['doctor_id' => $doctor->id]);

        $this->actingAs($doctor)
            ->delete(route('cases.destroy', $dentalCase))
            ->assertRedirect(route('cases.index'));

        $this->assertDatabaseMissing('dental_cases', ['id' => $dentalCase->id]);
    }
}
