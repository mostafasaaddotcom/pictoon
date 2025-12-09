<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" :href="route('stories.show', $story)" wire:navigate>
                <flux:icon name="arrow-left" class="h-4 w-4" />
            </flux:button>
            <div>
                <flux:heading size="xl">{{ __('Cover Prompts') }}</flux:heading>
                <flux:text class="text-zinc-500">{{ $story->idea }}</flux:text>
            </div>
        </div>
        <flux:button variant="primary" :href="route('story-covers.create', $story)" wire:navigate>
            {{ __('Add Cover') }}
        </flux:button>
    </div>

    <x-action-message class="me-3" on="cover-deleted">
        {{ __('Cover deleted.') }}
    </x-action-message>

    @if ($covers->isEmpty())
        <div class="flex flex-1 items-center justify-center rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700">
            <div class="text-center">
                <flux:icon name="photo" class="mx-auto h-12 w-12 text-zinc-400" />
                <flux:heading size="lg" class="mt-4">{{ __('No cover prompts yet') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Add prompts for front and back covers.') }}</flux:text>
                <div class="mt-6">
                    <flux:button variant="primary" :href="route('story-covers.create', $story)" wire:navigate>
                        {{ __('Add First Cover') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @else
        <div class="grid gap-4">
            @foreach ($covers as $cover)
                <div wire:key="cover-{{ $cover->id }}" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <flux:badge color="{{ $cover->type === 'front' ? 'green' : 'blue' }}">
                                    {{ $cover->type === 'front' ? __('Front Cover') : __('Back Cover') }}
                                </flux:badge>
                            </div>
                            <flux:text class="mt-2 line-clamp-2">{{ Str::limit($cover->image_prompt, 150) }}</flux:text>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button size="sm" :href="route('story-covers.show', [$story, $cover])" wire:navigate>
                                {{ __('View') }}
                            </flux:button>
                            <flux:button size="sm" :href="route('story-covers.edit', [$story, $cover])" wire:navigate>
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:modal.trigger name="delete-cover-{{ $cover->id }}">
                                <flux:button size="sm" variant="danger">
                                    {{ __('Delete') }}
                                </flux:button>
                            </flux:modal.trigger>

                            <flux:modal name="delete-cover-{{ $cover->id }}" class="min-w-[22rem]">
                                <div class="space-y-6">
                                    <div>
                                        <flux:heading size="lg">{{ __('Delete Cover') }}</flux:heading>
                                        <flux:text class="mt-2">
                                            {{ __('Are you sure you want to delete this :type cover? This action cannot be undone.', ['type' => $cover->type]) }}
                                        </flux:text>
                                    </div>
                                    <div class="flex gap-2">
                                        <flux:modal.close>
                                            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                        </flux:modal.close>
                                        <flux:button variant="danger" wire:click="delete({{ $cover->id }})">
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
    @endif
</div>
