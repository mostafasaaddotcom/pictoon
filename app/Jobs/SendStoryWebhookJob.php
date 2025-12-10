<?php

namespace App\Jobs;

use App\Models\Story;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendStoryWebhookJob implements ShouldQueue
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
        public Story $story
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $payload = [
                'story_id' => $this->story->id,
                'user_id' => $this->story->user_id,
                'idea' => $this->story->idea,
                'description' => $this->story->description,
                'moral_lesson' => $this->story->moral_lesson,
                'language' => $this->story->language,
                'pages_count' => $this->story->pages_count,
                'images' => $this->story->images,
                'is_active' => $this->story->is_active,
                'created_at' => $this->story->created_at->toIso8601String(),
            ];

            $response = Http::timeout(30)->post(
                'https://n8n.srv871797.hstgr.cloud/webhook/generate-story-pages',
                $payload
            );

            if (! $response->successful()) {
                Log::error('Story webhook failed for story '.$this->story->id, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception('Webhook request failed with status: '.$response->status());
            }

            Log::info('Story webhook sent successfully for story '.$this->story->id);

        } catch (\Exception $e) {
            Log::error('Failed to send story webhook for story '.$this->story->id, [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::error('SendStoryWebhookJob failed permanently for story '.$this->story->id, [
            'error' => $exception?->getMessage(),
        ]);
    }
}
