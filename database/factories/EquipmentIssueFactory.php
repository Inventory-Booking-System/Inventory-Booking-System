<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentIssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->unique()->randomElement(['Keyboard Broken', 'Monitor Broken', 'Mouse Broken', 'Keyboard Vandalised', 'Mouse Vandalised', 'PC Unplugged']),
            'cost' => $this->faker->numberBetween(10,100),
        ];
    }
}
