<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" :href="route('stories.index')" wire:navigate>
                <flux:icon name="arrow-left" class="h-4 w-4" />
            </flux:button>
            <flux:heading size="xl">{{ __('Story Details') }}</flux:heading>
        </div>
        <div class="flex items-center gap-2">
            <flux:button :href="route('story-pages.index', $story)" wire:navigate>
                {{ __('Manage Pages') }}
            </flux:button>
            <flux:button :href="route('story-covers.index', $story)" wire:navigate>
                {{ __('Manage Covers') }}
            </flux:button>
            <flux:button :href="route('stories.edit', $story)" wire:navigate>
                {{ __('Edit Story') }}
            </flux:button>
        </div>
    </div>

    <div class="mx-auto w-full max-w-2xl space-y-6">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="space-y-6">
                <div>
                    <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        {{ __('Story Idea') }}
                    </flux:text>
                    <flux:heading size="lg" class="mt-1">{{ $story->idea }}</flux:heading>
                </div>

                <flux:separator />

                <div>
                    <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        {{ __('Description') }}
                    </flux:text>
                    <flux:text class="mt-1 whitespace-pre-wrap">{{ $story->description }}</flux:text>
                </div>

                <flux:separator />

                <div>
                    <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        {{ __('Moral Lesson') }}
                    </flux:text>
                    <flux:text class="mt-1">{{ $story->moral_lesson }}</flux:text>
                </div>

                <flux:separator />

                <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Language') }}
                        </flux:text>
                        <flux:badge class="mt-1">{{ strtoupper($story->language) }}</flux:badge>
                    </div>

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Pages') }}
                        </flux:text>
                        <flux:text class="mt-1 font-medium">{{ $story->pages_count }} ({{ $story->pageImagePrompts()->count() }} {{ __('prompts') }})</flux:text>
                    </div>

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Covers') }}
                        </flux:text>
                        <flux:text class="mt-1 font-medium">{{ $story->coverImagePrompts()->count() }} {{ __('prompts') }}</flux:text>
                    </div>

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Status') }}
                        </flux:text>
                        <flux:badge class="mt-1" :color="$story->is_active ? 'green' : 'zinc'">
                            {{ $story->is_active ? __('Active') : __('Inactive') }}
                        </flux:badge>
                    </div>
                </div>

                @if ($story->images && count($story->images) > 0)
                    <flux:separator />

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Cover Images') }}
                        </flux:text>
                        <div class="mt-2 grid grid-cols-2 gap-4">
                            @foreach ($story->images as $image)
                                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                                    <img src="{{ $image }}" alt="{{ __('Cover image') }}" class="h-32 w-full object-cover" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <flux:separator />

                <div class="grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Created') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ $story->created_at->format('M d, Y H:i') }}</flux:text>
                    </div>

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Last Updated') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ $story->updated_at->format('M d, Y H:i') }}</flux:text>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
