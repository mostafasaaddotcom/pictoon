<?php

use App\Livewire\StoryPages\Create;
use App\Models\Story;
use App\Models\StoryPageImagePrompt;
use App\Models\User;
use Livewire\Livewire;

test('create page prompt page is displayed for story owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/pages/create")->assertOk();
});

test('create page prompt page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/pages/create")->assertForbidden();
});

test('page prompt can be created with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('page_number', 1)
        ->set('scene_title', 'The Beginning')
        ->set('image_prompt', 'A child standing in a magical forest')
        ->set('story_text', 'Once upon a time, there was a brave child.')
        ->set('emotions', 'curious, excited')
        ->set('art_style', 'watercolor')
        ->call('save')
        ->assertRedirect(route('story-pages.index', $story));

    expect(StoryPageImagePrompt::where('scene_title', 'The Beginning')->exists())->toBeTrue();
});

test('created page prompt belongs to authenticated user and story', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('page_number', 1)
        ->set('scene_title', 'My Scene')
        ->set('image_prompt', 'A beautiful scene')
        ->set('story_text', 'Story text here')
        ->set('art_style', 'cartoon')
        ->call('save');

    $page = StoryPageImagePrompt::where('scene_title', 'My Scene')->first();

    expect($page->user_id)->toEqual($user->id);
    expect($page->story_id)->toEqual($story->id);
});

test('page prompt creation validates page_number minimum', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('page_number', 0)
        ->set('scene_title', 'Title')
        ->set('image_prompt', 'Prompt')
        ->set('story_text', 'Text')
        ->set('art_style', 'cartoon')
        ->call('save')
        ->assertHasErrors(['page_number']);
});

test('page prompt creation requires scene_title', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('page_number', 1)
        ->set('scene_title', '')
        ->set('image_prompt', 'Prompt')
        ->set('story_text', 'Text')
        ->set('art_style', 'cartoon')
        ->call('save')
        ->assertHasErrors(['scene_title']);
});

test('page prompt creation requires image_prompt', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('page_number', 1)
        ->set('scene_title', 'Title')
        ->set('image_prompt', '')
        ->set('story_text', 'Text')
        ->set('art_style', 'cartoon')
        ->call('save')
        ->assertHasErrors(['image_prompt']);
});

test('page prompt creation requires story_text', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('page_number', 1)
        ->set('scene_title', 'Title')
        ->set('image_prompt', 'Prompt')
        ->set('story_text', '')
        ->set('art_style', 'cartoon')
        ->call('save')
        ->assertHasErrors(['story_text']);
});

test('page prompt creation requires art_style', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('page_number', 1)
        ->set('scene_title', 'Title')
        ->set('image_prompt', 'Prompt')
        ->set('story_text', 'Text')
        ->set('art_style', '')
        ->call('save')
        ->assertHasErrors(['art_style']);
});

test('emotions field is optional', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('page_number', 1)
        ->set('scene_title', 'Title')
        ->set('image_prompt', 'Prompt')
        ->set('story_text', 'Text')
        ->set('emotions', '')
        ->set('art_style', 'cartoon')
        ->call('save')
        ->assertHasNoErrors();

    expect(StoryPageImagePrompt::where('scene_title', 'Title')->exists())->toBeTrue();
});

test('page number auto-increments based on existing pages', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'page_number' => 3,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(Create::class, ['story' => $story]);

    expect($component->get('page_number'))->toEqual(4);
});
