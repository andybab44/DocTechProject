<?php

namespace App\Services;

use App\Models\License;
use App\Models\User;

class LicenseService
{
    public function create(User $user, array $data): License
    {
        return $user->license()->create([
            'expires_at' => $data['expires_at'] ?? null,
            'is_active'  => true,
            'modules'    => $data['modules'] ?? null,
        ]);
    }

    public function update(License $license, array $data): License
    {
        $license->update([
            'expires_at' => $data['expires_at'] ?? null,
            'modules'    => $data['modules'] ?? null,
        ]);

        return $license->fresh();
    }

    public function toggleActive(License $license): License
    {
        $license->update(['is_active' => !$license->is_active]);

        return $license->fresh();
    }
}
