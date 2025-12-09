<?php

use App\Livewire\StoryCustomMades\Edit;
use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\User;
use Livewire\Livewire;

test('edit custom made page is displayed for owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/edit")->assertOk();
});

test('edit custom made page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/edit")->assertForbidden();
});

test('custom made can be updated with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'child_name' => 'Alice',
        'child_gender' => 'female',
        'child_age' => 7,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'customMade' => $customMade])
        ->set('child_name', 'Updated Alice')
        ->set('child_gender', 'female')
        ->set('child_age', 8)
        ->set('status', 'processing')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('custom-made-updated');

    $customMade->refresh();

    expect($customMade->child_name)->toEqual('Updated Alice');
    expect($customMade->child_age)->toEqual(8);
    expect($customMade->status)->toEqual('processing');
});

test('custom made update validates child_name', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'customMade' => $customMade])
        ->set('child_name', '')
        ->call('save')
        ->assertHasErrors(['child_name']);
});

test('custom made update validates child_gender', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'customMade' => $customMade])
        ->set('child_gender', 'invalid')
        ->call('save')
        ->assertHasErrors(['child_gender']);
});

test('custom made update validates status', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'customMade' => $customMade])
        ->set('status', 'invalid_status')
        ->call('save')
        ->assertHasErrors(['status']);
});

test('custom made from different story cannot be edited', function () {
    $user = User::factory()->create();
    $story1 = Story::factory()->create(['user_id' => $user->id]);
    $story2 = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story2->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story1->id}/custom-mades/{$customMade->id}/edit")->assertNotFound();
});
