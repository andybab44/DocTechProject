<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Module;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLicenseRequest;
use App\Http\Requests\Admin\UpdateLicenseRequest;
use App\Models\License;
use App\Models\User;
use App\Services\LicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LicenseController extends Controller
{
    public function __construct(private readonly LicenseService $licenseService) {}

    public function index(): View
    {
        $users   = User::with('license')->orderBy('name')->paginate(20);
        $modules = Module::cases();

        return view('admin.licenses.index', compact('users', 'modules'));
    }

    public function create(): View
    {
        $users   = User::whereDoesntHave('license')->orderBy('name')->get();
        $modules = Module::cases();

        return view('admin.licenses.create', compact('users', 'modules'));
    }

    public function store(StoreLicenseRequest $request): RedirectResponse
    {
        $user = User::findOrFail($request->validated('user_id'));
        $this->licenseService->create($user, $request->validated());

        return redirect()
            ->route('admin.licenses.index')
            ->with('success', 'License created for ' . $user->name . '.');
    }

    public function edit(License $license): View
    {
        $modules = Module::cases();
        $license->load('user');

        return view('admin.licenses.edit', compact('license', 'modules'));
    }

    public function update(UpdateLicenseRequest $request, License $license): RedirectResponse
    {
        $this->licenseService->update($license, $request->validated());

        return redirect()
            ->route('admin.licenses.index')
            ->with('success', 'License updated.');
    }

    public function toggleActive(License $license): RedirectResponse
    {
        $this->licenseService->toggleActive($license);

        return redirect()
            ->route('admin.licenses.index')
            ->with('success', 'License ' . ($license->is_active ? 'deactivated' : 'activated') . '.');
    }
}
