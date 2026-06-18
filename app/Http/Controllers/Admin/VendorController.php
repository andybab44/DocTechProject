<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVendorRequest;
use App\Http\Requests\Admin\UpdateVendorRequest;
use App\Models\Vendor;
use App\Services\VendorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function __construct(private readonly VendorService $vendorService) {}

    public function index(): View
    {
        $vendors = $this->vendorService->paginate();

        return view('admin.vendors.index', compact('vendors'));
    }

    public function create(): View
    {
        return view('admin.vendors.create');
    }

    public function store(StoreVendorRequest $request): RedirectResponse
    {
        $this->vendorService->create($request->validated());

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', __('app.vendors.success_created'));
    }

    public function edit(Vendor $vendor): View
    {
        return view('admin.vendors.edit', compact('vendor'));
    }

    public function update(UpdateVendorRequest $request, Vendor $vendor): RedirectResponse
    {
        $this->vendorService->update($vendor, $request->validated());

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', __('app.vendors.success_updated'));
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        $this->vendorService->delete($vendor);

        return redirect()
            ->route('admin.vendors.index')
            ->with('success', __('app.vendors.success_deleted'));
    }
}
