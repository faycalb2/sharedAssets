<?php

namespace Tests\Feature;

use App\Models\Tag;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /** @test */
    public function can_see_tags()
    {
        $admin = User::first();
        
        $response = $this->actingAs($admin)->json('GET', route('tags.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function can_add_tag()
    {
        $admin = User::first();

        $this->actingAs($admin)->json(
            'POST', 
            route('tags.store'),
            [
                'name' => 'My new tag!',
                'user_id' => $admin->id,
            ]
        );

        $this->assertDatabaseHas('tags',[
            'name' => 'My new tag!',
        ]);
    }

    /** @test */
    public function can_update_tag()
    {
        $tag = Tag::first();
        $admin = $tag->user()->first();

        $this->actingAs($admin)->json(
            'PATCH', 
            route('tags.update', $tag),
            [
                'name' => 'My updated tag!'
            ]
        );

        $this->assertDatabaseHas('tags',[
            'name' => 'My updated tag!',
        ]);
    }

    /** @test */
    public function can_delete_tag()
    {
        $tag = Tag::first();
        $admin = $tag->user()->first();

        $this->actingAs($admin)->json(
            'DELETE', 
            route('tags.destroy', $tag)
        );

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }
}
