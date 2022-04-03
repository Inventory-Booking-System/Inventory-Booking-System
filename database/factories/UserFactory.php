<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Support\Str;

class UserFactory extends Factory
{

    protected $model = \App\Models\User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'forename' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'email' => $this->faker->email()
        ];
    }
}
