<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_is_blocked(): void
    {
        $response = $this->getJson('/api/projects');
        $response->assertStatus(401);
    }

    public function test_user_can_login_and_get_profile(): void
    {
        $user = User::factory()->create([
            'email' => 'test@accountant.io',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@accountant.io',
            'password' => 'password',
        ]);

        $loginResponse->assertStatus(200)->assertJsonStructure(['token', 'user']);
        $token = $loginResponse->json('token');

        $userResponse = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/auth/user');
        
        $userResponse->assertStatus(200)->assertJsonPath('user.email', 'test@accountant.io');
    }
}
