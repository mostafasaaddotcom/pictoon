<?php

use App\Livewire\Stories\Create;
use App\Models\Story;
use App\Models\User;
use Livewire\Livewire;

test('create story page is displayed', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get('/stories/create')->assertOk();
});

test('story can be created with valid data', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('idea', 'A magical adventure')
        ->set('description', 'A story about a brave child who saves the kingdom')
        ->set('moral_lesson', 'Courage conquers all')
        ->set('language', 'en')
        ->set('pages_count', 15)
        ->set('is_active', true)
        ->call('save')
        ->assertRedirect(route('stories.index'));

    expect(Story::where('idea', 'A magical adventure')->exists())->toBeTrue();
});

test('created story belongs to authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('idea', 'My story')
        ->set('description', 'Description')
        ->set('moral_lesson', 'Lesson')
        ->set('language', 'en')
        ->set('pages_count', 10)
        ->call('save');

    $story = Story::where('idea', 'My story')->first();

    expect($story->user_id)->toEqual($user->id);
});

test('story creation requires idea', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('idea', '')
        ->set('description', 'Description')
        ->set('moral_lesson', 'Lesson')
        ->set('language', 'en')
        ->set('pages_count', 10)
        ->call('save')
        ->assertHasErrors(['idea']);
});

test('story creation requires description', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('idea', 'My story')
        ->set('description', '')
        ->set('moral_lesson', 'Lesson')
        ->set('language', 'en')
        ->set('pages_count', 10)
        ->call('save')
        ->assertHasErrors(['description']);
});

test('story creation requires moral_lesson', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('idea', 'My story')
        ->set('description', 'Description')
        ->set('moral_lesson', '')
        ->set('language', 'en')
        ->set('pages_count', 10)
        ->call('save')
        ->assertHasErrors(['moral_lesson']);
});

test('story creation requires valid language', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('idea', 'My story')
        ->set('description', 'Description')
        ->set('moral_lesson', 'Lesson')
        ->set('language', 'invalid')
        ->set('pages_count', 10)
        ->call('save')
        ->assertHasErrors(['language']);
});

test('pages_count must be at least 1', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('idea', 'My story')
        ->set('description', 'Description')
        ->set('moral_lesson', 'Lesson')
        ->set('language', 'en')
        ->set('pages_count', 0)
        ->call('save')
        ->assertHasErrors(['pages_count']);
});

test('pages_count must be at most 50', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('idea', 'My story')
        ->set('description', 'Description')
        ->set('moral_lesson', 'Lesson')
        ->set('language', 'en')
        ->set('pages_count', 51)
        ->call('save')
        ->assertHasErrors(['pages_count']);
});

test('story can be created with image urls', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Create::class)
        ->set('idea', 'My story')
        ->set('description', 'Description')
        ->set('moral_lesson', 'Lesson')
        ->set('language', 'en')
        ->set('pages_count', 10)
        ->set('images_input', 'https://example.com/image1.jpg, https://example.com/image2.jpg')
        ->call('save');

    $story = Story::where('idea', 'My story')->first();

    expect($story->images)->toBeArray();
    expect($story->images)->toContain('https://example.com/image1.jpg');
    expect($story->images)->toContain('https://example.com/image2.jpg');
});
