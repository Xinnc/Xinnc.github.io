<?php

namespace Database\Seeders;

use App\Domains\Shared\Model\Role;
use App\Domains\User\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $roles = ['admin', 'manager', 'employee'];

        foreach ($roles as $roleName) {
            Role::updateOrCreate(
                ['role' => $roleName],
                ['role' => $roleName]
            );
        }

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'admin',
                'surname'    => 'admin',
                'last_name'  => 'admin',
                'email'      => 'admin@gmail.com',
                'password'   => Hash::make('Admin123'),
                'role_id'    => Role::where('role', 'admin')->first()->id,
            ]
        );
    }
}
