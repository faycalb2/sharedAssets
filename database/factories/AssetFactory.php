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
        $user_id = User::all()->random()->id;

        $tags = Tag::where('user_id', '=', $user_id)->pluck('id')->toArray();
        
        $teams = Team::
                    whereHas('users', function($q) use($user_id) {
                        $q->where('user_id', '=', $user_id);  
                    })
                    ->pluck('id')->toArray();
        return [
            'label' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'user_id' => $user_id,
            'tag_id' => $this->faker->numberBetween($tags[0], $tags[array_key_last($tags)]),
            'team_id' => $this->faker->numberBetween($teams[0], $teams[array_key_last($teams)]),
        ];
    }
}
