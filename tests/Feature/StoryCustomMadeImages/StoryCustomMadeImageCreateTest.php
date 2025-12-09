<?php

use App\Livewire\StoryCustomMadeImages\Create;
use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\StoryCustomMadeImage;
use App\Models\User;
use Livewire\Livewire;

test('create image page is displayed for owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/images/create")->assertOk();
});

test('create image page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/{$customMade->id}/images/create")->assertForbidden();
});

test('image can be created with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story, 'customMade' => $customMade])
        ->set('page_number', 1)
        ->set('image_type', 'page')
        ->call('save')
        ->assertRedirect(route('custom-made-images.index', [$story, $customMade]));

    expect(StoryCustomMadeImage::where('page_number', 1)->where('image_type', 'page')->exists())->toBeTrue();
});

test('created image belongs to authenticated user, story, and custom made', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story, 'customMade' => $customMade])
        ->set('page_number', 5)
        ->set('image_type', 'page')
        ->set('reference_number', 10)
        ->call('save');

    $image = StoryCustomMadeImage::where('page_number', 5)->first();

    expect($image->user_id)->toEqual($user->id);
    expect($image->story_id)->toEqual($story->id);
    expect($image->story_custom_made_id)->toEqual($customMade->id);
    expect($image->status)->toEqual('pending');
    expect($image->reference_number)->toEqual(10);
});

test('image creation validates page_number', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story, 'customMade' => $customMade])
        ->set('page_number', -1)
        ->set('image_type', 'page')
        ->call('save')
        ->assertHasErrors(['page_number']);
});

test('image creation validates image_type', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story, 'customMade' => $customMade])
        ->set('page_number', 1)
        ->set('image_type', 'invalid_type')
        ->call('save')
        ->assertHasErrors(['image_type']);
});

test('image creation validates image_url format', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story, 'customMade' => $customMade])
        ->set('page_number', 1)
        ->set('image_type', 'page')
        ->set('image_url', 'not-a-valid-url')
        ->call('save')
        ->assertHasErrors(['image_url']);
});

test('image can be created with valid image_url', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story, 'customMade' => $customMade])
        ->set('page_number', 1)
        ->set('image_type', 'page')
        ->set('image_url', 'https://example.com/image.jpg')
        ->call('save')
        ->assertHasNoErrors();

    $image = StoryCustomMadeImage::where('page_number', 1)->first();
    expect($image->image_url)->toEqual('https://example.com/image.jpg');
});

test('image can be created with cover_front type', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story, 'customMade' => $customMade])
        ->set('page_number', 0)
        ->set('image_type', 'cover_front')
        ->call('save')
        ->assertHasNoErrors();

    expect(StoryCustomMadeImage::where('image_type', 'cover_front')->exists())->toBeTrue();
});

test('image can be created with cover_back type', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story, 'customMade' => $customMade])
        ->set('page_number', 99)
        ->set('image_type', 'cover_back')
        ->call('save')
        ->assertHasNoErrors();

    expect(StoryCustomMadeImage::where('image_type', 'cover_back')->exists())->toBeTrue();
});

test('create page with mismatched custom made returns 404', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $otherStory = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    $this->get("/stories/{$otherStory->id}/custom-mades/{$customMade->id}/images/create")->assertNotFound();
});
