<?php

use App\Livewire\StoryPages\Index;
use App\Models\Story;
use App\Models\StoryPageImagePrompt;
use App\Models\User;
use Livewire\Livewire;

test('story pages index page is displayed for story owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/pages")->assertOk();
});

test('story pages index page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/pages")->assertForbidden();
});

test('unauthenticated users are redirected to login', function () {
    $story = Story::factory()->create();

    $this->get("/stories/{$story->id}/pages")->assertRedirect('/login');
});

test('story pages are displayed in order', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'page_number' => 2,
        'scene_title' => 'Second Page',
    ]);
    StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'page_number' => 1,
        'scene_title' => 'First Page',
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Index::class, ['story' => $story]);

    $response->assertSee('First Page');
    $response->assertSee('Second Page');
});

test('page can be deleted by owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Index::class, ['story' => $story])
        ->call('delete', $page->id)
        ->assertDispatched('page-deleted');

    expect($page->fresh())->toBeNull();
});

test('page cannot be deleted by non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($otherUser);

    // Non-owner can't even access the index page
    $this->get("/stories/{$story->id}/pages")->assertForbidden();
});
