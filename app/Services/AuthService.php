<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Attempt to log in a user with the given credentials.
     *
     * @param  array{email: string, password: string, remember?: bool}  $credentials
     * @throws ValidationException
     */
    public function login(array $credentials): User
    {
        $remember = $credentials['remember'] ?? false;

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('auth.deactivated'),
            ]);
        }

        return $user;
    }

    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Redirect path based on the user's role.
     */
    public function redirectPathForRole(Role $role): string
    {
        return match ($role) {
            Role::Admin      => '/dashboard/admin',
            Role::Doctor     => '/dashboard/doctor',
            Role::Technician => '/dashboard/technician',
        };
    }
}
