<?php

namespace Tests\Feature\Inventory;

use App\Enums\Module;
use App\Models\InventoryItem;
use App\Models\InventoryUsage;
use App\Models\License;
use App\Models\User;
use App\Models\WorkJob;
use App\Notifications\LowStockNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class InventoryUsageTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_user_with_license_can_log_usage(): void
    {
        $technician = $this->userWithLicense('technician');
        $item = InventoryItem::factory()->create(['quantity' => 20]);

        $this->actingAs($technician)
            ->post(route('inventory.usage.store', $item), [
                'quantity_used' => 3,
                'work_job_id'   => null,
                'notes'         => 'Used during procedure',
            ])
            ->assertRedirect(route('inventory.show', $item))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'quantity' => 17]);
        $this->assertDatabaseHas('inventory_usages', [
            'inventory_item_id' => $item->id,
            'used_by'           => $technician->id,
            'quantity_used'     => 3,
        ]);
    }

    public function test_usage_linked_to_work_job(): void
    {
        $technician = $this->userWithLicense('technician');
        $item = InventoryItem::factory()->create(['quantity' => 10]);
        $job  = WorkJob::factory()->create(['technician_id' => $technician->id]);

        $this->actingAs($technician)
            ->post(route('inventory.usage.store', $item), [
                'quantity_used' => 2,
                'work_job_id'   => $job->id,
                'notes'         => null,
            ])
            ->assertRedirect(route('inventory.show', $item));

        $this->assertDatabaseHas('inventory_usages', [
            'inventory_item_id' => $item->id,
            'work_job_id'       => $job->id,
        ]);
    }

    public function test_usage_rejected_when_insufficient_stock(): void
    {
        $technician = $this->userWithLicense('technician');
        $item = InventoryItem::factory()->create(['quantity' => 2]);

        $this->actingAs($technician)
            ->post(route('inventory.usage.store', $item), [
                'quantity_used' => 10,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('quantity_used');
    }

    public function test_usage_requires_positive_quantity(): void
    {
        $technician = $this->userWithLicense('technician');
        $item = InventoryItem::factory()->create(['quantity' => 10]);

        $this->actingAs($technician)
            ->post(route('inventory.usage.store', $item), [
                'quantity_used' => 0,
            ])
            ->assertSessionHasErrors('quantity_used');
    }

    public function test_usage_requires_valid_work_job_id(): void
    {
        $technician = $this->userWithLicense('technician');
        $item = InventoryItem::factory()->create(['quantity' => 10]);

        $this->actingAs($technician)
            ->post(route('inventory.usage.store', $item), [
                'quantity_used' => 1,
                'work_job_id'   => 99999,
            ])
            ->assertSessionHasErrors('work_job_id');
    }

    public function test_low_stock_notification_sent_when_threshold_crossed(): void
    {
        Notification::fake();

        $admin      = User::factory()->admin()->create();
        $technician = $this->userWithLicense('technician');
        $item = InventoryItem::factory()->create([
            'quantity'            => 6,
            'low_stock_threshold' => 5,
        ]);

        $this->actingAs($technician)
            ->post(route('inventory.usage.store', $item), ['quantity_used' => 2]);

        Notification::assertSentTo($admin, LowStockNotification::class);
    }

    public function test_no_low_stock_notification_when_above_threshold(): void
    {
        Notification::fake();

        User::factory()->admin()->create();
        $technician = $this->userWithLicense('technician');
        $item = InventoryItem::factory()->create([
            'quantity'            => 50,
            'low_stock_threshold' => 5,
        ]);

        $this->actingAs($technician)
            ->post(route('inventory.usage.store', $item), ['quantity_used' => 1]);

        Notification::assertNothingSent();
    }

    public function test_no_low_stock_notification_when_threshold_is_zero(): void
    {
        Notification::fake();

        User::factory()->admin()->create();
        $technician = $this->userWithLicense('technician');
        $item = InventoryItem::factory()->create([
            'quantity'            => 2,
            'low_stock_threshold' => 0,
        ]);

        $this->actingAs($technician)
            ->post(route('inventory.usage.store', $item), ['quantity_used' => 2]);

        Notification::assertNothingSent();
    }

    public function test_user_without_license_cannot_log_usage(): void
    {
        $user = User::factory()->technician()->create();
        $item = InventoryItem::factory()->create(['quantity' => 10]);

        $this->actingAs($user)
            ->post(route('inventory.usage.store', $item), ['quantity_used' => 1])
            ->assertForbidden();
    }

    public function test_guest_cannot_log_usage(): void
    {
        $item = InventoryItem::factory()->create(['quantity' => 10]);

        $this->post(route('inventory.usage.store', $item), ['quantity_used' => 1])
            ->assertRedirect(route('login'));
    }
}
