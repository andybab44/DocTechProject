<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Return a paginated list of all users with optional search and role filters.
     *
     * @return LengthAwarePaginator<User>
     */
    public function paginate(int $perPage = 15, string $search = '', ?string $role = null): LengthAwarePaginator
    {
        return User::withCount(['workJobsAsDoctor', 'workJobsAsTechnician'])
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($role !== null && $role !== '', fn ($q) => $q->where('role', $role))
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Create a new user.
     *
     * @param  array{name: string, email: string, password: string, role: string}  $data
     */
    public function create(array $data): User
    {
        return User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => Role::from($data['role']),
            'is_active' => true,
        ]);
    }

    /**
     * Update an existing user's profile. Password is only changed when provided.
     *
     * @param  array{name: string, email: string, role: string, password?: string}  $data
     */
    public function update(User $user, array $data): User
    {
        $payload = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => Role::from($data['role']),
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return $user->fresh();
    }

    /**
     * Toggle the active status of a user.
     */
    public function setActive(User $user, bool $active): User
    {
        $user->update(['is_active' => $active]);

        return $user->fresh();
    }
}
