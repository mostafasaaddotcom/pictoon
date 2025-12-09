<?php

namespace App\Livewire\StoryPages;

use App\Models\Story;
use App\Models\StoryPageImagePrompt;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public Story $story;

    public StoryPageImagePrompt $pagePrompt;

    public int $page_number = 1;

    public string $scene_title = '';

    public string $image_prompt = '';

    public string $story_text = '';

    public string $emotions = '';

    public string $art_style = '';

    /**
     * Mount the component.
     */
    public function mount(Story $story, StoryPageImagePrompt $pagePrompt): void
    {
        // Ensure pagePrompt belongs to the story
        abort_if($pagePrompt->story_id !== $story->id, 404);

        $this->authorize('update', $pagePrompt);

        $this->story = $story;
        $this->pagePrompt = $pagePrompt;
        $this->page_number = $pagePrompt->page_number;
        $this->scene_title = $pagePrompt->scene_title;
        $this->image_prompt = $pagePrompt->image_prompt;
        $this->story_text = $pagePrompt->story_text;
        $this->emotions = $pagePrompt->emotions ?? '';
        $this->art_style = $pagePrompt->art_style;
    }

    /**
     * Save the page prompt.
     */
    public function save(): void
    {
        $this->authorize('update', $this->pagePrompt);

        $validated = $this->validate([
            'page_number' => ['required', 'integer', 'min:1'],
            'scene_title' => ['required', 'string', 'max:255'],
            'image_prompt' => ['required', 'string'],
            'story_text' => ['required', 'string'],
            'emotions' => ['nullable', 'string'],
            'art_style' => ['required', 'string', 'max:255'],
        ]);

        // Convert empty emotions to null
        if (empty($validated['emotions'])) {
            $validated['emotions'] = null;
        }

        $this->pagePrompt->update($validated);

        $this->dispatch('page-updated');
    }

    public function render()
    {
        return view('livewire.story-pages.edit');
    }
}
