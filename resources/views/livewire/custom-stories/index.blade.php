<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Custom Stories') }}</flux:heading>
            <flux:text class="text-zinc-500">{{ __('Manage personalized stories for children') }}</flux:text>
        </div>
        <flux:button variant="primary" wire:click="openModal">
            {{ __('Generate New') }}
        </flux:button>
    </div>

    @if (session('message'))
        <flux:callout variant="success">
            {{ session('message') }}
        </flux:callout>
    @endif

    <x-action-message class="me-3" on="custom-story-deleted">
        {{ __('Custom story deleted.') }}
    </x-action-message>

    <div class="flex items-center gap-4">
        <div class="flex-1">
            <flux:input
                wire:model.live.debounce.300ms="search"
                type="search"
                :placeholder="__('Search by child name or story...')"
            />
        </div>
    </div>

    @if ($customStories->isEmpty())
        <div class="flex flex-1 items-center justify-center rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700">
            <div class="text-center">
                <flux:icon name="users" class="mx-auto h-12 w-12 text-zinc-400" />
                <flux:heading size="lg" class="mt-4">{{ __('No custom stories yet') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Generate personalized stories for children.') }}</flux:text>
                <div class="mt-6">
                    <flux:button variant="primary" wire:click="openModal">
                        {{ __('Generate First Story') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($customStories as $customStory)
                <div wire:key="custom-story-{{ $customStory->id }}" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                @if ($customStory->child_image_url)
                                    <img src="{{ $customStory->child_image_url }}" alt="{{ $customStory->child_name }}" width="50" height="50" class=" rounded-full object-cover">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                        <flux:icon name="user" class="h-6 w-6 text-zinc-400" />
                                    </div>
                                @endif
                                <div>
                                    <flux:heading size="sm">{{ $customStory->child_name }}</flux:heading>
                                    <flux:text class="text-xs text-zinc-500">
                                        {{ $customStory->child_age }} {{ __('years') }}, {{ ucfirst($customStory->child_gender) }}
                                    </flux:text>
                                </div>
                            </div>
                            <flux:badge color="{{ match($customStory->status) {
                                'pending' => 'yellow',
                                'processing' => 'blue',
                                'completed' => 'green',
                                'failed' => 'red',
                                default => 'zinc'
                            } }}">
                                {{ ucfirst($customStory->status) }}
                            </flux:badge>
                        </div>

                        <flux:separator />

                        <div>
                            <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                {{ __('Story') }}
                            </flux:text>
                            <flux:text class="mt-1 line-clamp-2">{{ $customStory->story->idea }}</flux:text>
                        </div>

                        <div class="flex items-center gap-2">
                            <flux:button size="sm" :href="route('story-custom-mades.show', [$customStory->story, $customStory])" wire:navigate>
                                {{ __('View') }}
                            </flux:button>
                            <flux:button size="sm" :href="route('custom-made-images.index', [$customStory->story, $customStory])" wire:navigate>
                                {{ __('Images') }}
                            </flux:button>
                            @if ($customStory->pdf_final_url)
                                <flux:button size="sm" href="{{ $customStory->pdf_final_url }}" download>
                                    <flux:icon name="document-arrow-down" class="-ml-1 mr-1 h-4 w-4" />
                                    {{ __('PDF') }}
                                </flux:button>
                            @endif
                            <flux:modal.trigger name="delete-custom-story-{{ $customStory->id }}">
                                <flux:button size="sm" variant="danger">
                                    {{ __('Delete') }}
                                </flux:button>
                            </flux:modal.trigger>

                            <flux:modal name="delete-custom-story-{{ $customStory->id }}" class="min-w-[22rem]">
                                <div class="space-y-6">
                                    <div>
                                        <flux:heading size="lg">{{ __('Delete Custom Story') }}</flux:heading>
                                        <flux:text class="mt-2">
                                            {{ __('Are you sure you want to delete this custom story for :name? This action cannot be undone.', ['name' => $customStory->child_name]) }}
                                        </flux:text>
                                    </div>
                                    <div class="flex gap-2">
                                        <flux:modal.close>
                                            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                        </flux:modal.close>
                                        <flux:button variant="danger" wire:click="delete({{ $customStory->id }})">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </div>
                                </div>
                            </flux:modal>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $customStories->links() }}
        </div>
    @endif

    <!-- Generate Modal -->
    <flux:modal wire:model="showModal" class="w-full max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Generate Custom Story') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ __('Create a personalized story for a child.') }}
                </flux:text>
            </div>

            <form wire:submit="generate" class="space-y-4">
                <flux:select wire:model="story_id" :label="__('Select Story')">
                    <option value="">{{ __('Choose a story...') }}</option>
                    @foreach ($stories as $story)
                        <option value="{{ $story->id }}">{{ $story->idea }}</option>
                    @endforeach
                </flux:select>
                @error('story_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror

                <flux:input
                    wire:model="child_name"
                    type="text"
                    :label="__('Child Name')"
                    :placeholder="__('Enter child name')"
                    required
                />

                <div class="grid grid-cols-2 gap-4">
                    <flux:select wire:model="child_gender" :label="__('Gender')">
                        <option value="male">{{ __('Male') }}</option>
                        <option value="female">{{ __('Female') }}</option>
                    </flux:select>

                    <flux:input
                        wire:model="child_age"
                        type="number"
                        :label="__('Age')"
                        min="1"
                        max="18"
                        required
                    />
                </div>

                <div>
                    <flux:text class="mb-2 text-sm font-medium">{{ __('Child Photo') }}</flux:text>
                    <input
                        type="file"
                        wire:model="child_image"
                        accept="image/*"
                        class="block w-full text-sm text-zinc-500 file:mr-4 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-zinc-700 hover:file:bg-zinc-200 dark:text-zinc-400 dark:file:bg-zinc-800 dark:file:text-zinc-300"
                    />
                    @error('child_image') <span class="text-sm text-red-500">{{ $message }}</span> @enderror

                    @if ($child_image)
                        <div class="mt-2">
                            <img src="{{ $child_image->temporaryUrl() }}" alt="Preview" class="h-24 w-24 rounded-lg object-cover">
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <flux:button type="button" variant="ghost" wire:click="closeModal">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="generate">{{ __('Generate') }}</span>
                        <span wire:loading wire:target="generate">{{ __('Generating...') }}</span>
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
