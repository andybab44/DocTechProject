<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RestockInventoryItemRequest;
use App\Http\Requests\Admin\StoreInventoryItemRequest;
use App\Http\Requests\Admin\UpdateInventoryItemRequest;
use App\Models\InventoryItem;
use App\Models\Vendor;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventoryItemController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService) {}

    public function create(): View
    {
        $vendors = Vendor::orderBy('name')->get();

        return view('admin.inventory.create', compact('vendors'));
    }

    public function store(StoreInventoryItemRequest $request): RedirectResponse
    {
        $this->inventoryService->create($request->validated());

        return redirect()
            ->route('inventory.index')
            ->with('success', __('app.inventory.success_created'));
    }

    public function edit(InventoryItem $inventoryItem): View
    {
        $vendors = Vendor::orderBy('name')->get();

        return view('admin.inventory.edit', compact('inventoryItem', 'vendors'));
    }

    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $this->inventoryService->update($inventoryItem, $request->validated());

        return redirect()
            ->route('inventory.index')
            ->with('success', __('app.inventory.success_updated'));
    }

    public function restock(RestockInventoryItemRequest $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $this->inventoryService->restock($inventoryItem, $request->validated('quantity'));

        return redirect()
            ->route('inventory.show', $inventoryItem)
            ->with('success', __('app.inventory.success_restocked'));
    }

    public function destroy(InventoryItem $inventoryItem): RedirectResponse
    {
        $this->inventoryService->delete($inventoryItem);

        return redirect()
            ->route('inventory.index')
            ->with('success', __('app.inventory.success_deleted'));
    }
}
