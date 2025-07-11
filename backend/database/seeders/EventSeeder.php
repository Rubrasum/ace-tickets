<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define 10 locations to be reused
        $locations = [
            'Orlando Convention Center - Orlando',
            'Miami Beach Convention Center - Miami Beach',
            'Tampa Convention Center - Tampa',
            'Lakeland Linder Regional Airport',
            'Broward County Convention Center - Fort Lauderdale',
            'Orange County Convention Center - Orlando',
            'Lakeland Center - Lakeland',
            'Tallahassee Leon County Civic Center - Tallahassee',
            'Pensacola Bay Center - Pensacola',
            'Daytona Beach Ocean Center - Daytona Beach'
        ];

        // 1. Generate 100 past events, 2 months apart
        $pastDate = now()->subYears(2); // Start 2 years ago
        for ($i = 0; $i < 100; $i++) {
            Event::factory()
                ->withStartDate($pastDate->copy())
                ->withRandomLocation($locations)
                ->past()
                ->create();
            $pastDate->addMonths(2);
        }

        // 2. Generate 4 upcoming events (next few months)
        Event::factory()->count(4)
            ->withRandomLocation($locations)
            ->future()
            ->create();

        // 3. Create the specific SUN N FUN event
        Event::create([
            'name' => 'ACE SUN N FUN Aerospace Expo',
            'slug' => 'ace-sun-n-fun-aerospace-expo',
            'location' => 'Lakeland Linder Regional Airport',
            'starts_at' => '2026-04-07 08:00:00',
            'ends_at' => '2026-04-13 17:00:00',
            'description' => 'The premier aviation event featuring aircraft displays, airshows, educational seminars, and the latest in aerospace technology. A week-long celebration of flight.',
            'is_active' => true,
            'ticket_limit' => 200000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Generate 10 more future events, 1 year apart
        $futureDate = now()->addYear(); // Start 1 year from now
        for ($i = 0; $i < 10; $i++) {
            Event::factory()
                ->withStartDate($futureDate->copy())
                ->withRandomLocation($locations)
                ->future()
                ->create();
            $futureDate->addYear();
        }
    }
}
