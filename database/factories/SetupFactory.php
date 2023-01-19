<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Loan;
use App\Models\Setup;
use App\Models\Location;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setup>
 */
class SetupFactory extends Factory
{

    protected $model = \App\Models\Setup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'created_at' => Carbon::now(),
            'title' => $this->faker->text(50)
        ];
    }

    public function withLoan(Loan $loan)
    {
        return $this->state([
            'loan_id' => $loan->id
        ]);
    }

    public function withLocation(Location $location)
    {
        return $this->state([
            'location_id' => $location->id
        ]);
    }
}
