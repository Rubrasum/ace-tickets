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
     * This is to create the Employee accounts that would be assigned access to the scanners and the counters. Need
     * to create devices for them all but the device specific functionality is not the priority.
     */
    public function run(): void
    {

        // Make sure roles exist
        $scannerRole = Role::firstOrCreate([
            'name' => 'ticket scanner',
            'guard_name' => 'api',
        ]);
        $counterRole = Role::firstOrCreate([
            'name' => 'ticket counter',
            'guard_name' => 'web',
        ]);

        // Create and assign scanner devices and users.
        Device::factory()->count(200)->scanner()->create()->each(function ($device) use ($scannerRole) {
            $user = User::factory()->create([
                'device_id' => $device->id,
            ]);
            $user->assignRole($scannerRole);
        });

        // Create and assign counter devices and users
        Device::factory()->count(20)->counter()->create()->each(function ($device) use ($counterRole) {
            $user = User::factory()->create([
                'device_id' => $device->id,
            ]);
            $user->assignRole($counterRole);
        });
    }
}
