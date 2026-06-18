<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Http\Requests\StoreInventoryUsageRequest;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventoryItemController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService) {}

    public function index(): View
    {
        $items = $this->inventoryService->paginate();

        return view('inventory.index', compact('items'));
    }

    public function show(InventoryItem $inventoryItem): View
    {
        $inventoryItem->load('vendor');
        $usages   = $this->inventoryService->usageForItem($inventoryItem);
        $workJobs = auth()->user()->isAdmin()
            ? \App\Models\WorkJob::orderBy('scheduled_at', 'desc')->get()
            : auth()->user()->workJobsAsTechnician()->orderBy('scheduled_at', 'desc')->get();

        return view('inventory.show', compact('inventoryItem', 'usages', 'workJobs'));
    }

    public function logUsage(StoreInventoryUsageRequest $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $data = $request->validated();

        if ($data['quantity_used'] > $inventoryItem->quantity) {
            return back()->withErrors(['quantity_used' => __('app.inventory.error_insufficient_stock')]);
        }

        $this->inventoryService->logUsage($inventoryItem, auth()->user(), $data);

        return redirect()
            ->route('inventory.show', $inventoryItem)
            ->with('success', __('app.inventory.success_usage_logged'));
    }
}
