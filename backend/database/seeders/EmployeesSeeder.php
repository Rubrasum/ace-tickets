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
        // Make sure devices exist
        $scanner_devices = Device::factory()->count(200)->scanner()->create();
        $counter_devices = Device::factory()->count(20)->counter()->create();

        // Make sure roles exist
        $scannerRole = Role::firstOrCreate([
            'name' => 'ticket scanner',
            'guard_name' => 'api',
        ]);
        $counterRole = Role::firstOrCreate([
            'name' => 'ticket counter',
            'guard_name' => 'web',
        ]);


        // Assign scanner users
        $scanner_devices->each(function ($device) {
            User::factory()
                ->withRole('ticket scanner')
                ->create([
                    'role' => 'ticket scanner',
                    'device_id' => $device->id,
                ]);
        });
        // Assign counter users
        $counter_devices->each(function ($device) {
            User::factory()
                ->withRole('ticket counter')
                ->create([
                    'role' => 'ticket counter',
                    'device_id' => $device->id,
                ]);
        });
    }
}
