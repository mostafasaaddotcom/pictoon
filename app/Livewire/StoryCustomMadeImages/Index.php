<?php

namespace App\Livewire\StoryCustomMadeImages;

use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\StoryCustomMadeImage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
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

        $this->authorize('viewAny', [StoryCustomMadeImage::class, $customMade]);

        $this->story = $story;
        $this->customMade = $customMade;
    }

    /**
     * Delete an image.
     */
    public function delete(int $id): void
    {
        $image = StoryCustomMadeImage::findOrFail($id);

        $this->authorize('delete', $image);

        $image->delete();

        $this->dispatch('image-deleted');
    }

    /**
     * Refresh the list when an image is updated.
     */
    #[On('image-updated')]
    public function refreshList(): void
    {
        // Component will re-render automatically
    }

    public function render()
    {
        $images = $this->customMade->images()
            ->orderBy('page_number')
            ->orderBy('image_type')
            ->get();

        return view('livewire.story-custom-made-images.index', [
            'images' => $images,
        ]);
    }
}
