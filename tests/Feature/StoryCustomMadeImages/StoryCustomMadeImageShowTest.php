<?php

use App\Livewire\StoryCustomMadeImages\Show;
use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\StoryCustomMadeImage;
use App\Models\User;
use Livewire\Livewire;

test('show image page is displayed for owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/images/{$image->id}")->assertOk();
});

test('show image page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/images/{$image->id}")->assertForbidden();
});

test('unauthenticated users are redirected to login', function () {
    $story = Story::factory()->create();
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $story->user_id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->create([
        'user_id' => $story->user_id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/images/{$image->id}")->assertRedirect('/login');
});

test('image details are displayed', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'child_name' => 'Alice Wonderland',
    ]);
    $image = StoryCustomMadeImage::factory()->pending()->page()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
        'page_number' => 5,
        'reference_number' => 42,
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Show::class, ['story' => $story, 'customMade' => $customMade, 'image' => $image]);

    $response->assertSee('Alice Wonderland');
    $response->assertSee('Page 5');
    $response->assertSee('Pending');
    $response->assertSee('42');
});

test('image from different custom made cannot be viewed', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade1 = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $customMade2 = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade2->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade1->id}/images/{$image->id}")->assertNotFound();
});

test('image with mismatched story returns 404', function () {
    $user = User::factory()->create();
    $story1 = Story::factory()->create(['user_id' => $user->id]);
    $story2 = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story1->id,
    ]);
    $image = StoryCustomMadeImage::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story1->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story2->id}/custom-mades/{$customMade->id}/images/{$image->id}")->assertNotFound();
});

test('front cover type is displayed correctly', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->coverFront()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
        'page_number' => 0,
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Show::class, ['story' => $story, 'customMade' => $customMade, 'image' => $image]);

    $response->assertSee('Front Cover');
});

test('back cover type is displayed correctly', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->coverBack()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
        'page_number' => 99,
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Show::class, ['story' => $story, 'customMade' => $customMade, 'image' => $image]);

    $response->assertSee('Back Cover');
});

test('completed status is displayed correctly', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->completed()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Show::class, ['story' => $story, 'customMade' => $customMade, 'image' => $image]);

    $response->assertSee('Completed');
});

test('failed status is displayed correctly', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->failed()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Show::class, ['story' => $story, 'customMade' => $customMade, 'image' => $image]);

    $response->assertSee('Failed');
});
