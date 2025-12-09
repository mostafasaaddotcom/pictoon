<?php

namespace App\Livewire\StoryPages;

use App\Models\Story;
use App\Models\StoryPageImagePrompt;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    public Story $story;

    public StoryPageImagePrompt $pagePrompt;

    /**
     * Mount the component.
     */
    public function mount(Story $story, StoryPageImagePrompt $pagePrompt): void
    {
        // Ensure pagePrompt belongs to the story
        abort_if($pagePrompt->story_id !== $story->id, 404);

        $this->authorize('view', $pagePrompt);

        $this->story = $story;
        $this->pagePrompt = $pagePrompt;
    }

    public function render()
    {
        return view('livewire.story-pages.show');
    }
}
