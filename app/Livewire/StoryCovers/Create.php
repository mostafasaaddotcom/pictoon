<?php

namespace App\Livewire\StoryCovers;

use App\Models\Story;
use App\Models\StoryCoverImagePrompt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public Story $story;

    public string $type = 'front';

    public string $image_prompt = '';

    /**
     * Mount the component.
     */
    public function mount(Story $story): void
    {
        $this->authorize('create', [StoryCoverImagePrompt::class, $story]);

        $this->story = $story;

        // Default to front if no covers exist, otherwise check what's missing
        $existingTypes = $story->coverImagePrompts()->pluck('type')->toArray();
        if (! in_array('front', $existingTypes)) {
            $this->type = 'front';
        } elseif (! in_array('back', $existingTypes)) {
            $this->type = 'back';
        }
    }

    /**
     * Save the cover prompt.
     */
    public function save(): void
    {
        $this->authorize('create', [StoryCoverImagePrompt::class, $this->story]);

        $validated = $this->validate([
            'type' => ['required', 'string', Rule::in(['front', 'back'])],
            'image_prompt' => ['required', 'string'],
        ]);

        StoryCoverImagePrompt::create([
            'user_id' => Auth::id(),
            'story_id' => $this->story->id,
            ...$validated,
        ]);

        $this->redirect(route('story-covers.index', $this->story), navigate: true);
    }

    public function render()
    {
        return view('livewire.story-covers.create');
    }
}
