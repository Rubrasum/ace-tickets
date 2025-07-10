<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'app_used' => 'none',      // fixed value
            'type' => 'mobile',               // fixed value
            'is_active' => true,
            'last_login_at' => now(),
            'last_login_by' => null,
        ];
    }

    public function counter(): static
    {
        return $this->state(function () {
            return [
                'app_used' => 'counter-app',
                'type' => fake()->randomElement(['tablet', 'desktop']),
            ];
        });
    }

    public function scanner(): static
    {
        return $this->state([
            'app_used' => 'scanner-app',
            'type' => 'mobile',
        ]);
    }
}
