<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\InventoryItem;
use App\Models\InventoryUsage;
use App\Models\User;
use App\Models\WorkJob;
use App\Notifications\LowStockNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InventoryService
{
    /**
     * Return a paginated list of all inventory items.
     *
     * @return LengthAwarePaginator<InventoryItem>
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return InventoryItem::withCount('usages')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Return all inventory items ordered by name (for select dropdowns).
     *
     * @return Collection<int, InventoryItem>
     */
    public function all(): Collection
    {
        return InventoryItem::orderBy('name')->get();
    }

    /**
     * Create a new inventory item.
     *
     * @param  array{name: string, description: ?string, quantity: int, unit: string, category: ?string, low_stock_threshold: int}  $data
     */
    public function create(array $data): InventoryItem
    {
        return InventoryItem::create($data);
    }

    /**
     * Update an existing inventory item's details (not quantity — use restock for that).
     *
     * @param  array{name: string, description: ?string, unit: string, category: ?string, low_stock_threshold: int}  $data
     */
    public function update(InventoryItem $item, array $data): InventoryItem
    {
        $item->update($data);

        return $item->fresh();
    }

    /**
     * Add stock to an existing inventory item.
     */
    public function restock(InventoryItem $item, int $quantity): InventoryItem
    {
        $item->increment('quantity', $quantity);

        return $item->fresh();
    }

    /**
     * Delete an inventory item.
     */
    public function delete(InventoryItem $item): void
    {
        $item->delete();
    }

    /**
     * Log usage of an inventory item against an optional work job.
     * Deducts from stock and fires a low-stock notification to admins if threshold is crossed.
     *
     * @param  array{quantity_used: int, work_job_id: ?int, notes: ?string}  $data
     */
    public function logUsage(InventoryItem $item, User $user, array $data): InventoryUsage
    {
        $usage = InventoryUsage::create([
            'inventory_item_id' => $item->id,
            'work_job_id'       => $data['work_job_id'] ?? null,
            'used_by'           => $user->id,
            'quantity_used'     => $data['quantity_used'],
            'notes'             => $data['notes'] ?? null,
        ]);

        $item->decrement('quantity', $data['quantity_used']);
        $item->refresh();

        if ($item->low_stock_threshold > 0 && $item->isLowStock()) {
            $this->notifyAdminsLowStock($item);
        }

        return $usage;
    }

    /**
     * Return paginated usage records for a given item.
     *
     * @return LengthAwarePaginator<InventoryUsage>
     */
    public function usageForItem(InventoryItem $item, int $perPage = 20): LengthAwarePaginator
    {
        return $item->usages()
            ->with(['user', 'workJob'])
            ->latest()
            ->paginate($perPage);
    }

    private function notifyAdminsLowStock(InventoryItem $item): void
    {
        User::where('role', Role::Admin)->each(
            fn (User $admin) => $admin->notify(new LowStockNotification($item))
        );
    }
}
