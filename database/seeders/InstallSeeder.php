<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Make admin user
        $userAdmin = User::factory()->count(1)->withPasswordSet()->create()->first();
        Role::factory()->count(1)->withUser($userAdmin)->create();
    }
}
