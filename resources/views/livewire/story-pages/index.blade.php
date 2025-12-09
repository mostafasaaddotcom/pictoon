<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" :href="route('stories.show', $story)" wire:navigate>
                <flux:icon name="arrow-left" class="h-4 w-4" />
            </flux:button>
            <div>
                <flux:heading size="xl">{{ __('Page Prompts') }}</flux:heading>
                <flux:text class="text-zinc-500">{{ $story->idea }}</flux:text>
            </div>
        </div>
        <flux:button variant="primary" :href="route('story-pages.create', $story)" wire:navigate>
            {{ __('Add Page') }}
        </flux:button>
    </div>

    <x-action-message class="me-3" on="page-deleted">
        {{ __('Page deleted.') }}
    </x-action-message>

    @if ($pages->isEmpty())
        <div class="flex flex-1 items-center justify-center rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700">
            <div class="text-center">
                <flux:icon name="document-text" class="mx-auto h-12 w-12 text-zinc-400" />
                <flux:heading size="lg" class="mt-4">{{ __('No page prompts yet') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Add prompts for each page of your story.') }}</flux:text>
                <div class="mt-6">
                    <flux:button variant="primary" :href="route('story-pages.create', $story)" wire:navigate>
                        {{ __('Add First Page') }}
                    </flux:button>
                </div>
            </div>
        </div>
    @else
        <div class="grid gap-4">
            @foreach ($pages as $page)
                <div wire:key="page-{{ $page->id }}" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <flux:badge>{{ __('Page') }} {{ $page->page_number }}</flux:badge>
                                <flux:heading size="lg">{{ $page->scene_title }}</flux:heading>
                            </div>
                            <flux:text class="mt-2 line-clamp-2">{{ Str::limit($page->story_text, 150) }}</flux:text>
                            <div class="mt-3 flex items-center gap-4 text-sm text-zinc-500">
                                <span>{{ __('Art Style') }}: {{ $page->art_style }}</span>
                                @if ($page->emotions)
                                    <span>{{ __('Emotions') }}: {{ Str::limit($page->emotions, 30) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button size="sm" :href="route('story-pages.show', [$story, $page])" wire:navigate>
                                {{ __('View') }}
                            </flux:button>
                            <flux:button size="sm" :href="route('story-pages.edit', [$story, $page])" wire:navigate>
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:modal.trigger name="delete-page-{{ $page->id }}">
                                <flux:button size="sm" variant="danger">
                                    {{ __('Delete') }}
                                </flux:button>
                            </flux:modal.trigger>

                            <flux:modal name="delete-page-{{ $page->id }}" class="min-w-[22rem]">
                                <div class="space-y-6">
                                    <div>
                                        <flux:heading size="lg">{{ __('Delete Page') }}</flux:heading>
                                        <flux:text class="mt-2">
                                            {{ __('Are you sure you want to delete page :number? This action cannot be undone.', ['number' => $page->page_number]) }}
                                        </flux:text>
                                    </div>
                                    <div class="flex gap-2">
                                        <flux:modal.close>
                                            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                        </flux:modal.close>
                                        <flux:button variant="danger" wire:click="delete({{ $page->id }})">
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
