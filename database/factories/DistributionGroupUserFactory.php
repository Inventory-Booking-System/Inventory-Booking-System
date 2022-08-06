<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DistributionGroupUser;
use App\Models\DistributionGroup;
use App\Models\User;
use Illuminate\Support\Str;

class DistributionGroupUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'distribution_group_id' => DistributionGroup::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
