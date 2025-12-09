<?php

namespace App\Livewire\CustomStories;

use App\Jobs\GenerateStoryImagesJob;
use App\Models\Story;
use App\Models\StoryCustomMade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $showModal = false;

    public ?int $story_id = null;

    public string $child_name = '';

    public string $child_gender = 'male';

    public int $child_age = 5;

    public $child_image;

    public string $search = '';

    /**
     * Open the generate modal.
     */
    public function openModal(): void
    {
        $this->reset(['story_id', 'child_name', 'child_gender', 'child_age', 'child_image']);
        $this->showModal = true;
    }

    /**
     * Close the generate modal.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    /**
     * Generate a new custom story.
     */
    public function generate(): void
    {
        $validated = $this->validate([
            'story_id' => ['required', 'integer', 'exists:stories,id'],
            'child_name' => ['required', 'string', 'max:255'],
            'child_gender' => ['required', 'string', Rule::in(['male', 'female'])],
            'child_age' => ['required', 'integer', 'min:1', 'max:18'],
            'child_image' => ['required', 'image', 'max:5120'],
        ]);

        // Verify story belongs to user
        $story = Story::where('id', $validated['story_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Store the image
        $imagePath = $this->child_image->store('child-images', 'public');
        $imageUrl = config('app.url').'/storage/'.$imagePath;  

        // Create the custom made record
        $customMade = StoryCustomMade::create([
            'user_id' => Auth::id(),
            'story_id' => $story->id,
            'child_name' => $validated['child_name'],
            'child_gender' => $validated['child_gender'],
            'child_age' => $validated['child_age'],
            'child_image_url' => $imageUrl,
            'status' => 'pending',
        ]);

        // Dispatch the job to send webhook
        GenerateStoryImagesJob::dispatch($customMade);

        $this->closeModal();
        $this->dispatch('custom-story-created');

        session()->flash('message', __('Custom story generation started! The images will be generated shortly.'));
    }

    /**
     * Delete a custom story.
     */
    public function delete(int $id): void
    {
        $customMade = StoryCustomMade::findOrFail($id);

        $this->authorize('delete', $customMade);

        $customMade->delete();

        $this->dispatch('custom-story-deleted');
    }

    /**
     * Update search and reset pagination.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $customStories = StoryCustomMade::with('story')
            ->where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('child_name', 'like', '%'.$this->search.'%')
                        ->orWhereHas('story', function ($sq) {
                            $sq->where('idea', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->latest()
            ->paginate(12);

        $stories = Story::where('user_id', Auth::id())
            ->where('is_active', true)
            ->orderBy('idea')
            ->get();

        return view('livewire.custom-stories.index', [
            'customStories' => $customStories,
            'stories' => $stories,
        ]);
    }
}
