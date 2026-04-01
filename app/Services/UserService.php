<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Return a paginated list of all users.
     *
     * @return LengthAwarePaginator<User>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::orderBy('name')->paginate($perPage);
    }

    /**
     * Create a new user.
     *
     * @param  array{name: string, email: string, password: string, role: string}  $data
     */
    public function create(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => Role::from($data['role']),
        ]);
    }
}
