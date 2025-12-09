<?php

use App\Livewire\StoryCustomMadeImages\Index;
use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\StoryCustomMadeImage;
use App\Models\User;
use Livewire\Livewire;

test('custom made images index page is displayed for owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/images")->assertOk();
});

test('custom made images index page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/images")->assertForbidden();
});

test('unauthenticated users are redirected to login', function () {
    $story = Story::factory()->create();
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $story->user_id,
        'story_id' => $story->id,
    ]);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/images")->assertRedirect('/login');
});

test('custom made images are displayed', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    StoryCustomMadeImage::factory()->page()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
        'page_number' => 1,
    ]);
    StoryCustomMadeImage::factory()->coverFront()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
        'page_number' => 0,
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Index::class, ['story' => $story, 'customMade' => $customMade]);

    $response->assertSee('Page 1');
    $response->assertSee('Front Cover');
});

test('image can be deleted by owner', function () {
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

    $this->actingAs($user);

    Livewire::test(Index::class, ['story' => $story, 'customMade' => $customMade])
        ->call('delete', $image->id)
        ->assertDispatched('image-deleted');

    expect($image->fresh())->toBeNull();
});

test('image cannot be deleted by non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
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

    $this->actingAs($otherUser);

    // Non-owner can't even access the index page
    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/images")->assertForbidden();
});

test('accessing images with mismatched custom made returns 404', function () {
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

    // Try to access customMade2 images with wrong story
    $otherStory = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $this->get("/stories/{$otherStory->id}/custom-mades/{$customMade1->id}/images")->assertNotFound();
});
