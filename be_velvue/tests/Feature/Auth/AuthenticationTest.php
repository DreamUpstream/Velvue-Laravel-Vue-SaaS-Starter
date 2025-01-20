<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->has('success')
        );
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->hasAll(['success', 'message', 'errors'])
        );
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create([
            'ulid' => Str::ulid()->toBase32(),
        ]);

        $response = $this->actingAs($user)->postJson('/api/logout');

        $response->assertJson(['success' => true], true);
    }
}
