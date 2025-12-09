<?php

namespace App\Livewire\StoryPages;

use App\Models\Story;
use App\Models\StoryPageImagePrompt;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public Story $story;

    /**
     * Mount the component.
     */
    public function mount(Story $story): void
    {
        $this->authorize('viewAny', [StoryPageImagePrompt::class, $story]);

        $this->story = $story;
    }

    /**
     * Delete a page prompt.
     */
    public function delete(StoryPageImagePrompt $pagePrompt): void
    {
        $this->authorize('delete', $pagePrompt);

        $pagePrompt->delete();

        $this->dispatch('page-deleted');
    }

    public function render()
    {
        $pages = $this->story->pageImagePrompts()
            ->orderBy('page_number')
            ->get();

        return view('livewire.story-pages.index', [
            'pages' => $pages,
        ]);
    }
}
