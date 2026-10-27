<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_create_post(): void
    {
        $response = $this->postJson('/api/posts', [
            'title' => 'Test post',
            'description' => 'Test description',
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_post(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => 'Test post',
            'description' => 'Test description',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('posts', [
            'title' => 'Test post',
            'user_id' => $user->id,
        ]);
    }


    public function test_post_cannot_be_created_without_a_title(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => '',
            'description' => 'Test description',
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['title']);
    }

    public function test_regular_user_cannot_create_post(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => 'Test post',
            'description' => 'Test description',
        ]);

        $response->assertForbidden();
    }
}
