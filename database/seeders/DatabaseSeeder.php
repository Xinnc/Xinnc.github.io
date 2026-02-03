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

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::updateOrCreate(['role' => 'admin']);
        Role::updateOrCreate(['role' => 'manager']);
        Role::updateOrCreate(['role' => 'employee']);

        User::updateOrCreate([
            'first_name' => 'admin',
            'surname' => 'admin',
            'last_name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin123'),
            'role_id' => Role::where('role', 'admin')->first()->id,
        ]);
    }
}
