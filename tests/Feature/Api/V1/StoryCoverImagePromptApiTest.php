<?php

use App\Models\Story;
use App\Models\StoryCoverImagePrompt;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('unauthenticated request returns 401', function () {
    $this->getJson('/api/v1/story-cover-image-prompts')
        ->assertUnauthorized();
});

test('index requires story_id parameter', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/story-cover-image-prompts');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['story_id']);
});

test('index validates story_id exists', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/story-cover-image-prompts?story_id=99999');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['story_id']);
});

test('index returns cover prompts for user story', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    StoryCoverImagePrompt::factory()->front()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    StoryCoverImagePrompt::factory()->back()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/story-cover-image-prompts?story_id={$story->id}");

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'story_id',
                    'type',
                    'image_prompt',
                    'meta_data',
                ],
            ],
        ]);
});

test('index returns empty array when no prompts exist', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/story-cover-image-prompts?story_id={$story->id}");

    $response->assertOk()
        ->assertJsonCount(0, 'data');
});

test('index returns 403 for non-owner story', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/story-cover-image-prompts?story_id={$story->id}");

    $response->assertForbidden();
});

test('index does not return other users prompts', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    // Create prompts for both users on the same story
    StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    StoryCoverImagePrompt::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/story-cover-image-prompts?story_id={$story->id}");

    $response->assertOk()
        ->assertJsonCount(1, 'data');
});
