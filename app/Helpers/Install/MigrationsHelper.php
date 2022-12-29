<?php

// Author: John Doe
// License: MIT
// Source: https://github.com/rashidlaasri/LaravelInstaller
// Note: This file has been modified to be included directly in the software rather than via a composer package

namespace App\Helpers\Install;

use Illuminate\Support\Facades\DB;

trait MigrationsHelper
{
    /**
     * Get the migrations in /database/migrations.
     *
     * @return array Array of migrations name, empty if no migrations are existing
     */
    public function getMigrations()
    {
        $migrations = glob(database_path().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR.'*.php');

        return str_replace('.php', '', $migrations);
    }

    /**
     * Get the migrations that have already been ran.
     *
     * @return \Illuminate\Support\Collection List of migrations
     */
    public function getExecutedMigrations()
    {
        // migrations table should exist, if not, user will receive an error.
        return DB::table('migrations')->get()->pluck('migration');
    }
}
