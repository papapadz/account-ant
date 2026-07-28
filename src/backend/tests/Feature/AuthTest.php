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
}
