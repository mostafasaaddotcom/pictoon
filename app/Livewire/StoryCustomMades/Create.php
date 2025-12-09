<?php

namespace App\Livewire\StoryCustomMades;

use App\Models\Story;
use App\Models\StoryCustomMade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public Story $story;

    public string $child_name = '';

    public string $child_gender = 'male';

    public int $child_age = 5;

    public string $child_image_url = '';

    /**
     * Mount the component.
     */
    public function mount(Story $story): void
    {
        $this->authorize('create', [StoryCustomMade::class, $story]);

        $this->story = $story;
    }

    /**
     * Save the custom made.
     */
    public function save(): void
    {
        $this->authorize('create', [StoryCustomMade::class, $this->story]);

        $validated = $this->validate([
            'child_name' => ['required', 'string', 'max:255'],
            'child_gender' => ['required', 'string', Rule::in(['male', 'female', 'other'])],
            'child_age' => ['required', 'integer', 'min:1', 'max:18'],
            'child_image_url' => ['nullable', 'string', 'url', 'max:2048'],
        ]);

        StoryCustomMade::create([
            'user_id' => Auth::id(),
            'story_id' => $this->story->id,
            'child_name' => $validated['child_name'],
            'child_gender' => $validated['child_gender'],
            'child_age' => $validated['child_age'],
            'child_image_url' => $validated['child_image_url'] ?: null,
            'status' => 'pending',
        ]);

        $this->redirect(route('story-custom-mades.index', $this->story), navigate: true);
    }

    public function render()
    {
        return view('livewire.story-custom-mades.create');
    }
}
