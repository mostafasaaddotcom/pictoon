<?php

namespace App\Livewire\StoryCustomMadeImages;

use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\StoryCustomMadeImage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    public Story $story;

    public StoryCustomMade $customMade;

    public StoryCustomMadeImage $image;

    /**
     * Mount the component.
     */
    public function mount(Story $story, StoryCustomMade $customMade, StoryCustomMadeImage $image): void
    {
        // Ensure customMade belongs to the story
        abort_if($customMade->story_id !== $story->id, 404);

        // Ensure image belongs to the customMade
        abort_if($image->story_custom_made_id !== $customMade->id, 404);

        $this->authorize('view', $image);

        $this->story = $story;
        $this->customMade = $customMade;
        $this->image = $image;
    }

    public function render()
    {
        return view('livewire.story-custom-made-images.show');
    }
}
