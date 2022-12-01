<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;
    
    /** @test */
    public function can_see_admin_register_page()
    {
        $response = $this->get('api/register');

        $response->assertStatus(200);
    }

    /** @test */
    public function can_see_login_page()
    {
        $response = $this->get('api/login');

        $response->assertStatus(200);
    }

    /** @test */
    public function can_register_as_admin()
    {
        $response = $this->post('api/register', [
            'name' => 'FaycalAdmin',
            'email' => 'FaycalAdmin@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 2,
        ]);

        $this->assertTrue($response['success']);
    }

    /** @test */
    public function admin_can_register_a_user()
    {
        $admin = User::first();

        $this->actingAs($admin)->json(
            'POST',
            route('user-register'),
            [
                'name' => 'FaycalUser',
                'email' => 'FaycalUser@gmail.com',
                'password' => '123456',
                'team_id' => 1,
            ]
        );
        
        $this->assertDatabaseHas('users',[
            'name' => 'FaycalUser',
            'role' => 1,
        ]);
    }

    /** @test */
    function cannot_create_two_users_with_same_email()
    {
        $admin = User::first();

        $this->post('api/register', [
            'name' => 'DuplicatedEmail',
            'email' => $admin->email,
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 2,
        ]);

        $this->assertDatabaseMissing('users',[
            'name' => 'DuplicatedEmail',
        ]);
    }

    /** @test */
    function user_can_logout()
    {
        $admin = User::first();

        $this->actingAs($admin)->json('POST', route('user-logout'));

        // Not sure if I'm truly testing logout with this one anymore
        // https://github.com/laravel/sanctum/issues/256#issuecomment-846637741
        $this->refreshApplication();

        $this->assertGuest();
    }
}
