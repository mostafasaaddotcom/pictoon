<?php

namespace App\Jobs;

use App\Models\StoryCustomMade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateStoryImagesJob implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public StoryCustomMade $customMade
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Update status to processing
        $this->customMade->update(['status' => 'processing']);

        try {
            // Load relationships
            $this->customMade->load(['story.pageImagePrompts', 'story.coverImagePrompts']);

            $story = $this->customMade->story;

            // Get child image as base64
            $childImageBase64 = null;
            $childImageMimeType = null;
            if ($this->customMade->child_image_url) {
                $imagePath = str_replace('/storage/', '', parse_url($this->customMade->child_image_url, PHP_URL_PATH));
                if (Storage::disk('public')->exists($imagePath)) {
                    $imageContent = Storage::disk('public')->get($imagePath);
                    $childImageBase64 = base64_encode($imageContent);
                    $childImageMimeType = Storage::disk('public')->mimeType($imagePath);
                }
            }

            // Build the payload
            $payload = [
                'custom_made_id' => $this->customMade->id,
                'child' => [
                    'name' => $this->customMade->child_name,
                    'age' => $this->customMade->child_age,
                    'gender' => $this->customMade->child_gender,
                    'image_base64' => $childImageBase64,
                    'image_mime_type' => $childImageMimeType,
                ],
                'story' => [
                    'id' => $story->id,
                    'idea' => $story->idea,
                    'description' => $story->description,
                    'moral_lesson' => $story->moral_lesson,
                    'language' => $story->language,
                    'pages_count' => $story->pages_count,
                ],
                'page_prompts' => $story->pageImagePrompts->map(function ($prompt) {
                    return [
                        'page_number' => $prompt->page_number,
                        'scene_title' => $prompt->scene_title,
                        'image_prompt' => $prompt->image_prompt,
                        'story_text' => $prompt->story_text,
                        'emotions' => $prompt->emotions,
                        'art_style' => $prompt->art_style,
                    ];
                })->toArray(),
                'cover_prompts' => $story->coverImagePrompts->map(function ($prompt) {
                    return [
                        'type' => $prompt->type,
                        'image_prompt' => $prompt->image_prompt,
                        'meta_data' => $prompt->meta_data,
                    ];
                })->toArray(),
            ];

            // Send the webhook
            $response = Http::timeout(30)->post(
                'https://n8n.srv871797.hstgr.cloud/webhook/generate-story-images',
                $payload
            );

            if (! $response->successful()) {
                Log::error('Webhook failed for custom made '.$this->customMade->id, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                $this->customMade->update(['status' => 'failed']);

                return;
            }

            Log::info('Webhook sent successfully for custom made '.$this->customMade->id);

        } catch (\Exception $e) {
            Log::error('Failed to send webhook for custom made '.$this->customMade->id, [
                'error' => $e->getMessage(),
            ]);

            $this->customMade->update(['status' => 'failed']);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::error('GenerateStoryImagesJob failed permanently for custom made '.$this->customMade->id, [
            'error' => $exception?->getMessage(),
        ]);

        $this->customMade->update(['status' => 'failed']);
    }
}
