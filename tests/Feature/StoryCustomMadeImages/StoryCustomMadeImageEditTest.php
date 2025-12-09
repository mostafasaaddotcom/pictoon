<?php

use App\Livewire\StoryCustomMadeImages\Edit;
use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\StoryCustomMadeImage;
use App\Models\User;
use Livewire\Livewire;

test('edit image page is displayed for owner', function () {
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

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/images/{$image->id}/edit")->assertOk();
});

test('edit image page returns 403 for non-owner', function () {
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

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/images/{$image->id}/edit")->assertForbidden();
});

test('image can be updated with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);
    $image = StoryCustomMadeImage::factory()->pending()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'story_custom_made_id' => $customMade->id,
        'page_number' => 1,
        'image_type' => 'page',
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'customMade' => $customMade, 'image' => $image])
        ->set('page_number', 5)
        ->set('image_type', 'cover_front')
        ->set('status', 'completed')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('image-updated');

    $image->refresh();

    expect($image->page_number)->toEqual(5);
    expect($image->image_type)->toEqual('cover_front');
    expect($image->status)->toEqual('completed');
});

test('image update validates page_number', function () {
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

    Livewire::test(Edit::class, ['story' => $story, 'customMade' => $customMade, 'image' => $image])
        ->set('page_number', -1)
        ->call('save')
        ->assertHasErrors(['page_number']);
});

test('image update validates image_type', function () {
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

    Livewire::test(Edit::class, ['story' => $story, 'customMade' => $customMade, 'image' => $image])
        ->set('image_type', 'invalid_type')
        ->call('save')
        ->assertHasErrors(['image_type']);
});

test('image update validates status', function () {
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

    Livewire::test(Edit::class, ['story' => $story, 'customMade' => $customMade, 'image' => $image])
        ->set('status', 'invalid_status')
        ->call('save')
        ->assertHasErrors(['status']);
});

test('image update validates image_url format', function () {
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

    Livewire::test(Edit::class, ['story' => $story, 'customMade' => $customMade, 'image' => $image])
        ->set('image_url', 'not-a-valid-url')
        ->call('save')
        ->assertHasErrors(['image_url']);
});

test('image from different custom made cannot be edited', function () {
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

    $this->get("/stories/{$story->id}/custom-mades/{$customMade1->id}/images/{$image->id}/edit")->assertNotFound();
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

    $this->get("/stories/{$story2->id}/custom-mades/{$customMade->id}/images/{$image->id}/edit")->assertNotFound();
});

test('image can update reference_number', function () {
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
        'reference_number' => null,
    ]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story, 'customMade' => $customMade, 'image' => $image])
        ->set('reference_number', 42)
        ->call('save')
        ->assertHasNoErrors();

    $image->refresh();
    expect($image->reference_number)->toEqual(42);
});
