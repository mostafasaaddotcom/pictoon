<?php

use App\Livewire\Stories\Edit;
use App\Models\Story;
use App\Models\User;
use Livewire\Livewire;

test('edit story page is displayed for owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/edit")->assertOk();
});

test('edit story page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}/edit")->assertForbidden();
});

test('story can be updated with valid data', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story])
        ->set('idea', 'Updated story idea')
        ->set('description', 'Updated description')
        ->set('moral_lesson', 'Updated lesson')
        ->set('language', 'fr')
        ->set('pages_count', 20)
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('story-updated');

    $story->refresh();

    expect($story->idea)->toEqual('Updated story idea');
    expect($story->description)->toEqual('Updated description');
    expect($story->moral_lesson)->toEqual('Updated lesson');
    expect($story->language)->toEqual('fr');
    expect($story->pages_count)->toEqual(20);
});

test('story update validates required fields', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story])
        ->set('idea', '')
        ->call('save')
        ->assertHasErrors(['idea']);
});

test('story update validates language', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story])
        ->set('language', 'invalid')
        ->call('save')
        ->assertHasErrors(['language']);
});

test('story update validates pages_count range', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story])
        ->set('pages_count', 100)
        ->call('save')
        ->assertHasErrors(['pages_count']);
});

test('story active status can be updated', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id, 'is_active' => true]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story])
        ->set('is_active', false)
        ->call('save')
        ->assertHasNoErrors();

    expect($story->fresh()->is_active)->toBeFalse();
});

test('story images can be updated', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id, 'images' => []]);

    $this->actingAs($user);

    Livewire::test(Edit::class, ['story' => $story])
        ->set('images_input', 'https://example.com/new-image.jpg')
        ->call('save')
        ->assertHasNoErrors();

    expect($story->fresh()->images)->toContain('https://example.com/new-image.jpg');
});
