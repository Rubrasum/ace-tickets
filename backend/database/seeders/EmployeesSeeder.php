<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class EmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scannerRole = Role::firstOrCreate([
            'name' => 'ticket scanner',
            'guard_name' => 'api',
        ]);
        $counterRole = Role::firstOrCreate([
            'name' => 'ticket counter',
            'guard_name' => 'web',
        ]);


        User::factory()
            ->count(200)
            ->withRole('ticket scanner')
            ->create([
                'role' => 'ticket scanner',
            ])
            ->each(function ($user) {
                $device = Device::factory()->scanner()->create();
                $user->device_id = $device->id;
                $user->save();
            });
        // Create 20 counter users + devices
        User::factory()
            ->count(20)
            ->withRole('ticket counter')
            ->create([
                'role' => 'ticket counter',
            ])
            ->each(function ($user) {
                $device = Device::factory()->counter()->create();
                $user->device_id = $device->id;
                $user->save();
            });
    }
}
