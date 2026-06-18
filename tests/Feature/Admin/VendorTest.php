<?php

namespace Tests\Feature\Admin;

use App\Models\InventoryItem;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name'  => 'Dental Supplies Co.',
            'email' => 'orders@dentalsupplies.example',
            'phone' => '+40 123 456 789',
            'notes' => 'Main supplier.',
        ], $overrides);
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function test_guest_cannot_view_vendor_list(): void
    {
        $this->get(route('admin.vendors.index'))->assertRedirect(route('login'));
    }

    public function test_doctor_cannot_view_vendor_list(): void
    {
        $this->actingAs(User::factory()->doctor()->create())
            ->get(route('admin.vendors.index'))
            ->assertForbidden();
    }

    public function test_technician_cannot_view_vendor_list(): void
    {
        $this->actingAs(User::factory()->technician()->create())
            ->get(route('admin.vendors.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_vendor_list(): void
    {
        Vendor::factory()->count(3)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.vendors.index'))
            ->assertOk()
            ->assertViewIs('admin.vendors.index')
            ->assertViewHas('vendors');
    }

    // -------------------------------------------------------------------------
    // Create / store
    // -------------------------------------------------------------------------

    public function test_admin_can_create_a_vendor(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.vendors.store'), $this->validPayload())
            ->assertRedirect(route('admin.vendors.index'));

        $this->assertDatabaseHas('vendors', ['email' => 'orders@dentalsupplies.example']);
    }

    public function test_vendor_email_must_be_unique(): void
    {
        Vendor::factory()->create(['email' => 'dupe@example.com']);

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('admin.vendors.store'), $this->validPayload(['email' => 'dupe@example.com']))
            ->assertSessionHasErrors('email');
    }

    public function test_doctor_cannot_create_a_vendor(): void
    {
        $this->actingAs(User::factory()->doctor()->create())
            ->post(route('admin.vendors.store'), $this->validPayload())
            ->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // Edit / update
    // -------------------------------------------------------------------------

    public function test_admin_can_update_a_vendor(): void
    {
        $vendor = Vendor::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.vendors.update', $vendor), $this->validPayload(['name' => 'Updated Name']))
            ->assertRedirect(route('admin.vendors.index'));

        $this->assertDatabaseHas('vendors', ['id' => $vendor->id, 'name' => 'Updated Name']);
    }

    public function test_update_allows_same_email_for_own_vendor(): void
    {
        $vendor = Vendor::factory()->create(['email' => 'mine@example.com']);

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.vendors.update', $vendor), $this->validPayload(['email' => 'mine@example.com']))
            ->assertRedirect(route('admin.vendors.index'));
    }

    // -------------------------------------------------------------------------
    // Destroy
    // -------------------------------------------------------------------------

    public function test_admin_can_delete_a_vendor(): void
    {
        $vendor = Vendor::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.vendors.destroy', $vendor))
            ->assertRedirect(route('admin.vendors.index'));

        $this->assertDatabaseMissing('vendors', ['id' => $vendor->id]);
    }

    public function test_deleting_vendor_nullifies_inventory_item_vendor_id(): void
    {
        $vendor = Vendor::factory()->create();
        $item   = InventoryItem::factory()->create(['vendor_id' => $vendor->id]);

        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('admin.vendors.destroy', $vendor));

        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'vendor_id' => null]);
    }
}
