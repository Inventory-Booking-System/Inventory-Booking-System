<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
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
            'email' => $this->faker->unique()->email(),
            'role_id' => 0,
        ];
    }

    public function withSuperAdmin()
    {
        return $this->state([
            'forename' => 'Super',
            'surname' => 'Admin',
            'email' => "admin@admin123.com",
            'role_id' => 1,
            'password' => Hash::make('1234'),
        ]);
    }
}
