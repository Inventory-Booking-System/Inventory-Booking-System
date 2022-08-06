<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Location;
use Illuminate\Support\Str;

class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement(['R0', 'Ranmore Hall', 'Gym', 'Common Room', 'BPA', 'B8']),
        ];
    }
}
