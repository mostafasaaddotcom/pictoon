<?php

namespace App\Livewire\Stories;

use App\Models\Story;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Create extends Component
{
    public string $idea = '';

    public string $description = '';

    public string $moral_lesson = '';

    public string $language = 'en';

    public int $pages_count = 10;

    public string $images_input = '';

    public bool $is_active = true;

    /**
     * Save the story.
     */
    public function save(): void
    {
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

        Story::create([
            'user_id' => Auth::id(),
            'idea' => $validated['idea'],
            'description' => $validated['description'],
            'moral_lesson' => $validated['moral_lesson'],
            'language' => $validated['language'],
            'pages_count' => $validated['pages_count'],
            'images' => $images,
            'is_active' => $validated['is_active'],
        ]);

        $this->redirect(route('stories.index'), navigate: true);
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
        return view('livewire.stories.create');
    }
}
