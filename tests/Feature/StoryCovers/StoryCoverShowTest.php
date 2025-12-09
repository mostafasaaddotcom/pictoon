<?php

use App\Livewire\StoryCovers\Show;
use App\Models\Story;
use App\Models\StoryCoverImagePrompt;
use App\Models\User;
use Livewire\Livewire;

test('show cover prompt page is displayed for owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $cover = StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/covers/{$cover->id}")->assertOk();
});

test('show cover prompt page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $cover = StoryCoverImagePrompt::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/covers/{$cover->id}")->assertForbidden();
});

test('unauthenticated users are redirected to login', function () {
    $story = Story::factory()->create();
    $cover = StoryCoverImagePrompt::factory()->create([
        'user_id' => $story->user_id,
        'story_id' => $story->id,
    ]);

    $this->get("/stories/{$story->id}/covers/{$cover->id}")->assertRedirect('/login');
});

test('cover prompt details are displayed', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $cover = StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'type' => 'front',
        'image_prompt' => 'A magical book cover with glowing elements',
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Show::class, ['story' => $story, 'coverPrompt' => $cover]);

    $response->assertSee('Front Cover');
    $response->assertSee('A magical book cover with glowing elements');
});

test('cover from different story cannot be viewed', function () {
    $user = User::factory()->create();
    $story1 = Story::factory()->create(['user_id' => $user->id]);
    $story2 = Story::factory()->create(['user_id' => $user->id]);
    $cover = StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story2->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story1->id}/covers/{$cover->id}")->assertNotFound();
});
