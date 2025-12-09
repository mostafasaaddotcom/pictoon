<?php

namespace App\Livewire\StoryCovers;

use App\Models\Story;
use App\Models\StoryCoverImagePrompt;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public Story $story;

    public StoryCoverImagePrompt $coverPrompt;

    public string $type = 'front';

    public string $image_prompt = '';

    /**
     * Mount the component.
     */
    public function mount(Story $story, StoryCoverImagePrompt $coverPrompt): void
    {
        // Ensure coverPrompt belongs to the story
        abort_if($coverPrompt->story_id !== $story->id, 404);

        $this->authorize('update', $coverPrompt);

        $this->story = $story;
        $this->coverPrompt = $coverPrompt;
        $this->type = $coverPrompt->type;
        $this->image_prompt = $coverPrompt->image_prompt;
    }

    /**
     * Save the cover prompt.
     */
    public function save(): void
    {
        $this->authorize('update', $this->coverPrompt);

        $validated = $this->validate([
            'type' => ['required', 'string', Rule::in(['front', 'back'])],
            'image_prompt' => ['required', 'string'],
        ]);

        $this->coverPrompt->update($validated);

        $this->dispatch('cover-updated');
    }

    public function render()
    {
        return view('livewire.story-covers.edit');
    }
}
