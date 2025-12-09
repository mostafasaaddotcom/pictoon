<?php

use App\Livewire\StoryPages\Show;
use App\Models\Story;
use App\Models\StoryPageImagePrompt;
use App\Models\User;
use Livewire\Livewire;

test('show page prompt page is displayed for owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/pages/{$page->id}")->assertOk();
});

test('show page prompt page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/pages/{$page->id}")->assertForbidden();
});

test('unauthenticated users are redirected to login', function () {
    $story = Story::factory()->create();
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $story->user_id,
        'story_id' => $story->id,
    ]);

    $this->get("/stories/{$story->id}/pages/{$page->id}")->assertRedirect('/login');
});

test('page prompt details are displayed', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'page_number' => 3,
        'scene_title' => 'The Adventure Begins',
        'image_prompt' => 'A magical forest with glowing mushrooms',
        'story_text' => 'Once upon a time in a magical land...',
        'emotions' => 'wonder, excitement',
        'art_style' => 'watercolor',
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Show::class, ['story' => $story, 'pagePrompt' => $page]);

    $response->assertSee('The Adventure Begins');
    $response->assertSee('A magical forest with glowing mushrooms');
    $response->assertSee('Once upon a time in a magical land...');
    $response->assertSee('wonder, excitement');
    $response->assertSee('watercolor');
});

test('page from different story cannot be viewed', function () {
    $user = User::factory()->create();
    $story1 = Story::factory()->create(['user_id' => $user->id]);
    $story2 = Story::factory()->create(['user_id' => $user->id]);
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story2->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story1->id}/pages/{$page->id}")->assertNotFound();
});
