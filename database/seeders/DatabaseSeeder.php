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
        // Tag::factory()->count(5)->for(
        //     User::factory(7), 'taggable')
        //     ->hasAttached(Team::factory()->count(4))
        // ->create();

        User::factory(7)
            ->hasAttached(Team::factory(2))
            ->create();

        // Tag::factory(7)
        //     ->hasAssets(3)
        //     ->create();
        
        // Asset::factory(10)->create();
    }
}
