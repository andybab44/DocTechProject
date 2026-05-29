<?php

namespace Tests\Unit\Services;

use App\Models\InventoryItem;
use App\Models\User;
use App\Models\WorkJob;
use App\Notifications\LowStockNotification;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private InventoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InventoryService();
    }

    public function test_create_persists_item(): void
    {
        $item = $this->service->create([
            'name'                => 'Bandage',
            'description'         => null,
            'quantity'            => 50,
            'unit'                => 'pcs',
            'category'            => 'Medical',
            'low_stock_threshold' => 10,
        ]);

        $this->assertDatabaseHas('inventory_items', ['name' => 'Bandage', 'quantity' => 50]);
        $this->assertInstanceOf(InventoryItem::class, $item);
    }

    public function test_update_changes_item_fields(): void
    {
        $item = InventoryItem::factory()->create(['name' => 'Old Name']);

        $this->service->update($item, [
            'name'                => 'New Name',
            'description'         => 'Updated',
            'unit'                => 'ml',
            'category'            => null,
            'low_stock_threshold' => 20,
        ]);

        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'name' => 'New Name', 'unit' => 'ml']);
    }

    public function test_restock_increments_quantity(): void
    {
        $item = InventoryItem::factory()->create(['quantity' => 10]);

        $this->service->restock($item, 40);

        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'quantity' => 50]);
    }

    public function test_delete_removes_item(): void
    {
        $item = InventoryItem::factory()->create();

        $this->service->delete($item);

        $this->assertDatabaseMissing('inventory_items', ['id' => $item->id]);
    }

    public function test_log_usage_deducts_quantity(): void
    {
        $user = User::factory()->technician()->create();
        $item = InventoryItem::factory()->create(['quantity' => 20]);

        $this->service->logUsage($item, $user, ['quantity_used' => 5, 'work_job_id' => null, 'notes' => null]);

        $this->assertDatabaseHas('inventory_items', ['id' => $item->id, 'quantity' => 15]);
    }

    public function test_log_usage_creates_record(): void
    {
        $user = User::factory()->technician()->create();
        $item = InventoryItem::factory()->create(['quantity' => 20]);

        $usage = $this->service->logUsage($item, $user, ['quantity_used' => 3, 'work_job_id' => null, 'notes' => 'test note']);

        $this->assertDatabaseHas('inventory_usages', [
            'inventory_item_id' => $item->id,
            'used_by'           => $user->id,
            'quantity_used'     => 3,
            'notes'             => 'test note',
        ]);
    }

    public function test_log_usage_links_work_job(): void
    {
        $user = User::factory()->technician()->create();
        $item = InventoryItem::factory()->create(['quantity' => 10]);
        $job  = WorkJob::factory()->create(['technician_id' => $user->id]);

        $this->service->logUsage($item, $user, ['quantity_used' => 1, 'work_job_id' => $job->id, 'notes' => null]);

        $this->assertDatabaseHas('inventory_usages', [
            'inventory_item_id' => $item->id,
            'work_job_id'       => $job->id,
        ]);
    }

    public function test_log_usage_sends_low_stock_notification_to_admins(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $user  = User::factory()->technician()->create();
        $item  = InventoryItem::factory()->create(['quantity' => 6, 'low_stock_threshold' => 5]);

        $this->service->logUsage($item, $user, ['quantity_used' => 2, 'work_job_id' => null, 'notes' => null]);

        Notification::assertSentTo($admin, LowStockNotification::class);
    }

    public function test_no_notification_when_threshold_is_zero(): void
    {
        Notification::fake();

        User::factory()->admin()->create();
        $user = User::factory()->technician()->create();
        $item = InventoryItem::factory()->create(['quantity' => 2, 'low_stock_threshold' => 0]);

        $this->service->logUsage($item, $user, ['quantity_used' => 2, 'work_job_id' => null, 'notes' => null]);

        Notification::assertNothingSent();
    }

    public function test_is_low_stock_returns_true_when_at_threshold(): void
    {
        $item = InventoryItem::factory()->create(['quantity' => 5, 'low_stock_threshold' => 5]);

        $this->assertTrue($item->isLowStock());
    }

    public function test_is_low_stock_returns_false_when_above_threshold(): void
    {
        $item = InventoryItem::factory()->create(['quantity' => 10, 'low_stock_threshold' => 5]);

        $this->assertFalse($item->isLowStock());
    }
}
