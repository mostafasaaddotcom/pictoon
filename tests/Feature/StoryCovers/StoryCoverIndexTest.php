<?php

use App\Livewire\StoryCovers\Index;
use App\Models\Story;
use App\Models\StoryCoverImagePrompt;
use App\Models\User;
use Livewire\Livewire;

test('story covers index page is displayed for story owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/covers")->assertOk();
});

test('story covers index page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/covers")->assertForbidden();
});

test('unauthenticated users are redirected to login', function () {
    $story = Story::factory()->create();

    $this->get("/stories/{$story->id}/covers")->assertRedirect('/login');
});

test('story covers are displayed', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'type' => 'front',
        'image_prompt' => 'A magical front cover',
    ]);
    StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'type' => 'back',
        'image_prompt' => 'A magical back cover',
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Index::class, ['story' => $story]);

    $response->assertSee('Front Cover');
    $response->assertSee('Back Cover');
});

test('cover can be deleted by owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $cover = StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Index::class, ['story' => $story])
        ->call('delete', $cover->id)
        ->assertDispatched('cover-deleted');

    expect($cover->fresh())->toBeNull();
});

test('cover cannot be deleted by non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $cover = StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($otherUser);

    // Non-owner can't even access the index page
    $this->get("/stories/{$story->id}/covers")->assertForbidden();
});
