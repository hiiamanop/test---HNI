<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $token;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 'admin',
        ]);
        
        // Generate token
        $this->token = JWTAuth::fromUser($this->user);
    }

    /** @test */
    public function it_can_list_all_users()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password',
            'role' => 'user',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/users', $userData);

        $response->assertStatus(201);
        $response->assertJsonFragment(['email' => 'new@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }

    /** @test */
    public function it_can_show_a_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/users/' . $this->user->id);

        $response->assertStatus(200);
        $response->assertJsonFragment(['email' => 'test@example.com']);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/users/' . $this->user->id);

        $response->assertStatus(200);
        $this->assertSoftDeleted('users', ['id' => $this->user->id]);
    }
}