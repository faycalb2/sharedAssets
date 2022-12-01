<?php

namespace Tests\Feature;

use App\Models\Team;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function can_see_teams()
    {
        $admin = User::first();
        
        $response = $this->actingAs($admin)->json('GET', route('teams.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function can_add_team()
    {
        $admin = User::first();

        $this->actingAs($admin)->json(
            'POST', 
            route('teams.store'),
            [
                'name' => 'My new team!',
            ]
        );

        $this->assertDatabaseHas('teams',[
            'name' => 'My new team!',
        ]);
    }

    /** @test */
    public function can_update_team()
    {
        $team = team::first();
        $admin = $team->users()->first();

        $this->actingAs($admin)->json(
            'PATCH', 
            route('teams.update', $team),
            [
                'name' => 'My updated team!'
            ]
        );

        $this->assertDatabaseHas('teams',[
            'name' => 'My updated team!',
        ]);
    }

    /** @test */
    public function can_delete_team()
    {
        $team = team::first();
        $admin = $team->users()->first();

        $this->actingAs($admin)->json(
            'DELETE', 
            route('teams.destroy', $team)
        );

        $this->assertDatabaseMissing('teams', [
            'id' => $team->id,
        ]);
    }
}
