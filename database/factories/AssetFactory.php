<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'label' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'tag_id' => Tag::all()->random()->id,
            'team_id' => Team::all()->random()->id,
            'user_id' => User::all()->random()->id,
        ];
    }
}
