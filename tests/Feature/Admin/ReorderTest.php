<?php

namespace Tests\Feature\Admin;

use App\Enums\Module;
use App\Mail\ReorderRequestMail;
use App\Models\InventoryItem;
use App\Models\License;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReorderTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function userWithInventory(string $role = 'admin'): User
    {
        $user = User::factory()->{$role}()->create();
        License::factory()->for($user)->create([
            'is_active'  => true,
            'expires_at' => now()->addYear(),
            'modules'    => [Module::Inventory->value],
        ]);

        return $user;
    }

    // -------------------------------------------------------------------------
    // Access control — index
    // -------------------------------------------------------------------------

    public function test_guest_cannot_access_reorder_page(): void
    {
        $this->get(route('inventory.reorder'))->assertRedirect(route('login'));
    }

    public function test_user_without_inventory_module_is_forbidden(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('inventory.reorder'))
            ->assertForbidden();
    }

    public function test_admin_with_inventory_module_can_access_reorder_page(): void
    {
        $this->actingAs($this->userWithInventory('admin'))
            ->get(route('inventory.reorder'))
            ->assertOk()
            ->assertViewIs('inventory.reorder');
    }

    public function test_technician_with_inventory_module_can_access_reorder_page(): void
    {
        $this->actingAs($this->userWithInventory('technician'))
            ->get(route('inventory.reorder'))
            ->assertOk();
    }

    // -------------------------------------------------------------------------
    // Index data
    // -------------------------------------------------------------------------

    public function test_low_stock_items_are_separated_from_items_without_vendor(): void
    {
        $vendor = Vendor::factory()->create();
        InventoryItem::factory()->lowStock()->create(['vendor_id' => $vendor->id]);
        InventoryItem::factory()->lowStock()->create(['vendor_id' => null]);

        $response = $this->actingAs($this->userWithInventory('admin'))
            ->get(route('inventory.reorder'))
            ->assertOk();

        $this->assertCount(1, $response->viewData('withVendor'));
        $this->assertCount(1, $response->viewData('withoutVendor'));
    }

    // -------------------------------------------------------------------------
    // Send
    // -------------------------------------------------------------------------

    public function test_send_dispatches_one_email_per_vendor(): void
    {
        Mail::fake();

        $vendor1 = Vendor::factory()->create();
        $vendor2 = Vendor::factory()->create();
        $item1   = InventoryItem::factory()->create(['vendor_id' => $vendor1->id]);
        $item2   = InventoryItem::factory()->create(['vendor_id' => $vendor1->id]);
        $item3   = InventoryItem::factory()->create(['vendor_id' => $vendor2->id]);

        $this->actingAs($this->userWithInventory('admin'))
            ->post(route('inventory.reorder.send'), [
                'items' => [
                    ['inventory_item_id' => $item1->id, 'quantity' => 10],
                    ['inventory_item_id' => $item2->id, 'quantity' => 5],
                    ['inventory_item_id' => $item3->id, 'quantity' => 3],
                ],
            ])
            ->assertRedirect(route('inventory.index'))
            ->assertSessionHas('success');

        Mail::assertSentCount(2);
        Mail::assertSent(ReorderRequestMail::class, fn ($mail) => $mail->hasTo($vendor1->email));
        Mail::assertSent(ReorderRequestMail::class, fn ($mail) => $mail->hasTo($vendor2->email));
    }

    public function test_items_without_vendor_are_skipped(): void
    {
        Mail::fake();

        $itemNoVendor = InventoryItem::factory()->create(['vendor_id' => null]);

        $this->actingAs($this->userWithInventory('admin'))
            ->post(route('inventory.reorder.send'), [
                'items' => [
                    ['inventory_item_id' => $itemNoVendor->id, 'quantity' => 5],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('warning');

        Mail::assertNothingSent();
    }

    public function test_send_requires_at_least_one_item(): void
    {
        $this->actingAs($this->userWithInventory('admin'))
            ->post(route('inventory.reorder.send'), ['items' => []])
            ->assertSessionHasErrors('items');
    }

    public function test_send_requires_valid_quantity(): void
    {
        $item = InventoryItem::factory()->create(['vendor_id' => Vendor::factory()->create()->id]);

        $this->actingAs($this->userWithInventory('admin'))
            ->post(route('inventory.reorder.send'), [
                'items' => [['inventory_item_id' => $item->id, 'quantity' => 0]],
            ])
            ->assertSessionHasErrors('items.0.quantity');
    }

    public function test_guest_cannot_send_reorder(): void
    {
        $item = InventoryItem::factory()->create();

        $this->post(route('inventory.reorder.send'), [
            'items' => [['inventory_item_id' => $item->id, 'quantity' => 1]],
        ])->assertRedirect(route('login'));
    }
}
