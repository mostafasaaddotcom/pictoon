<?php

use App\Jobs\SendStoryWebhookJob;
use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('job sends story data to webhook', function () {
    Http::fake([
        'https://n8n.srv871797.hstgr.cloud/*' => Http::response(['success' => true], 200),
    ]);

    $user = User::factory()->create();
    $story = Story::factory()->create([
        'user_id' => $user->id,
        'idea' => 'A magical adventure',
        'description' => 'A story about magic',
        'moral_lesson' => 'Be kind',
        'language' => 'en',
        'pages_count' => 10,
        'images' => ['https://example.com/image1.jpg'],
        'is_active' => true,
    ]);

    $job = new SendStoryWebhookJob($story);
    $job->handle();

    Http::assertSent(function ($request) use ($story) {
        $data = $request->data();

        return $request->url() === 'https://n8n.srv871797.hstgr.cloud/webhook-test/generate-story-pages'
            && $data['story_id'] === $story->id
            && $data['user_id'] === $story->user_id
            && $data['idea'] === 'A magical adventure'
            && $data['description'] === 'A story about magic'
            && $data['moral_lesson'] === 'Be kind'
            && $data['language'] === 'en'
            && $data['pages_count'] === 10
            && $data['images'] === ['https://example.com/image1.jpg']
            && $data['is_active'] === true;
    });
});

test('job throws exception on webhook failure', function () {
    Http::fake([
        '*' => Http::response(['error' => 'Server error'], 500),
    ]);

    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $job = new SendStoryWebhookJob($story);

    expect(fn () => $job->handle())->toThrow(\Exception::class);
});

test('job throws exception on connection error', function () {
    Http::fake(function () {
        throw new \Exception('Connection timeout');
    });

    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $job = new SendStoryWebhookJob($story);

    expect(fn () => $job->handle())->toThrow(\Exception::class);
});

test('job has correct retry configuration', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);

    $job = new SendStoryWebhookJob($story);

    expect($job->tries)->toBe(3);
    expect($job->backoff)->toBe(60);
});
