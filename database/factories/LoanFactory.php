<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Loan;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{

    protected $model = \App\Models\Loan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'status_id' => $this->faker->numberBetween(0,6),
            'start_date_time' => Carbon::now(),
            'end_date_time' => Carbon::now(),
            'details' => $this->faker->text,
            'created_at' => Carbon::now()
        ];
    }

    public function withUser(User $user)
    {
        return $this->state([
            'user_id' => $user->id
        ]);
    }

    public function withCreator(User $user)
    {
        return $this->state([
            'created_by' => $user->id
        ]);
    }
}
