<?php

namespace App\Livewire\StoryCovers;

use App\Models\Story;
use App\Models\StoryCoverImagePrompt;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    public Story $story;

    public StoryCoverImagePrompt $coverPrompt;

    /**
     * Mount the component.
     */
    public function mount(Story $story, StoryCoverImagePrompt $coverPrompt): void
    {
        // Ensure coverPrompt belongs to the story
        abort_if($coverPrompt->story_id !== $story->id, 404);

        $this->authorize('view', $coverPrompt);

        $this->story = $story;
        $this->coverPrompt = $coverPrompt;
    }

    public function render()
    {
        return view('livewire.story-covers.show');
    }
}
