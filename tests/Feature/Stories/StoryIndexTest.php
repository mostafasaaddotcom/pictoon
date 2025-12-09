<?php

use App\Livewire\Stories\Index;
use App\Models\Story;
use App\Models\User;
use Livewire\Livewire;

test('stories index page is displayed for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get('/stories')->assertOk();
});

test('unauthenticated users are redirected to login', function () {
    $this->get('/stories')->assertRedirect('/login');
});

test('users can only see their own stories', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userStory = Story::factory()->create(['user_id' => $user->id, 'idea' => 'My Story']);
    $otherStory = Story::factory()->create(['user_id' => $otherUser->id, 'idea' => 'Other Story']);

    $this->actingAs($user);

    $response = Livewire::test(Index::class);

    $response->assertSee('My Story');
    $response->assertDontSee('Other Story');
});

test('stories can be searched by idea', function () {
    $user = User::factory()->create();

    Story::factory()->create(['user_id' => $user->id, 'idea' => 'Magical Adventure']);
    Story::factory()->create(['user_id' => $user->id, 'idea' => 'Space Journey']);

    $this->actingAs($user);

    $response = Livewire::test(Index::class)
        ->set('search', 'Magical');

    $response->assertSee('Magical Adventure');
    $response->assertDontSee('Space Journey');
});

test('stories can be searched by description', function () {
    $user = User::factory()->create();

    Story::factory()->create([
        'user_id' => $user->id,
        'idea' => 'Story One',
        'description' => 'A tale about dragons',
    ]);
    Story::factory()->create([
        'user_id' => $user->id,
        'idea' => 'Story Two',
        'description' => 'A tale about unicorns',
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Index::class)
        ->set('search', 'dragons');

    $response->assertSee('Story One');
    $response->assertDontSee('Story Two');
});

test('story active status can be toggled', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id, 'is_active' => true]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('toggleActive', $story->id);

    expect($story->fresh()->is_active)->toBeFalse();
});

test('users cannot toggle other users story status', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id, 'is_active' => true]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('toggleActive', $story->id)
        ->assertForbidden();
});

test('story can be deleted by owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('delete', $story->id)
        ->assertDispatched('story-deleted');

    expect($story->fresh())->toBeNull();
});

test('story cannot be deleted by non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('delete', $story->id)
        ->assertForbidden();

    expect($story->fresh())->not->toBeNull();
});
