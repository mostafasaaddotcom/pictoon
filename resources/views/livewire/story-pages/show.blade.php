<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" :href="route('story-pages.index', $story)" wire:navigate>
                <flux:icon name="arrow-left" class="h-4 w-4" />
            </flux:button>
            <div>
                <flux:heading size="xl">{{ __('Page Details') }}</flux:heading>
                <flux:text class="text-zinc-500">{{ $story->idea }}</flux:text>
            </div>
        </div>
        <flux:button :href="route('story-pages.edit', [$story, $pagePrompt])" wire:navigate>
            {{ __('Edit Page') }}
        </flux:button>
    </div>

    <div class="mx-auto w-full max-w-2xl space-y-6">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <flux:badge size="lg">{{ __('Page') }} {{ $pagePrompt->page_number }}</flux:badge>
                    <flux:heading size="lg">{{ $pagePrompt->scene_title }}</flux:heading>
                </div>

                <flux:separator />

                <div>
                    <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        {{ __('Story Text') }}
                    </flux:text>
                    <flux:text class="mt-1 whitespace-pre-wrap">{{ $pagePrompt->story_text }}</flux:text>
                </div>

                <flux:separator />

                <div>
                    <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        {{ __('Image Prompt') }}
                    </flux:text>
                    <div class="mt-1 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800">
                        <flux:text class="whitespace-pre-wrap font-mono text-sm">{{ $pagePrompt->image_prompt }}</flux:text>
                    </div>
                </div>

                <flux:separator />

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Art Style') }}
                        </flux:text>
                        <flux:badge class="mt-1">{{ $pagePrompt->art_style }}</flux:badge>
                    </div>

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Emotions') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ $pagePrompt->emotions ?: __('Not specified') }}</flux:text>
                    </div>
                </div>

                <flux:separator />

                <div class="grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Created') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ $pagePrompt->created_at->format('M d, Y H:i') }}</flux:text>
                    </div>

                    <div>
                        <flux:text class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                            {{ __('Last Updated') }}
                        </flux:text>
                        <flux:text class="mt-1">{{ $pagePrompt->updated_at->format('M d, Y H:i') }}</flux:text>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
