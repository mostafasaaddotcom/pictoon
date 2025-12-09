<?php

use App\Livewire\StoryPages\Edit;
use App\Models\Story;
use App\Models\StoryPageImagePrompt;
use App\Models\User;
use Livewire\Livewire;

test('edit page prompt page is displayed for owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/pages/{$page->id}/edit")->assertOk();
});

test('edit page prompt page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/pages/{$page->id}/edit")->assertForbidden();
});

test('page prompt can be updated with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'pagePrompt' => $page])
        ->set('page_number', 5)
        ->set('scene_title', 'Updated Title')
        ->set('image_prompt', 'Updated prompt')
        ->set('story_text', 'Updated story text')
        ->set('emotions', 'happy, joyful')
        ->set('art_style', 'anime')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('page-updated');

    $page->refresh();

    expect($page->page_number)->toEqual(5);
    expect($page->scene_title)->toEqual('Updated Title');
    expect($page->image_prompt)->toEqual('Updated prompt');
    expect($page->story_text)->toEqual('Updated story text');
    expect($page->emotions)->toEqual('happy, joyful');
    expect($page->art_style)->toEqual('anime');
});

test('page prompt update validates required fields', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'pagePrompt' => $page])
        ->set('scene_title', '')
        ->call('save')
        ->assertHasErrors(['scene_title']);
});

test('page prompt update validates page_number minimum', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'pagePrompt' => $page])
        ->set('page_number', 0)
        ->call('save')
        ->assertHasErrors(['page_number']);
});

test('emotions can be cleared', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $page = StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'emotions' => 'some emotions',
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'pagePrompt' => $page])
        ->set('emotions', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($page->fresh()->emotions)->toBeNull();
});
