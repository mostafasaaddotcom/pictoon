<?php

use App\Livewire\StoryCovers\Edit;
use App\Models\Story;
use App\Models\StoryCoverImagePrompt;
use App\Models\User;
use Livewire\Livewire;

test('edit cover prompt page is displayed for owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $cover = StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/covers/{$cover->id}/edit")->assertOk();
});

test('edit cover prompt page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $cover = StoryCoverImagePrompt::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/covers/{$cover->id}/edit")->assertForbidden();
});

test('cover prompt can be updated with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $cover = StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'type' => 'front',
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'coverPrompt' => $cover])
        ->set('type', 'back')
        ->set('image_prompt', 'Updated prompt')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('cover-updated');

    $cover->refresh();

    expect($cover->type)->toEqual('back');
    expect($cover->image_prompt)->toEqual('Updated prompt');
});

test('cover prompt update validates type', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $cover = StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'coverPrompt' => $cover])
        ->set('type', 'invalid')
        ->call('save')
        ->assertHasErrors(['type']);
});

test('cover prompt update requires image_prompt', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $cover = StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'coverPrompt' => $cover])
        ->set('image_prompt', '')
        ->call('save')
        ->assertHasErrors(['image_prompt']);
});
