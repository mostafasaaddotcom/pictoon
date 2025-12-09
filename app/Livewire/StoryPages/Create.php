<?php

namespace App\Livewire\StoryPages;

use App\Models\Story;
use App\Models\StoryPageImagePrompt;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public Story $story;

    public int $page_number = 1;

    public string $scene_title = '';

    public string $image_prompt = '';

    public string $story_text = '';

    public string $emotions = '';

    public string $art_style = 'watercolor';

    /**
     * Mount the component.
     */
    public function mount(Story $story): void
    {
        $this->authorize('create', [StoryPageImagePrompt::class, $story]);

        $this->story = $story;

        // Set next page number
        $lastPage = $story->pageImagePrompts()->max('page_number');
        $this->page_number = $lastPage ? $lastPage + 1 : 1;
    }

    /**
     * Save the page prompt.
     */
    public function save(): void
    {
        $this->authorize('create', [StoryPageImagePrompt::class, $this->story]);

        $validated = $this->validate([
            'page_number' => ['required', 'integer', 'min:1'],
            'scene_title' => ['required', 'string', 'max:255'],
            'image_prompt' => ['required', 'string'],
            'story_text' => ['required', 'string'],
            'emotions' => ['nullable', 'string'],
            'art_style' => ['required', 'string', 'max:255'],
        ]);

        StoryPageImagePrompt::create([
            'user_id' => Auth::id(),
            'story_id' => $this->story->id,
            ...$validated,
        ]);

        $this->redirect(route('story-pages.index', $this->story), navigate: true);
    }

    public function render()
    {
        return view('livewire.story-pages.create');
    }
}
