<?php

namespace Tests\Feature;

use App\Models\Tag;
use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssetTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function can_see_assets()
    {
        $admin = User::first();
        
        $response = $this->actingAs($admin)->json('GET', route('assets.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function can_add_asset()
    {
        $admin = User::first();

        $tag = $admin->tags()->create([
            'name' => 'TagName',
        ]);

        $team = Team::create([
            'name' => 'TeamName',
        ]);
        $team->users()->attach($admin->id);

        $this->actingAs($admin)->json(
            'POST', 
            route('assets.store'),
            [
                'label' => 'My new task!',
                'content' => 'Something',
                'user_id' => $admin->id,
                'team_id' => $team->id,
            ]
        );

        $this->assertDatabaseHas('assets',[
            'label' => 'My new task!',
        ]);
    }

    /** @test */
    public function can_update_asset()
    {
        $asset = Asset::first();
        $admin = $asset->user()->first();

        $this->actingAs($admin)->json(
            'PATCH', 
            route('assets.update', $asset),
            [
                'label' => 'My updated task!',
                'content' => 'Something',
                'team_id' => $asset->team_id,
            ]
        );

        $this->assertDatabaseHas('assets',[
            'label' => 'My updated task!',
        ]);
    }

    /** @test */
    public function can_delete_asset()
    {
        $asset = Asset::first();
        $admin = $asset->user()->first();

        $this->actingAs($admin)->json(
            'DELETE', 
            route('assets.destroy', $asset)
        );

        $this->assertDatabaseMissing('assets', [
            'id' => $asset->id,
        ]);
    }
}
