<?php

use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\StoryCustomMadeImage;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('unauthenticated requests return 401', function () {
    $this->postJson('/api/v1/story-custom-made-images', [])
        ->assertUnauthorized();
});

test('index returns all images for authenticated user', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    StoryCustomMadeImage::factory()->count(3)->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/story-custom-made-images');

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

test('index filters by status', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    StoryCustomMadeImage::factory()->pending()->count(2)->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    StoryCustomMadeImage::factory()->completed()->count(3)->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/story-custom-made-images?status=completed');

    $response->assertOk()
        ->assertJsonCount(3, 'data');

    foreach ($response->json('data') as $image) {
        expect($image['status'])->toBe('completed');
    }
});

test('index filters by story_custom_made_id', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade1 = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $customMade2 = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    StoryCustomMadeImage::factory()->count(2)->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade1->id,
    ]);

    StoryCustomMadeImage::factory()->count(4)->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade2->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/story-custom-made-images?story_custom_made_id={$customMade1->id}");

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

test('index filters by both status and story_custom_made_id', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    StoryCustomMadeImage::factory()->pending()->count(2)->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    StoryCustomMadeImage::factory()->completed()->count(1)->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/story-custom-made-images?status=pending&story_custom_made_id={$customMade->id}");

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

test('index validates status values', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/story-custom-made-images?status=invalid');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

test('index validates story_custom_made_id exists', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/story-custom-made-images?story_custom_made_id=99999');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['story_custom_made_id']);
});

test('index does not return other users images', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $otherStory = Story::factory()->create(['user_id' => $otherUser->id]);
    $otherCustomMade = StoryCustomMade::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $otherStory->id,
    ]);

    StoryCustomMadeImage::factory()->count(2)->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    StoryCustomMadeImage::factory()->count(5)->create([
        'user_id' => $otherUser->id,
        'story_id' => $otherStory->id,
        'story_custom_made_id' => $otherCustomMade->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/story-custom-made-images');

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

test('create image with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/story-custom-made-images', [
        'story_custom_made_id' => $customMade->id,
        'page_number' => 1,
        'image_type' => 'page',
        'reference_number' => 'ref-10',
        'image_url' => 'https://example.com/image.jpg',
        'status' => 'completed',
    ]);

    $response->assertCreated()
        ->assertJsonPath('message', 'Image created successfully')
        ->assertJsonPath('data.page_number', 1)
        ->assertJsonPath('data.image_type', 'page')
        ->assertJsonPath('data.status', 'completed');

    expect(StoryCustomMadeImage::where('page_number', 1)->exists())->toBeTrue();
});

test('create image returns validation errors', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/story-custom-made-images', [
        'page_number' => -1,
        'image_type' => 'invalid',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['story_custom_made_id', 'page_number', 'image_type']);
});

test('create image requires valid story_custom_made_id', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/story-custom-made-images', [
        'story_custom_made_id' => 99999,
        'page_number' => 1,
        'image_type' => 'page',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['story_custom_made_id']);
});

test('create image returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/story-custom-made-images', [
        'story_custom_made_id' => $customMade->id,
        'page_number' => 1,
        'image_type' => 'page',
    ]);

    $response->assertForbidden();
});

test('update image with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->pending()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
        'page_number' => 1,
        'image_type' => 'page',
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/story-custom-made-images/{$image->id}", [
        'status' => 'completed',
        'image_url' => 'https://example.com/new-image.jpg',
    ]);

    $response->assertOk()
        ->assertJsonPath('message', 'Image updated successfully')
        ->assertJsonPath('data.status', 'completed')
        ->assertJsonPath('data.image_url', 'https://example.com/new-image.jpg');
});

test('partial update with PATCH works', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->pending()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
        'page_number' => 1,
    ]);

    Sanctum::actingAs($user);

    $response = $this->patchJson("/api/v1/story-custom-made-images/{$image->id}", [
        'status' => 'processing',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'processing');
});

test('update image returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/story-custom-made-images/{$image->id}", [
        'status' => 'completed',
    ]);

    $response->assertForbidden();
});

test('update image validates status values', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/story-custom-made-images/{$image->id}", [
        'status' => 'invalid_status',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

test('update image validates image_type values', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/v1/story-custom-made-images/{$image->id}", [
        'image_type' => 'invalid_type',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['image_type']);
});

test('get image by reference number', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
        'reference_number' => 'ref-abc-123',
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/story-custom-made-images/reference/ref-abc-123');

    $response->assertOk()
        ->assertJsonPath('data.id', $image->id)
        ->assertJsonPath('data.reference_number', 'ref-abc-123');
});

test('get image by reference number returns 404 if not found', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/story-custom-made-images/reference/non-existent');

    $response->assertNotFound();
});

test('get image by reference number returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);
    StoryCustomMadeImage::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
        'reference_number' => 'ref-other-user',
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/story-custom-made-images/reference/ref-other-user');

    $response->assertForbidden();
});

test('create image with cover types', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    Sanctum::actingAs($user);

    // Test cover_front
    $response = $this->postJson('/api/v1/story-custom-made-images', [
        'story_custom_made_id' => $customMade->id,
        'page_number' => 0,
        'image_type' => 'cover_front',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.image_type', 'cover_front');

    // Test cover_back
    $response = $this->postJson('/api/v1/story-custom-made-images', [
        'story_custom_made_id' => $customMade->id,
        'page_number' => 99,
        'image_type' => 'cover_back',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.image_type', 'cover_back');
});
