<?php

namespace App\Livewire\StoryCustomMades;

use App\Models\Story;
use App\Models\StoryCustomMade;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    public Story $story;

    public StoryCustomMade $customMade;

    /**
     * Mount the component.
     */
    public function mount(Story $story, StoryCustomMade $customMade): void
    {
        // Ensure customMade belongs to the story
        abort_if($customMade->story_id !== $story->id, 404);

        $this->authorize('view', $customMade);

        $this->story = $story;
        $this->customMade = $customMade;
    }

    /**
     * Get the count of required images (page prompts + cover prompts).
     */
    #[Computed]
    public function requiredImagesCount(): int
    {
        return $this->story->pageImagePrompts()->count()
             + $this->story->coverImagePrompts()->count();
    }

    /**
     * Get the count of completed images for this custom made.
     */
    #[Computed]
    public function completedImagesCount(): int
    {
        return $this->customMade->images()
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Check if all required images are completed and PDF can be regenerated.
     */
    #[Computed]
    public function canRegeneratePdf(): bool
    {
        return $this->completedImagesCount > 0
            && $this->completedImagesCount >= $this->requiredImagesCount;
    }

    /**
     * Send webhook to regenerate the PDF with completed images.
     */
    public function regeneratePdf(): void
    {
        if (! $this->canRegeneratePdf) {
            return;
        }

        $images = $this->customMade->images()
            ->where('status', 'completed')
            ->get()
            ->map(fn ($img) => [
                'page_number' => $img->page_number,
                'image_type' => $img->image_type,
                'image_url' => $img->image_url,
            ])
            ->toArray();

        try {
            $response = Http::timeout(30)->post(
                'https://n8n.srv871797.hstgr.cloud/webhook/regenerate-final-pdf',
                [
                    'user_id' => $this->customMade->user_id,
                    'story_id' => $this->customMade->story_id,
                    'story_custom_made_id' => $this->customMade->id,
                    'images' => $images,
                ]
            );

            if ($response->successful()) {
                session()->flash('message', __('PDF regeneration request sent successfully.'));
            } else {
                session()->flash('error', __('Failed to send PDF regeneration request.'));
                Log::error('Regenerate PDF webhook failed', [
                    'custom_made_id' => $this->customMade->id,
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            session()->flash('error', __('Failed to send PDF regeneration request.'));
            Log::error('Regenerate PDF webhook exception', [
                'custom_made_id' => $this->customMade->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.story-custom-mades.show');
    }
}
