<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendReorderRequest;
use App\Mail\ReorderRequestMail;
use App\Models\InventoryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReorderController extends Controller
{
    public function index(): View
    {
        $items = InventoryItem::with('vendor')
            ->orderByRaw('quantity <= low_stock_threshold DESC')
            ->orderBy('name')
            ->get();

        // Group by vendor; null-vendor items are separate
        $withVendor    = $items->filter(fn ($i) => $i->vendor_id !== null)->groupBy('vendor_id');
        $withoutVendor = $items->filter(fn ($i) => $i->vendor_id === null);

        return view('inventory.reorder', compact('items', 'withVendor', 'withoutVendor'));
    }

    public function send(SendReorderRequest $request): RedirectResponse
    {
        $rows = collect($request->validated('items'));

        // Load items with vendor
        $itemMap = InventoryItem::with('vendor')
            ->whereIn('id', $rows->pluck('inventory_item_id'))
            ->get()
            ->keyBy('id');

        // Build lines grouped by vendor; skip items without a vendor
        $byVendor = $rows
            ->filter(fn ($row) => $itemMap[$row['inventory_item_id']]?->vendor !== null)
            ->groupBy(fn ($row) => $itemMap[$row['inventory_item_id']]->vendor_id);

        if ($byVendor->isEmpty()) {
            return back()->with('warning', __('app.reorder.no_vendor_items'));
        }

        $reference = 'RO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        foreach ($byVendor as $vendorId => $vendorRows) {
            $vendor = $itemMap[$vendorRows->first()['inventory_item_id']]->vendor;

            $lines = $vendorRows->map(fn ($row) => [
                'item'     => $itemMap[$row['inventory_item_id']],
                'quantity' => $row['quantity'],
            ])->values()->all();

            Mail::to($vendor->email)->send(new ReorderRequestMail($vendor, $lines, $reference));
        }

        $vendorCount = $byVendor->count();

        return redirect()
            ->route('inventory.index')
            ->with('success', __('app.reorder.sent', ['count' => $vendorCount]));
    }
}
