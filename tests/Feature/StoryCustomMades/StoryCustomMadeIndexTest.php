<?php

use App\Livewire\StoryCustomMades\Index;
use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\User;
use Livewire\Livewire;

test('story custom mades index page is displayed for story owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades")->assertOk();
});

test('story custom mades index page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades")->assertForbidden();
});

test('unauthenticated users are redirected to login', function () {
    $story = Story::factory()->create();

    $this->get("/stories/{$story->id}/custom-mades")->assertRedirect('/login');
});

test('story custom mades are displayed', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'child_name' => 'Alice',
        'child_age' => 7,
    ]);
    StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'child_name' => 'Bob',
        'child_age' => 5,
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Index::class, ['story' => $story]);

    $response->assertSee('Alice');
    $response->assertSee('Bob');
});

test('custom made can be deleted by owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Index::class, ['story' => $story])
        ->call('delete', $customMade->id)
        ->assertDispatched('custom-made-deleted');

    expect($customMade->fresh())->toBeNull();
});

test('custom made cannot be deleted by non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($otherUser);

    // Non-owner can't even access the index page
    $this->get("/stories/{$story->id}/custom-mades")->assertForbidden();
});
