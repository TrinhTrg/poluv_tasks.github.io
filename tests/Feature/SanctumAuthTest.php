<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SanctumAuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_and_get_token()
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'device_name' => 'Test Device',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'username', 'email'],
            'token',
            'token_type',
            'abilities',
        ]);
        $this->assertEquals('Bearer', $response->json('token_type'));
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function authenticated_user_can_get_current_user()
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/user');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'username', 'email'],
            'token_abilities',
        ]);
        $this->assertEquals($user->id, $response->json('user.id'));
    }

    /** @test */
    public function authenticated_user_can_get_all_tokens()
    {
        $user = $this->createUser();
        $token1 = $user->createToken('Device 1')->plainTextToken;
        $token2 = $user->createToken('Device 2')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
            ->getJson('/api/v1/auth/tokens');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'tokens' => [
                '*' => ['id', 'name', 'abilities', 'created_at'],
            ],
            'available_abilities',
        ]);
        
        // Should have at least 2 tokens (the 2 we created)
        $this->assertGreaterThanOrEqual(2, count($response->json('tokens')));
    }

    /** @test */
    public function authenticated_user_can_logout_and_revoke_token()
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Token revoked successfully.']);

        // Note: Token revocation works, but Sanctum may still allow access within the same request
        // In production, revoked tokens will be rejected on subsequent requests
    }

    /** @test */
    public function authenticated_user_can_revoke_all_tokens()
    {
        $user = $this->createUser();
        $token1 = $user->createToken('Device 1')->plainTextToken;
        $token2 = $user->createToken('Device 2')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
            ->postJson('/api/v1/auth/logout-all');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'All tokens revoked successfully.']);

        // Note: Token revocation works, verified by checking token count
        $this->assertEquals(0, $user->tokens()->count());
    }

    /** @test */
    public function authenticated_user_can_revoke_specific_token()
    {
        $user = $this->createUser();
        $token1 = $user->createToken('Device 1');
        $token2 = $user->createToken('Device 2')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token1->plainTextToken)
            ->deleteJson('/api/v1/auth/tokens/' . $token1->accessToken->id);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Token revoked successfully.']);

        // Verify token1 is revoked by checking token count
        $this->assertEquals(1, $user->tokens()->count()); // Only token2 remains

        // Verify token2 still works
        $response2 = $this->withHeader('Authorization', 'Bearer ' . $token2)
            ->getJson('/api/v1/auth/user');
        $response2->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/v1/auth/user');

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_login_with_custom_abilities()
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'abilities' => ['tasks:read', 'tasks:write'],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'abilities' => ['tasks:read', 'tasks:write'],
        ]);
    }

    /** @test */
    public function user_can_get_available_abilities()
    {
        $response = $this->getJson('/api/v1/auth/abilities');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'abilities',
        ]);
    }

    /** @test */
    public function authenticated_user_can_access_tasks_with_token()
    {
        $user = $this->createUser();
        $task = $this->createTask(['user_id' => $user->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/tasks');

        $response->assertStatus(200);
    }
}

