<?php

namespace App\Livewire\Stories;

use App\Models\Story;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    /**
     * Reset pagination when search changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Toggle the active status of a story.
     */
    public function toggleActive(Story $story): void
    {
        $this->authorize('update', $story);

        $story->update(['is_active' => ! $story->is_active]);
    }

    /**
     * Delete a story.
     */
    public function delete(Story $story): void
    {
        $this->authorize('delete', $story);

        $story->delete();

        $this->dispatch('story-deleted');
    }

    public function render()
    {
        $stories = Story::query()
            ->forUser(Auth::user())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('idea', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.stories.index', [
            'stories' => $stories,
        ]);
    }
}
