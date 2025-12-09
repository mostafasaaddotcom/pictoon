<?php

namespace App\Livewire\StoryCustomMadeImages;

use App\Models\Story;
use App\Models\StoryCustomMade;
use App\Models\StoryCustomMadeImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public Story $story;

    public StoryCustomMade $customMade;

    public int $page_number = 1;

    public string $image_type = 'page';

    public ?int $reference_number = null;

    public string $image_url = '';

    /**
     * Mount the component.
     */
    public function mount(Story $story, StoryCustomMade $customMade): void
    {
        // Ensure customMade belongs to the story
        abort_if($customMade->story_id !== $story->id, 404);

        $this->authorize('create', [StoryCustomMadeImage::class, $customMade]);

        $this->story = $story;
        $this->customMade = $customMade;
    }

    /**
     * Save the image.
     */
    public function save(): void
    {
        $this->authorize('create', [StoryCustomMadeImage::class, $this->customMade]);

        $validated = $this->validate([
            'page_number' => ['required', 'integer', 'min:0'],
            'image_type' => ['required', 'string', Rule::in(['page', 'cover_front', 'cover_back'])],
            'reference_number' => ['nullable', 'integer', 'min:0'],
            'image_url' => ['nullable', 'string', 'url', 'max:2048'],
        ]);

        StoryCustomMadeImage::create([
            'user_id' => Auth::id(),
            'story_id' => $this->story->id,
            'story_custom_made_id' => $this->customMade->id,
            'page_number' => $validated['page_number'],
            'image_type' => $validated['image_type'],
            'reference_number' => $validated['reference_number'],
            'image_url' => $validated['image_url'] ?: null,
            'status' => 'pending',
        ]);

        $this->redirect(route('custom-made-images.index', [$this->story, $this->customMade]), navigate: true);
    }

    public function render()
    {
        return view('livewire.story-custom-made-images.create');
    }
}
