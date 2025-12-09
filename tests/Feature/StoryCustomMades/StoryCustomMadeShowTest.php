<?php

use App\Livewire\StoryCustomMades\Show;
use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\User;
use Livewire\Livewire;

test('show custom made page is displayed for owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}")->assertOk();
});

test('show custom made page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}")->assertForbidden();
});

test('unauthenticated users are redirected to login', function () {
    $story = Story::factory()->create();
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $story->user_id,
        'story_id' => $story->id,
    ]);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}")->assertRedirect('/login');
});

test('custom made details are displayed', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'child_name' => 'Alice Wonderland',
        'child_gender' => 'female',
        'child_age' => 7,
        'status' => 'pending',
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Show::class, ['story' => $story, 'customMade' => $customMade]);

    $response->assertSee('Alice Wonderland');
    $response->assertSee('Female');
    $response->assertSee('7');
    $response->assertSee('Pending');
});

test('custom made from different story cannot be viewed', function () {
    $user = User::factory()->create();
    $story1 = Story::factory()->create(['user_id' => $user->id]);
    $story2 = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story2->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story1->id}/custom-mades/{$customMade->id}")->assertNotFound();
});

test('completed custom made shows pdf download button', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->completed()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'child_name' => 'Test Child',
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Show::class, ['story' => $story, 'customMade' => $customMade]);

    $response->assertSee('Download PDF');
});
