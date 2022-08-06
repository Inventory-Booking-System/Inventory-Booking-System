<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DistributionGroup;
use Illuminate\Support\Str;

class DistributionGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Y7', 'Y8', 'Y9', 'Y10', 'Y11', 'Y12', 'Y13', 'Lower School', 'Upper School']),
        ];
    }
}
