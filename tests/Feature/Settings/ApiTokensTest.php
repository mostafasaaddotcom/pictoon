<?php

use App\Livewire\Settings\ApiTokens;
use App\Models\User;
use Livewire\Livewire;

test('api tokens page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/settings/api-tokens')->assertOk();
});

test('guests cannot access api tokens page', function () {
    $this->get('/settings/api-tokens')
        ->assertRedirect('/login');
});

test('user can generate token', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(ApiTokens::class)
        ->call('createToken');

    $response->assertHasNoErrors();

    expect($user->tokens()->count())->toBe(1);
    expect($response->get('plainTextToken'))->not->toBeNull();
});

test('user can revoke token', function () {
    $user = User::factory()->create();
    $user->createToken('api-token');

    $this->actingAs($user);

    expect($user->tokens()->count())->toBe(1);

    $response = Livewire::test(ApiTokens::class)
        ->call('revokeToken');

    $response->assertHasNoErrors();

    expect($user->tokens()->count())->toBe(0);
});

test('generating new token revokes old token', function () {
    $user = User::factory()->create();
    $oldToken = $user->createToken('api-token');

    $this->actingAs($user);

    expect($user->tokens()->count())->toBe(1);

    $response = Livewire::test(ApiTokens::class)
        ->call('createToken');

    $response->assertHasNoErrors();

    // Should still have only 1 token (old one revoked, new one created)
    expect($user->tokens()->count())->toBe(1);

    // The token ID should be different
    $newTokenId = $user->tokens()->first()->id;
    expect($newTokenId)->not->toBe($oldToken->accessToken->id);
});

test('hasToken is true when user has token', function () {
    $user = User::factory()->create();
    $user->createToken('api-token');

    $this->actingAs($user);

    $response = Livewire::test(ApiTokens::class);

    expect($response->viewData('hasToken'))->toBeTrue();
});

test('hasToken is false when user has no token', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(ApiTokens::class);

    expect($response->viewData('hasToken'))->toBeFalse();
});

test('plainTextToken is cleared after revoking', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test(ApiTokens::class)
        ->call('createToken');

    expect($response->get('plainTextToken'))->not->toBeNull();

    $response->call('revokeToken');

    expect($response->get('plainTextToken'))->toBeNull();
});
