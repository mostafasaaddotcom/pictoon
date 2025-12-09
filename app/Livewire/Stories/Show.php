<?php

namespace App\Livewire\Stories;

use App\Models\Story;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Show extends Component
{
    public Story $story;

    /**
     * Mount the component.
     */
    public function mount(Story $story): void
    {
        $this->authorize('view', $story);

        $this->story = $story;
    }

    public function render()
    {
        return view('livewire.stories.show');
    }
}
