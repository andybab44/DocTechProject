<?php

namespace Tests\Feature\Inventory;

use App\Enums\Module;
use App\Models\InventoryItem;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryItemTest extends TestCase
{
    use RefreshDatabase;

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    private function userWithLicense(string $role = 'technician'): User
    {
        $user = User::factory()->{$role}()->create();
        License::factory()->for($user)->create([
            'is_active'  => true,
            'expires_at' => now()->addYear(),
            'modules'    => [Module::Inventory->value],
        ]);
        return $user;
    }

    private function adminWithLicense(): User
    {
        return $this->userWithLicense('admin');
    }

    // ----------------------------------------------------------------
    // Module gating — guests & users without license
    // ----------------------------------------------------------------

    public function test_guest_cannot_access_inventory(): void
    {
        $this->get(route('inventory.index'))->assertRedirect(route('login'));
    }

    public function test_user_without_license_cannot_access_inventory(): void
    {
        $user = User::factory()->technician()->create();

        $this->actingAs($user)
            ->get(route('inventory.index'))
            ->assertForbidden();
    }

    public function test_user_with_expired_license_cannot_access_inventory(): void
    {
        $user = User::factory()->technician()->create();
        License::factory()->for($user)->expired()->create(['modules' => [Module::Inventory->value]]);

        $this->actingAs($user)
            ->get(route('inventory.index'))
            ->assertForbidden();
    }

    // ----------------------------------------------------------------
    // Index
    // ----------------------------------------------------------------

    public function test_user_with_inventory_license_can_view_index(): void
    {
        $user = $this->userWithLicense('technician');
        InventoryItem::factory()->count(3)->create();

        $this->actingAs($user)
            ->get(route('inventory.index'))
            ->assertOk()
            ->assertViewIs('inventory.index');
    }

    public function test_admin_with_inventory_license_can_view_index(): void
    {
        $admin = $this->adminWithLicense();

        $this->actingAs($admin)
            ->get(route('inventory.index'))
            ->assertOk();
    }

    // ----------------------------------------------------------------
    // Show
    // ----------------------------------------------------------------

    public function test_user_with_license_can_view_item_detail(): void
    {
        $user = $this->userWithLicense('technician');
        $item = InventoryItem::factory()->create();

        $this->actingAs($user)
            ->get(route('inventory.show', $item))
            ->assertOk()
            ->assertViewIs('inventory.show')
            ->assertSee($item->name);
    }

    // ----------------------------------------------------------------
    // Admin — create / store
    // ----------------------------------------------------------------

    public function test_non_admin_cannot_access_admin_create_form(): void
    {
        $user = $this->userWithLicense('technician');

        $this->actingAs($user)
            ->get(route('admin.inventory.create'))
            ->assertForbidden();
    }

    public function test_admin_can_view_create_form(): void
    {
        $admin = $this->adminWithLicense();

        $this->actingAs($admin)
            ->get(route('admin.inventory.create'))
            ->assertOk();
    }

    public function test_admin_can_create_inventory_item(): void
    {
        $admin = $this->adminWithLicense();

        $this->actingAs($admin)
            ->post(route('admin.inventory.store'), [
                'name'                => 'Latex Gloves',
                'description'         => null,
                'quantity'            => 100,
                'unit'                => 'box',
                'category'            => 'PPE',
                'low_stock_threshold' => 10,
            ])
            ->assertRedirect(route('inventory.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('inventory_items', ['name' => 'Latex Gloves', 'quantity' => 100]);
    }

    public function test_create_requires_name(): void
    {
        $admin = $this->adminWithLicense();

        $this->actingAs($admin)
            ->post(route('admin.inventory.store'), ['name' => '', 'quantity' => 10, 'unit' => 'pcs', 'low_stock_threshold' => 0])
            ->assertSessionHasErrors('name');
    }

    public function test_create_requires_non_negative_quantity(): void
    {
        $admin = $this->adminWithLicense();

        $this->actingAs($admin)
            ->post(route('admin.inventory.store'), ['name' => 'Item', 'quantity' => -1, 'unit' => 'pcs', 'low_stock_threshold' => 0])
            ->assertSessionHasErrors('quantity');
    }

    // ----------------------------------------------------------------
    // Admin — edit / update
    // ----------------------------------------------------------------

    public function test_admin_can_edit_inventory_item(): void
    {
        $admin = $this->adminWithLicense();
        $item  = InventoryItem::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.inventory.update', $item), [
                'name'                => 'Updated Name',
                'description'         => null,
                'unit'                => 'ml',
                'category'            => 'Chemical',
                'low_stock_threshold' => 20,
            ])
            ->assertRedirect(route('inventory.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'name' => 'Updated Name', 'unit' => 'ml']);
    }

    public function test_non_admin_cannot_update_inventory_item(): void
    {
        $user = $this->userWithLicense('technician');
        $item = InventoryItem::factory()->create();

        $this->actingAs($user)
            ->put(route('admin.inventory.update', $item), [
                'name' => 'X', 'unit' => 'pcs', 'low_stock_threshold' => 0,
            ])
            ->assertForbidden();
    }

    // ----------------------------------------------------------------
    // Admin — restock
    // ----------------------------------------------------------------

    public function test_admin_can_restock_item(): void
    {
        $admin = $this->adminWithLicense();
        $item  = InventoryItem::factory()->create(['quantity' => 10]);

        $this->actingAs($admin)
            ->post(route('admin.inventory.restock', $item), ['quantity' => 50])
            ->assertRedirect(route('inventory.show', $item))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'quantity' => 60]);
    }

    public function test_restock_requires_positive_quantity(): void
    {
        $admin = $this->adminWithLicense();
        $item  = InventoryItem::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.inventory.restock', $item), ['quantity' => 0])
            ->assertSessionHasErrors('quantity');
    }

    public function test_non_admin_cannot_restock(): void
    {
        $user = $this->userWithLicense('technician');
        $item = InventoryItem::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.inventory.restock', $item), ['quantity' => 5])
            ->assertForbidden();
    }

    // ----------------------------------------------------------------
    // Admin — destroy
    // ----------------------------------------------------------------

    public function test_admin_can_delete_inventory_item(): void
    {
        $admin = $this->adminWithLicense();
        $item  = InventoryItem::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.inventory.destroy', $item))
            ->assertRedirect(route('inventory.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('inventory_items', ['id' => $item->id]);
    }

    public function test_non_admin_cannot_delete_inventory_item(): void
    {
        $user = $this->userWithLicense('technician');
        $item = InventoryItem::factory()->create();

        $this->actingAs($user)
            ->delete(route('admin.inventory.destroy', $item))
            ->assertForbidden();
    }
}
