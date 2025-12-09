<?php

use App\Jobs\GenerateStoryImagesJob;
use App\Livewire\CustomStories\Index;
use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('custom stories index page is displayed for authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get('/custom-stories')->assertOk();
});

test('unauthenticated users are redirected to login', function () {
    $this->get('/custom-stories')->assertRedirect('/login');
});

test('custom stories are displayed', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'child_name' => 'Alice',
    ]);
    StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'child_name' => 'Bob',
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Index::class);

    $response->assertSee('Alice');
    $response->assertSee('Bob');
});

test('only user own custom stories are displayed', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $otherStory = Story::factory()->create(['user_id' => $otherUser->id]);

    StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'child_name' => 'MyChild',
    ]);
    StoryCustomMade::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $otherStory->id,
        'child_name' => 'OtherChild',
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Index::class);

    $response->assertSee('MyChild');
    $response->assertDontSee('OtherChild');
});

test('search filters custom stories by child name', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'child_name' => 'Alice',
    ]);
    StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'child_name' => 'Bob',
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Index::class)
        ->set('search', 'Alice');

    $response->assertSee('Alice');
    $response->assertDontSee('Bob');
});

test('modal can be opened and closed', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->assertSet('showModal', false)
        ->call('openModal')
        ->assertSet('showModal', true)
        ->call('closeModal')
        ->assertSet('showModal', false);
});

test('custom story can be generated with valid data', function () {
    Storage::fake('public');
    Queue::fake();

    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id, 'is_active' => true]);

    $this->actingAs($user);

    // Create a simple fake file with image mime type
    $file = UploadedFile::fake()->create('child.jpg', 100, 'image/jpeg');

    Livewire::test(Index::class)
        ->set('story_id', $story->id)
        ->set('child_name', 'Alice')
        ->set('child_gender', 'female')
        ->set('child_age', 7)
        ->set('child_image', $file)
        ->call('generate')
        ->assertHasNoErrors();

    expect(StoryCustomMade::where('child_name', 'Alice')->exists())->toBeTrue();

    $customMade = StoryCustomMade::where('child_name', 'Alice')->first();
    expect($customMade->user_id)->toEqual($user->id);
    expect($customMade->story_id)->toEqual($story->id);
    expect($customMade->child_gender)->toEqual('female');
    expect($customMade->child_age)->toEqual(7);
    expect($customMade->status)->toEqual('pending');

    Queue::assertPushed(GenerateStoryImagesJob::class, function ($job) use ($customMade) {
        return $job->customMade->id === $customMade->id;
    });
});

test('generation validates story_id is required', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->set('child_name', 'Alice')
        ->set('child_gender', 'female')
        ->set('child_age', 7)
        ->call('generate')
        ->assertHasErrors(['story_id']);
});

test('generation validates child_name is required', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->set('story_id', $story->id)
        ->set('child_name', '')
        ->set('child_gender', 'female')
        ->set('child_age', 7)
        ->call('generate')
        ->assertHasErrors(['child_name']);
});

test('generation validates child_gender', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->set('story_id', $story->id)
        ->set('child_name', 'Alice')
        ->set('child_gender', 'invalid')
        ->set('child_age', 7)
        ->call('generate')
        ->assertHasErrors(['child_gender']);
});

test('generation validates child_age range', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->set('story_id', $story->id)
        ->set('child_name', 'Alice')
        ->set('child_gender', 'female')
        ->set('child_age', 0)
        ->call('generate')
        ->assertHasErrors(['child_age']);

    Livewire::test(Index::class)
        ->set('story_id', $story->id)
        ->set('child_name', 'Alice')
        ->set('child_gender', 'female')
        ->set('child_age', 19)
        ->call('generate')
        ->assertHasErrors(['child_age']);
});

test('generation validates child_image is required', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->set('story_id', $story->id)
        ->set('child_name', 'Alice')
        ->set('child_gender', 'female')
        ->set('child_age', 7)
        ->call('generate')
        ->assertHasErrors(['child_image']);
});

test('custom story can be deleted by owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('delete', $customMade->id)
        ->assertDispatched('custom-story-deleted');

    expect($customMade->fresh())->toBeNull();
});

test('custom story cannot be deleted by non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);
    $customMade = StoryCustomMade::factory()->create([
        'user_id' => $otherUser->id,
        'story_id' => $story->id,
    ]);

    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('delete', $customMade->id)
        ->assertForbidden();
});

test('cannot generate custom story for another user story', function () {
    Storage::fake('public');
    Queue::fake();

    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherStory = Story::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user);

    // Create a simple fake file with image mime type
    $file = UploadedFile::fake()->create('child.jpg', 100, 'image/jpeg');

    // The story doesn't belong to the user, so it should throw ModelNotFoundException
    $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    Livewire::test(Index::class)
        ->set('story_id', $otherStory->id)
        ->set('child_name', 'Alice')
        ->set('child_gender', 'female')
        ->set('child_age', 7)
        ->set('child_image', $file)
        ->call('generate');

    Queue::assertNotPushed(GenerateStoryImagesJob::class);
});
