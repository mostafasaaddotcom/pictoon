<?php

use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('unauthenticated request returns 401', function () {
    $this->putJson('/api/v1/story-custom-mades/1', [])
        ->assertUnauthorized();
});

test('update returns 404 for non-existent custom made', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->putJson('/api/v1/story-custom-mades/99999', [
        'status' => 'completed',
    ]);

    $response->assertNotFound();
});

test('update custom made with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'status' => 'pending',
        'pdf_final_url' => null,
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/story-custom-mades/{$customMade->id}", [
        'status' => 'completed',
        'pdf_final_url' => 'https://example.com/story.pdf',
    ]);

    $response->assertOk()
        ->assertJsonPath('message', 'Story custom made updated successfully.')
        ->assertJsonPath('data.status', 'completed')
        ->assertJsonPath('data.pdf_final_url', 'https://example.com/story.pdf');

    expect($customMade->fresh()->status)->toBe('completed');
    expect($customMade->fresh()->pdf_final_url)->toBe('https://example.com/story.pdf');
});

test('update with PATCH works', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'status' => 'pending',
    ]);

    Sanctum::actingAs($user);

    $response = $this->patchJson("/api/v1/story-custom-mades/{$customMade->id}", [
        'status' => 'processing',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'processing');
});

test('update returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/story-custom-mades/{$customMade->id}", [
        'status' => 'completed',
    ]);

    $response->assertForbidden();
});

test('update validates status values', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/story-custom-mades/{$customMade->id}", [
        'status' => 'invalid_status',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

test('update validates pdf_final_url is a valid url', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/story-custom-mades/{$customMade->id}", [
        'pdf_final_url' => 'not-a-valid-url',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['pdf_final_url']);
});

test('update allows nullable pdf_final_url', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'pdf_final_url' => 'https://example.com/old.pdf',
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/story-custom-mades/{$customMade->id}", [
        'pdf_final_url' => null,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.pdf_final_url', null);
});

test('update allows partial update with only status', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'status' => 'pending',
        'pdf_final_url' => 'https://example.com/existing.pdf',
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/story-custom-mades/{$customMade->id}", [
        'status' => 'completed',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'completed')
        ->assertJsonPath('data.pdf_final_url', 'https://example.com/existing.pdf');
});

test('update allows partial update with only pdf_final_url', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'status' => 'pending',
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/story-custom-mades/{$customMade->id}", [
        'pdf_final_url' => 'https://example.com/new.pdf',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.pdf_final_url', 'https://example.com/new.pdf')
        ->assertJsonPath('data.status', 'pending');
});
