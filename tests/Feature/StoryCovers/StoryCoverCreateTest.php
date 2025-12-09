<?php

use App\Livewire\StoryCovers\Create;
use App\Models\Story;
use App\Models\StoryCoverImagePrompt;
use App\Models\User;
use Livewire\Livewire;

test('create cover prompt page is displayed for story owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/covers/create")->assertOk();
});

test('create cover prompt page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/covers/create")->assertForbidden();
});

test('cover prompt can be created with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('type', 'front')
        ->set('image_prompt', 'A magical book cover with a child')
        ->call('save')
        ->assertRedirect(route('story-covers.index', $story));

    expect(StoryCoverImagePrompt::where('type', 'front')->exists())->toBeTrue();
});

test('created cover prompt belongs to authenticated user and story', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('type', 'back')
        ->set('image_prompt', 'A beautiful back cover')
        ->call('save');

    $cover = StoryCoverImagePrompt::where('type', 'back')->first();

    expect($cover->user_id)->toEqual($user->id);
    expect($cover->story_id)->toEqual($story->id);
});

test('cover prompt creation validates type', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('type', 'invalid')
        ->set('image_prompt', 'Some prompt')
        ->call('save')
        ->assertHasErrors(['type']);
});

test('cover prompt creation requires image_prompt', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('type', 'front')
        ->set('image_prompt', '')
        ->call('save')
        ->assertHasErrors(['image_prompt']);
});

test('type defaults to front when no covers exist', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $component = Livewire::test(Create::class, ['story' => $story]);

    expect($component->get('type'))->toEqual('front');
});

test('type defaults to back when front cover exists', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'type' => 'front',
    ]);

    $this->actingAs($user);

    $component = Livewire::test(Create::class, ['story' => $story]);

    expect($component->get('type'))->toEqual('back');
});
