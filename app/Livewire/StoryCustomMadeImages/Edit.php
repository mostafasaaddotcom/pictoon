<?php

namespace App\Livewire\StoryCustomMadeImages;

use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\StoryCustomMadeImage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public Story $story;

    public StoryCustomMade $customMade;

    public StoryCustomMadeImage $image;

    public int $page_number = 1;

    public string $image_type = 'page';

    public ?int $reference_number = null;

    public string $image_url = '';

    public string $status = 'pending';

    /**
     * Mount the component.
     */
    public function mount(Story $story, StoryCustomMade $customMade, StoryCustomMadeImage $image): void
    {
        // Ensure customMade belongs to the story
        abort_if($customMade->story_id !== $story->id, 404);

        // Ensure image belongs to the customMade
        abort_if($image->story_custom_made_id !== $customMade->id, 404);

        $this->authorize('update', $image);

        $this->story = $story;
        $this->customMade = $customMade;
        $this->image = $image;
        $this->page_number = $image->page_number;
        $this->image_type = $image->image_type;
        $this->reference_number = $image->reference_number;
        $this->image_url = $image->image_url ?? '';
        $this->status = $image->status;
    }

    /**
     * Save the image.
     */
    public function save(): void
    {
        $this->authorize('update', $this->image);

        $validated = $this->validate([
            'page_number' => ['required', 'integer', 'min:0'],
            'image_type' => ['required', 'string', Rule::in(['page', 'cover_front', 'cover_back'])],
            'reference_number' => ['nullable', 'integer', 'min:0'],
            'image_url' => ['nullable', 'string', 'url', 'max:2048'],
            'status' => ['required', 'string', Rule::in(['pending', 'processing', 'completed', 'failed'])],
        ]);

        $this->image->update([
            'page_number' => $validated['page_number'],
            'image_type' => $validated['image_type'],
            'reference_number' => $validated['reference_number'],
            'image_url' => $validated['image_url'] ?: null,
            'status' => $validated['status'],
        ]);

        $this->dispatch('image-updated');
    }

    public function render()
    {
        return view('livewire.story-custom-made-images.edit');
    }
}
