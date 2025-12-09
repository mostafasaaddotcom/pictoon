<?php

use App\Livewire\StoryCustomMades\Create;
use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\User;
use Livewire\Livewire;

test('create custom made page is displayed for story owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/create")->assertOk();
});

test('create custom made page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/custom-mades/create")->assertForbidden();
});

test('custom made can be created with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('child_name', 'Alice')
        ->set('child_gender', 'female')
        ->set('child_age', 7)
        ->call('save')
        ->assertRedirect(route('story-custom-mades.index', $story));

    expect(StoryCustomMade::where('child_name', 'Alice')->exists())->toBeTrue();
});

test('created custom made belongs to authenticated user and story', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('child_name', 'Bob')
        ->set('child_gender', 'male')
        ->set('child_age', 5)
        ->call('save');

    $customMade = StoryCustomMade::where('child_name', 'Bob')->first();

    expect($customMade->user_id)->toEqual($user->id);
    expect($customMade->story_id)->toEqual($story->id);
    expect($customMade->status)->toEqual('pending');
});

test('custom made creation validates child_name', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('child_name', '')
        ->set('child_gender', 'male')
        ->set('child_age', 5)
        ->call('save')
        ->assertHasErrors(['child_name']);
});

test('custom made creation validates child_gender', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('child_name', 'Test')
        ->set('child_gender', 'invalid')
        ->set('child_age', 5)
        ->call('save')
        ->assertHasErrors(['child_gender']);
});

test('custom made creation validates child_age', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('child_name', 'Test')
        ->set('child_gender', 'male')
        ->set('child_age', 0)
        ->call('save')
        ->assertHasErrors(['child_age']);
});

test('custom made creation validates child_image_url format', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('child_name', 'Test')
        ->set('child_gender', 'male')
        ->set('child_age', 5)
        ->set('child_image_url', 'not-a-valid-url')
        ->call('save')
        ->assertHasErrors(['child_image_url']);
});

test('custom made can be created with valid image url', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Create::class, ['story' => $story])
        ->set('child_name', 'Test')
        ->set('child_gender', 'female')
        ->set('child_age', 6)
        ->set('child_image_url', 'https://example.com/image.jpg')
        ->call('save')
        ->assertHasNoErrors();

    $customMade = StoryCustomMade::where('child_name', 'Test')->first();
    expect($customMade->child_image_url)->toEqual('https://example.com/image.jpg');
});
