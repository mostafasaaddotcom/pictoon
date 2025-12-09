<?php

use App\Livewire\Stories\Show;
use App\Models\Story;
use App\Models\User;
use Livewire\Livewire;

test('show story page is displayed for owner', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}")->assertOk();
});

test('show story page returns 403 for non-owner', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user);

    $this->get("/stories/{$story->id}")->assertForbidden();
});

test('show story page displays story details', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create([
        'user_id' => $user->id,
        'idea' => 'A magical adventure',
        'description' => 'Story about a brave child',
        'moral_lesson' => 'Courage conquers all',
        'language' => 'en',
        'pages_count' => 15,
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Show::class, ['story' => $story]);

    $response->assertSee('A magical adventure');
    $response->assertSee('Story about a brave child');
    $response->assertSee('Courage conquers all');
    $response->assertSee('EN');
    $response->assertSee('15');
});

test('show story page displays active status', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create([
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Show::class, ['story' => $story]);

    $response->assertSee('Active');
});

test('show story page displays inactive status', function () {
    $user = User::factory()->create();
    $story = Story::factory()->inactive()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Show::class, ['story' => $story]);

    $response->assertSee('Inactive');
});

test('unauthenticated users are redirected to login', function () {
    $story = Story::factory()->create();

    $this->get("/stories/{$story->id}")->assertRedirect('/login');
});
