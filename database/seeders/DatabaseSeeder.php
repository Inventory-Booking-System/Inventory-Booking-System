<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\User;
use App\Models\Location;
use App\Models\DistributionGroup;
use App\Models\DistributionGroupUser;
use App\Models\EquipmentIssue;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(200)->create();
        Asset::factory()->count(20)->create();
        Location::factory()->count(6)->create();
        EquipmentIssue::factory()->count(6)->create();
        DistributionGroup::factory()->count(6)->create();
        DistributionGroupUser::factory()->count(100)->create();

        //User::factory()->count(6)->hasAttached(DistributionGroup::factory())->create();
    }
}
