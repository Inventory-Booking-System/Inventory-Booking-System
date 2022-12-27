<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'role_id' => $this->faker->numberBetween(0,1),
        ];
    }

    public function withUser(User $user)
    {
        return $this->state([
            'user_id' => $user->id,
        ]);
    }
}
