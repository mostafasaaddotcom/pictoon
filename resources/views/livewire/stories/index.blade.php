<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Stories') }}</flux:heading>
        <flux:button variant="primary" :href="route('stories.create')" wire:navigate>
            {{ __('Create Story') }}
        </flux:button>
    </div>

    <div class="flex items-center gap-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('Search stories...') }}"
            icon="magnifying-glass"
            class="max-w-sm"
        />
    </div>

    <x-action-message class="me-3" on="story-deleted">
        {{ __('Story deleted.') }}
    </x-action-message>

    @if ($stories->isEmpty())
        <div class="flex flex-1 items-center justify-center rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700">
            <div class="text-center">
                <flux:icon name="book-open" class="mx-auto h-12 w-12 text-zinc-400" />
                <flux:heading size="lg" class="mt-4">{{ __('No stories yet') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Get started by creating your first story.') }}</flux:text>
                <div class="mt-6">
                    <flux:button variant="primary" :href="route('stories.create')" wire:navigate>
                        {{ __('Create Story') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @else
        <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Idea') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Language') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Pages') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Status') }}
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">{{ __('Actions') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-900">
                    @foreach ($stories as $story)
                        <tr wire:key="story-{{ $story->id }}">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex flex-col">
                                    <flux:text class="font-medium">{{ Str::limit($story->idea, 50) }}</flux:text>
                                    <flux:text class="text-xs text-zinc-500">{{ Str::limit($story->moral_lesson, 40) }}</flux:text>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <flux:badge>{{ strtoupper($story->language) }}</flux:badge>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <flux:text>{{ $story->pages_count }}</flux:text>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <flux:switch
                                    wire:click="toggleActive({{ $story->id }})"
                                    :checked="$story->is_active"
                                />
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-end text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button size="sm" :href="route('stories.show', $story)" wire:navigate>
                                        {{ __('View') }}
                                    </flux:button>
                                    <flux:button size="sm" :href="route('stories.edit', $story)" wire:navigate>
                                        {{ __('Edit') }}
                                    </flux:button>
                                    <flux:modal.trigger name="delete-story-{{ $story->id }}">
                                        <flux:button size="sm" variant="danger">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </flux:modal.trigger>

                                    <flux:modal name="delete-story-{{ $story->id }}" class="min-w-[22rem]">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">{{ __('Delete Story') }}</flux:heading>
                                                <flux:text class="mt-2">
                                                    {{ __('Are you sure you want to delete this story? This action cannot be undone.') }}
                                                </flux:text>
                                            </div>
                                            <div class="flex gap-2">
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                                </flux:modal.close>
                                                <flux:button variant="danger" wire:click="delete({{ $story->id }})">
                                                    {{ __('Delete') }}
                                                </flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $stories->links() }}
        </div>
    @endif
</div>
