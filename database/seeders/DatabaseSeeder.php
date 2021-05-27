<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersSeeder::class,
        ]);
    }
}
