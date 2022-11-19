<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)
            ->hasAttached(
                Team::factory()->count(3)
            )
            ->create();
        \App\Models\Tag::factory(10)->create();
        \App\Models\Asset::factory(10)->create();
    }
}
