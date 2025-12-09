<?php

namespace App\Livewire\StoryCovers;

use App\Models\Story;
use App\Models\StoryCoverImagePrompt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
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
        $this->authorize('viewAny', [StoryCoverImagePrompt::class, $story]);

        $this->story = $story;
    }

    /**
     * Delete a cover prompt.
     */
    public function delete(int $id): void
    {
        $cover = StoryCoverImagePrompt::findOrFail($id);

        $this->authorize('delete', $cover);

        $cover->delete();

        $this->dispatch('cover-deleted');
    }

    /**
     * Refresh the list when a cover is updated.
     */
    #[On('cover-updated')]
    public function refreshList(): void
    {
        // Component will re-render automatically
    }

    public function render()
    {
        $covers = $this->story->coverImagePrompts()
            ->orderBy('type') // 'back' comes before 'front' alphabetically, but that's OK
            ->get();

        return view('livewire.story-covers.index', [
            'covers' => $covers,
        ]);
    }
}
