<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\Incident;
use App\Models\User;
use App\Models\Location;
use App\Models\DistributionGroup;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
 */
class IncidentFactory extends Factory
{

    protected $model = \App\Models\Incident::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'start_date_time' => Carbon::now(),
            'status_id' => 0,
            'evidence' => $this->faker->text,
            'details' => $this->faker->text,
            'created_at' => Carbon::now(),
        ];
    }

    public function withStatusId($statusId)
    {
        return $this->state([
            'status_id' => $statusId
        ]);
    }

    public function withLocation(Location $location)
    {
        return $this->state([
            'location_id' => $location->id
        ]);
    }

    public function withDistributionGroup(DistributionGroup $distributionGroup)
    {
        return $this->state([
            'distribution_id' => $distributionGroup->id
        ]);
    }

    public function withCreator(User $user)
    {
        return $this->state([
            'created_by' => $user->id
        ]);
    }

    public function withEvidence($evidence)
    {
        return $this->state([
            'evidence' => $evidence
        ]);
    }
}
