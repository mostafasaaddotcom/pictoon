<?php

use App\Models\Story;
use App\Models\StoryPageImagePrompt;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('unauthenticated request returns 401', function () {
    $this->getJson('/api/v1/story-page-image-prompts')
        ->assertUnauthorized();
});

test('index requires story_id parameter', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/story-page-image-prompts');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['story_id']);
});

test('index validates story_id exists', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/story-page-image-prompts?story_id=99999');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['story_id']);
});

test('index returns page prompts for user story', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    StoryPageImagePrompt::factory()->count(3)->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/story-page-image-prompts?story_id={$story->id}");

    $response->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'story_id',
                    'page_number',
                    'scene_title',
                    'image_prompt',
                    'story_text',
                    'emotions',
                    'art_style',
                ],
            ],
        ]);
});

test('index returns empty array when no prompts exist', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/story-page-image-prompts?story_id={$story->id}");

    $response->assertOk()
        ->assertJsonCount(0, 'data');
});

test('index returns 403 for non-owner story', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/story-page-image-prompts?story_id={$story->id}");

    $response->assertForbidden();
});

test('index does not return other users prompts', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    // Create prompts for both users on the same story
    StoryPageImagePrompt::factory()->count(2)->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    StoryPageImagePrompt::factory()->count(3)->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/story-page-image-prompts?story_id={$story->id}");

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

test('store creates page image prompt with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/story-page-image-prompts', [
        'story_id' => $story->id,
        'page_number' => 1,
        'scene_title' => 'The Beginning',
        'image_prompt' => 'A child walking through a magical forest',
        'story_text' => 'Once upon a time, in a land far away...',
        'emotions' => 'wonder, excitement',
        'art_style' => 'watercolor',
    ]);

    $response->assertCreated()
        ->assertJsonPath('message', 'Page image prompt created successfully')
        ->assertJsonPath('data.page_number', 1)
        ->assertJsonPath('data.scene_title', 'The Beginning')
        ->assertJsonPath('data.image_prompt', 'A child walking through a magical forest')
        ->assertJsonPath('data.story_text', 'Once upon a time, in a land far away...')
        ->assertJsonPath('data.emotions', 'wonder, excitement')
        ->assertJsonPath('data.art_style', 'watercolor');

    expect(StoryPageImagePrompt::where('story_id', $story->id)->exists())->toBeTrue();
});

test('store unauthenticated request returns 401', function () {
    $this->postJson('/api/v1/story-page-image-prompts', [])
        ->assertUnauthorized();
});

test('store returns validation errors for missing required fields', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/story-page-image-prompts', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['story_id', 'page_number', 'scene_title', 'image_prompt', 'story_text', 'art_style']);
});

test('store validates story_id exists', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/story-page-image-prompts', [
        'story_id' => 99999,
        'page_number' => 1,
        'scene_title' => 'Test',
        'image_prompt' => 'Test prompt',
        'story_text' => 'Test text',
        'art_style' => 'watercolor',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['story_id']);
});

test('store returns 403 for non-owner story', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/story-page-image-prompts', [
        'story_id' => $story->id,
        'page_number' => 1,
        'scene_title' => 'Test',
        'image_prompt' => 'Test prompt',
        'story_text' => 'Test text',
        'art_style' => 'watercolor',
    ]);

    $response->assertForbidden();
});

test('store allows nullable emotions field', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/story-page-image-prompts', [
        'story_id' => $story->id,
        'page_number' => 1,
        'scene_title' => 'The Beginning',
        'image_prompt' => 'A magical scene',
        'story_text' => 'Once upon a time...',
        'art_style' => 'watercolor',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.emotions', null);
});

test('store validates page_number minimum', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/story-page-image-prompts', [
        'story_id' => $story->id,
        'page_number' => 0,
        'scene_title' => 'Test',
        'image_prompt' => 'Test prompt',
        'story_text' => 'Test text',
        'art_style' => 'watercolor',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['page_number']);
});
