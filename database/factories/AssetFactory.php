<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Asset;
use Illuminate\Support\Str;

class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Ranmore Loan Laptop', 'HD Stills Camera', 'HD Video Camera', 'Bradley Loan Laptop', 'Box of Headphones', 'Student Laptop']),
            'tag' => $this->faker->unique()->randomNumber(4),
            'description' => $this->faker->sentence()
        ];
    }
}
