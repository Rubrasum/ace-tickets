<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure admin role exists
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // Create an admin user and assign role
        $admin = User::factory()
            ->withRole('admin') // using your factory macro
            ->create([
                'email' => 'dev@joebeze.com',
                'role' => 'admin',
                'password' => Hash::make('password'),
            ]);
    }
}
