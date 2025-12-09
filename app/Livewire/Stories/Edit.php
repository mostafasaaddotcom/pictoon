<?php

namespace App\Livewire\Stories;

use App\Models\Story;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Edit extends Component
{
    public Story $story;

    public string $idea = '';

    public string $description = '';

    public string $moral_lesson = '';

    public string $language = '';

    public int $pages_count = 10;

    public string $images_input = '';

    public bool $is_active = true;

    /**
     * Mount the component.
     */
    public function mount(Story $story): void
    {
        $this->authorize('update', $story);

        $this->story = $story;
        $this->idea = $story->idea;
        $this->description = $story->description;
        $this->moral_lesson = $story->moral_lesson;
        $this->language = $story->language;
        $this->pages_count = $story->pages_count;
        $this->images_input = implode(', ', $story->images ?? []);
        $this->is_active = $story->is_active;
    }

    /**
     * Save the story.
     */
    public function save(): void
    {
        $this->authorize('update', $this->story);

        $validated = $this->validate([
            'idea' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'moral_lesson' => ['required', 'string', 'max:255'],
            'language' => ['required', 'string', 'in:en,ar,fr,es,de'],
            'pages_count' => ['required', 'integer', 'min:1', 'max:50'],
            'images_input' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $images = $this->parseImages($validated['images_input'] ?? '');

        $this->story->update([
            'idea' => $validated['idea'],
            'description' => $validated['description'],
            'moral_lesson' => $validated['moral_lesson'],
            'language' => $validated['language'],
            'pages_count' => $validated['pages_count'],
            'images' => $images,
            'is_active' => $validated['is_active'],
        ]);

        $this->dispatch('story-updated');
    }

    /**
     * Parse comma-separated image URLs.
     *
     * @return array<string>
     */
    protected function parseImages(string $input): array
    {
        if (empty($input)) {
            return [];
        }

        return array_filter(
            array_map('trim', explode(',', $input)),
            fn ($url) => filter_var($url, FILTER_VALIDATE_URL)
        );
    }

    public function render()
    {
        return view('livewire.stories.edit');
    }
}
