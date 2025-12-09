<?php

use App\Jobs\GenerateStoryImagesJob;
use App\Models\Story;
use App\Models\StoryCoverImagePrompt;
use App\Models\StoryCustomMade;
use App\Models\StoryPageImagePrompt;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('job updates custom made status to processing', function () {
    Http::fake([
        '*' => Http::response(['success' => true], 200),
    ]);

    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->pending()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $job = new GenerateStoryImagesJob($customMade);
    $job->handle();

    $customMade->refresh();
    expect($customMade->status)->toEqual('processing');
});

test('job sends correct payload to webhook', function () {
    Storage::fake('public');
    Http::fake([
        'https://n8n.srv871797.hstgr.cloud/*' => Http::response(['success' => true], 200),
    ]);

    // Create a fake image file
    $imageContent = 'fake-image-content';
    Storage::disk('public')->put('child-images/test-child.jpg', $imageContent);

    $user = User::factory()->create();
    $story = Story::factory()->create([
        'user_id' => $user->id,
        'idea' => 'A magical adventure',
        'description' => 'A story about magic',
        'moral_lesson' => 'Be kind',
        'language' => 'en',
        'pages_count' => 10,
    ]);

    StoryPageImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'page_number' => 1,
        'scene_title' => 'The Beginning',
        'image_prompt' => 'A child in a forest',
        'story_text' => 'Once upon a time',
        'emotions' => 'happy',
        'art_style' => 'watercolor',
    ]);

    StoryCoverImagePrompt::factory()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'type' => 'front',
        'image_prompt' => 'A magical forest cover',
        'meta_data' => ['key' => 'value'],
    ]);

    $customMade = StoryCustomMade::factory()->pending()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
        'child_name' => 'Alice',
        'child_age' => 7,
        'child_gender' => 'female',
        'child_image_url' => '/storage/child-images/test-child.jpg',
    ]);

    $job = new GenerateStoryImagesJob($customMade);
    $job->handle();

    Http::assertSent(function ($request) use ($customMade, $story, $imageContent) {
        $data = $request->data();

        return $request->url() === 'https://n8n.srv871797.hstgr.cloud/webhook-test/generate-story-images'
            && $data['custom_made_id'] === $customMade->id
            && $data['child']['name'] === 'Alice'
            && $data['child']['age'] === 7
            && $data['child']['gender'] === 'female'
            && $data['child']['image_base64'] === base64_encode($imageContent)
            && $data['story']['id'] === $story->id
            && $data['story']['idea'] === 'A magical adventure'
            && count($data['page_prompts']) === 1
            && count($data['cover_prompts']) === 1;
    });
});

test('job sets status to failed on webhook failure', function () {
    Http::fake([
        '*' => Http::response(['error' => 'Server error'], 500),
    ]);

    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->pending()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $job = new GenerateStoryImagesJob($customMade);
    $job->handle();

    $customMade->refresh();
    expect($customMade->status)->toEqual('failed');
});

test('job sets status to failed on exception', function () {
    Http::fake(function () {
        throw new \Exception('Connection timeout');
    });

    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->pending()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $job = new GenerateStoryImagesJob($customMade);

    try {
        $job->handle();
    } catch (\Exception $e) {
        // Expected
    }

    $customMade->refresh();
    expect($customMade->status)->toEqual('failed');
});

test('failed method sets status to failed', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create(['user_id' => $user->id]);
    $customMade = StoryCustomMade::factory()->processing()->create([
        'user_id' => $user->id,
        'story_id' => $story->id,
    ]);

    $job = new GenerateStoryImagesJob($customMade);
    $job->failed(new \Exception('Test exception'));

    $customMade->refresh();
    expect($customMade->status)->toEqual('failed');
});
