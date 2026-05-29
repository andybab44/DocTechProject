<?php

namespace Tests\Feature\Admin;

use App\Enums\Module;
use App\Models\License;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function adminWithLicense(): User
    {
        $admin = User::factory()->admin()->create();
        License::factory()->for($admin)->create([
            'is_active'  => true,
            'expires_at' => now()->addYear(),
            'modules'    => [Module::Reviews->value],
        ]);
        return $admin;
    }

    // -------------------------------------------------------------------------
    // Module gating — index
    // -------------------------------------------------------------------------

    public function test_guest_cannot_access_admin_reviews(): void
    {
        $this->get(route('admin.reviews.index'))->assertRedirect(route('login'));
    }

    public function test_admin_without_reviews_license_cannot_access_admin_reviews(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.reviews.index'))
            ->assertForbidden();
    }

    public function test_doctor_cannot_access_admin_reviews(): void
    {
        $doctor = User::factory()->doctor()->create();
        License::factory()->for($doctor)->create([
            'is_active' => true,
            'modules'   => [Module::Reviews->value],
        ]);

        $this->actingAs($doctor)
            ->get(route('admin.reviews.index'))
            ->assertForbidden();
    }

    public function test_technician_cannot_access_admin_reviews(): void
    {
        $tech = User::factory()->technician()->create();
        License::factory()->for($tech)->create([
            'is_active' => true,
            'modules'   => [Module::Reviews->value],
        ]);

        $this->actingAs($tech)
            ->get(route('admin.reviews.index'))
            ->assertForbidden();
    }

    public function test_admin_with_reviews_license_can_access_admin_reviews(): void
    {
        $admin = $this->adminWithLicense();
        Review::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('admin.reviews.index'))
            ->assertOk()
            ->assertViewIs('admin.reviews.index');
    }

    // -------------------------------------------------------------------------
    // toggleVisibility
    // -------------------------------------------------------------------------

    public function test_guest_cannot_toggle_review_visibility(): void
    {
        $review = Review::factory()->create();

        $this->patch(route('admin.reviews.toggle', $review))
            ->assertRedirect(route('login'));
    }

    public function test_admin_without_license_cannot_toggle_visibility(): void
    {
        $admin  = User::factory()->admin()->create();
        $review = Review::factory()->create();

        $this->actingAs($admin)
            ->patch(route('admin.reviews.toggle', $review))
            ->assertForbidden();
    }

    public function test_admin_can_hide_visible_review(): void
    {
        $admin  = $this->adminWithLicense();
        $review = Review::factory()->create(['is_visible' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.reviews.toggle', $review))
            ->assertRedirect(route('admin.reviews.index'));

        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'is_visible' => false]);
    }

    public function test_admin_can_show_hidden_review(): void
    {
        $admin  = $this->adminWithLicense();
        $review = Review::factory()->hidden()->create();

        $this->actingAs($admin)
            ->patch(route('admin.reviews.toggle', $review))
            ->assertRedirect(route('admin.reviews.index'));

        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'is_visible' => true]);
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_guest_cannot_delete_review(): void
    {
        $review = Review::factory()->create();

        $this->delete(route('admin.reviews.destroy', $review))
            ->assertRedirect(route('login'));
    }

    public function test_admin_without_license_cannot_delete_review(): void
    {
        $admin  = User::factory()->admin()->create();
        $review = Review::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.reviews.destroy', $review))
            ->assertForbidden();
    }

    public function test_admin_can_delete_review(): void
    {
        $admin  = $this->adminWithLicense();
        $review = Review::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.reviews.destroy', $review))
            ->assertRedirect(route('admin.reviews.index'));

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    // -------------------------------------------------------------------------
    // Content checks
    // -------------------------------------------------------------------------

    public function test_admin_index_shows_both_visible_and_hidden_reviews(): void
    {
        $admin = $this->adminWithLicense();

        $visible = Review::factory()->create(['is_visible' => true]);
        $hidden  = Review::factory()->hidden()->create();

        $response = $this->actingAs($admin)->get(route('admin.reviews.index'));

        $response->assertSee($visible->reviewer->name);
        $response->assertSee($hidden->reviewer->name);
    }
}
