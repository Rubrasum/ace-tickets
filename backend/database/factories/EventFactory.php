<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->sentence(3, false);
        $eventTypes = ['Festival', 'Conference', 'Expo', 'Workshop', 'Concert', 'Seminar', 'Market', 'Fair', 'Competition', 'Gala'];
        $locations = [
            'Convention Center', 'Community Hall', 'Downtown Plaza', 'Riverside Park',
            'Grand Ballroom', 'Exhibition Hall', 'Outdoor Amphitheater', 'Civic Center',
            'Memorial Stadium', 'Art Museum', 'Conference Center', 'Town Square'
        ];

        $fullName = $name . ' ' . $this->faker->randomElement($eventTypes);
        $startDate = $this->faker->dateTimeBetween('-2 years', '+2 years');
        $duration = $this->faker->numberBetween(1, 7); // 1-7 days
        $endDate = (clone $startDate)->modify("+{$duration} days");

        return [
            'name' => $fullName,
            'slug' => Str::slug($fullName),
            'location' => $this->faker->randomElement($locations) . ' - ' . $this->faker->city(),
            'starts_at' => $startDate,
            'ends_at' => $endDate,
            'description' => $this->faker->paragraph(3),
            'is_active' => false, // 80% chance of being active
            'ticket_limit' => $this->faker->numberBetween(50, 10000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Create an event with a specific start date
     */
    public function withStartDate($date)
    {
        return $this->state(function (array $attributes) use ($date) {
            $startDate = is_string($date) ? new \DateTime($date) : $date;
            $duration = $this->faker->numberBetween(1, 7);
            $endDate = (clone $startDate)->modify("+{$duration} days");

            return [
                'starts_at' => $startDate,
                'ends_at' => $endDate,
            ];
        });
    }


    /**
     * Create events with random locations from provided array
     */
    public function withRandomLocation($locations)
    {
        return $this->state(function (array $attributes) use ($locations) {
            return [
                'location' => collect($locations)->random(),
            ];
        });
    }
    /**
     * Create a past event
     */
    public function past()
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-2 years', '-1 month');
            $duration = $this->faker->numberBetween(1, 7);
            $endDate = (clone $startDate)->modify("+{$duration} days");

            return [
                'starts_at' => $startDate,
                'ends_at' => $endDate,
                'is_active' => false, // Past events are inactive
            ];
        });
    }

    /**
     * Create a future event
     */
    public function future()
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('+1 month', '+2 years');
            $duration = $this->faker->numberBetween(1, 7);
            $endDate = (clone $startDate)->modify("+{$duration} days");

            return [
                'starts_at' => $startDate,
                'ends_at' => $endDate,
                'is_active' => true, // Future events are active
            ];
        });
    }
}
