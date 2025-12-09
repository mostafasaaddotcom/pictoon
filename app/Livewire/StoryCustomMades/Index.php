<?php

namespace App\Livewire\StoryCustomMades;

use App\Models\Story;
use App\Models\StoryCustomMade;
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
        $this->authorize('viewAny', [StoryCustomMade::class, $story]);

        $this->story = $story;
    }

    /**
     * Delete a custom made.
     */
    public function delete(int $id): void
    {
        $customMade = StoryCustomMade::findOrFail($id);

        $this->authorize('delete', $customMade);

        $customMade->delete();

        $this->dispatch('custom-made-deleted');
    }

    /**
     * Refresh the list when a custom made is updated.
     */
    #[On('custom-made-updated')]
    public function refreshList(): void
    {
        // Component will re-render automatically
    }

    public function render()
    {
        $customMades = $this->story->customMades()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.story-custom-mades.index', [
            'customMades' => $customMades,
        ]);
    }
}
