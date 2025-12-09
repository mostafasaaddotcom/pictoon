<?php

namespace App\Livewire\StoryCustomMades;

use App\Models\Story;
use App\Models\StoryCustomMade;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public Story $story;

    public StoryCustomMade $customMade;

    public string $child_name = '';

    public string $child_gender = 'male';

    public int $child_age = 5;

    public string $child_image_url = '';

    public string $status = 'pending';

    /**
     * Mount the component.
     */
    public function mount(Story $story, StoryCustomMade $customMade): void
    {
        // Ensure customMade belongs to the story
        abort_if($customMade->story_id !== $story->id, 404);

        $this->authorize('update', $customMade);

        $this->story = $story;
        $this->customMade = $customMade;
        $this->child_name = $customMade->child_name;
        $this->child_gender = $customMade->child_gender;
        $this->child_age = $customMade->child_age;
        $this->child_image_url = $customMade->child_image_url ?? '';
        $this->status = $customMade->status;
    }

    /**
     * Save the custom made.
     */
    public function save(): void
    {
        $this->authorize('update', $this->customMade);

        $validated = $this->validate([
            'child_name' => ['required', 'string', 'max:255'],
            'child_gender' => ['required', 'string', Rule::in(['male', 'female', 'other'])],
            'child_age' => ['required', 'integer', 'min:1', 'max:18'],
            'child_image_url' => ['nullable', 'string', 'url', 'max:2048'],
            'status' => ['required', 'string', Rule::in(['pending', 'processing', 'completed', 'failed'])],
        ]);

        $this->customMade->update([
            'child_name' => $validated['child_name'],
            'child_gender' => $validated['child_gender'],
            'child_age' => $validated['child_age'],
            'child_image_url' => $validated['child_image_url'] ?: null,
            'status' => $validated['status'],
        ]);

        $this->dispatch('custom-made-updated');
    }

    public function render()
    {
        return view('livewire.story-custom-mades.edit');
    }
}
